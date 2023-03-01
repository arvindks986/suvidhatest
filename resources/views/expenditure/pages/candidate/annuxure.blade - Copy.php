
@extends('expenditure.layouts.candidate.dashboard-theme')
@section('content')

<?php
  	$st 			= getstatebystatecode($candidateData->st_code);
	$namePrefix     = \Route::current()->action['prefix'];
?>
<style type="text/css">
.inputright {
	text-align: right;
}
.sch1totalerror{
	color: #ff0000;
	font-size:12px;
}
</style>
<main role="main" class="inner cover mb-1">
    <div class="card-header pt-2" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-sm-12"><h4><b> ECRP</b></h4></div>				
            </div> 
        </div>
    </div>
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <ul id="breadcrumb" class="pt-1">
                        <li><a href="#">ECRP Election Details</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>	
	<section class="tab-defrent">
		<div class="container-fluid">
			<div class="row">
				<div class="card  mt-3  p-2">								
					<ul class="tabs">
						<li>
							<a href="#tab1" >Annexure-E2</a>
						</li>						
						<li>
							<a href="#tab2">Acknowledegement Form</a>
						</li>						
					</ul>					
					<div class="tabs-content">	
					<div id="tab1" class="tabContainer">
				<form id="annuxureForm" action="#" method="post">	
				{{ csrf_field() }} 	

					<input type="hidden" name="candidate_id_update" value="<?php echo !empty($GetAbstractData[0]->candidate_id)?$GetAbstractData[0]->candidate_id:"";?>" id="candidate_id_update">

	 				<input type="hidden" name="candidate_id" value="{{$candidateData->candidate_id}}" id="candidate_id">

				<div class="tab"><!-- tab 01 -->
					<div class="table-responsive">
						<table class="table table-bordered">											 
						  <thead>
							<tr>
							  <th colspan="3" align="center" class="text-center">ABSTRACT STATEMENT OF ELECTION EXPENSES</th>							  					  
							</tr>
							<tr>
							  <th colspan="3" align="center"  class="text-center">PART 1</th>						  
							</tr>
						  </thead>
						  <tbody>
							<tr>
								<td>I</td>
								<td><label>Name of the Candidate </label> </td>
								<td>
								<div class="d-flex">
<!-- <label class="mt-2 mr-2">Sh./Smt./Km.</label>
 --><input type="text" value="{{$candidateData->cand_name}}" name="name_of_candidate" class="form-control" placeholder="" readonly>
								</div></td>
							</tr>
							<tr>
								<td>II</td>
								<td><label>Number and name of Constituency </label></td>
								<td>
									<input type="text" class="form-control" name="no_name_of_constituency" value="{{$candidateData->pc_no}} - {{$candiatePcName}} " placeholder="" readonly="readonly">
								</td>
							</tr>
							<tr>
								<td>III</td>
								<td><label>Name of State/Union Territory</label> </td>
								<td><input type="text" class="form-control" name="name_of_state" value="{{$st->ST_NAME}}" placeholder="" readonly="readonly"></td>
							</tr>
							<tr>
								<td>IV</td>
								<td><label>Nature of Election (Please mention whether General Election to State Assembly / Lok Sabha / Bye- election) <label></td>
								<td><input type="text" class="form-control" name="name_of_election" placeholder="" value="General" readonly="readonly"></td>
							</tr>
							<tr>
								<td>V</td>
								<td><label>Date of declaration of result </label></td>
								<td><input type="date" class="form-control" name="date_of_declaration_result" placeholder="" value="2019-05-23" readonly="readonly"></td>
							</tr>
							<tr>
								<td>VI</td>
								<td><label>Name and address of the Election Agent </label></td>
								<td><input type="text" class="form-control" name="name_address_election_agent" placeholder="" value="{{$candidateData->candidate_residence_address}}" readonly="readonly"></td>
							</tr>
							<tr>
								<td>VII</td>
								<td>If candidate is set up by a political party, Please mention the name of the political party</td>
								@php
									$partyData	 =	getpartybyid($candidateData->party_id);
								@endphp
								<td><input type="text" class="form-control" name="name_of_political_party" placeholder="" value="{{$partyData->PARTYNAME}}" readonly="readonly"></td>
							</tr>
							<tr>
								<td>VII</td>
								<td>Whether the party is a recognised Yes/No political party</td>
								<td>
								<select id="" name="party_recognised_status" class="form-control width-80" >
									<option value="Yes" selected="&quot;selected&quot;">Yes</option>
									<option value="No">No</option>
								</select></td>
							</tr>
							
							
							
						  </tbody>
						</table>
						
						<table class="table table-bordered">
						  <tbody>
							<tr>
								<td width="25%"><label>Date</label></td>
								<td width="25%"><?php echo date('d-m-Y'); ?></td>
								<td width="25%"><label>Signature of the Candidate</label> </td>
								<td width="25%"></td>
							</tr>	
							<tr>
								<td><label>Place</label></td>
								<td>{{$st->ST_NAME}}</td>
								<td><label>Name </label></td>
								<td>{{$candidateData->cand_name}}</td>
							</tr>
						  </tbody>
						</table>
					  </div>					  
					  <div class="clearfix"></div>
				</div><!-- tab 01 close -->
				
				<div class="tab"><!-- tab 02 open -->
					  <div class="table-responsive">
						<table class="table table-bordered">											 
						  <thead>
							<tr>
							  <th colspan="6" align="center" class="text-center">PART-II ABSTRACT STATEMENT OF ELECTION EXPENSES EXPENDITURE OF CANDIDATE </th>							  					  
							</tr>
							<tr>
							  <th align="center"  class="text-center">S. No.</th>						  
							  <th align="center"  class="text-center">Particulars </th>						  
							  <th align="center"  class="text-center">Amt. Incurred / Auth. by Candidate Election Agent (in Rs.) </th>						  
							  <th align="center"  class="text-center">Amt. Incurred authorized by Pol. Party(in Rs.)  </th>						  
							  <th align="center"  class="text-center">Amt. Incurred / authorized by Other(in Rs.)  </th>						  
							  <th align="center"  class="text-center">Total Expenditure (3) + (4) + (5)  </th>						  
							</tr>
						  </thead>
						  <tbody>	
							<tr>
								<td>1</td>
								<td>2</td>
								<td>3</td>
								<td>4</td>
								<td>5</td>
								<td>6</td>
							</tr>
							<tr>
								<td>I</td>
								<td><label>Expenses in public meeting, rally,procession etc.:-l.a.: Expences in public meeting , rally, procession etc. (i.e. other than once with the Star Campaigners of the political party)<br>(Enclose as per Schedule-3)</label></td>
								<td><input type="text" class="form-control amt1 inputright" name="public_expenses_meeting_star_3" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_star_3)?$GetAbstractData[0]->public_expenses_meeting_star_3:"";?>" pattern="\d*" maxlength="7" placeholder="0"></td>
								<td><input type="text" class="form-control amt2 inputright" name="public_expenses_meeting_star_4" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_star_4)?$GetAbstractData[0]->public_expenses_meeting_star_4:"";?>"></td>
								<td>
									<input type="text" class="form-control amt3 inputright" name="public_expenses_meeting_star_5" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_star_5)?$GetAbstractData[0]->public_expenses_meeting_star_5:"";?>"></td>
								<td><input type="text" class="form-control subtotal" name="public_expenses_meeting_star_6" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_star_6)?$GetAbstractData[0]->public_expenses_meeting_star_6:"";?>" readonly></td>
							</tr>
							<tr>
								<td></td>
								<td><label>l. b. expenditure in public meeting rally, procession etc. with the star Campaigner(s) (ie other than those for general party propaganda)<br>(Enclose as per Schedule-2)</label>	</td>
								<td><input type="text" class="form-control amt1 inputright" name="public_expenses_meeting_general_3" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_general_3)?$GetAbstractData[0]->public_expenses_meeting_general_3:"";?>"></td>
								<td><input type="text" class="form-control amt2  inputright" name="public_expenses_meeting_general_4" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_general_4)?$GetAbstractData[0]->public_expenses_meeting_general_4:"";?>"></td>
								<td><input type="text" class="form-control amt3  inputright" name="public_expenses_meeting_general_5" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_general_5)?$GetAbstractData[0]->public_expenses_meeting_general_5:"";?>"></td>
								<td><input type="text" class="form-control subtotal" name="public_expenses_meeting_general_6" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->public_expenses_meeting_general_6)?$GetAbstractData[0]->public_expenses_meeting_star_3:"";?>" readonly></td>
							</tr>
							<tr>
								<td>II</td>
								<td><label>Campaign materials other,rally, procession etc. mentioned in S. No.I above(Enclose as per Schedule-3)</label></td>
								<td><input type="text" class="form-control amt1  inputright" name="compaign_material_3" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_material_3)?$GetAbstractData[0]->compaign_material_3:"";?>"></td>
								<td><input type="text" class="form-control amt2  inputright" name="compaign_material_4" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_material_4)?$GetAbstractData[0]->compaign_material_4:"";?>"></td>
								<td><input type="text" class="form-control amt3 inputright" name="compaign_material_5" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_material_5)?$GetAbstractData[0]->compaign_material_5:"";?>"></td>
								<td><input type="text" class="form-control subtotal" name="compaign_material_6" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_material_6)?$GetAbstractData[0]->compaign_material_6:"";?>" readonly></td>
							</tr>
							<tr> 
								<td>III</td>
								<td><label>Campaign, through print and electronic media including cable network, bulk SMS or internet and Social media (Enclose as per Schedule-4)</label></td>
								<td><input type="text" class="form-control amt1 inputright" name="compaign_through_print_media_3" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_through_print_media_3)?$GetAbstractData[0]->compaign_through_print_media_3:"";?>"></td>
								<td><input type="text" class="form-control amt2 inputright" name="compaign_through_print_media_4" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_through_print_media_4)?$GetAbstractData[0]->compaign_through_print_media_4:"";?>"></td>
								<td><input type="text" class="form-control amt3  inputright" name="compaign_through_print_media_5" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_through_print_media_5)?$GetAbstractData[0]->compaign_through_print_media_5:"";?>"></td>
								<td><input type="text" class="form-control subtotal" name="compaign_through_print_media_6" pattern="\d*" maxlength="7" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->compaign_through_print_media_6)?$GetAbstractData[0]->compaign_through_print_media_6:"";?>" readonly></td>
							</tr>
							<tr>
								<td>IV</td>
								<td><label>Expenditure on campaign vehicle(s), used by candidate(Enclose as per schedule-5)</label></td>
								<td><input type="text" name="expenditure_on_compaign_vehicle_3" pattern="\d*" maxlength="7" class="form-control amt1  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_vehicle_3)?$GetAbstractData[0]->expenditure_on_compaign_vehicle_3:"";?>"></td>
								<td><input type="text" name="expenditure_on_compaign_vehicle_4" pattern="\d*" maxlength="7" class="form-control amt2  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_vehicle_4)?$GetAbstractData[0]->expenditure_on_compaign_vehicle_4:"";?>"></td>
								<td><input type="text" name="expenditure_on_compaign_vehicle_5" pattern="\d*" maxlength="7" class="form-control amt3  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_vehicle_5)?$GetAbstractData[0]->expenditure_on_compaign_vehicle_5:"";?>"></td>
								<td><input type="text" name="expenditure_on_compaign_vehicle_6" pattern="\d*" maxlength="7" class="form-control subtotal" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_vehicle_6)?$GetAbstractData[0]->expenditure_on_compaign_vehicle_6:"";?>" readonly></td>
							</tr>
							<tr>
								<td>V</td>
								<td><label>Expenses of campaign workers / agents (Enclose as per Schedule —6)</label></td>
								<td><input type="text" name="expenditure_on_compaign_workers_3" pattern="\d*" maxlength="7" class="form-control amt1  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_workers_3)?$GetAbstractData[0]->expenditure_on_compaign_workers_3:"";?>"></td>
								<td><input type="text" name="expenditure_on_compaign_workers_4" pattern="\d*" maxlength="7" class="form-control amt2  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_workers_4)?$GetAbstractData[0]->expenditure_on_compaign_workers_4:"";?>"></td>
								<td><input type="text" name="expenditure_on_compaign_workers_5" pattern="\d*" maxlength="7" class="form-control amt3  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_workers_5)?$GetAbstractData[0]->expenditure_on_compaign_workers_5:"";?>"></td>
								<td><input type="text" name="expenditure_on_compaign_workers_6" pattern="\d*" maxlength="7" class="form-control subtotal" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenditure_on_compaign_workers_6)?$GetAbstractData[0]->expenditure_on_compaign_workers_6:"";?>" readonly></td>
							</tr>
							<tr>
								<td>VI</td>
								<td><label>Any other campaign expenditure</label></td>
								<td><input type="text" name="any_other_compaign_expenditure_3" pattern="\d*" maxlength="7" class="form-control amt1  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->any_other_compaign_expenditure_3)?$GetAbstractData[0]->any_other_compaign_expenditure_3:"";?>"></td>
								<td><input type="text" name="any_other_compaign_expenditure_4" pattern="\d*" maxlength="7" class="form-control amt2  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->any_other_compaign_expenditure_4)?$GetAbstractData[0]->any_other_compaign_expenditure_4:"";?>"></td>
								<td><input type="text" name="any_other_compaign_expenditure_5" pattern="\d*" maxlength="7" class="form-control amt3  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->any_other_compaign_expenditure_5)?$GetAbstractData[0]->any_other_compaign_expenditure_5:"";?>"></td>
								<td><input type="text" name="any_other_compaign_expenditure_6" pattern="\d*" maxlength="7" class="form-control subtotal" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->any_other_compaign_expenditure_6)?$GetAbstractData[0]->any_other_compaign_expenditure_6:"";?>" readonly></td>
							</tr>
							<tr>
								<td>VII</td>
								<td><label>Expenses incurred on publishing of declaration regarding criminal cases (Enclose as per Schedule-10)*</label></td>
								<td><input type="text" name="expenses_incurred_on_publishing_3" pattern="\d*" maxlength="7" class="form-control amt1  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenses_incurred_on_publishing_3)?$GetAbstractData[0]->expenses_incurred_on_publishing_3:"";?>"></td>

								<td><input type="text" name="expenses_incurred_on_publishing_4" pattern="\d*" maxlength="7" class="form-control amt2  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenses_incurred_on_publishing_4)?$GetAbstractData[0]->expenses_incurred_on_publishing_4:"";?>"></td>

								<td><input type="text" name="expenses_incurred_on_publishing_5" pattern="\d*" maxlength="7" class="form-control amt3  inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenses_incurred_on_publishing_5)?$GetAbstractData[0]->expenses_incurred_on_publishing_5:"";?>"></td>

								<td><input type="text" name="expenses_incurred_on_publishing_6" pattern="\d*" maxlength="7" class="form-control subtotal" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->expenses_incurred_on_publishing_6)?$GetAbstractData[0]->expenses_incurred_on_publishing_6:"";?>" readonly></td>
							</tr>
													
						  </tbody>
						  <tfoot>							
							<tr>
								<td></td>
								<td><strong>Grand Total</strong></td>
								<td><input type="text" name="grand_total_candidate_agent" id="grand_total_candidate_agent" pattern="\d*" maxlength="7" class="form-control " id="" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->grand_total_candidate_agent)?$GetAbstractData[0]->grand_total_candidate_agent:"";?>" readonly></td>
								<td><input type="text" name="grand_total_amt_incurred_by_pol_party" id="grand_total_amt_incurred_by_pol_party" pattern="\d*" maxlength="7" class="form-control " placeholder="0" value="<?php echo !empty($GetAbstractData[0]->grand_total_amt_incurred_by_pol_party)?$GetAbstractData[0]->grand_total_amt_incurred_by_pol_party:"";?>" readonly></td>
								<td><input type="text" name="grand_total_amt_incurred_by_other" id="grand_total_amt_incurred_by_other" pattern="\d*" maxlength="7" class="form-control " placeholder="0" value="<?php echo !empty($GetAbstractData[0]->grand_total_amt_incurred_by_other)?$GetAbstractData[0]->grand_total_amt_incurred_by_other:"";?>" readonly></td>
								
								<td><input type="text" name="total_expenditure"  id="total" class="form-control" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->total_expenditure)?$GetAbstractData[0]->total_expenditure:"";?>" readonly></td>
							</tr>
						  </tfoot>
						</table>						
					  </div>
					  <div class="clearfix"></div>	
					  
				</div><!-- tab 02 close -->	 
				<div class="tab">
					  <div class="table-responsive">
						<table class="table table-bordered">											 
						  <thead>
							<tr>
							  <th colspan="3" align="center" class="text-center">PART-III: ABSTRACT OF SOURCE OF FUNDS RAISED BY CANDIDATE </th>							  					  
							</tr>
							<tr>
							  <th align="center"  class="text-center">S. No.</th>						  
							  <th align="center"  class="text-center">Particulars </th>						  
							  <th align="center"  class="text-center">Amount (in Rs.) </th>						  
							  					  
							</tr>
						  </thead>
						  <tbody>	
							<tr>
								<td>1</td>
								<td>2</td>
								<td>3</td>								
							</tr>
							<tr>
								<td>I</td>
								<td><label>Amount of own funds used for the election campaign (
								Enclose as per Schedule-7)</label></td>
								<td><input type="text" name="amt_own_funds_election_compaign" pattern="\d*" maxlength="7" class="form-control fund1 inputright" placeholder="0"  value="<?php echo !empty($GetAbstractData[0]->amt_own_funds_election_compaign)?$GetAbstractData[0]->amt_own_funds_election_compaign:"";?>"></td>								
							</tr>
							<tr>
								<td>II</td>
								<td><label>Lump sum amount received from the party (ies) in cash or cheque etc. <br>
								(Enclose as per Schedule-8)</label></td>
								<td><input type="text" name="lump_sum_amt_from_party" pattern="\d*" maxlength="7" class="form-control fund2 inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->lump_sum_amt_from_party)?$GetAbstractData[0]->lump_sum_amt_from_party:"";?>"></td>								
							</tr>
							<tr>
								<td>III</td>
								<td><label>Lump sum amount received from any persion/company/firm/association/body of persons etc. as loan. gift or donation etc.<br>(
								Enclose as per Schedule-9)</label></td>
								<td><input type="text" name="lump_sum_amt_from_other" pattern="\d*" maxlength="7" class="form-control fund3 inputright" placeholder="0" value="<?php echo !empty($GetAbstractData[0]->lump_sum_amt_from_other)?$GetAbstractData[0]->lump_sum_amt_from_other:"";?>"></td>								
							</tr>													
						  </tbody>
						  <tfoot>							
							<tr>
								<td></td>
								<td><strong>Grand Total</strong></td>
								<td><span id="fundtotal"></span></td>								
							</tr>
						  </tfoot>
						</table>						
					  </div>					  
					<div class="clearfix"></div>
				</div><!-- tab 03 close --> 

					
				<div style="overflow:auto;">
				  <div style="float:right;">
					<button type="button" class="btn btn-primary" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
					<button type="button" class="btn btn-primary" id="nextBtn" onclick="nextPrev(1)">Next</button>
				  </div>

				  <div class="successClass"><span class="successAnnuxure"></span></div>
				</div>
				
				<div style="text-align:center;margin-top:40px;">
				  <span class="step"></span>
				  <span class="step"></span>
				  <span class="step"></span>				  
				</div>
				
					</form>  
					</div><!-- tab-1 --->
						
						<div id="tab2" class="tabContainer">
							<!-- indu -->
							
				<form id="aknolwdgeForm" action="action="{{url('printEcrpStatusReport')}}"" method="post">				
					  <div class="ScheduleTab">
					  <div class="table-responsive">
						<h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch1">
                          <thead class="text-center">
                            <tr>
                              <th colspan="6" align="center"><h5><strong>Schedule - 1</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="6">Expenses in public meeting, rally, procession etc, (ie: other than those with Star Campaigners of the Political party)</th>
                            </tr>
                            <tr>
                              <th rowspan="2">S. No</th>
                              <th rowspan="2">Nature of Expenditure</th>
                              <th rowspan="2">Total Amount in Rs.</th>
                              <th colspan="3">Source of Expenditure</th>
                            </tr>
                            <tr>
                              
                              <th >Amt. Incurred / Auth. by Candidate / Agent</th>
                              <th >Amt. Incurred / by Pol. Party with Name</th>
                              <th >Amt. Incurred by Others</th>                  
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                            </tr>

                            @if(!empty($getSch1))
	                            @foreach($getSch1 as $getExpSch1)
	                            
	                            <tr>
	                                <td>@php echo $getExpSch1->id @endphp</td>
	                                <td><label>@php echo $getExpSch1->naturename @endphp</label></td>
	                                <input type="hidden" name="@php echo $getExpSch1->id @endphp['fund'][nature_of_exp_id]" value="@php echo $getExpSch1->id @endphp" />

	                                <td><input type="text" name="@php echo $getExpSch1->id @endphp['fund'][total_amt]"  pattern="\d*" maxlength="7" class="form-control inputright s1amt src_total_amt ackSch1input" placeholder="0" value="@php echo !empty($getExpSch1->total_amt)? $getExpSch1->total_amt:""; @endphp" readonly>
	                                	<span class="sumerror"></span>
	                                </td>


	                                <td><input type="text" name="@php echo $getExpSch1->id @endphp['fund'][src_amt_incurred_cand]" id="@php echo $getExpSch1->id @endphp['fund'][src_amt_incurred_cand]" pattern="^[0–9]$" maxlength="7" class="form-control inputright s1amt1 ackSch1input src_amt_incurred_cand" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_cand)? $getExpSch1->src_amt_incurred_cand:""; @endphp"></td>

	                                <td><input type="text" name="@php echo $getExpSch1->id @endphp['fund'][src_amt_incurred_pp]" pattern="\d*" maxlength="7" class="form-control inputright s1amt2 ackSch1input src_amt_incurred_pp" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_pp)? $getExpSch1->src_amt_incurred_pp:""; @endphp"></td>

	                                <td><input type="text" name="@php echo $getExpSch1->id @endphp['fund'][src_amt_incurred_other]" pattern="\d*" maxlength="7" class="form-control inputright s1amt3 ackSch1input src_amt_incurred_other" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_other)? $getExpSch1->src_amt_incurred_other:""; @endphp"></td>
	                            </tr>
	                            @endforeach
	                        @else
	                        	@foreach($natureofExp as $natofExp)
	                        	<tr>
	                                <td>@php echo $natofExp['id'] @endphp</td>
	                                <td><label>@php echo $natofExp['naturename'] @endphp</label></td>
	                                <input type="hidden" name="@php echo $natofExp['id'] @endphp['fund'][nature_of_exp_id]" value="@php echo $natofExp['id'] @endphp" />

	                                <td><input type="text" name="@php echo $natofExp['id'] @endphp['fund'][total_amt]"  pattern="\d*" maxlength="7" class="form-control inputright src_total_amt s1amt ackSch1input" placeholder="0" value="@php echo !empty($getExpSch1->total_amt)? $getExpSch1->total_amt:""; @endphp" readonly></td>

	                                <td><input type="text" name="@php echo $natofExp['id'] @endphp['fund'][src_amt_incurred_cand]" id="1['fund'][src_amt_incurred_cand]" pattern="\d*" maxlength="7" class="form-control inputright ackSch1input s1amt1 src_amt_incurred_cand" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_cand)? $getExpSch1->src_amt_incurred_cand:""; @endphp"></td>

	                                <td><input type="text" name="@php echo $natofExp['id'] @endphp['fund'][src_amt_incurred_pp]" pattern="\d*" maxlength="7" class="form-control inputright s1amt2 ackSch1input src_amt_incurred_pp" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_pp)? $getExpSch1->src_amt_incurred_pp:""; @endphp"></td>

	                                <td><input type="text" name="@php echo $natofExp['id'] @endphp['fund'][src_amt_incurred_other]" pattern="\d*" maxlength="7" class="form-control inputright s1amt3 ackSch1input src_amt_incurred_other" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_other)? $getExpSch1->src_amt_incurred_other:""; @endphp"></td>
	                            </tr>
	                            @endforeach
	                        @endif
                            <tr>
                                <td>&nbsp;</td>
                                <td><label>Total</label></td>
                                <td><input type="text" class="form-control st1 total_amt" placeholder="" readonly="readonly">
                                	<span class="sch1totalerror"></span>
                                </td>
                                <td><input type="text" class="form-control st2 total_cand_amt" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control st3 total_pp_amt" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control st4 total_other_amt" placeholder="" readonly="readonly"></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
					  
					</div><!--  ScheduleTab close -->  
					<div class="ScheduleTab">
                      <div class="table-responsive">
					  <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch2">
                          <thead class="text-center">
                            <tr>
                              <th colspan="8" align="center"><h5><strong>Schedule - 2</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="8">Expenditure in public meeting rally, procession etc. with the Star Campaigner(s) as apportioned to candidate (ie: other than those for general party propaganda)</th>
                            </tr>
                            <tr>
                              <th>S. No</th>
                              <th>Date</th>
                              <th>Venue</th>
                              <th>Name of the Star Campaigner(s) & Name of the Party</th>
                              <th colspan="3">Amount of Expenditure on public meeting rally, procession etc. with the Star Campaigner(s) apportioned to the candidate (As other than for general party propaganda) in Rs. </th>
                              <th >Remarks If Any </th>
                            </tr>
                            <tr>
                                <th rowspan="3">1</th>
                                <th rowspan="3">2</th>
                                <th rowspan="3">3</th>
                                <th rowspan="3">4</th>
                                <th colspan="3">5</th>
                                <th rowspan="3">6</th>                                
                            </tr>
                            <tr>
                                <th colspan="3">Source of Expenditure </th>
                            </tr>
                            <tr>
                              <th >Amount by Candidate/Agent </th>
                              <th >Amount by Political Party </th>
                              <th >Amount by Others </th>                  
                            </tr>
                          </thead>
                            <tbody>
                            
                             @if(!empty($getSch2))
	                            @foreach($getSch2 as $getExpSch2)
	                            	<tr>
		                                <td>@php echo $getExpSch2->id @endphp</td>
		                                <td><input type="text" class="form-control campdatetimepicker" name="@php echo $getExpSch2->id @endphp['starcampaigner'][meetingdate]" placeholder="" value="@php echo $getExpSch2->meetingdate @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch2->id @endphp['starcampaigner'][venue]" placeholder="" value="@php echo  $getExpSch2->venue ; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch2->id @endphp['starcampaigner'][name_of_start_and_party]" placeholder="" value="@php echo $getExpSch2->name_of_start_and_party @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" name="@php echo $getExpSch2->id @endphp['starcampaigner'][src_amt_by_cand]" maxlength="7" placeholder="" value="@php echo $getExpSch2->src_amt_by_cand ; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" name="@php echo $getExpSch2->id @endphp['starcampaigner'][src_amt_by_pp]" maxlength="7" placeholder="" value="@php echo $getExpSch2->src_amt_by_pp ; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" name="@php echo $getExpSch2->id @endphp['starcampaigner'][src_amt_by_other]" maxlength="7" placeholder="" value="@php echo $getExpSch2->src_amt_by_other ;  @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch2->id @endphp['starcampaigner'][remarks]" placeholder="" value="@php echo $getExpSch2->remarks ; @endphp"></td>
		                            </tr>
	                            @endforeach
	                         @else
	                         	<tr>
	                                <td>1</td>
	                                <td><input type="text" class="form-control campdatetimepicker" name="1['starcampaigner'][meetingdate]" placeholder="" id="campdatetimepicker1"></td>
	                                <td><input type="text" class="form-control" name="1['starcampaigner'][venue]" placeholder=""></td>
	                                <td><input type="text" class="form-control" name="1['starcampaigner'][name_of_start_and_party]" placeholder=""></td>
	                                <td><input type="text" class="form-control ackSch1input" name="1['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="1['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="1['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control" name="1['starcampaigner'][remarks]" placeholder=""></td>
	                            </tr>
	                            <tr>
	                                <td>2</td>
	                                <td><input type="text" class="form-control campdatetimepicker" name="2['starcampaigner'][meetingdate]" placeholder="" id="campdatetimepicker2"></td>
	                                <td><input type="text" class="form-control" name="2['starcampaigner'][venue]" placeholder=""></td>
	                                <td><input type="text" class="form-control" name="2['starcampaigner'][name_of_start_and_party]" placeholder=""></td>
	                                <td><input type="text" class="form-control ackSch1input" name="2['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="2['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="2['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control" name="2['starcampaigner'][remarks]" placeholder=""></td>
	                            </tr>
	                            <tr>
	                                <td>3</td>
	                                <td><input type="text" class="form-control campdatetimepicker" name="3['starcampaigner'][meetingdate]" placeholder=""></td>
	                                <td><input type="text" class="form-control" name="3['starcampaigner'][venue]" placeholder=""></td>
	                                <td><input type="text" class="form-control" name="3['starcampaigner'][name_of_start_and_party]" placeholder=""></td>
	                                <td><input type="text" class="form-control ackSch1input" name="3['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="3['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="3['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control" name="3['starcampaigner'][remarks]" placeholder=""></td>
	                            </tr>
	                            <tr>
	                                <td>4</td>
	                                <td><input type="text" class="form-control campdatetimepicker" name="4['starcampaigner'][meetingdate]" placeholder=""></td>
	                                <td><input type="text" class="form-control" name="4['starcampaigner'][venue]" placeholder=""></td>
	                                <td><input type="text" class="form-control" name="4['starcampaigner'][name_of_start_and_party]" placeholder=""></td>
	                                <td><input type="text" class="form-control ackSch1input" name="4['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="4['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control ackSch1input" name="4['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
	                                <td><input type="text" class="form-control" name="4['starcampaigner'][remarks]" placeholder=""></td>
	                            </tr>
                            @endif
                            <tfoot>
                                <tr>
	                                <td colspan="3">Total</td>
	                                <td colspan="3">&nbsp;</td>
	                                <td colspan="3">&nbsp;</td>
	                            </tr>
                            <tfoot>
                           
                          </tbody>
                        </table>
                      </div>
					</div><!-- ScheduleTab close-->
					<div class="ScheduleTab">

                       <div class="table-responsive">
					   <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch3">
                          <thead class="text-center">
                            <tr>3
                              <th colspan="7" align="center"><h5><strong>Schedule - 3</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="7">Details of expenditure on campaign material, like handbills, pamphlets, posters, hoardings, banners, cut-outs, gates & arches, video and audio cassettes, CDs/DVDs, Loud Speakers, amplifiers, digital TV/board display, 3 D display etc. for candidate’s election campaign (ie: other than those covered in Schedule- 1 & 2)</th>
                            </tr>
                            <tr>
                              <th rowspan="2">S. No</th>
                              <th rowspan="2">Nature of Expenses</th>
                              <th rowspan="2">Total Amount in Rs.</th>
                              <th colspan="3">Source of Expenditure</th>
                              <th rowspan="2">Remarks If Any</th>
                            </tr>
                            <tr>
                              
                              <th >Amt. By Candidate / Agent</th>
                              <th >Amt. By Pol. Party</th>
                              <th >Amt. by Others</th>                  
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                            </tr>
                            @if(!empty($getSch3))
	                            @foreach($getSch3 as $getExpSch3)
	                                <tr>
		                                <td>@php echo $getExpSch3->id @endphp</td>
		                                
		                                <td><input type="text" class="form-control" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][nature_of_expense]" placeholder="" value="@php echo !empty($getExpSch3->nature_of_expense)? $getExpSch3->nature_of_expense:""; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" readonly name="@php echo $getExpSch3->id @endphp['campaigmaterial'][total_amt]" placeholder="" value="@php echo !empty($getExpSch3->total_amt)? $getExpSch3->total_amt:""; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][src_amt_by_cand]" placeholder="" value="@php echo !empty($getExpSch3->src_amt_by_cand)? $getExpSch3->src_amt_by_cand:""; @endphp" maxlength="7"></td>

		                                <td><input type="text" class="form-control ackSch1input" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][src_amt_by_pp]" placeholder="" value="@php echo !empty($getExpSch3->src_amt_by_pp)? $getExpSch3->src_amt_by_pp:""; @endphp" maxlength="7"></td>

		                                <td><input type="text" class="form-control ackSch1input" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][src_amt_by_other]" placeholder="" value="@php echo !empty($getExpSch3->src_amt_by_other)? $getExpSch3->src_amt_by_other:""; @endphp" maxlength="7"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][remarks]" placeholder="" value="@php echo !empty($getExpSch3->remarks)? $getExpSch3->remarks:""; @endphp"></td>
		                            </tr>
	                         	@endforeach
	                        @else
                            <tr>
                                <td>1</td>
                                <td><input type="text" class="form-control" name="1['campaigmaterial'][nature_of_expense]" placeholder=""></td>
                                <td><input type="text" class="form-control ackSch1input" name="1['campaigmaterial'][total_amt]" readonly placeholder=""></td>
                                <td><input type="text" class="form-control ackSch1input" name="1['campaigmaterial'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="1['campaigmaterial'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="1['campaigmaterial'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control" name="1['campaigmaterial'][remarks]" placeholder=""></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><input type="text" class="form-control" name="2['campaigmaterial'][nature_of_expense]" placeholder=""></td>
                                <td><input type="text" class="form-control ackSch1input" name="2['campaigmaterial'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" name="2['campaigmaterial'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="2['campaigmaterial'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="2['campaigmaterial'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control" name="2['campaigmaterial'][remarks]" placeholder=""></td>
                            </tr>
                             <tr>
                                <td>3</td>
                                <td><input type="text" class="form-control" name="3['campaigmaterial'][nature_of_expense]" placeholder=""></td>
                                <td><input type="text" class="form-control ackSch1input" name="3['campaigmaterial'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" name="3['campaigmaterial'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="3['campaigmaterial'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="3['campaigmaterial'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control" name="3['campaigmaterial'][remarks]" placeholder=""></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><input type="text" class="form-control" name="4['campaigmaterial'][nature_of_expense]" placeholder=""></td>
                                <td><input type="text" class="form-control ackSch1input" name="4['campaigmaterial'][total_amt]" placeholder="" readonly></td>
                                <td><input type="text" class="form-control ackSch1input" name="4['campaigmaterial'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="4['campaigmaterial'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="4['campaigmaterial'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control" name="4['campaigmaterial'][remarks]" placeholder=""></td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="2">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">
                 <div class="table-responsive">
				 <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch4">
                          <thead class="text-center">
                            <tr>
                              <th colspan="11" align="center"><h5><strong>Schedule - 4</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="11">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in privately owned newspapers/TV/radio channels etc.</th>
                            </tr>
                            <tr>
                              <th rowspan="2">S. No.</th>
                              <th rowspan="2">Nature of medium  (electronic/print) and duration</th>
                              <th rowspan="2">Name of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
                              <th rowspan="2">Address of media provider </th>
                              <th rowspan="2">Price of Media</th>
                              <th rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
                              <th rowspan="2">Commission of Agency</th>
                              <th rowspan="2">Total Amount in Rs. <br />Col. (6)+(8)</th>
                              <th colspan="3">Sources of Expenditure</th>
                            </tr>
                            <tr>
                              <th >Amt. By candidate/agent</th>
                              <th >Amt. By Pol. Party</th>
                              <th >Amt. By others</th>                  
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                                <td>8</td>
                                <td>9</td>
                                <td>10</td>
                                <td>11</td>
                            </tr>
                           @if(!empty($getSch4))
	                            @foreach($getSch4 as $getExpSch4)
	                                <tr>
		                                <td>@php echo $getExpSch4->id @endphp</td>
		                              
		                                <td>
		                                	<select class="form-control" name="@php echo $getExpSch4->id @endphp['expdetails'][nature_of_medium]" @endphp>
			                                	<option value="">--Select Media--</option>
			                                	<option value="Electronic Media" @if($getExpSch4->nature_of_medium == 'Electronic Media') selected="selected" @endif >Electronic Media</option>	
			                                	<option value="Print Media"@if($getExpSch4->nature_of_medium == 'Print Media') selected="selected" @endif>Print Media</option>
			                                </select>
		                                </td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch4->id @endphp['expdetails'][name_of_media]" placeholder="" value="@php echo !empty($getExpSch4->name_of_media)? $getExpSch4->name_of_media:""; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch4->id @endphp['expdetails'][address_of_media]" placeholder="" value="@php echo !empty($getExpSch4->address_of_media)? $getExpSch4->address_of_media:""; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch4->id @endphp['expdetails'][name_address_of_agency]" placeholder="" value="@php echo !empty($getExpSch4->name_address_of_agency)? $getExpSch4->name_address_of_agency:""; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch4->id @endphp['expdetails'][price_of_the_media]" placeholder="" value="@php echo !empty($getExpSch4->price_of_the_media)? $getExpSch4->price_of_the_media:""; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExpSch4->id @endphp['expdetails'][commission_of_agency]" placeholder="" value="@php echo !empty($getExpSch4->commission_of_agency)? $getExpSch4->commission_of_agency:""; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" readonly name="@php echo $getExpSch4->id @endphp['expdetails'][total_amt]" placeholder="" value="@php echo $getExpSch4->total_amt ; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" maxlength="7" name="@php echo $getExpSch4->id @endphp['expdetails'][src_amt_by_cand]" placeholder="" value="@php echo !empty($getExpSch4->src_amt_by_cand)? $getExpSch4->src_amt_by_cand:""; @endphp"></td>

		                               <td><input type="text" class="form-control ackSch1input" maxlength="7" name="@php echo $getExpSch4->id @endphp['expdetails'][src_amt_by_pp]" placeholder="" value="@php echo !empty($getExpSch4->src_amt_by_pp)? $getExpSch4->src_amt_by_pp:""; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" maxlength="7" name="@php echo $getExpSch4->id @endphp['expdetails'][src_amt_by_other]" placeholder="" value="@php echo !empty($getExpSch4->src_amt_by_other)? $getExpSch4->src_amt_by_other:""; @endphp"></td>
		                            </tr>		                        
		                        @endforeach
		                    @else
		                    <tr>
                                <td>1</td>
                                <td><select class="form-control" name="1['expdetails'][nature_of_medium]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][name_of_media]"placeholder=""></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][price_of_the_media]"placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="1['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="1['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control ackSch1input subtotal4a" name="1['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" name="1['expdetails'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="1['expdetails'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="1['expdetails'][src_amt_by_other]" maxlength="7" placeholder=""></td>
                            </tr>
                            <tr>
                            	<td>2</td>
                                <td><select class="form-control" name="2['expdetails'][nature_of_medium]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][name_of_media]"placeholder=""></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][price_of_the_media]"placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="2['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="2['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control ackSch1input subtotal4a" name="2['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" name="2['expdetails'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="2['expdetails'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="2['expdetails'][src_amt_by_other]" maxlength="7" placeholder=""></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><select class="form-control" name="3['expdetails'][nature_of_medium]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][name_of_media]"placeholder=""></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][price_of_the_media]"placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="3['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="3['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control ackSch1input subtotal4a" name="3['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" name="3['expdetails'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="3['expdetails'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="3['expdetails'][src_amt_by_other]" maxlength="7" placeholder=""></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><select class="form-control" name="4['expdetails'][nature_of_medium]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][name_of_media]"placeholder=""></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][price_of_the_media]"placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="4['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="4['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control ackSch1input subtotal4a" name="4['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" name="4['expdetails'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="4['expdetails'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" name="4['expdetails'][src_amt_by_other]" maxlength="7" placeholder=""></td>
                            </tr>
                            @endif
                             <tr>
                                <td colspan="7">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">


                 <div class="table-responsive">
				 <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch5">
                          <thead class="text-center">
                            <tr>
                              <th colspan="11" align="center"><h5><strong>Schedule - 4A</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="11">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in newspapers/TV/radio channels, owned by the candidate or by the political party sponsoring the candidate.</th>
                            </tr>
                            <tr>
                              <th rowspan="2">S. No.</th>
                              <th rowspan="2">Nature of medium  (electronic/print) and duration</th>
                              <th rowspan="2">Name of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
                              <th rowspan="2">Address of media provider </th>
                              <th rowspan="2">Price of Media</th>
                              <th rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
                              <th rowspan="2">Commission of Agency</th>
                              <th rowspan="2">Total Amount in Rs. <br />Col. (6)+(8)</th>
                              <th colspan="3">Sources of Expenditure</th>
                            </tr>
                            <tr>
                              
                              <th >Amt. By candidate/agent</th>
                              <th >Amt. By Pol. Party</th>
                              <th >Amt. By others</th>                  
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                                <td>8</td>
                                <td>9</td>
                                <td>10</td>
                                <td>11</td>
                            </tr>
                            @if(!empty($getSch4a))
                            	@foreach($getSch4a as $getExp4a)
                            		<tr>
		                                <td>@php echo $getExp4a->id @endphp
		                                	<input type="hidden" name="@php echo $getExp4a->id @endphp['expdetails'][id]" value="@php echo $getExp4a->id @endphp">
		                                </td>
		                                <td>
		                                <select class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][nature_of_media]" @endphp>
		                                	<option value="">--Select Media--</option>
		                                	<option value="Electronic Media" @if($getExp4a->nature_of_media == 'Electronic Media') selected="selected" @endif >Electronic Media</option>	
		                                	<option value="Print Media"@if($getExp4a->nature_of_media == 'Print Media') selected="selected" @endif>Print Media</option>
		                                </select>
		                                </td>
		                                <td><input type="text" class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][name_of_media]" placeholder="" value="@php echo !empty($getExp4a->name_of_media)? $getExp4a->name_of_media:""; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][address_of_media]" placeholder="" value="@php echo !empty($getExp4a->address_of_media)? $getExp4a->address_of_media:""; @endphp"></td>

		                                <td><input type="text" class="form-control priceofmedia" name="@php echo $getExp4a->id @endphp['expdetails'][price_of_the_media]" placeholder="" value="@php echo !empty($getExp4a->price_of_the_media)? $getExp4a->price_of_the_media:""; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][name_address_of_agency]" placeholder="" value="@php echo !empty($getExp4a->name_address_of_agency)? $getExp4a->name_address_of_agency:""; @endphp"></td>

		                                <td><input type="text" class="form-control commissionofmedia" name="@php echo $getExp4a->id @endphp['expdetails'][commission_of_agency]" value="@php echo !empty($getExp4a->commission_of_agency)? $getExp4a->commission_of_agency:""; @endphp"></td>

		                                <td><input type="text" class="form-control subtotal4a" name="@php echo $getExp4a->id @endphp['expdetails'][total_amt]" placeholder="" value="@php echo $getExp4a->total_amt @endphp" readonly="readonly"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][src_amt_by_cand]" placeholder="" value="@php echo !empty($getExp4a->src_amt_by_cand)? $getExp4a->src_amt_by_cand:""; @endphp"></td>

		                                <td><input type="text" class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][src_amt_by_pp]" placeholder="" value="@php echo !empty($getExp4a->src_amt_by_pp)? $getExp4a->src_amt_by_pp:""; @endphp">
		                                </td>

		                                <td><input type="text" class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][src_amt_by_other]" placeholder="" value="@php echo !empty($getExp4a->src_amt_by_other)? $getExp4a->src_amt_by_other:""; @endphp"></td>
		                            </tr>
                            	@endforeach
                            @else
                            <tr>
                                <td>1</td>
                                <td>
                                <select class="form-control" name="1['expdetails'][nature_of_media]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select>
                                </td>
                                 <td><input type="text" class="form-control" name="1['expdetails'][name_of_media]" placeholder=""></td>
                                 <td><input type="text" class="form-control" name="1['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="1['expdetails'][price_of_the_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="1['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control subtotal4a" name="1['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][src_amt_by_cand]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][src_amt_by_pp]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][src_amt_by_other]" placeholder=""></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
                                <select class="form-control" name="2['expdetails'][nature_of_media]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select>
                                </td>
                                <td><input type="text" class="form-control" name="2['expdetails'][name_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="2['expdetails'][price_of_the_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="2['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control subtotal4a" name="2['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][src_amt_by_cand]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][src_amt_by_pp]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][src_amt_by_other]" placeholder=""></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
                                <select class="form-control" name="3['expdetails'][nature_of_media]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select>
                                </td>
                                <td><input type="text" class="form-control" name="3['expdetails'][name_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="3['expdetails'][price_of_the_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="3['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control subtotal4a" name="3['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][src_amt_by_cand]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][src_amt_by_pp]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][src_amt_by_other]" placeholder=""></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>
                                <select class="form-control" name="4['expdetails'][nature_of_media]">
                                	<option value="">--Select Media--</option>
                                	<option value="Electronic Media">Electronic Media</option>	
                                	<option value="Print Media">Print Media</option>
                                </select>
                                </td>
                                <td><input type="text" class="form-control" name="4['expdetails'][name_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][address_of_media]" placeholder=""></td>
                                <td><input type="text" class="form-control priceofmedia" name="4['expdetails'][price_of_the_media]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][name_address_of_agency]" placeholder=""></td>
                                <td><input type="text" class="form-control commissionofmedia" name="4['expdetails'][commission_of_agency]"></td>
                                <td><input type="text" class="form-control subtotal4a" name="4['expdetails'][total_amt]" placeholder="" readonly="readonly"></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][src_amt_by_cand]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][src_amt_by_pp]" placeholder=""></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][src_amt_by_other]" placeholder=""></td>
                            </tr>
                            @endif
                             <tr>
                                <td colspan="7">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">

                 <div class="table-responsive" id="aknolwdgeForm-sch6">
				 <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered">
                          <thead class="text-center">
                            <tr>
                              <th colspan="10" align="center"><h5><strong>Schedule - 5</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="10">Details of expenditure on campaign vehicle (s) and poll expenditure on vehicle (s) for candidate's election campaign</th>
                            </tr>
                            <tr>
                              <th rowspan="2">S. No.</th>
                              <th rowspan="2">Regn. No. of Vehicle & Type of vehicle</th>
                              <th colspan="3">Hiring Charges of vehicle</th>
                              <th rowspan="2">No. of Days for which used</th>
                              <th rowspan="2">Total amt. incurred/auth in Rs.</th>
                              <th colspan="3">Source of Expenditure</th>
                            </tr>
                            <tr>
                              
                              <th >Rate for Hiring of vehicle/ Maintenance</th>
                              <th >Fuel Charges (If not covered under hiring)</th>
                              <th >Driver;s Charges (If not covered under hiring)</th>
                              <th>Amt. By candidate/agent</th>
                              <th>Amt. By Pol. Party</th>
                              <th>Amt. By others</th>                  
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3a</td>
                                <td>3b</td>
                                <td>3c</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                                <td>8</td>
                            </tr>
                            @if(!empty($getSch5))
                            	@foreach($getSch5 as $getExp5)
                            		<tr>
		                                <td>@php echo $getExp5->id ; @endphp</td>
		                                <input type="hidden" value="@php echo $getExp5->id ; @endphp" name="@php echo $getExp5->id ; @endphp['expdetails'][id]"/>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][regn_no_of_vehicle]" value="@php echo $getExp5->regn_no_of_vehicle ; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][hir_rate_for_vehicle]" value="@php echo  $getExp5->hir_rate_for_vehicle ; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][hir_fuel_charges]" value="@php echo $getExp5->hir_fuel_charges; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][hir_driver_charges]" value="@php echo  $getExp5->hir_driver_charges; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][no_of_days]" value="@php echo  $getExp5->no_of_days; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][total_amt_incurred]" value="@php echo $getExp5->total_amt_incurred ; @endphp" readonly="readonly"></td>

		                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][src_amt_by_cand]" maxlength="7" value="@php echo  $getExp5->src_amt_by_cand; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][src_amt_by_pp]" maxlength="7" value="@php echo $getExp5->src_amt_by_pp ; @endphp"></td>

		                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][src_amt_by_other]" maxlength="7" value="@php echo $getExp5->src_amt_by_other; @endphp"></td>
		                            </tr>
                            	@endforeach
                            @else
                            <tr>
                                <td>1</td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][regn_no_of_vehicle]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][hir_rate_for_vehicle]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][hir_fuel_charges]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][hir_driver_charges]" ></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="1['expdetails'][no_of_days]"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="1['expdetails'][total_amt_incurred]" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="1['expdetails'][src_amt_by_cand]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="1['expdetails'][src_amt_by_pp]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="1['expdetails'][src_amt_by_other]" maxlength="7"></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][regn_no_of_vehicle]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][hir_rate_for_vehicle]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][hir_fuel_charges]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][hir_driver_charges]" ></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="2['expdetails'][no_of_days]"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="2['expdetails'][total_amt_incurred]" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="2['expdetails'][src_amt_by_cand]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="2['expdetails'][src_amt_by_pp]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="2['expdetails'][src_amt_by_other]" maxlength="7"></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][regn_no_of_vehicle]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][hir_rate_for_vehicle]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][hir_fuel_charges]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][hir_driver_charges]" ></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="3['expdetails'][no_of_days]"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="3['expdetails'][total_amt_incurred]" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="3['expdetails'][src_amt_by_cand]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="3['expdetails'][src_amt_by_pp]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="3['expdetails'][src_amt_by_other]" maxlength="7"></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][regn_no_of_vehicle]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][hir_rate_for_vehicle]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][hir_fuel_charges]" ></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][hir_driver_charges]" ></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="4['expdetails'][no_of_days]"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="4['expdetails'][total_amt_incurred]" readonly="readonly"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="4['expdetails'][src_amt_by_cand]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="4['expdetails'][src_amt_by_pp]" maxlength="7"></td>
                                <td><input type="text" class="form-control ackSch1input" placeholder="" name="4['expdetails'][src_amt_by_other]" maxlength="7"></td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="6">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">


                 <div class="table-responsive" id="aknolwdgeForm-sch7">
				 <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered">
                          <thead class="text-center">
                            <tr>
                              <th colspan="10" align="center"><h5><strong>Schedule - 6</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="10">Details of expenditure on campaign workers/agents and on candidate's booths (kiosks) outside polling stations for distribution of voter's slips</th>
                            </tr>
                            <tr>
                              <th rowspan="2">S. No.</th>
                              <th rowspan="2">Date </th>
                              <th rowspan="2">Venue</th>
                              <th colspan="3">Expenses on campaign workers/agents</th>
                              <th rowspan="2">Total amt. incurred/auth. in Rs.</th>
                              <th colspan="3">Sources of Expenditure</th>
                            </tr>
                            <tr>
                              <th >Nature of Expenses </th>
                              <th >Rate</th>
                              <th >No. Of Workers / agents No. of Kiosks</th>
                              <th>Amt. By candidate/agent</th>
                              <th>Amt. By Pol Party</th>
                              <th>Amt. by Other</th>                  
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3a</td>
                                <td>3b</td>
                                <td>3c</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                                <td>8</td>
                            </tr>
                            @if(!empty($getSch6))
                            	@foreach($getSch6 as $getExp6)
                            		<tr>
		                                <td>@php echo $getExp6->id ; @endphp
		                                	<input type="hidden" name="@php echo $getExp6->id ; @endphp['expdetails'][id]" value="@php echo $getExp6->id ; @endphp" />
		                                </td>
		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][venu_date]" value="@php echo !empty($getExp6->venu_date)? $getExp6->venu_date:""; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][venu_details]" value="@php echo !empty($getExp6->venu_details)? $getExp6->venu_details:""; @endphp"></td>

		                                <td>Candidate's booths (Kiosks) set up for distribution of voter's slips
		                                	<input type="hidden" name="@php echo $getExp6->id ; @endphp['expdetails'][expense_nature]" value="Candidate booths (Kiosks) set up for distribution of voters slips">
		                                </td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][expense_nature_rate]" value="@php echo !empty($getExp6->expense_nature_rate)? $getExp6->expense_nature_rate:""; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][worker_agents_count]" value="@php echo !empty($getExp6->worker_agents_count)? $getExp6->worker_agents_count:""; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][total_amnt]" value="@php echo !empty($getExp6->total_amnt)? $getExp6->total_amnt:""; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][source_amnt_by_cand]" value="@php echo !empty($getExp6->source_amnt_by_cand)? $getExp6->source_amnt_by_cand:""; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][source_amnt_by_polparty]" value="@php echo !empty($getExp6->source_amnt_by_polparty)? $getExp6->source_amnt_by_polparty:""; @endphp"></td>

		                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][source_amnt_by_others]" value="@php echo !empty($getExp6->source_amnt_by_others)? $getExp6->source_amnt_by_others:""; @endphp"></td>
		                            </tr>
                            	@endforeach
                            @else
                            <tr>
                                <td>1</td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][venu_date]"></td>
                                <td><input type="text" class="form-control" name="1['expdetails'][venu_details]" name=""></td>
                                <td>Candidate's booths (Kiosks) set up for distribution of voter's slips
                                	<input type="hidden" name="1['expdetails'][expense_nature]" value="Candidate booths (Kiosks) set up for distribution of voters slips">
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][expense_nature_rate]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][worker_agents_count]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][total_amnt]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][source_amnt_by_cand]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][source_amnt_by_polparty]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][source_amnt_by_others]"></td>
                            </tr>
                            <tr>
                                <td>2</td>
                               <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][venu_date]"></td>
                                <td><input type="text" class="form-control" name="2['expdetails'][venu_details]" name=""></td>
                                <td>Campaign workers honorarium/salary etc.
                                	<input type="hidden" name="2['expdetails'][expense_nature]" value="Campaign workers honorarium/salary etc.">
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][expense_nature_rate]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][worker_agents_count]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][total_amnt]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][source_amnt_by_cand]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][source_amnt_by_polparty]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][source_amnt_by_others]"></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][venu_date]"></td>
                                <td><input type="text" class="form-control" name="3['expdetails'][venu_details]" name=""></td>
                                <td>Boarding
                                	<input type="hidden" name="3['expdetails'][expense_nature]" value="Boarding">
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][expense_nature_rate]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][worker_agents_count]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][total_amnt]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][source_amnt_by_cand]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][source_amnt_by_polparty]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][source_amnt_by_others]"></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][venu_date]"></td>
                                <td><input type="text" class="form-control" name="4['expdetails'][venu_details]" name=""></td>
                                <td>Lodging
                                	<input type="hidden" name="4['expdetails'][expense_nature]" value="Lodging">
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][expense_nature_rate]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][worker_agents_count]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][total_amnt]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][source_amnt_by_cand]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][source_amnt_by_polparty]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][source_amnt_by_others]"></td>
                            </tr>
                             <tr>
                                <td>5</td>
                                <td><input type="text" class="form-control" placeholder="" name="5['expdetails'][venu_date]"></td>
                                <td><input type="text" class="form-control" name="5['expdetails'][venu_details]" name=""></td>
                                <td>Others
                                	<input type="hidden" name="5['expdetails'][expense_nature]" value="Others"/>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="5['expdetails'][expense_nature_rate]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="5['expdetails'][worker_agents_count]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="5['expdetails'][total_amnt]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="5['expdetails'][source_amnt_by_cand]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="5['expdetails'][source_amnt_by_polparty]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="5['expdetails'][source_amnt_by_others]"></td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="5">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">


                  <div class="table-responsive">
				  <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch8">
                          <thead class="text-center">
                            <tr>
                              <th colspan="5" align="center"><h5><strong>Schedule - 7</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="5">Details of Amount of own fund used for the election campaign</th>
                            </tr>
                            <tr>
                              <th>S. No.</th>
                              <th>Date</th>
                              <th>Cash/DD/Cheque no. etc. with details of drawee bank</th>
                              <th>Total Amount in Rs.</th>
                              <th>Remarks</th>
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                            </tr>
                            @if(!empty($getSch7))
                            	@foreach($getSch7 as $getExp7)
                            	<tr>
	                                <td>@php echo $getExp7->id @endphp
	                                	<input type="hidden" name="@php echo $getExp7->id @endphp['expdetails'][id]" value="@php echo $getExp7->id @endphp" />
	                                </td>
	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp7->id @endphp['expdetails'][submit_date]" value="@php echo !empty($getExp7->submit_date)? $getExp7->submit_date:""; @endphp"></td>
	                                <td>
	                                	<select name="@php echo $getExp7->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp7->id @endphp">
	                                		<option>--Select Payment Type--</option>
	                                		<option value="cash" @if($getExp7->payment_type == "cash") selected="selected" @endif>Cash</option>
	                                		<option value="dd" @if($getExp7->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
	                                		<option value="cheque" @if($getExp7->payment_type == "cheque") selected="selected" @endif>Cheque</option>
	                                	</select>
	                                	<div class="col-md-12 paymenttype@php echo $getExp7->id @endphp" @if($getExp7->payment_type == "cash") style="margin-top: 20px; display: none;" @elseif($getExp7->payment_type == "dd") style="margin-top: 20px; display: block;" @elseif($getExp7->payment_type == "cheque") style="margin-top: 20px; display: block;" @endif>
	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp7->id @endphp['expdetails'][cheque_date]" class="form-control" placeholder="" value="@php echo !empty($getExp7->cheque_date)? $getExp7->cheque_date:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp7->id @endphp['expdetails'][cheque_bank]" class="form-control" placeholder="" value="@php echo !empty($getExp7->cheque_bank)? $getExp7->cheque_bank:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp7->id @endphp['expdetails'][cheque_ifsc]" class="form-control" placeholder="" value="@php echo !empty($getExp7->cheque_ifsc)? $getExp7->cheque_ifsc:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp7->id @endphp['expdetails'][cheque_number]" class="form-control" placeholder="" value="@php echo $getExp7->cheque_number; @endphp"></div>
	                                	</div>
	                                </td>
	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp7->id @endphp['expdetails'][amount]" value="@php echo $getExp7->amount; @endphp"></td>

	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp7->id @endphp['expdetails'][remarks]" value="@php echo $getExp7->remarks; @endphp"></td>
	                            </tr>
                            	@endforeach
                            @else
                            <tr>
                                <td>1</td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][submit_date]"></td>
                                <td>
                                	<select name="1['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype1">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype1" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_date]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][amount]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][remarks]"></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][submit_date]"></td>
                                <td>
                                	<select name="2['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype2">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype2" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_date]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][amount]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][remarks]"></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][submit_date]"></td>
                                <td>
                                	<select name="3['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype3">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype3" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_date]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][amount]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][remarks]"></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][submit_date]"></td>
                                <td>
                                	<select name="4['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype4">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype4" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_date]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][amount]"></td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][remarks]"></td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="2">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">

                  <div class="table-responsive">
				  <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch9">
                          <thead class="text-center">
                            <tr>
                              <th colspan="6" align="center"><h5><strong>Schedule - 8</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="6">Details of Lump sum amount received from the party (ies) in cash or cheque or DD or by Account Transfer</th>
                            </tr>
                            <tr>
                              <th>S. No.</th>
                              <th>Name of the Political Party</th>
                              <th>Date</th>
                              <th>Cash/DD/Cheque no etc. with details of drawee bank</th>
                              <th>Total Amount in Rs.</th>
                              <th>Remarks, If Any</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                            </tr>
                            @if(!empty($getSch8))
                            	@foreach($getSch8 as $getExp8)
                            	<tr>
	                                <td>@php echo $getExp8->id @endphp
	                                	<input type="hidden" name="@php echo $getExp8->id @endphp['expdetails'][id]" value="@php echo $getExp8->id @endphp" />
	                                </td>

	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][party_id]" value="@php echo !empty($getExp8->party_id)? $getExp8->party_id:""; @endphp"></td>

	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][submit_date]" value="@php echo !empty($getExp8->submit_date)? $getExp8->submit_date:""; @endphp"></td>

	                                <td>
	                                	<select name="@php echo $getExp8->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp8->id @endphp">
	                                		<option>--Select Payment Type--</option>
	                                		<option value="cash" @if($getExp8->payment_type == "cash") selected="selected" @endif>Cash</option>
	                                		<option value="dd" @if($getExp8->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
	                                		<option value="cheque" @if($getExp8->payment_type == "cheque") selected="selected" @endif>Cheque</option>
	                                	</select>
	                                	<div class="col-md-12 paymenttype@php echo $getExp8->id @endphp" @if($getExp8->payment_type == "cash") style="margin-top: 20px; display: none;" @elseif($getExp8->payment_type == "dd") style="margin-top: 20px; display: block;" @elseif($getExp8->payment_type == "cheque") style="margin-top: 20px; display: block;" @endif>
	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp8->id @endphp['expdetails'][cheque_date]" class="form-control" placeholder="" value="@php echo !empty($getExp8->cheque_date)? $getExp8->cheque_date:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp8->id @endphp['expdetails'][cheque_bank]" class="form-control" placeholder="" value="@php echo !empty($getExp8->cheque_bank)? $getExp8->cheque_bank:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp8->id @endphp['expdetails'][cheque_ifsc]" class="form-control" placeholder="" value="@php echo !empty($getExp8->cheque_ifsc)? $getExp8->cheque_ifsc:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp8->id @endphp['expdetails'][cheque_number]" class="form-control" placeholder="" value="@php echo $getExp8->cheque_number; @endphp"></div>
	                                	</div>
	                                </td>
	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][amount]" value="@php echo !empty($getExp8->amount)? $getExp8->amount:""; @endphp"></td>

	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][remarks]" value="@php echo !empty($getExp8->remarks)? $getExp8->remarks:""; @endphp"></td>
	                            </tr>
                            	@endforeach
                            @else
                            <tr>
                                <td>1</td>

                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][party_id]" value="@php echo !empty($getExp8->party_id)? $getExp8->party_id:""; @endphp"></td>

                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][submit_date]" value="@php echo !empty($getExp8->submit_date)? $getExp8->submit_date:""; @endphp"></td>

                                <td>
                                	<select name="1['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype1">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype1" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][amount]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][remarks]"></td>
                            </tr>
                            <tr>
                                <td>2</td>

                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][party_id]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][submit_date]" ></td>

                                <td>
                                	<select name="2['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype2">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype2" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][amount]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][remarks]"></td>
                            </tr>
                            <tr>
                                <td>3</td>

                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][party_id]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][submit_date]" ></td>

                                <td>
                                	<select name="3['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype3">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype3" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][amount]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][remarks]"></td>
                            </tr>
                            <tr>
                                <td>4</td>

                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][party_id]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][submit_date]"/></td>

                                <td>
                                	<select name="4['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype4">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype4" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][amount]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][remarks]"></td>
                            </tr>
                            @endif
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="2">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">


					<div class="table-responsive">
					<h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
                        <table class="table table-bordered" id="aknolwdgeForm-sch10">
                          <thead class="text-center">
                            <tr>
                              <th colspan="8" align="center"><h5><strong>Schedule - 9</strong></h5></th>
                            </tr>
                            <tr>
                              <th colspan="8">Details of  Lump sum amount received from any person/company/firm/associations/body of persons etc. as loan, gift or donation etc.</th>
                            </tr>
                            <tr>
                              <th>S. No.</th>
                              <th>Name</th>
                              <th>Address</th>
                              <th>Date</th>
                              <th>Cash/DD/Cheque no. etc. with details of drawee bank</th>
                              <th>Mention whether loan, gift or donation etc.</th>
                              <th>Total Amount in Rs.</th>
                              <th>Remarks</th>
                            </tr>
                          </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td>7</td>
                                <td>8</td>
                            </tr>
                            @if(!empty($getSch9))
                            	@foreach($getSch9 as $getExp9)
                            	<tr>
	                                <td>@php echo $getExp9->id; @endphp
	                                	<input type="hidden" value="@php echo $getExp9->id; @endphp" name="@php echo $getExp9->id; @endphp['expdetails'][id]" />
	                                </td>

	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][name]" value="@php echo !empty($getExp9->name)? $getExp9->name:""; @endphp"></td>

	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][address]" value="@php echo !empty($getExp9->address)? $getExp9->address:""; @endphp"></td>

	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][submit_date]" value="@php echo !empty($getExp9->submit_date)? $getExp9->submit_date:""; @endphp"></td>

	                                <td>
	                                	<select name="@php echo $getExp9->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp9->id @endphp">
	                                		<option>--Select Payment Type--</option>
	                                		<option value="cash" @if($getExp9->payment_type == "cash") selected="selected" @endif>Cash</option>
	                                		<option value="dd" @if($getExp9->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
	                                		<option value="cheque" @if($getExp9->payment_type == "cheque") selected="selected" @endif>Cheque</option>
	                                	</select>
	                                	<div class="col-md-12 paymenttype@php echo $getExp9->id @endphp" @if($getExp9->payment_type == "cash") style="margin-top: 20px; display: none;" @elseif($getExp9->payment_type == "dd") style="margin-top: 20px; display: block;" @elseif($getExp9->payment_type == "cheque") style="margin-top: 20px; display: block;" @endif>
	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp9->id @endphp['expdetails'][cheque_date]" class="form-control" placeholder="" value="@php echo !empty($getExp9->cheque_date)? $getExp9->cheque_date:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp9->id @endphp['expdetails'][cheque_bank]" class="form-control" placeholder="" value="@php echo !empty($getExp9->cheque_bank)? $getExp9->cheque_bank:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp9->id @endphp['expdetails'][cheque_ifsc]" class="form-control" placeholder="" value="@php echo !empty($getExp9->cheque_ifsc)? $getExp9->cheque_ifsc:""; @endphp"></div>

	                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp9->id @endphp['expdetails'][cheque_number]" class="form-control" placeholder="" value="@php echo $getExp9->cheque_number; @endphp"></div>
	                                	</div>
	                                </td>
	                                <td>
	                                	<select name="@php echo $getExp9->id; @endphp['expdetails'][amount_details]" class="form-control paymenttype">
	                                		<option>--Select Amount Detail--</option>
	                                		<option value="loan" value="loan" @if($getExp9->amount_details == "loan") selected="selected" @endif>Loan</option>
	                                		<option value="gift" value="gift" @if($getExp9->amount_details == "gift") selected="selected" @endif>Gift</option>
	                                		<option value="donation" value="donation" @if($getExp9->amount_details == "donation") selected="selected" @endif>Donation</option>
	                                	</select>
	                                </td>
	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][amount]" value="@php echo $getExp9->amount; @endphp"></td>
	                                <td><input type="text" class="form-control" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][remarks]"  value="@php echo $getExp9->remarks; @endphp"></td>
	                            </tr>
                            	@endforeach
                            @else
                            <tr>
                                <td>1</td>

                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][name]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][address]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][submit_date]"></td>

                                <td>
                                	<select name="1['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype1">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype1" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td>
                                	<select name="1['expdetails'][amount_details]" class="form-control paymenttype">
                                		<option>--Select Amount Detail--</option>
                                		<option value="loan">Loan</option>
                                		<option value="gift">Gift</option>
                                		<option value="donation">Donation</option>
                                	</select>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][amount]" ></td>

                                <td><input type="text" class="form-control" placeholder="" name="1['expdetails'][remarks]"></td>
                            </tr>
                             <tr>
                                <td>1</td>

                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][name]" ></td>

                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][address]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][submit_date]"></td>

                                <td>
                                	<select name="2['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype2">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype1" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td>
                                	<select name="2['expdetails'][amount_details]" class="form-control paymenttype">
                                		<option>--Select Amount Detail--</option>
                                		<option value="loan">Loan</option>
                                		<option value="gift">Gift</option>
                                		<option value="donation">Donation</option>
                                	</select>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][amount]" ></td>

                                <td><input type="text" class="form-control" placeholder="" name="2['expdetails'][remarks]"></td>
                            </tr>
                             <tr>
                                <td>3</td>

                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][name]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][address]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][submit_date]"></td>

                                <td>
                                	<select name="3['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype3">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype3" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td>
                                	<select name="3['expdetails'][amount_details]" class="form-control paymenttype">
                                		<option>--Select Amount Detail--</option>
                                		<option value="loan">Loan</option>
                                		<option value="gift">Gift</option>
                                		<option value="donation">Donation</option>
                                	</select>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][amount]" ></td>

                                <td><input type="text" class="form-control" placeholder="" name="3['expdetails'][remarks]"></td>
                            </tr>
                             <tr>
                                <td>4</td>

                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][name]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][address]"></td>

                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][submit_date]"></td>

                                <td>
                                	<select name="4['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype4">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype4" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
                                </td>
                                <td>
                                	<select name="4['expdetails'][amount_details]" class="form-control paymenttype">
                                		<option>--Select Amount Detail--</option>
                                		<option value="loan">Loan</option>
                                		<option value="gift">Gift</option>
                                		<option value="donation">Donation</option>
                                	</select>
                                </td>
                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][amount]" ></td>

                                <td><input type="text" class="form-control" placeholder="" name="4['expdetails'][remarks]"></td>
                            </tr>
                            @endif
                            <tr>
                                <td>&nbsp;</td>
                                <td colspan="2">Total</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
				
				</div><!-- ScheduleTab close-->
				
				<div class="ScheduleTab">
				
				 <div class="table-responsive">
				 <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
						<table class="table table-bordered" id="aknolwdgeForm-sch11">											 
						  <thead class="text-center">
						  <tr>
                              <th colspan="8" align="center"><h5><strong>Schedule - 10</strong></h5></th>
                            </tr>
							<tr>
							  <th colspan="8" align="center" class="text-center">Details of expenditure incurred on publishing criminal antecedents, if any in newspaper and TV Channel </th>							  					  
							</tr>
							<tr>
							  <th align="center"  class="text-center">Sl. No.</th>						  
							  <th align="center"  class="text-center" colspan="3">Newspaper</th>						  
							  <th align="center"  class="text-center" colspan="3">Television</th>	
							  <th>Mode of payment (electronic/cheque/DD/Cash) (PI. specify)</th>			  
							  					  
							</tr>
							<tr>
								<th></th>
								<th><label>Name of Newspaper</label></th>
								<th><label>Date of publishing</label></th>
								<th><label>Expenses that may have been incurred (in Rs.)</label></th>
								<th><label>Name of channel</label></th>
								<th><label>Date & Time of insertion/telecast</label></th>
								<th><label>Expenses that may have been incurred (in Rs.)</label></th>
								<th></th>															
							</tr>
						  </thead>
						  <tbody>
							<tr>
								<td>1</td>															
								<td>2</td>															
								<td>3</td>															
								<td>4</td>															
								<td>5</td>															
								<td>6</td>															
								<td>7</td>															
								<td>8</td>															
							</tr>
							@if(!empty($getSch10))
                            	@foreach($getSch10 as $getExp10)
                            		<tr>
										<td>@php echo $getExp10->id  @endphp</td>
										<td><input type="text" class="form-control" placeholder="" name="@php echo $getExp10->id  @endphp['expdetails'][newspaper_name]"  value="@php echo $getExp10->newspaper_name @endphp"></td>

										<td><input type="text" class="form-control" placeholder="" name="@php echo $getExp10->id  @endphp['expdetails'][news_publishing_date]"  value="@php echo $getExp10->news_publishing_date @endphp"></td>

										<td><input type="text" class="form-control" placeholder="" name="@php echo $getExp10->id  @endphp['expdetails'][expense_on_news]"  value="@php echo $getExp10->expense_on_news @endphp"></td>

										<td><input type="text" class="form-control" placeholder="" name="@php echo $getExp10->id  @endphp['expdetails'][channel_name]"  value="@php echo $getExp10->channel_name @endphp"></td>

										<td><input type="text" class="form-control" placeholder="" name="@php echo $getExp10->id  @endphp['expdetails'][telecost_dateTime]"  value="@php echo $getExp10->telecost_dateTime @endphp"></td>

										<td><input type="text" class="form-control" placeholder="" name="@php echo $getExp10->id  @endphp['expdetails'][expense_on_channel]"  value="@php echo $getExp10->expense_on_channel @endphp"></td>
													
										<td>
											<select name="@php echo $getExp10->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp10->id @endphp">
		                                		<option>--Select Payment Type--</option>
		                                		<option value="cash" @if($getExp10->payment_type == "cash") selected="selected" @endif>Cash</option>
		                                		<option value="dd" @if($getExp10->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
		                                		<option value="cheque" @if($getExp10->payment_type == "cheque") selected="selected" @endif>Cheque</option>
		                                	</select>
		                                	<div class="col-md-12 paymenttype@php echo $getExp10->id @endphp" @if($getExp10->payment_type == "cash") style="margin-top: 20px; display: none;" @elseif($getExp10->payment_type == "dd") style="margin-top: 20px; display: block;" @elseif($getExp10->payment_type == "cheque") style="margin-top: 20px; display: block;" @endif>
		                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp10->id @endphp['expdetails'][cheque_date]" class="form-control" placeholder="" value="@php echo !empty($getExp10->cheque_date)? $getExp10->cheque_date:""; @endphp"></div>

		                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp10->id @endphp['expdetails'][cheque_bank]" class="form-control" placeholder="" value="@php echo !empty($getExp10->cheque_bank)? $getExp10->cheque_bank:""; @endphp"></div>

		                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp10->id @endphp['expdetails'][cheque_ifsc]" class="form-control" placeholder="" value="@php echo $getExp10->cheque_ifsc ; @endphp"></div>

		                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="@php echo $getExp10->id @endphp['expdetails'][cheque_no]" class="form-control" placeholder="" value="@php echo $getExp10->cheque_no ; @endphp"></div>
		                                	</div>
                                	    </td>
									</tr>
                            	@endforeach
                            @else					  
							<tr>
								<td>1</td>
								<td><input type="text" class="form-control" placeholder="" name="1['expdetails'][newspaper_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="1['expdetails'][news_publishing_date]"></td>
								<td><input type="text" class="form-control" placeholder="" name="1['expdetails'][expense_on_news]"></td>
								<td><input type="text" class="form-control" placeholder="" name="1['expdetails'][channel_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="1['expdetails'][telecost_dateTime]"></td>
								<td><input type="text" class="form-control" placeholder="" name="1['expdetails'][expense_on_channel]"></td>
								<td>
									<select name="1['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype1">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype1" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="1['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
								</td>													
							</tr>
							<tr>
								<td>2</td>
								<td><input type="text" class="form-control" placeholder="" name="2['expdetails'][newspaper_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="2['expdetails'][news_publishing_date]"></td>
								<td><input type="text" class="form-control" placeholder="" name="2['expdetails'][expense_on_news]"></td>
								<td><input type="text" class="form-control" placeholder="" name="2['expdetails'][channel_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="2['expdetails'][telecost_dateTime]"></td>
								<td><input type="text" class="form-control" placeholder="" name="2['expdetails'][expense_on_channel]"></td>
								<td>
									<select name="2['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype2">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype2" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="2['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
								</td>							
							</tr>
							<tr>
								<td>3</td>
								<td><input type="text" class="form-control" placeholder="" name="3['expdetails'][newspaper_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="3['expdetails'][news_publishing_date]"></td>
								<td><input type="text" class="form-control" placeholder="" name="3['expdetails'][expense_on_news]"></td>
								<td><input type="text" class="form-control" placeholder="" name="3['expdetails'][channel_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="3['expdetails'][telecost_dateTime]"></td>
								<td><input type="text" class="form-control" placeholder="" name="3['expdetails'][expense_on_channel]"></td>
								<td>
									<select name="3['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype3">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype3" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="3['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
								</td>								
							</tr>
							<tr>
								<td>4</td>
								<td><input type="text" class="form-control" placeholder="" name="4['expdetails'][newspaper_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="4['expdetails'][news_publishing_date]"></td>
								<td><input type="text" class="form-control" placeholder="" name="4['expdetails'][expense_on_news]"></td>
								<td><input type="text" class="form-control" placeholder="" name="4['expdetails'][channel_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="4['expdetails'][telecost_dateTime]"></td>
								<td><input type="text" class="form-control" placeholder="" name="4['expdetails'][expense_on_channel]"></td>
								<td>
									<select name="4['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype4">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype4" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="4['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
								</td>							
							</tr>
							<tr>
								<td>5</td>
								<td><input type="text" class="form-control" placeholder="" name="5['expdetails'][newspaper_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="5['expdetails'][news_publishing_date]"></td>
								<td><input type="text" class="form-control" placeholder="" name="5['expdetails'][expense_on_news]"></td>
								<td><input type="text" class="form-control" placeholder="" name="5['expdetails'][channel_name]"></td>
								<td><input type="text" class="form-control" placeholder="" name="5['expdetails'][telecost_dateTime]"></td>
								<td><input type="text" class="form-control" placeholder="" name="5['expdetails'][expense_on_channel]"></td>
								<td>
									<select name="5['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype5">
                                		<option>--Select Payment Type--</option>
                                		<option value="cash">Cash</option>
                                		<option value="dd">Demand Draft</option>
                                		<option value="cheque">Cheque</option>
                                	</select>
                                	<div class="col-md-12 paymenttype5" style="margin-top: 20px; display: none;">
                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="5['expdetails'][cheque_date]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="5['expdetails'][cheque_bank]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="5['expdetails'][cheque_ifsc]" class="form-control" placeholder=""></div>

                                		<div style="float: left; width: 20%; margin-left: 2%;"><input type="text" name="5['expdetails'][cheque_number]" class="form-control" placeholder=""></div>
                                	</div>
								</td>	
							</tr>
							@endif										
						  </tbody>
						  <tfoot>							
							<tr>								
								<td colspan="3"><strong>Grand Total</strong></td>
								<td></td>
								<td colspan="2"></td>
								<td></td>								
								<td></td>								
							</tr>
						  </tfoot>
						</table>						
					  </div>
					  <div class="clearfix"></div>					  
					  </div><!-- ScheduleTab close-->
				  <!-- indu -->
				  
				<div style="overflow:auto;">
				  <div style="float:right;">
					<button type="button" class="btn btn-primary" id="ScheduleTab_prevBtn" onclick="ScheduleTab_nextPrev(-1)" style="display: none;">Previous</button>
					<button type="button" class="btn btn-primary" id="ScheduleTab_nextBtn" onclick="ScheduleTab_nextPrev(1)">Next</button>
				  </div>
				</div>
				
				<div style="text-align:center;margin-top:40px;">
				  <span class="ScheduleTab_step active"></span>
				  <span class="ScheduleTab_step"></span>
				  <span class="ScheduleTab_step"></span>				  
				  <span class="ScheduleTab_step"></span>				  
				  <span class="ScheduleTab_step"></span>				  
				  <span class="ScheduleTab_step"></span>				  
				  <span class="ScheduleTab_step"></span>				  
				  <span class="ScheduleTab_step"></span>				  
				  <span class="ScheduleTab_step"></span>				  
				  <span class="ScheduleTab_step"></span>
				  <span class="ScheduleTab_step"></span>				  
				</div>
					</form>
				</div><!-- tab-2 -->
					
					</div><!-- tab_content -->
					
					
					  
					  <div class="clearfix"></div>
					  					  
				</div>				
			</div>
		</div>
	</section>
    
</main>


 <div class="modal fade" id="annuxuree2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
						<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel" style="text-align: -webkit-center;">Successfully Submitted.</h4>
                 </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">
jQuery('ul.tabs').each(function(){  
	  var $active, $content, $links = jQuery(this).find('a');
	  $active = jQuery($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
	  $active.addClass('active');
	  $content = jQuery($active[0].hash);
	  $links.not($active).each(function () {
		jQuery(this.hash).hide();
	  });
	  jQuery(this).on('click', 'a', function(e){   
		$active.removeClass('active');
		$content.hide();    
		$active = jQuery(this);
		$content = jQuery(this.hash);    
		$active.addClass('active');
		$content.show();   
		e.preventDefault();
	  });
	});
	
	

var currentTab = 0; 
showTab(currentTab); 
function showTab(n) { 
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block"; 
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
    

  } else {

    document.getElementById("nextBtn").innerHTML = "Next";
    //$( "#nextBtn" ).removeClass("submitAnnuxure");
  }  
  fixStepIndicator(n)
}

function nextPrev(n) {
  
  var x = document.getElementsByClassName("tab"); 
 // alert(x[currentTab]); 
    if (n == 1 && !validateForm()) return true; 
	  x[currentTab].style.display = "none"; 
	  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {

alert('dddddd');

//alert('test');
/////////// form submitted //////////////////
        var data = jQuery("#annuxureForm").serialize();
        $.ajax({
            data: data,
            type: "post",
            dataType: "json",
            url: "{{url('/ropc/SaveAnnuxureData')}}",
            success: function (response) {
            	//alert(response);
                         response = response.trim();    
                         if(response=="1")
                         {

                         	$('.successAnnuxure').text("Successfully submitted");
                         }

                         if(response=="0")
                         {
                         	$('.successAnnuxure').text("Some internal error");
                         }
            }
        });


location.reload();
    // ... the form gets submitted:
    //document.getElementById("annuxureForm").submit();
    return false;
  }  

////////////////////validation for form by manish //////////
//*******************ECI FILTER FORM VALIDATION STARTS********************//
$("#annuxureForm").validate({
    rules: {
        public_expenses_meeting_star_3: {number:true,min:1,max:9999999},
     	public_expenses_meeting_star_4: {number:true,min:1,max:9999999},
     	public_expenses_meeting_star_5: {number:true,min:1,max:9999999},
     	public_expenses_meeting_star_6: {number:true,min:1,max:9999999},   
     	public_expenses_meeting_general_3: {number:true,min:1,max:9999999},
     	public_expenses_meeting_general_4: {number:true,min:1,max:9999999},
     	public_expenses_meeting_general_5: {number:true,min:1,max:9999999},
     	public_expenses_meeting_general_6: {number:true,min:1,max:9999999},
     	compaign_material_3: {number:true,min:1,max:9999999},
     	compaign_material_4: {number:true,min:1,max:9999999},
     	compaign_material_5: {number:true,min:1,max:9999999},
     	compaign_material_6: {number:true,min:1,max:9999999},
     	compaign_through_print_media_3: {number:true,min:1,max:9999999},
     	compaign_through_print_media_4: {number:true,min:1,max:9999999},
     	compaign_through_print_media_5: {number:true,min:1,max:9999999},
     	compaign_through_print_media_6: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_vehicle_3: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_vehicle_4: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_vehicle_5: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_vehicle_6: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_workers_3: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_workers_4: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_workers_5: {number:true,min:1,max:9999999},
     	expenditure_on_compaign_workers_6: {number:true,min:1,max:9999999},
     	any_other_compaign_expenditure_3: {number:true,min:1,max:9999999},
     	any_other_compaign_expenditure_4: {number:true,min:1,max:9999999},
     	any_other_compaign_expenditure_5: {number:true,min:1,max:9999999},
     	any_other_compaign_expenditure_6: {number:true,min:1,max:9999999},
     	expenses_incurred_on_publishing_3: {number:true,min:1,max:9999999},
     	expenses_incurred_on_publishing_4: {number:true,min:1,max:9999999},
     	expenses_incurred_on_publishing_5: {number:true,min:1,max:9999999},
     	expenses_incurred_on_publishing_6: {number:true,min:1,max:9999999},
     	amt_own_funds_election_compaign: {number:true,min:1,max:9999999},
     	lump_sum_amt_from_party: {number:true,min:1,max:9999999},
     	lump_sum_amt_from_other: {number:true,min:1,max:9999999},
     	grand_total_candidate_agent: {number:true,min:1,max:9999999},
     	grand_total_amt_incurred_by_pol_party: {number:true,min:1,max:9999999},
     	grand_total_amt_incurred_by_other: {number:true,min:1,max:9999999},
     	total_expenditure: {number:true,min:1,max:9999999},
       
    },
    messages: {
       public_expenses_meeting_star_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        public_expenses_meeting_star_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        public_expenses_meeting_star_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        public_expenses_meeting_star_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        public_expenses_meeting_general_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        public_expenses_meeting_general_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        public_expenses_meeting_general_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        public_expenses_meeting_general_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_material_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_material_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_material_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_material_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_through_print_media_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_through_print_media_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_through_print_media_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        compaign_through_print_media_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_vehicle_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_vehicle_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_vehicle_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_vehicle_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_workers_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_workers_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_workers_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        expenditure_on_compaign_workers_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        any_other_compaign_expenditure_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        any_other_compaign_expenditure_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },

         any_other_compaign_expenditure_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         any_other_compaign_expenditure_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         expenses_incurred_on_publishing_3: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         expenses_incurred_on_publishing_4: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         expenses_incurred_on_publishing_5: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         expenses_incurred_on_publishing_6: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         amt_own_funds_election_compaign: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         lump_sum_amt_from_party: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         lump_sum_amt_from_other: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
         grand_total_candidate_agent: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },

        grand_total_amt_incurred_by_pol_party: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },

        grand_total_amt_incurred_by_other: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
        total_expenditure: {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },

    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error)
        } else {
            error.insertAfter(element);
        }
    }
});
//********************ECI FILTER FORM VALIDATION ENDS********************//
////////////////end here ////////////////////////////

showTab(currentTab);
 
}




function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = true;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}


/*  Second class */
var Schedule_currentTab = 0; 
Schedule_showTab(Schedule_currentTab); 
function Schedule_showTab(n) { 
  var ScheduleTab = document.getElementsByClassName("ScheduleTab");
  ScheduleTab[n].style.display = "block"; 
  if (n == 0) {
    document.getElementById("ScheduleTab_prevBtn").style.display = "none";
  } else {
    document.getElementById("ScheduleTab_prevBtn").style.display = "inline";
  }
  if (n == (ScheduleTab.length - 1)) {
    document.getElementById("ScheduleTab_nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("ScheduleTab_nextBtn").innerHTML = "Next";
  }  
  fixStepIndicator(n)
}

/*jQuery('#aknolwdgeForm-sch4a #ScheduleTab_nextBtn').click(function(){
	jQuery.ajax({
	    url: APP_URL+"/candidate/saveAckformSch4a",
	    type: 'POST',
	    data: jQuery('#aknolwdgeForm-sch4a :input').serialize()+ "&_token={{csrf_token()}}",
	    success: function(data){
			//alert('test');
	    }
    });
})*/

function ScheduleTab_nextPrev(n) {
  
  var ScheduleTab = document.getElementsByClassName("ScheduleTab");  
  if (n == 1 && !validateForm()) return true; 
  

  ScheduleTab[Schedule_currentTab].style.display = "none"; 
  Schedule_currentTab = Schedule_currentTab + n;
    jQuery.ajax({
	    url: APP_URL+"/candidate/saveAckformSch"+Schedule_currentTab,
	    type: 'POST',
	    data: jQuery('#aknolwdgeForm-sch'+Schedule_currentTab+' :input').serialize()+ "&_token={{csrf_token()}}",
	    success: function(data){
			//alert('test');
	    }
    });

  // if you have reached the end of the form...
  if (Schedule_currentTab >= ScheduleTab.length) {

//alert('dddddd');
$("#aknolwdgeForm").validate({
    rules: {
    	"1['fund'][src_amt_incurred_cand]":{number:true,min:1,max:9999999},
    	},
    messages: {
       "1['fund'][src_amt_incurred_cand]": {
            number:"Please enter only number",max:"Please enter value not more than 9999999",min:"Please enter value not less than 1"
        },
	},
    errorElement: 'div',
    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error)
        } else {
            error.insertAfter(element);
        }
    }
});

    // ... the form gets submitted:
    document.getElementById("Schedule").submit();
    return false;
  }  
  Schedule_showTab(Schedule_currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var ScheduleTab, y, i, valid = true;
  ScheduleTab = document.getElementsByClassName("ScheduleTab");
  y = ScheduleTab[Schedule_currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = true;
    }
  }
  
  if (valid) {
    document.getElementsByClassName("ScheduleTab_step")[Schedule_currentTab].className += " finish";
  }
  return valid; // return the valid status
}


function validateForm3() {
  // This function deals with validation of the form fields
  var ScheduleTab, y, i, valid = true;
  ScheduleTab = document.getElementsByClassName("ScheduleTab");
  y = ScheduleTab[Schedule_currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = true;
    }
  }
  
  if (valid) {
    document.getElementsByClassName("ScheduleTab_step")[Schedule_currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {  
  var i, ScheduleTab = document.getElementsByClassName("ScheduleTab_step");
  for (i = 0; i < ScheduleTab.length; i++) {
    ScheduleTab[i].className = ScheduleTab[i].className.replace(" active", "");
  }  
  ScheduleTab[n].className += " active";
}
/*  Second class */


//*******************EXTRA VALIDATION METHODS STARTS********************//
//maxsize
$.validator.addMethod('maxSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
});
//minsize
$.validator.addMethod('minSize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size >= param)
});
//alphanumeric
$.validator.addMethod("alphnumericregex", function (value, element) {
    return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
});
//alphaonly
$.validator.addMethod("onlyalphregex", function (value, element) {
    return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
});
//without space
$.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//

//*******************ECI FILTER FORM VALIDATION STARTS********************//
$("#EciCustomReportFilter").validate({
    rules: {
        state: {required: true, noSpace: true},
        ScheduleList: {number: true},
    },
    messages: {
        state: {
            required: "Select state name.",
            noSpace: "State name must be without space.",
        },
        ScheduleList: {
            number: "Scedule ID should be numbers only.",
        },
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error)
        } else {
            error.insertAfter(element);
        }
    }
});
//********************ECI FILTER FORM VALIDATION ENDS********************//

$(document).on("keyup", ".amt1, .amt2, .amt3", function() {
    var $row = $(this).closest("tr"),
      amt1 = parseInt($row.find('.amt1').val()),
      amt2 = parseInt($row.find('.amt2').val()),
      amt3 = parseInt($row.find('.amt3').val()),

      subTotal = amt1 + amt2 + amt3;
    $row.find('.subtotal').val(isNaN(subTotal) ? 0 : subTotal);
    totalIt()
});


$(document).on("keyup", ".priceofmedia, .commissionofmedia", function() {
    var $row = $(this).closest("tr"),
      price4a = parseInt($row.find('.priceofmedia').val());
      commission4a = parseInt($row.find('.commissionofmedia').val());

      subTotal = price4a + commission4a;
    $row.find('.subtotal4a').val(isNaN(subTotal) ? 0 : subTotal);
});

///////aknolwdge form//////////
function totalIt() {
  var total = 0;
  $(".subtotal").each(function() {
    var val = this.value;
    total += val == "" || isNaN(val) ? 0 : parseInt(val);
  });
  $("#total").val(total);


  var total1 = 0;
  $(".amt1").each(function() {
    var val = this.value;
    total1 += val == "" || isNaN(val) ? 0 : parseInt(val);
  });
  $("#grand_total_candidate_agent").val(total1);

  var total2 = 0;
  $(".amt2").each(function() {
    var val = this.value;
    total2 += val == "" || isNaN(val) ? 0 : parseInt(val);
  });
  $("#grand_total_amt_incurred_by_pol_party").val(total2);

 var total3 = 0;
  $(".amt3").each(function() {
    var val = this.value;
    total3 += val == "" || isNaN(val) ? 0 : parseInt(val);
  });
  $("#grand_total_amt_incurred_by_other").val(total3);

  }

function totalItsc1() {
  var total = 0;
  $(".s1amt").each(function() {
    var val = this.value;
    total += val == "" || isNaN(val) ? 0 : parseInt(val);

  });
  $(".st1").val(total);
  if(total > 7000000){
  	jQuery('.sch1totalerror').html('value should not be greater than 70 Lacs');
  }else{
  	jQuery('.sch1totalerror').html();
  }

  var total1 = 0;
  $(".s1amt1").each(function() {
    var val = this.value;
    total1 += val == "" || isNaN(val) ? 0 : parseInt(val);
  });
  $(".st2").val(total1);

  var total2 = 0;
  $(".s1amt2").each(function() {
    var val = this.value;
    total2 += val == "" || isNaN(val) ? 0 : parseInt(val);
  });
  $(".st3").val(total2);

 var total3 = 0;
  $(".s1amt3").each(function() {
    var val = this.value;
    total3 += val == "" || isNaN(val) ? 0 : parseInt(val);
  });
  $(".st4").val(total3);

  }

$(document).on("keyup", ".s1amt1, .s1amt2, .s1amt3", function() {
    var $row = $(this).closest("tr"),
      s1amt1 = parseInt($row.find('.s1amt1').val()),
      s1amt2 = parseInt($row.find('.s1amt2').val()),
      s1amt3 = parseInt($row.find('.s1amt3').val()),

      subTotal = s1amt1 + s1amt2 + s1amt3;
    $row.find('.s1amt').val(isNaN(subTotal) ? 0 : subTotal);
    totalItsc1()

    //called when key is pressed in textbox
    $(".ackSch1input").on("keypress keyup blur",function (event) {    
       $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

});

jQuery(document).ready(function(){
	$('.campdatetimepicker').datetimepicker({
		format: 'YYYY-MM-DD',
		maxDate: '2019-06-10'	 
	});

	//Acknolegement Form Schedule 1 src_amt_total amount total
	if(jQuery('.src_total_amt').val() != ''){
		var sum = 0;
	    $('.src_total_amt').each(function() {   
	        sum += +this.value;

	    });
	   	jQuery('.total_amt').val(sum);    
	}

	//Acknolegement Form Schedule 1 src_amt_incurred_cand amount total
	if(jQuery('.src_amt_incurred_cand').val() != ''){
		var sum = 0;
	    $('.src_amt_incurred_cand').each(function() {   
	        sum += +this.value;
	    });
	    jQuery('.total_cand_amt').val(sum); 
	}

	//Acknolegement Form Schedule 1 src_amt_incurred_pp amount total
	if(jQuery('.src_amt_incurred_pp').val() != ''){
		var sum = 0;
	    $('.src_amt_incurred_pp').each(function() {   
	        sum += +this.value;
	    });
	    jQuery('.total_pp_amt').val(sum); 
	}

	//Acknolegement Form Schedule 1 src_amt_incurred_other amount total
	if(jQuery('.src_amt_incurred_other').val() != ''){
		var sum = 0;
	    $('.src_amt_incurred_other').each(function() {   
	        sum += +this.value;
	    });

	    jQuery('.total_other_amt').val(sum); 
	}

	jQuery('.paymenttype').change(function(){
		var paymenttypeid   = jQuery(this).attr('id');
		var paymenttypeval  = jQuery(this).val();
		if(paymenttypeval == "cash"){
			jQuery("."+paymenttypeid).hide();
		}else{
			jQuery("."+paymenttypeid).show();
		}
	});
})
</script>

<!--**********FORM VALIDATION ENDS*************--> 
@endsection
