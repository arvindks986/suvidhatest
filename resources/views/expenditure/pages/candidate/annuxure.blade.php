
@extends('admin.layouts.pc.ecrp-theme')
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
.bdrline{
 border: 0;
 outline: 0;
 background: transparent;
 border-bottom: 1px dotted black;
}
input.bdrline {
    font-weight: bold;
}
.tableShadow{    margin-top: 19px;
}
.UploadMsg{    font-size: 17px;
    font-weight: bold;}
	.download tr td {
    padding: 17px;
    font-size: 15px;
}
input[type=file] {
    width: 190px;
    margin-right: 14px;
}

.btn-circle_1 {
    width: 40px;
    height: 40px;
    padding: 10px 15px;
    text-align: center;
    font-size: 14px;
    line-height: 1;
    border-radius: 50%;
}
</style>
<main role="main" class="inner cover mb-1">
    <div class="card-header pt-2" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-sm-12"><h4><b>ECRP</b></h4></div>				
            </div> 
        </div>
    </div>
   
<section class="tab-defrent">
	<div class="container-fluid">
		<div class="card mt-4" style="">
			<div class="row"> 
				<div class="col-md-6 ml-4 mt-2 mr-4">
					<div class="table-responsive"> 
                           <table class="table mb-4 tableShadow">
                                <tbody>
                                    <tr>                  
                                        <td width="50%" class="bdr-none"><strong class="grayClr">Name of the candidate :</strong></td>
                                        <td class="bdr-none">{{$candidateData->cand_name}}</td>
                                    </tr>
									<tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Number and Name of Constituency :</strong></td>
                                        <td class="bdr-none">{{$candidateData->pc_no}} - {{$candidateData->PC_NAME}}</td>
                                    </tr>
                                    <tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Name of the State / Union Territory :</strong></td>
                                        <td class="bdr-none">{{$candidateData->ST_NAME}}</td>
                                    </tr>
									<tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Name of Election :</strong></td>
                                        <td class="bdr-none">{{$candidateData->ELECTION_TYPE}}</td>
                                    </tr>
									<tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Date of Declaration of result :</strong></td>
                                        <td class="bdr-none">23-05-2019</td>
                                    </tr>
									<tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Address of the Candidate :</strong></td>
                                        <td class="bdr-none">{{$candidateData->candidate_residence_address}}</td>
                                    </tr>                                
                                    <tr>                                    
                                        <td class="bdr-none"><strong class="grayClr">Political Party Affliation, If Any :</strong></td>
                                        <td class="bdr-none">{{$candidateData->PARTYNAME}} </td>
                                    </tr>                           
                                </tbody>
                            </table>
                          </div>
						</div>
				<div class="col-md-5 mt-2">
					<div class="table-responsive"> 
                        <table class="table mb-4 tableShadow download">
                             <tbody>  
								<tr>
									<td>Download ECRP PDF File</td>
									<td>
									@if(!empty($getSch1[0]))
									<a href="{{url('/candidate/downloadEcrpStatusReport')}}">Click here to Download ECRP PDF File</a> </td>
									@else
									First Filled ECRP Form	
									@endif	
								</tr>
								<tr>
									<td>Download Form of Affidavit</td>
									<td><a href="{{url('/ExpenditureReport/affidavit_form.pdf')}}" target="_blank">Click here to Download Affidavit Form</a> </td>
								</tr>
								<tr>
									<td>Download Form of Acknolegement</td>
									<td><a href="{{url('/ExpenditureReport/acknowledgement_form.pdf')}}" target="_blank">Click here to Download Acknolegement Form</a> </td>
								</tr>
									 <!-- <tr>
									<td>Upload ECRP PDF File Signed Copy</td>
									
									@if(empty($candidateData->filename))
									<td>	
									<form method="post" action="" enctype="multipart/form-data" id="upload_ecrp_file">
										<input type="file" name="pdf" id="file">
										<button type="button" id="upload_file">Upload File</button>
									</form>
									<br>
									<span class="UploadMsg"></span>
									</td>
									@else
									<td >ECR Already Uploaded</td>	
									@endif	
									</tr>
									<tr>
									<td>Upload Affidavit Form</td>
									@if(empty($candidateData->affidavit))
									<td>	
									<form method="post" action="" enctype="multipart/form-data" id="upload_ecrp_file_aff">
										<input type="file" name="pdf_aff" id="file_aff">
										<button type="button" id="upload_file_aff">Upload File</button>
									</form>
									<br>
									<span class="UploadMsg_aff"></span>
									</td>
									@else
									<td >Affidavit Already Uploaded</td>	
									@endif	
									</tr>
								   <tr>
									<td>Upload Acknolegement Form</td>
									@if(empty($candidateData->acknowledgement))
									<td>	
									<form method="post" action="" enctype="multipart/form-data" id="upload_ecrp_file_ack">
									<input type="file" name="pdf_ack" id="file_ack">
									<button type="button" id="upload_file_ack">Upload File</button>
									</form>
									<br>
									<span class="UploadMsg_ack"></span>
									</td>
									@else
									<td >Acknolegement Already Uploaded</td>	
									@endif	
									</tr> -->
								</tbody>
							</table>
						</div>
					</div>
            </div>
        
<div class="card mt-3 p-2">								
		<ul class="tabs">
			<li>
				<a href="#tab2">Schedule  1 - 10</a>
			</li>
			<li id="Tab2Open">
				<a href="#tab1" id="openTab">Part  I - IV</a>
			</li>						
		</ul>					
<div class="tabs-content">	
	<div id="tab1" class="tabContainer">
		<form id="annuxureForm" action="#" method="post">	
			{{ csrf_field() }} 	
			<input type="hidden" name="candidate_id_update" value="<?php echo !empty($GetAbstractData[0]->candidate_id)?$GetAbstractData[0]->candidate_id:"";?>" id="candidate_id_update">
			<input type="hidden" name="candidate_id" value="{{$candidateData->candidate_id}}" id="candidate_id">

		<div class="tab">
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
					<td><label>Name of the Candidate</label></td>
					<td>
					<div class="d-flex">
					 <input type="text" value="{{$candidateData->cand_name}}" name="name_of_candidate" class="form-control" placeholder="" readonly>
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
					<td><label>Nature of Election (Please mention whether General Election to State Assembly / Lok Sabha / Bye- election) </label></td>
					<td><input type="text" class="form-control" name="name_of_election" placeholder="" value="General" readonly="readonly"></td>
				</tr>
				<tr>
					<td>V</td>
					<td><label>Date of declaration of result </label></td>
					<td><input type="date" class="form-control" name="date_of_declaration_result" placeholder="" value="@php echo $electionSch[0]->DATE_COUNT @endphp" readonly="readonly"></td>
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
	
		<div class="tab">
		  <div class="table-responsive">
			<table class="table table-bordered">											 
			  <thead>
				<tr>
				  <th colspan="6" align="center" class="text-center">PART-II ABSTRACT STATEMENT OF ELECTION EXPENSES EXPENDITURE OF CANDIDATE</th>			  
				</tr>
				<tr>
				  <th align="center"  class="text-center">S. No.</th>						  
				  <th align="center"  class="text-center">Particulars </th>						  
				  <th align="center"  class="text-center">Amt. Incurred / Auth. by Candidate Election Agent (in Rs.) </th>						  
				  <th align="center"  class="text-center">Amt. Incurred authorized by Pol. Party(in Rs.)  </th>						  
				  <th align="center"  class="text-center">Amt. Incurred / authorized by Other(in Rs.)  </th>						  
				  <th align="center"  class="text-center">Total Expenditure (3) + (4) + (5)</th>
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
					<td><label>Expenses in public meeting, rally,procession etc.:-l.a.: Expences in public meeting , rally, procession etc. (i.e. other than once with the Star Campaigners of the political party)<br>(Enclose as per Schedule-1)</label></td>
					<td><input type="text" class="form-control amt1 inputright total_cand_amt" name="public_expenses_meeting_star_3" readonly pattern="\d*" maxlength="7" placeholder="0"></td>
					<td><input type="text" class="form-control amt2 inputright total_pp_amt" name="public_expenses_meeting_star_4" readonly></td>
					<td>
						<input type="text" class="form-control amt3 inputright total_other_amt" name="public_expenses_meeting_star_5" readonly></td>
					<td><input type="text" class="form-control subtotalann total_amt" name="public_expenses_meeting_star_6" pattern="\d*" maxlength="7" placeholder="0"  readonly></td>
				</tr>
				<tr>
					<td></td>
					<td><label>l. b. expenditure in public meeting rally, procession etc. with the star Campaigner(s) (ie other than those for general party propaganda)<br>(Enclose as per Schedule-2)</label>	</td>
					<td><input type="text" class="form-control amt1 inputright total_cand_amt_sch2" name="public_expenses_meeting_general_3" pattern="\d*" maxlength="7" placeholder="0" readonly="readonly"></td>
					<td><input type="text" class="form-control amt2  inputright total_pp_amt_sch2" name="public_expenses_meeting_general_4" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control amt3 total_other_amt_sch2 inputright" name="public_expenses_meeting_general_5" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control subtotal total_amt_Sch2" name="public_expenses_meeting_general_6" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
				</tr>
				<tr>
					<td>II</td>
					<td><label>Campaign materials other,rally, procession etc. mentioned in S. No.I above(Enclose as per Schedule-3)</label></td>
					<td><input type="text" class="form-control amt1 finalcandtotalsch3 inputright" name="compaign_material_3" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control amt2 finalpptotalsch3 inputright" name="compaign_material_4" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control amt3 finalothertotalsch3 inputright" name="compaign_material_5" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control subtotal total_amt_Sch3" name="compaign_material_6" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
				</tr>
				<tr> 
					<td>III</td>
					<td><label>Campaign, through print and electronic media including cable network, bulk SMS or internet and Social media (Enclose as per Schedule-4)</label></td>
					<td><input type="text" class="form-control amt1 inputright totalCandSch4" name="compaign_through_print_media_3" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control amt2 inputright totalPPSch4" name="compaign_through_print_media_4" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control amt3  inputright totalotheramtsch4" name="compaign_through_print_media_5" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
					<td><input type="text" class="form-control subtotal total_amt_Sch4" name="compaign_through_print_media_6" pattern="\d*" maxlength="7" placeholder="0" readonly></td>
				</tr>
				<tr>
					<td>IV</td>
					<td><label>Expenditure on campaign vehicle(s), used by candidate(Enclose as per schedule-5)</label></td>
					<td><input type="text" name="expenditure_on_compaign_vehicle_3" pattern="\d*" maxlength="7" class="form-control amt1  inputright totalcandamtsch5" placeholder="0" readonly></td>
					<td><input type="text" name="expenditure_on_compaign_vehicle_4" pattern="\d*" maxlength="7" class="form-control amt2  inputright totalppamtsch5" placeholder="0" readonly></td>
					<td><input type="text" name="expenditure_on_compaign_vehicle_5" pattern="\d*" maxlength="7" class="form-control amt3  inputright totalotheramtsch5" placeholder="0" readonly></td>
					<td><input type="text" name="expenditure_on_compaign_vehicle_6" pattern="\d*" maxlength="7" class="form-control subtotal totalamtsch5" placeholder="0" readonly></td>
				</tr>
				<tr>
					<td>V</td>
					<td><label>Expenses of campaign workers / agents (Enclose as per Schedule — 6)</label></td>
					<td><input type="text" name="expenditure_on_compaign_workers_3" pattern="\d*" maxlength="7" class="form-control totalcandamtsch6 amt1  inputright" placeholder="0" readonly></td>
					<td><input type="text" name="expenditure_on_compaign_workers_4" pattern="\d*" maxlength="7" class="form-control amt2 totalppamtsch6 inputright" placeholder="0" readonly></td>
					<td><input type="text" name="expenditure_on_compaign_workers_5" pattern="\d*" maxlength="7" class="form-control amt3 totalotheramtsch6 inputright" placeholder="0" readonly></td>
					<td><input type="text" name="expenditure_on_compaign_workers_6" pattern="\d*" maxlength="7" class="form-control totalamtsch6 subtotal" placeholder="0" readonly></td>
				</tr>
				<tr>
					<td>VI</td>
					<td><label>Any other campaign expenditure</label></td>
					<td><input type="text" name="any_other_compaign_expenditure_3" pattern="\d*" maxlength="7" class="form-control amt1  inputright annexcandtotalamt" placeholder="0" readonly></td>
					<td><input type="text" name="any_other_compaign_expenditure_4" pattern="\d*" maxlength="7" class="form-control amt2  inputright annexpptotalamt" placeholder="0" readonly></td>
					<td><input type="text" name="any_other_compaign_expenditure_5" pattern="\d*" maxlength="7" class="form-control amt3  inputright annexothertotalamt" placeholder="0" readonly></td>
					<td><input type="text" name="any_other_compaign_expenditure_6" pattern="\d*" maxlength="7" class="form-control subtotal annextotalamt" placeholder="0" readonly></td>
				</tr>
				<tr>
					<td>VII</td>
					<td><label>Expenses incurred on publishing of declaration regarding criminal cases (Enclose as per Schedule-10)*</label></td>
					<td><input type="text" name="expenses_incurred_on_publishing_3" pattern="\d*" maxlength="7" class="form-control amt1 totalChannelExpSch10  inputright" placeholder="0" readonly=""></td>

					<td><input type="text" name="expenses_incurred_on_publishing_4" pattern="\d*" maxlength="7" class="form-control amt2  inputright" placeholder="0" readonly ></td>

					<td><input type="text" name="expenses_incurred_on_publishing_5" pattern="\d*" maxlength="7" class="form-control amt3  inputright" placeholder="0" readonly></td>

					<td><input type="text" name="expenses_incurred_on_publishing_6" pattern="\d*" maxlength="7" class="form-control subtotal totalChannelExpSch10" placeholder="0" readonly></td>
				</tr>
										
			  </tbody>
			  <tfoot>							
				<tr>
					<td></td>
					<td><strong>Grand Total</strong></td>
					<td><input type="text" name="grand_total_candidate_agent" id="grand_total_candidate_agent" pattern="\d*" maxlength="7" class="form-control grandtotalcandamt" id="" placeholder="0" readonly></td>

					<td><input type="text" name="grand_total_amt_incurred_by_pol_party" id="grand_total_amt_incurred_by_pol_party" pattern="\d*" maxlength="7" class="form-control grandtotalppamt" placeholder="0" readonly></td>

					<td><input type="text" name="grand_total_amt_incurred_by_other" id="grand_total_amt_incurred_by_other" pattern="\d*" maxlength="7" class="form-control grandtotalotheramt " placeholder="0" readonly></td>
					
					<td><input type="text" name="total_expenditure"  id="total" class="form-control grandtotalamt" placeholder="0" readonly></td>
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
					<td><input type="text" name="amt_own_funds_election_compaign" pattern="\d*" maxlength="7" class="form-control annex3amt fund1 inputright totalamtsch7" placeholder="0" readonly></td>								
				</tr>
				<tr>
					<td>II</td>
					<td><label>Lump sum amount received from the party (ies) in cash or cheque etc.
					<br>(Enclose as per Schedule-8)</label></td>
					<td><input type="text" name="lump_sum_amt_from_party" pattern="\d*" maxlength="7" class="form-control fund2 annex3amt inputright totalamtsch8" placeholder="0" readonly></td>								
				</tr>
				<tr>
					<td>III</td>
					<td><label>Lump sum amount received from any persion/company/firm/association/body of persons etc. as loan. gift or donation etc.<br>(
					Enclose as per Schedule-9)</label></td>
					<td><input type="text" name="lump_sum_amt_from_other" pattern="\d*" maxlength="7" class="form-control fund3 annex3amt inputright totalamtsch9" placeholder="0" readonly></td>
				</tr>													
			  </tbody>
			  <tfoot>							
				<tr>
					<td></td>
					<td><strong>Grand Total</strong></td>
					<td><input type="text" name="grand_total_source_funds" readonly class="form-control grandtotalannex3 inputright" ></td>								
				</tr>
			  </tfoot>
			</table>						
		  </div>					 
		</div><!-- tab 03 close -->

	<div class="tab">
	   <div class="table-responsive">
		<table class="table">											 
			  <thead>
				<tr>
				  <th colspan="3" align="center" class="text-center"><h5><strong>PART IV</strong></h5></th>							  					  
				</tr>
				<tr>
				  <th colspan="3" align="center"  class="text-center">FORM OF AFFIDAVIT</th>						  
				</tr>
			  </thead>
			  <tbody>
				<tr>
                    <td class="bdr-none pt-5">
                        <p class="p-2 d-flex">Before the District Election Officer &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline" style="flex:1"> &nbsp;&nbsp; (District, State/Union Territory)</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none  pt-4">
                        <p class="p-2 d-flex">Affidavit of Shri/Smt/Ms &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline" style="flex:1"> &nbsp;&nbsp; (S/o, W/o, D/o) &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline" style="flex:2"> &nbsp;&nbsp; I &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline" style="flex:3"> &nbsp;&nbsp; son/wife/daughter of &nbsp;&nbsp;</p>
					</td>
                </tr>
				<tr>
                    <td class="bdr-none">
						<p class="p-2 d-flex"><input type="text" placeholder="" class="bdrline" style="flex:1"> &nbsp;&nbsp; aged &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline"  style="flex:2"> &nbsp;&nbsp; years, r/o &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline"  style="flex:3"> &nbsp;&nbsp; do hereby solemnly and sincerely state and declare as under:</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-5">
                        <p class="p-2">(1)&nbsp;&nbsp; That I was a contesting candidate at the general election/bye election to the House of the people/Legislative Assembly of &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline"> &nbsp;&nbsp; from &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline"> &nbsp;&nbsp; Parliamentary/Assembly Constituency, the result of which was declared on &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline"> &nbsp;&nbsp; </p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-3">
                        <p class="p-2">(2)&nbsp;&nbsp; That I/My election agent kept a separate and correct account of all expenditure incurred / authorised by me / my election agent in connection with the above election between &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline"> &nbsp;&nbsp; (the date on which I was nominated) and the date of declaration of the result thereof, both days inclusive.</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-3">
                        <p class="p-2">(3)&nbsp;&nbsp; That the said account was maintained in the Register furnished by the Returning Officer for the purpose and the said Register itself is annexed hereto with the supporting vouchers/bills mentioned in the said account.</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-3">
                        <p class="p-2">(4)&nbsp;&nbsp; That the account of my election expenditure as annexed here to include all item of election expenditure    
							incurred or authorised by me or by my election agent, the political party which sponsored me, other associations/body of persion and other individuals supporting me,
							in connection with the election, and nothing has been concealed or withheld/suppressed therefrom (other than the expense on traval of leaders' covered by Explanations 1 and 2 )
							under section 77(1) of the Representation of the People Act; 1951).</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-3">
                        <p class="p-2">(5)&nbsp;&nbsp; That the Abstract Statement of election Expenses as Annexed as Annexure II to the said account also includes all expenditure incurred or authorised by me, my election agent, the political party which sponsored me, other associations / body or persons and other individuals supporting me, in connection with the election.</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-3">
                        <p class="p-2">(6)&nbsp;&nbsp; That the statement in the foregoing paragraphs (1) to (5) are true to the best of my knowledge and belief, that  nothing is false and nothing material has been concealed.</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-5">
                        <p class="p-2">Deponent</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-4">
                        <p class="p-2 d-flex">Solemnly affirmed/sworn by &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline" style="flex:1;"> &nbsp;&nbsp; at &nbsp;&nbsp; <input type="text" placeholder="" class="bdrline" style="flex:2;"> &nbsp;&nbsp; this day of 201<input type="text" placeholder="" class="bdrline" style="flex:3;"> &nbsp;&nbsp; before me.</p>
                    </td>
                </tr>
				<tr>
                    <td class="bdr-none pt-4 pb-5">
                        <p class="p-2">(Signature and seal of the Attesting Authority, i.e. Magistrate of the first Class or Oath Commission or Notary Public)</p>
                    </td>
                </tr>							
			</tbody>						
		</table>
	</div>
	</div><!-- tab 4 close -->
		
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
	  <span class="step"></span>				  
	</div>

	</form>  
</div><!-- tab-1 --->
						
<div id="tab2" class="tabContainer"><!-- Tab 2 -->	
<form id="aknolwdgeForm" action="" method="post"><!-- from Close -->

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
                <tbody class="addedRows1">
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                </tr>
                @if(!empty($getSch1))
                @php $i=1 @endphp
                    @foreach($getSch1 as $getExpSch1)
               <tr>
                <td>@php echo $i @endphp</td>
                <td><label>@php echo $getExpSch1->naturename @endphp</label></td>
                <input type="hidden" name="@php echo $getExpSch1->id @endphp[fund][nature_of_exp_id]" value="@php echo $getExpSch1->id @endphp" />
                <td><input type="text" name="@php echo $getExpSch1->id @endphp[fund][total_amt]"  pattern="\d*" maxlength="7" class="form-control inputright s1amt src_total_amt ackSch1input" placeholder="0" value="@php echo !empty($getExpSch1->total_amt)? $getExpSch1->total_amt:""; @endphp" readonly><span class="sumerror"></span>
                </td>
                <td><input type="text" name="@php echo $getExpSch1->id @endphp[fund][src_amt_incurred_cand]" id="@php echo $getExpSch1->id @endphp[fund][src_amt_incurred_cand]" pattern="^[0–9]$" maxlength="7" class="form-control inputright s1amt1 ackSch1input src_amt_incurred_cand" placeholder="0"
                value="@php echo !empty($getExpSch1->src_amt_incurred_cand)? $getExpSch1->src_amt_incurred_cand:""; @endphp"></td>
                <td><input type="text" name="@php echo $getExpSch1->id @endphp[fund][src_amt_incurred_pp]" pattern="\d*" maxlength="7" class="form-control inputright s1amt2 ackSch1input src_amt_incurred_pp" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_pp)? $getExpSch1->src_amt_incurred_pp:""; @endphp"></td>
                <td><input type="text" name="@php echo $getExpSch1->id @endphp[fund][src_amt_incurred_other]" pattern="\d*" maxlength="7" class="form-control inputright s1amt3 ackSch1input src_amt_incurred_other" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_other)? $getExpSch1->src_amt_incurred_other:""; @endphp"></td>
            </tr>
                    @php $i++; @endphp
                    @endforeach
                @else
                	@foreach($natureofExp as $natofExp)
            <tr <?php if($natofExp['id']=="16"){?> id="rowId" <?php } ?>>
                <td>@php echo $natofExp['id'] @endphp</td>
                <td><label>@php echo $natofExp['naturename'] @endphp</label></td>
                <input type="hidden" name="@php echo $natofExp['id'] @endphp[fund][nature_of_exp_id]" value="@php echo $natofExp['id'] @endphp" />
                <td><input type="text" name="@php echo $natofExp['id'] @endphp[fund][total_amt]"  pattern="\d*" maxlength="7" class="form-control inputright src_total_amt s1amt ackSch1input" placeholder="0" value="@php echo !empty($getExpSch1->total_amt)? $getExpSch1->total_amt:""; @endphp" readonly></td>
                <td><input type="text" name="@php echo $natofExp['id'] @endphp[fund][src_amt_incurred_cand]" id="1[fund][src_amt_incurred_cand]" pattern="\d*" maxlength="7" class="form-control inputright ackSch1input s1amt1 src_amt_incurred_cand" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_cand)? $getExpSch1->src_amt_incurred_cand:""; @endphp"></td>
                <td><input type="text" name="@php echo $natofExp['id'] @endphp[fund][src_amt_incurred_pp]" pattern="\d*" maxlength="7" class="form-control inputright s1amt2 ackSch1input src_amt_incurred_pp" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_pp)? $getExpSch1->src_amt_incurred_pp:""; @endphp"></td>
                <td><input type="text" name="@php echo $natofExp['id'] @endphp[fund][src_amt_incurred_other]" pattern="\d*" maxlength="7" class="form-control inputright s1amt3 ackSch1input src_amt_incurred_other" placeholder="0" value="@php echo !empty($getExpSch1->src_amt_incurred_other)? $getExpSch1->src_amt_incurred_other:""; @endphp"></td>
                @if($natofExp['id']=="16")	
                <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows1(this.form);">+</span></td>
                @endif
             </tr>
                    @endforeach
                @endif
              </tbody>
              <tr>
                <td>&nbsp;</td>
                <td><label>Total</label></td>
                <td><input type="text" class="form-control st1 inputright total_amt" placeholder="" readonly="readonly"><span class="sch1totalerror"></span>
                </td>
                <td><input type="text" class="form-control st2 inputright ackSch1input  total_cand_amt" placeholder="" readonly="readonly"></td>
                <td><input type="text" class="form-control st3 inputright ackSch1input  total_pp_amt" placeholder="" readonly="readonly"></td>
                <td><input type="text" class="form-control st4 inputright ackSch1input  total_other_amt" placeholder="" readonly="readonly"></td>
                </tr>
            </table>
          </div>
	</div><!--  ScheduleTab1 close 1-->

<div class="ScheduleTab">
    <div class="table-responsive">
		<h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered" id="aknolwdgeForm-sch2">
              <thead class="text-center">
                <tr>
                  <th colspan="9" align="center"><h5><strong>Schedule - 2</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="9">Expenditure in public meeting rally, procession etc. with the Star Campaigner(s) as apportioned to candidate (ie: other than those for general party propaganda)</th>
                </tr>
                <tr>
                  <th>S. No</th>
                  <th>Date</th>
                  <th>Venue</th>
                  <th>Name of the Star Campaigner(s) & Name of the Party</th>
                  <th colspan="3">Amount of Expenditure on public meeting rally, procession etc. with the Star Campaigner(s) apportioned to the candidate (As other than for general party propaganda) in Rs. </th>
                  <th >Remarks If Anys </th>
                  <th rowspan="4">Action</th>
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
                <tbody  class="addedRows">
                 @if(!empty($getSch2))
                 @php $i = 1; @endphp
                    @foreach($getSch2 as $getExpSch2)
                    	<tr >
                            <td>@php echo $i++ @endphp</td>
                            <td><input type="date" class="form-control" name="@php echo $getExpSch2->id @endphp['starcampaigner'][meetingdate]" placeholder="" value="@php echo $getExpSch2->meetingdate @endphp" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>

                            <td><input type="text" class="form-control alphanumericval" name="@php echo $getExpSch2->id @endphp['starcampaigner'][venue]" placeholder="" value="@php echo  $getExpSch2->venue ; @endphp" maxlength="100"></td>

                            <td><input type="text" class="form-control alphaval" name="@php echo $getExpSch2->id @endphp['starcampaigner'][name_of_start_and_party]" placeholder="" value="@php echo $getExpSch2->name_of_start_and_party @endphp" maxlength="100"></td>

                            <td><input type="text" class="form-control ackSch1input inputright amt_cand_sch2" name="@php echo $getExpSch2->id @endphp['starcampaigner'][src_amt_by_cand]" maxlength="7" placeholder="" value="@php echo $getExpSch2->src_amt_by_cand ; @endphp"></td>

                            <td><input type="text" class="form-control ackSch1input inputright amt_pp_sch2" name="@php echo $getExpSch2->id @endphp['starcampaigner'][src_amt_by_pp]" maxlength="7" placeholder="" value="@php echo $getExpSch2->src_amt_by_pp ; @endphp"></td>

                            <td><input type="text" class="form-control ackSch1input inputright amt_other_sch2" name="@php echo $getExpSch2->id @endphp['starcampaigner'][src_amt_by_other]" maxlength="7" placeholder="" value="@php echo $getExpSch2->src_amt_by_other ;  @endphp"></td>

                            <td><input type="text" class="form-control alphanumericval" name="@php echo $getExpSch2->id @endphp['starcampaigner'][remarks]" placeholder="" value="@php echo $getExpSch2->remarks ; @endphp"></td>
                             
                        </tr>
                    @endforeach
                 @else
                 	<tr >
                        <td>1</td>
                        <td><input type="date" class="form-control " name="1['starcampaigner'][meetingdate]" placeholder="" id="" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                        <td><input type="text" class="form-control alphanumericval" name="1['starcampaigner'][venue]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control alphaval" name="1['starcampaigner'][name_of_start_and_party]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control ackSch1input inputright amt_cand_sch2" pattern="^[0–9]$" name="1['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input inputright amt_pp_sch2" pattern="^[0–9]$" name="1['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input inputright amt_other_sch2" pattern="^[0–9]$" name="1['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control alphanumericval"  name="1['starcampaigner'][remarks]" placeholder="" maxlength="100"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td><input type="date" class="form-control " name="2['starcampaigner'][meetingdate]" placeholder="" id="" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                        <td><input type="text" class="form-control alphanumericval" name="2['starcampaigner'][venue]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control alphaval" name="2['starcampaigner'][name_of_start_and_party]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control ackSch1input amt_cand_sch2 inputright" pattern="^[0–9]$" name="2['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input amt_pp_sch2 inputright" pattern="^[0–9]$" name="2['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input amt_other_sch2 inputright" pattern="^[0–9]$" name="2['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control alphanumericval" name="2['starcampaigner'][remarks]" placeholder="" maxlength="100"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td><input type="date" class="form-control " name="3['starcampaigner'][meetingdate]" placeholder="" id="" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                        <td><input type="text" class="form-control alphanumericval" name="3['starcampaigner'][venue]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control alphaval" name="3['starcampaigner'][name_of_start_and_party]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control ackSch1input amt_cand_sch2 inputright" pattern="^[0–9]$" name="3['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input amt_pp_sch2 inputright" pattern="^[0–9]$" name="3['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input amt_other_sch2 inputright" pattern="^[0–9]$" name="3['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control alphanumericval" name="3['starcampaigner'][remarks]" placeholder=""  maxlength="100"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr id="rowId">
                        <td>4</td>
                        <td><input type="date" class="form-control " name="4['starcampaigner'][meetingdate]" placeholder="" id="" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                        <td><input type="text" class="form-control alphanumericval" name="4['starcampaigner'][venue]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control alphaval" name="4['starcampaigner'][name_of_start_and_party]" placeholder="" maxlength="100"></td>
                        <td><input type="text" class="form-control ackSch1input amt_cand_sch2 inputright" pattern="^[0–9]$" name="4['starcampaigner'][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input amt_pp_sch2 inputright" pattern="^[0–9]$" name="4['starcampaigner'][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control ackSch1input amt_other_sch2 inputright" pattern="^[0–9]$" name="4['starcampaigner'][src_amt_by_other]" placeholder="" maxlength="7"></td>
                        <td><input type="text" class="form-control alphanumericval" name="4['starcampaigner'][remarks]" placeholder="" maxlength="100"></td>
                        <td colspan="4"><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows2(this.form);">+</span></td>
                    </tr>
                @endif
                <tfoot>
                    <tr>
                        <td colspan="4">Total</td>
                        <td><input type="text" class="inputright form-control total_cand_amt_sch2" readonly="readonly" /></td>
                        <td><input type="text" class="form-control inputright total_pp_amt_sch2" readonly="readonly" /></td>
                        <td><input type="text" class="form-control inputright total_other_amt_sch2" readonly="readonly" /></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                </tfoot>
               
              </tbody>
            </table>
         </div>
	</div><!-- ScheduleTab close2-->

	<div class="ScheduleTab">
       <div class="table-responsive">
		  <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered" id="aknolwdgeForm-sch3">
              <thead class="text-center">
                <tr >3
                  <th colspan="8" align="center"><h5><strong>Schedule - 3</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="8">Details of expenditure on campaign material, like handbills, pamphlets, posters, hoardings, banners, cut-outs, gates & arches, video and audio cassettes, CDs/DVDs, Loud Speakers, amplifiers, digital TV/board display, 3 D display etc. for candidate’s election campaign (ie: other than those covered in Schedule- 1 & 2)</th>
                </tr>
                <tr>
                  <th rowspan="2">S. No</th>
                  <th rowspan="2">Nature of Expenses</th>
                  <th rowspan="2">Total Amount in Rs.</th>
                  <th colspan="3">Source of Expenditure</th>
                  <th rowspan="2">Remarks If Any</th>
                  <th rowspan="2">Action</th>
                </tr>
                <tr>
                  <th >Amt. By Candidate / Agent</th>
                  <th >Amt. By Pol. Party</th>
                  <th >Amt. by Others</th>                  
                </tr>
              </thead>
                <tbody class="addedRows3">
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                    <td>7</td>
                    <td>&nbsp;</td>
                </tr>
                @if(!empty($getSch3))
                	@php $i = 1; @endphp
                    @foreach($getSch3 as $getExpSch3)
                     <tr>
                        <td>@php echo $i++ @endphp</td>       
                        <td><input type="text" class="form-control alphaval" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][nature_of_expense]" placeholder="" value="@php echo !empty($getExpSch3->nature_of_expense)? $getExpSch3->nature_of_expense:""; @endphp"></td>
                        <td><input type="text"  class="form-control ackSch1input inputright totalsch3" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][total_amt]" placeholder="" value="@php echo !empty($getExpSch3->total_amt)? $getExpSch3->total_amt:""; @endphp"></td>
                        <td><input type="text" maxlength="7" class="form-control ackSch1input candamtsch3 inputright" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][src_amt_by_cand]" placeholder="" value="@php echo !empty($getExpSch3->src_amt_by_cand)? $getExpSch3->src_amt_by_cand:""; @endphp" maxlength="7"></td>
                        <td><input type="text" maxlength="7" class="form-control ackSch1input ppamtsch3 inputright" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][src_amt_by_pp]" placeholder="" value="@php echo !empty($getExpSch3->src_amt_by_pp)? $getExpSch3->src_amt_by_pp:""; @endphp" maxlength="7"></td>
                        <td><input type="text" maxlength="7" class="form-control ackSch1input otheramtsch3 inputright" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][src_amt_by_other]" placeholder="" value="@php echo !empty($getExpSch3->src_amt_by_other)? $getExpSch3->src_amt_by_other:""; @endphp" maxlength="7"></td>
                        <td><input type="text" maxlength="100" class="form-control alphanumericval" name="@php echo $getExpSch3->id @endphp['campaigmaterial'][remarks]" placeholder="" value="@php echo !empty($getExpSch3->remarks)? $getExpSch3->remarks:""; @endphp"></td>
                        <td>&nbsp;</td>

                    </tr>
                 	@endforeach
                @else
                <tr>
                    <td>1</td>
                    <td><input type="text" class="form-control alphanumericval" name="1[campaigmaterial][nature_of_expense]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input inputright totalsch3" name="1[campaigmaterial][total_amt]" placeholder="" ></td>
                    <td><input type="text" class="form-control ackSch1input inputright candamtsch3" name="1[campaigmaterial][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright ppamtsch3" name="1[campaigmaterial][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright otheramtsch3" name="1[campaigmaterial][src_amt_by_other]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="1[campaigmaterial][remarks]" placeholder=""  maxlength="100"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><input type="text" class="form-control alphanumericval" name="2[campaigmaterial][nature_of_expense]" placeholder=""  maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input totalsch3" name="2[campaigmaterial][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control inputright ackSch1input candamtsch3" name="2[campaigmaterial][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input ppamtsch3" name="2[campaigmaterial][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input otheramtsch3" name="2[campaigmaterial][src_amt_by_other]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="2[campaigmaterial][remarks]" placeholder=""  maxlength="100"></td>
                    <td>&nbsp;</td>
                </tr>
                 <tr>
                    <td>3</td>
                    <td><input type="text" class="form-control alphanumericval" name="3[campaigmaterial][nature_of_expense]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input totalsch3" name="3[campaigmaterial][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control inputright ackSch1input candamtsch3" name="3[campaigmaterial][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input ppamtsch3" name="3[campaigmaterial][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input otheramtsch3" name="3[campaigmaterial][src_amt_by_other]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="3[campaigmaterial][remarks]" placeholder=""  maxlength="100"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="rowId">
                    <td>4</td>
                    <td><input type="text" class="form-control alphanumericval" name="4[campaigmaterial][nature_of_expense]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input inputright totalsch3" name="4[campaigmaterial][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input inputright candamtsch3" name="4[campaigmaterial][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright ppamtsch3" name="4[campaigmaterial][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright otheramtsch3" name="4[campaigmaterial][src_amt_by_other]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="4[campaigmaterial][remarks]" placeholder=""  maxlength="100"></td>
                    <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows3(this.form);">+</span></td>
                </tr>
                @endif    
            </tbody>
            <tr>
                <td colspan="2">Total</td>
                <td><input type="text" class="form-control total_amt_Sch3 inputright" readonly="readonly" /></td>
                <td><input type="text" class="form-control finalcandtotalsch3 inputright" readonly="readonly" /></td>
                <td><input type="text" class="form-control finalpptotalsch3 inputright" readonly="readonly" /></td>
                <td><input type="text" class="form-control finalothertotalsch3 inputright" readonly="readonly" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
               
            </tr>
        </table>
    </div>
</div><!-- ScheduleTab close3-->
	
	<div class="ScheduleTab">
     <div class="table-responsive">
	   <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered" id="aknolwdgeForm-sch4">
              <thead class="text-center">
                <tr>
                  <th colspan="12" align="center"><h5><strong>Schedule - 4</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="12">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in privately owned newspapers/TV/radio channels etc.</th>
                </tr>
                <tr>
                  <th rowspan="2">S. No.</th>
                  <th rowspan="2">Nature of medium  (electronic/print) and duration</th>
                  <th rowspan="2">Name of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
                  <th rowspan="2" width="30%">Address of media provider </th>
                  <th rowspan="2">Price of Media</th>
                  <th rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
                  <th rowspan="2">Commission of Agency</th>
                  <th rowspan="2">Total Amount in Rs. <br />Col. (5)+(7)</th>
                  <th colspan="3">Sources of Expenditure</th>
                  <th rowspan="2">Action</th>
                </tr>
                <tr>
                  <th >Amt. By candidate/agent</th>
                  <th >Amt. By Pol. Party</th>
                  <th >Amt. By others</th>                  
                </tr>
              </thead>
                <tbody class="addedRows4">
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
                    <td>&nbsp;</td>
                </tr>
               @if(!empty($getSch4))
                     @php $i = 1; @endphp
                     @foreach($getSch4 as $getExpSch4)
                  <tr>
                    <td>@php echo $i++ @endphp</td>
                    <td>
                    	<select class="form-control" name="@php echo $getExpSch4->id @endphp['expdetails'][nature_of_medium]" @endphp>
                        	<option value="">Select Media</option>
                        	<option value="Electronic Media" @if($getExpSch4->nature_of_medium == 'Electronic Media') selected="selected" @endif >Electronic Media</option>	
                        	<option value="Print Media"@if($getExpSch4->nature_of_medium == 'Print Media') selected="selected" @endif>Print Media</option>
                        </select>
                    </td>

                    <td><input type="text" maxlength="60" class="form-control alphaval" name="@php echo $getExpSch4->id @endphp['expdetails'][name_of_media]" placeholder="" value="@php echo !empty($getExpSch4->name_of_media)? $getExpSch4->name_of_media:""; @endphp"></td>

                    <td><input type="text" maxlength="60" class="form-control alphanumericval" name="@php echo $getExpSch4->id @endphp['expdetails'][address_of_media]" placeholder="" value="@php echo !empty($getExpSch4->address_of_media)? $getExpSch4->address_of_media:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="ackSch1input form-control priceofmediasch4 inputright" name="@php echo $getExpSch4->id @endphp['expdetails'][price_of_the_media]" placeholder="" value="@php echo !empty($getExpSch4->price_of_the_media)? $getExpSch4->price_of_the_media:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control alphanumericval" name="@php echo $getExpSch4->id @endphp['expdetails'][name_address_of_agency]" placeholder="" value="@php echo !empty($getExpSch4->name_address_of_agency)? $getExpSch4->name_address_of_agency:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control ackSch1input commissionofmediasch4 inputright" name="@php echo $getExpSch4->id @endphp['expdetails'][commission_of_agency]" placeholder="" value="@php echo !empty($getExpSch4->commission_of_agency)? $getExpSch4->commission_of_agency:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control ackSch1input inputright" name="@php echo $getExpSch4->id @endphp['expdetails'][total_amt]" placeholder="" value="@php echo $getExpSch4->total_amt ; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control ackSch1input candamtsch4 inputright" maxlength="7" name="@php echo $getExpSch4->id @endphp['expdetails'][src_amt_by_cand]" placeholder="" value="@php echo !empty($getExpSch4->src_amt_by_cand)? $getExpSch4->src_amt_by_cand:""; @endphp"></td>

                   <td><input type="text" maxlength="7" class="form-control ackSch1input ppamtsch4 inputright" maxlength="7" name="@php echo $getExpSch4->id @endphp['expdetails'][src_amt_by_pp]" placeholder="" value="@php echo !empty($getExpSch4->src_amt_by_pp)? $getExpSch4->src_amt_by_pp:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control ackSch1input otheramtsch4 inputright" maxlength="7" name="@php echo $getExpSch4->id @endphp['expdetails'][src_amt_by_other]" placeholder="" value="@php echo !empty($getExpSch4->src_amt_by_other)? $getExpSch4->src_amt_by_other:""; @endphp"></td>
                    <td>&nbsp;</td>
                  </tr>		                        
                    @endforeach
                @else
                <tr>
                    <td>1</td>
                    <td><select class="form-control" name="1[expdetails][nature_of_medium]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select></td>
                    <td><input type="text" class="form-control alphaval" name="1[expdetails][name_of_media]"placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control alphanumericval" name="1[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input priceofmedia priceofmediasch4 inputright" name="1[expdetails][price_of_the_media]"placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="1[expdetails][name_address_of_agency]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input commissionofmedia commissionofmediasch4 inputright" name="1[expdetails][commission_of_agency]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input subtotal4 inputright" name="1[expdetails][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch4 inputright" name="1[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch4 inputright" name="1[expdetails][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch4 inputright" name="1[expdetails][src_amt_by_other]" maxlength="7" placeholder=""></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                	<td>2</td>
                    <td><select class="form-control" name="2[expdetails][nature_of_medium]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select></td>
                    <td><input type="text" class="form-control alphaval" name="2[expdetails][name_of_media]"placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control alphanumericval" name="2[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input priceofmedia priceofmediasch4 inputright" name="2[expdetails][price_of_the_media]"placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="2[expdetails][name_address_of_agency]"   placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input commissionofmedia commissionofmediasch4" maxlength="7" name="2[expdetails][commission_of_agency]"></td>
                    <td><input type="text" class="form-control inputright ackSch1input subtotal4
                    " name="2[expdetails][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control inputright ackSch1input candamtsch4" name="2[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input ppamtsch4" name="2[expdetails][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright otheramtsch4" name="2[expdetails][src_amt_by_other]" maxlength="7" placeholder=""></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><select class="form-control" name="3[expdetails][nature_of_medium]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select></td>
                    <td><input type="text" class="form-control alphaval" name="3[expdetails][name_of_media]"placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control alphanumericval" name="3[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright priceofmedia priceofmediasch4" name="3[expdetails][price_of_the_media]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="3[expdetails][name_address_of_agency]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input commissionofmedia commissionofmediasch4" name="3[expdetails][commission_of_agency]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input subtotal4 " name="3[expdetails][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch4 inputright" name="3[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch4" name="3[expdetails][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch4 inputright" name="3[expdetails][src_amt_by_other]" maxlength="7" placeholder=""></td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="rowId">
                    <td>4</td>
                    <td><select class="form-control" name="4[expdetails][nature_of_medium]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select></td>
                    <td><input type="text" class="form-control alphaval" name="4[expdetails][name_of_media]"placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control alphanumericval" name="4[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input priceofmedia priceofmediasch4 inputright" name="4[expdetails][price_of_the_media]"placeholder=""  maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="4[expdetails][name_address_of_agency]" placeholder=""  maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input inputright commissionofmedia commissionofmediasch4" name="4[expdetails][commission_of_agency]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input subtotal4 " name="4[expdetails][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input inputright candamtsch4" name="4[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright ppamtsch4" name="4[expdetails][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright otheramtsch4" name="4[expdetails][src_amt_by_other]" maxlength="7" placeholder=""></td>
                    <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows4(this.form);">+</span></td>
                </tr>
                @endif
                 
            </tbody>
            <tr>
	            <td colspan="3">Total</td>
	            <td colspan="2"><input type="" class="form-control totalpriceofmediasch4 inputright" readonly></td>
	            <td></td>
	            <td><input type="" class="form-control totalcommissionofmediasch4 inputright" readonly></td>
	            <td><input type="" class="form-control total_amtsch4 inputright" readonly="readonly"></td>
	            <td><input type="" class="form-control totalCandSch4 inputright" readonly="readonly"></td>
	            <td><input type="" class="form-control totalPPSch4 inputright" readonly="readonly"></td>
	            <td><input type="" class="form-control totalotheramtsch4 inputright" readonly></td>
	            <td>&nbsp;</td>
            </tr>
        </table>
    </div>
</div><!-- ScheduleTab close4-->
	
	<div class="ScheduleTab">
     <div class="table-responsive">
	  <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered" id="aknolwdgeForm-sch5">
              <thead class="text-center">
                <tr>
                  <th colspan="12" align="center"><h5><strong>Schedule - 4A</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="12">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in newspapers/TV/radio channels, owned by the candidate or by the political party sponsoring the candidate.</th>
                </tr>
                <tr>
                  <th rowspan="2">S. No.</th>
                  <th rowspan="2">Nature of medium  (electronic/print) and duration</th>
                  <th rowspan="2">Name of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
                  <th rowspan="2" width="30%">Address of media provider </th>
                  <th rowspan="2">Price of Media</th>
                  <th rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
                  <th rowspan="2">Commission of Agency</th>
                  <th rowspan="2">Total Amount in Rs. <br />Col. (5)+(7)</th>
                  <th colspan="3">Sources of Expenditure</th>
                  <th rowspan="2">Action</th>
                </tr>
                <tr>
                  <th >Amt. By candidate/agent</th>
                  <th >Amt. By Pol. Party</th>
                  <th >Amt. By others</th>                  
                </tr>
              </thead>
                <tbody class="addedRows4a">
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
                    <td>&nbsp;</td>
                </tr>
                @if(!empty($getSch4a))
                	@php $i = 1; @endphp
                	@foreach($getSch4a as $getExp4a)
        		<tr>
                    <td>@php echo $i++ @endphp
                    	<input type="hidden" name="@php echo $getExp4a->id @endphp['expdetails'][id]" value="@php echo $getExp4a->id @endphp">
                    </td>
                    <td>
                    <select class="form-control" name="@php echo $getExp4a->id @endphp['expdetails'][nature_of_media]" @endphp>
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media" @if($getExp4a->nature_of_media == 'Electronic Media') selected="selected" @endif >Electronic Media</option>	
                    	<option value="Print Media"@if($getExp4a->nature_of_media == 'Print Media') selected="selected" @endif>Print Media</option>
                    </select>
                    </td>
                    <td><input type="text" maxlength="50" class="form-control alphaval" name="@php echo $getExp4a->id @endphp['expdetails'][name_of_media]" placeholder="" value="@php echo !empty($getExp4a->name_of_media)? $getExp4a->name_of_media:""; @endphp"></td>

                    <td><input type="text" maxlength="50" class="form-control alphanumericval" name="@php echo $getExp4a->id @endphp['expdetails'][address_of_media]" placeholder="" value="@php echo !empty($getExp4a->address_of_media)? $getExp4a->address_of_media:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control inputright ackSch1input priceofmedia priceamt4a" name="@php echo $getExp4a->id @endphp['expdetails'][price_of_the_media]" placeholder="" value="@php echo !empty($getExp4a->price_of_the_media)? $getExp4a->price_of_the_media:""; @endphp"></td>

                    <td><input type="text" maxlength="60" class="form-control alphanumericval" name="@php echo $getExp4a->id @endphp['expdetails'][name_address_of_agency]" placeholder="" value="@php echo !empty($getExp4a->name_address_of_agency)? $getExp4a->name_address_of_agency:""; @endphp"></td>

                    <td><input type="text" maxlength="7"  class="form-control ackSch1input commissionofmedia inputright commissionamt4a" name="@php echo $getExp4a->id @endphp['expdetails'][commission_of_agency]" value="@php echo !empty($getExp4a->commission_of_agency)? $getExp4a->commission_of_agency:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control ackSch1input subtotal4a inputright totalamt4a" name="@php echo $getExp4a->id @endphp['expdetails'][total_amt]" placeholder="" value="@php echo $getExp4a->total_amt @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control ackSch1input inputright candamtsch4a" name="@php echo $getExp4a->id @endphp['expdetails'][src_amt_by_cand]" placeholder="" value="@php echo !empty($getExp4a->src_amt_by_cand)? $getExp4a->src_amt_by_cand:""; @endphp"></td>

                    <td><input type="text" maxlength="7" class="form-control ackSch1input inputright ppamtsch4a" name="@php echo $getExp4a->id @endphp['expdetails'][src_amt_by_pp]" placeholder="" value="@php echo !empty($getExp4a->src_amt_by_pp)? $getExp4a->src_amt_by_pp:""; @endphp">
                    </td>

                    <td><input type="text" maxlength="7" class="ackSch1input form-control inputright otheramtsch4a" name="@php echo $getExp4a->id @endphp['expdetails'][src_amt_by_other]" placeholder="" value="@php echo !empty($getExp4a->src_amt_by_other)? $getExp4a->src_amt_by_other:""; @endphp"></td>
                    <td>&nbsp;</td>
                </tr>
                	@endforeach
                @else
                <tr>
                    <td>1</td>
                    <td>
                    <select class="form-control" name="1[expdetails][nature_of_media]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select>
                    </td>
                     <td><input type="text" class="form-control alphaval" name="1[expdetails][name_of_media]" placeholder="" maxlength="100"></td>
                     <td><input type="text" class="form-control alphanumericval" name="1[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input priceamt4a priceofmedia" name="1[expdetails][price_of_the_media]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="1[expdetails][name_address_of_agency]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input commissionamt4a commissionofmedia" name="1[expdetails][commission_of_agency]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input totalamt4a subtotal4a" name="1[expdetails][total_amt]" placeholder="" ></td>
                    <td><input type="text" class="form-control inputright ackSch1input candamtsch4a" name="1[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input ppamtsch4a" name="1[expdetails][src_amt_by_pp]" placeholder=""  maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input otheramtsch4a" name="1[expdetails][src_amt_by_other]" placeholder="" maxlength="7"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>
                    <select class="form-control" name="2[expdetails][nature_of_media]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select>
                    </td>
                    <td><input type="text" class="form-control alphaval" name="2[expdetails][name_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control alphanumericval" name="2[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input priceofmedia priceamt4a" name="2[expdetails][price_of_the_media]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="2[expdetails][name_address_of_agency]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input commissionamt4a commissionofmedia" name="2[expdetails][commission_of_agency]"  maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input totalamt4a subtotal4a" name="2[expdetails][total_amt]" placeholder="" ></td>

                    <td><input type="text" class="form-control inputright ackSch1input candamtsch4a" name="2[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input ppamtsch4a" name="2[expdetails][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input otheramtsch4a" name="2[expdetails][src_amt_by_other]" placeholder=""  maxlength="7"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>
                    <select class="form-control" name="3[expdetails][nature_of_media]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select>
                    </td>
                    <td><input type="text" class="form-control alphaval" name="3[expdetails][name_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control alphanumericval" name="3[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input priceofmedia priceamt4a" name="3[expdetails][price_of_the_media]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control alphanumericval" name="3[expdetails][name_address_of_agency]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input commissionamt4a inputright commissionofmedia" name="3[expdetails][commission_of_agency]" maxlength="7"></td>
                    <td><input type="text" class="form-control subtotal4a totalamt4a inputright" name="3[expdetails][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch4a inputright" name="3[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch4a inputright" name="3[expdetails][src_amt_by_pp]" placeholder="" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch4a inputright" name="3[expdetails][src_amt_by_other]" placeholder="" maxlength="7"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="rowId">
                    <td>4</td>
                    <td>
                    <select class="form-control" name="4[expdetails][nature_of_media]">
                    	<option value="">Select Media</option>
                    	<option value="Electronic Media">Electronic Media</option>	
                    	<option value="Print Media">Print Media</option>
                    </select>
                    </td>
                    <td><input type="text" class="form-control alphaval" name="4[expdetails][name_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control alphanumericval" name="4[expdetails][address_of_media]" placeholder="" maxlength="100"></td>
                    <td><input type="text" class="form-control inputright ackSch1input priceofmedia priceamt4a" name="4[expdetails][price_of_the_media]" maxlength="7" placeholder=""></td>
                    <td><input type="text" class="form-control alphanumericval" name="4[expdetails][name_address_of_agency]" placeholder=""  maxlength="100"></td>
                    <td><input type="text" class="form-control ackSch1input commissionamt4a inputright commissionofmedia" name="4[expdetails][commission_of_agency]" maxlength="7"></td>

                    <td><input type="text" class="form-control ackSch1input totalamt4a inputright subtotal4a" name="4[expdetails][total_amt]" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch4a inputright" name="4[expdetails][src_amt_by_cand]" maxlength="7" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch4a inputright" name="4[expdetails][src_amt_by_pp]" maxlength="7" placeholder=""></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch4a inputright" name="4[expdetails][src_amt_by_other]" maxlength="7" placeholder=""></td>
                    <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows4a(this.form);">+</span></td>
                </tr>
                @endif    
            </tbody>
            <tr>
                <td colspan="4">Total</td>
                <td><input type="" class="form-control totalpriceofmediasch4a inputright" readonly></td>
                <td></td>
                <td><input type="" class="form-control totalcommissionofmediasch4a inputright" readonly></td>
                <td><input type="" class="form-control total_amtsch4a inputright" readonly="readonly"></td>
                <td><input type="" class="form-control totalCandSch4a inputright" readonly="readonly"></td>
                <td><input type="" class="form-control totalPPSch4a inputright" readonly="readonly"></td>
                <td><input type="" class="form-control totalotheramtsch4a inputright" readonly></td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
</div><!-- ScheduleTab close5-->
	
	<div class="ScheduleTab">
     <div class="table-responsive" id="aknolwdgeForm-sch6">
	   <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered">
              <thead class="text-center">
                <tr>
                  <th colspan="11" align="center"><h5><strong>Schedule - 5</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="11">Details of expenditure on campaign vehicle (s) and poll expenditure on vehicle (s) for candidate's election campaign</th>
                </tr>
                <tr>
                  <th rowspan="2">S. No.</th>
                  <th rowspan="2">Regn. No. of Vehicle & Type of vehicle</th>
                  <th colspan="3">Hiring Charges of vehicle</th>
                  <th rowspan="2">No. of Days for which used</th>
                  <th rowspan="2">Total amt. incurred/auth in Rs.</th>
                  <th colspan="3">Source of Expenditure</th>
                  <th rowspan="2">Action</th>
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
                <tbody class="addedRows5">
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
                    <td>&nbsp;</td>
                </tr>
                @if(!empty($getSch5))
                	@php $i = 1; @endphp
                	@foreach($getSch5 as $getExp5)
                <tr>
                    <td>@php echo $i++ @endphp</td>
                    <input type="hidden" value="@php echo $getExp5->id ; @endphp" name="@php echo $getExp5->id ; @endphp['expdetails'][id]"/>

                    <td><input type="text" class="form-control alphanumericval" maxlength="20" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][regn_no_of_vehicle]" value="@php echo $getExp5->regn_no_of_vehicle ; @endphp"></td>

                    <td><input type="text" class="form-control ackSch1input inputright" maxlength="7" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][hir_rate_for_vehicle]" value="@php echo  $getExp5->hir_rate_for_vehicle ; @endphp"></td>

                    <td><input type="text" class="form-control ackSch1input inputright" maxlength="7" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][hir_fuel_charges]" value="@php echo $getExp5->hir_fuel_charges; @endphp"></td>

                    <td><input type="text" class="form-control inputright ackSch1input" maxlength="7" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][hir_driver_charges]" value="@php echo  $getExp5->hir_driver_charges; @endphp"></td>

                    <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][no_of_days]" value="@php echo  $getExp5->no_of_days; @endphp"></td>

                    <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][total_amt_incurred]" value="@php echo $getExp5->total_amt_incurred ; @endphp"></td>

                    <td><input type="text" class="form-control inputright ackSch1input candamtsch5" maxlength="7" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][src_amt_by_cand]" maxlength="7" value="@php echo  $getExp5->src_amt_by_cand; @endphp"></td>

                    <td><input type="text" class="form-control inputright ackSch1input ppamtsch5" maxlength="7" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][src_amt_by_pp]" maxlength="7" value="@php echo $getExp5->src_amt_by_pp ; @endphp"></td>

                    <td><input type="text" class="form-control inputright ackSch1input otheramtsch5" maxlength="7" placeholder="" name="@php echo $getExp5->id ; @endphp['expdetails'][src_amt_by_other]" maxlength="7" value="@php echo $getExp5->src_amt_by_other; @endphp"></td>
                    <td>&nbsp;</td>

                </tr>
                	@endforeach
                @else
                <tr>
                    <td>1</td>
                    <td><input type="text" class="form-control alphanumericval" placeholder="" name="1[expdetails][regn_no_of_vehicle]" maxlength="20"></td>
                    <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="1[expdetails][hir_rate_for_vehicle]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="1[expdetails][hir_fuel_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="1[expdetails][hir_driver_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="1[expdetails][no_of_days]"  maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="1[expdetails][total_amt_incurred]"></td>
                    <td><input type="text" class="form-control inputright ackSch1input candamtsch5" placeholder="" name="1[expdetails][src_amt_by_cand]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input ppamtsch5" placeholder="" name="1[expdetails][src_amt_by_pp]" maxlength="7"></td>
                    <td><input type="text" class="form-control inputright ackSch1input otheramtsch5" placeholder="" name="1[expdetails][src_amt_by_other]" maxlength="7"></td>
                    <td>&nbsp;</td>

                </tr>
                <tr>
                    <td>2</td>
                    <td><input type="text" class="form-control alphanumericval" placeholder="" name="2[expdetails][regn_no_of_vehicle]" maxlength="20"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][hir_rate_for_vehicle]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][hir_fuel_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][hir_driver_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][no_of_days]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][total_amt_incurred]"></td>
                    <td><input type="text" class="form-control ackSch1input inputright candamtsch5" placeholder="" name="2[expdetails][src_amt_by_cand]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright ppamtsch5" placeholder="" name="2[expdetails][src_amt_by_pp]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright otheramtsch5" placeholder="" name="2[expdetails][src_amt_by_other]" maxlength="7"></td>
                    <td>&nbsp;</td>

                </tr>
                <tr>
                    <td>3</td>
                    <td><input type="text" class="form-control alphanumericval" placeholder="" name="3[expdetails][regn_no_of_vehicle]" maxlength="20"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="3[expdetails][hir_rate_for_vehicle]" maxlength="7"></td>
                    <td><input type="text" class="form-control  ackSch1input inputright" placeholder="" name="3[expdetails][hir_fuel_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="3[expdetails][hir_driver_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="3[expdetails][no_of_days]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="3[expdetails][total_amt_incurred]"></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch5 inputright" placeholder="" name="3[expdetails][src_amt_by_cand]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch5 inputright" placeholder="" name="3[expdetails][src_amt_by_pp]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch5 inputright" placeholder="" name="3[expdetails][src_amt_by_other]" maxlength="7"></td>
                    <td>&nbsp;</td>

                </tr>
                <tr id="rowId">
                    <td>4</td>
                    <td><input type="text" class="form-control alphanumericval" placeholder="" name="4[expdetails][regn_no_of_vehicle]" maxlength="20"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="4[expdetails][hir_rate_for_vehicle]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="4[expdetails][hir_fuel_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="4[expdetails][hir_driver_charges]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="4[expdetails][no_of_days]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="4[expdetails][total_amt_incurred]"></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch5 inputright" placeholder="" name="4[expdetails][src_amt_by_cand]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch5 inputright" placeholder="" name="4[expdetails][src_amt_by_pp]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch5 inputright" placeholder="" name="4[expdetails][src_amt_by_other]" maxlength="7"></td>
                    <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows5(this.form);">+</span></td>
                   
                </tr>
                @endif 
            </tbody>
             <tr>
                <td colspan="6">Total</td>
                <td>&nbsp;</td>
                <td><input type="text" class="form-control totalcandamtsch5 inputright" readonly></td>
                <td><input type="text" class="form-control totalppamtsch5 inputright" readonly></td>
                <td><input type="text" class="form-control totalotheramtsch5 inputright" readonly></td>
                <td>&nbsp;</td>
             </tr>
        </table>
    </div>
</div><!-- ScheduleTab close6-->
	
	<div class="ScheduleTab">
     <div class="table-responsive" id="aknolwdgeForm-sch7">
	   <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered">
              <thead class="text-center">
                <tr>
                  <th colspan="11" align="center"><h5><strong>Schedule - 6</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="11">Details of expenditure on campaign workers/agents and on candidate's booths (kiosks) outside polling stations for distribution of voter's slips</th>
                </tr>
                <tr>
                  <th rowspan="2">S. No.</th>
                  <th rowspan="2">Date </th>
                  <th rowspan="2">Venue</th>
                  <th colspan="3">Expenses on campaign workers/agents</th>
                  <th rowspan="2">Total amt. incurred/auth. in Rs.</th>
                  <th colspan="3">Sources of Expenditure</th>
                  <th rowspan="2">Action</th>
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
                <tbody class="addedRows6">
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
                    <td>&nbsp;</td>
                </tr>
                @if(!empty($getSch6))
                	@foreach($getSch6 as $getExp6)
                	@php $i = 1; @endphp
                <tr>
                    <td>@php echo $i++ @endphp
                    	<input type="hidden" name="@php echo $getExp6->id ; @endphp['expdetails'][id]" value="@php echo $getExp6->id ; @endphp" />
                    </td>
                    <td><input type="date" class="form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][venu_date]" value="@php echo !empty($getExp6->venu_date)? $getExp6->venu_date:""; @endphp" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                    <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][venu_details]" value="@php echo !empty($getExp6->venu_details)? $getExp6->venu_details:""; @endphp"></td>
                    <td>@php echo $getExp6->expense_nature @endphp
                    	<input type="hidden" name="@php echo $getExp6->id ; @endphp['expdetails'][expense_nature]" value="@php echo $getExp6->expense_nature @endphp">
                    </td>
                    <td><input type="text" maxlength="7" class="ackSch1input inputright form-control" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][expense_nature_rate]" value="@php echo !empty($getExp6->expense_nature_rate)? $getExp6->expense_nature_rate:""; @endphp"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" maxlength="7" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][worker_agents_count]" value="@php echo !empty($getExp6->worker_agents_count)? $getExp6->worker_agents_count:""; @endphp"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][total_amnt]" value="@php echo !empty($getExp6->total_amnt)? $getExp6->total_amnt:""; @endphp"></td>
                    <td><input type="text" class="form-control inputright ackSch1input candamtsch6" maxlength="7" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][source_amnt_by_cand]" value="@php echo !empty($getExp6->source_amnt_by_cand)? $getExp6->source_amnt_by_cand:""; @endphp"></td>
                    <td><input type="text" class="form-control inputright ppamtsch6 ackSch1input" maxlength="7" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][source_amnt_by_polparty]" value="@php echo !empty($getExp6->source_amnt_by_polparty)? $getExp6->source_amnt_by_polparty:""; @endphp"></td>
                    <td><input type="text" class="form-control inputright otheramtsch6 ackSch1input" maxlength="7" placeholder="" name="@php echo $getExp6->id ; @endphp['expdetails'][source_amnt_by_others]" value="@php echo !empty($getExp6->source_amnt_by_others)? $getExp6->source_amnt_by_others:""; @endphp"></td>
                    <td>&nbsp;</td>
                </tr>
                	@endforeach
                @else
                <tr>
                    <td>1</td>
                    <td><input type="date" class="form-control" placeholder="" name="1[expdetails][venu_date]" maxlength="100" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                    <td><input type="text" class="form-control alphanumericval" name="1[expdetails][venu_details]"  maxlength="100"></td>
                    <td>Candidate's booths (Kiosks) set up for distribution of voter's slips
                    	<input type="hidden" name="1[expdetails][expense_nature]" value="Candidate booths (Kiosks) set up for distribution of voters slips">
                    </td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="1[expdetails][expense_nature_rate]"  maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="1[expdetails][worker_agents_count]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="1[expdetails][total_amnt]"  maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch6 inputright" placeholder="" name="1[expdetails][source_amnt_by_cand]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch6 inputright" placeholder="" name="1[expdetails][source_amnt_by_polparty]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch6 inputright" placeholder="" name="1[expdetails][source_amnt_by_others]" maxlength="7"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                   <td>2</td>
                   <td><input type="date" class="form-control" placeholder="" name="2[expdetails][venu_date]" maxlength="100" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                    <td><input type="text" class="form-control alphanumericval" name="2[expdetails][venu_details]" maxlength="100"></td>
                    <td>Campaign workers honorarium/salary etc.
                    <input type="hidden" name="2[expdetails][expense_nature]" value="Campaign workers honorarium/salary etc.">
                    </td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][expense_nature_rate]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][worker_agents_count]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="2[expdetails][total_amnt]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch6 inputright" placeholder="" name="2[expdetails][source_amnt_by_cand]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch6 inputright" placeholder="" name="2[expdetails][source_amnt_by_polparty]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch6 inputright" placeholder="" name="2[expdetails][source_amnt_by_others]" maxlength="7"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><input type="date" class="form-control" placeholder="" name="3[expdetails][venu_date]" maxlength="100" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                    <td><input type="text" class="form-control alphanumericval" name="3[expdetails][venu_details]" maxlength="100"></td>
                    <td>Boarding
                    	<input type="hidden" name="3[expdetails][expense_nature]" value="Boarding">
                    </td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="3[expdetails][expense_nature_rate]"  maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="3[expdetails][worker_agents_count]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="3[expdetails][total_amnt]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright candamtsch6" placeholder="" name="3[expdetails][source_amnt_by_cand]"  maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright ppamtsch6" placeholder="" name="3[expdetails][source_amnt_by_polparty]"  maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright otheramtsch6" placeholder="" name="3[expdetails][source_amnt_by_others]" maxlength="7"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td><input type="date" class="form-control" placeholder="" name="4[expdetails][venu_date]" maxlength="100" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                    <td><input type="text" class="form-control alphanumericval" name="4[expdetails][venu_details]" maxlength="100"></td>
                    <td>Lodging
                    	<input type="hidden" name="4[expdetails][expense_nature]" value="Lodging">
                    </td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="4[expdetails][expense_nature_rate]" maxlength="7"></td>
                    <td><input type="text" class="form-control  ackSch1input inputright" placeholder="" name="4[expdetails][worker_agents_count]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="4[expdetails][total_amnt]" maxlength="7"></td>
                    <td><input type="text" class="form-control  ackSch1input inputright candamtsch6" placeholder="" name="4[expdetails][source_amnt_by_cand]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright ppamtsch6" placeholder="" name="4[expdetails][source_amnt_by_polparty]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright otheramtsch6" placeholder="" name="4[expdetails][source_amnt_by_others]" maxlength="7"></td>
                    <td>&nbsp;</td>
                </tr>
                 <tr id="rowId">
                    <td>5</td>
                    <td><input type="date" class="form-control" placeholder="" name="5[expdetails][venu_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                    <td><input type="text" class="form-control alphanumericval" name="5[expdetails][venu_details]" maxlength="100"></td>
                    <td>Others
                    	<input type="hidden" name="5[expdetails][expense_nature]" value="Others"/>
                    </td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="5[expdetails][expense_nature_rate]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="5[expdetails][worker_agents_count]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="5[expdetails][total_amnt]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input candamtsch6 inputright" placeholder="" name="5[expdetails][source_amnt_by_cand]"  maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input ppamtsch6 inputright" placeholder="" name="5[expdetails][source_amnt_by_polparty]" maxlength="7"></td>
                    <td><input type="text" class="form-control ackSch1input otheramtsch6 inputright" placeholder="" name="5[expdetails][source_amnt_by_others]" maxlength="7"></td>
                    <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows6(this.form);">+</span></td>
                    
                </tr>
                @endif
            </tbody>
            <tr>
            <td colspan="5">Total</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="text" class="form-control totalcandamtsch6 inputright" readonly /></td>
            <td><input type="text" class="form-control totalppamtsch6 inputright" readonly/></td>
            <td><input type="text" class="form-control totalotheramtsch6 inputright" readonly/></td>
            <td>&nbsp;</td>

            </tr>
        </table>
    </div>
</div><!-- ScheduleTab close7-->
	
	<div class="ScheduleTab">
      <div class="table-responsive">
	   <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered" id="aknolwdgeForm-sch8">
              <thead class="text-center">
                <tr>
                  <th colspan="6" align="center"><h5><strong>Schedule - 7</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="6">Details of Amount of own fund used for the election campaign</th>
                </tr>
                <tr>
                  <th>S. No.</th>
                  <th>Date</th>
                  <th>Cash/DD/Cheque no. etc. with details of drawee bank</th>
                  <th>Total Amount in Rs.</th>
                  <th>Remarks</th>
                  <th>Action</th>
                </tr>
              </thead>
                <tbody class="addedRows7">
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>&nbsp;</td>
                </tr>
                @if(!empty($getSch7))
                	@php $i = 1; @endphp
                	@foreach($getSch7 as $getExp7)
               <tr>
                <td>@php echo $i++ @endphp
                <input type="hidden" name="@php echo $getExp7->id @endphp['expdetails'][id]" value="@php echo $getExp7->id @endphp" />
                </td>
                <td><input type="date" class="form-control" placeholder="" name="@php echo $getExp7->id @endphp['expdetails'][submit_date]" value="@php echo !empty($getExp7->submit_date)? $getExp7->submit_date:""; @endphp" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                <td>
                	<select name="@php echo $getExp7->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp7->id @endphp">
            		<option>Select Payment Type</option>
            		<option value="cash" @if($getExp7->payment_type == "cash") selected="selected" @endif>Cash</option>
            		<option value="dd" @if($getExp7->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
            		<option value="cheque" @if($getExp7->payment_type == "cheque") selected="selected" @endif>Cheque</option>
                	</select>
                </td>
                <td><input type="text" class="form-control ackSch1input candamtsch7 inputright" maxlength="7" placeholder="" name="@php echo $getExp7->id @endphp['expdetails'][amount]" value="@php echo $getExp7->amount; @endphp"></td>
                <td><input type="text" class="form-control alphanumericval" maxlength="100" placeholder="" name="@php echo $getExp7->id @endphp['expdetails'][remarks]" value="@php echo $getExp7->remarks; @endphp"></td>
                <td>&nbsp;</td>
            </tr>
        	@endforeach
        @else
        <tr>
            <td>1</td>
            <td><input type="date" class="form-control" placeholder="" name="1[expdetails][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="1[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype1">
            		<option>Select Payment Type</option>
            		<option value="cash">Cash</option>
            		<option value="dd">Demand Draft</option>
            		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td><input type="text" class="form-control ackSch1input candamtsch7 inputright" placeholder="" name="1[expdetails][amount]" maxlength="7"></td>
            <td><input type="text" class="form-control alphanumericval" placeholder="" name="1[expdetails][remarks]" maxlength="100"></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>2</td>
            <td><input type="date" class="form-control" placeholder="" name="2[expdetails][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="2[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype2">
            		<option>Select Payment Type</option>
            		<option value="cash">Cash</option>
            		<option value="dd">Demand Draft</option>
            		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td><input type="text" class="form-control ackSch1input candamtsch7 inputright" placeholder="" name="2[expdetails][amount]" maxlength="7"></td>
            <td><input type="text" class="form-control alphanumericval" placeholder="" name="2[expdetails][remarks]" maxlength="100"></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>3</td>
            <td><input type="date" class="form-control" placeholder="" name="3[expdetails][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="3[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype3">
            		<option>Select Payment Type</option>
            		<option value="cash">Cash</option>
            		<option value="dd">Demand Draft</option>
            		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td><input type="text" class="form-control ackSch1input candamtsch7 inputright" placeholder="" name="3[expdetails][amount]" maxlength="7"></td>
            <td><input type="text" class="form-control alphanumericval" placeholder="" name="3[expdetails][remarks]" maxlength="100"></td>
            <td>&nbsp;</td>
        </tr>
        <tr id="rowId">
            <td>4</td>
            <td><input type="date" class="form-control" placeholder="" name="4[expdetails][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="4[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype4">
            		<option>Select Payment Type</option>
            		<option value="cash">Cash</option>
            		<option value="dd">Demand Draft</option>
            		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td><input type="text" class="form-control ackSch1input candamtsch7 inputright" placeholder="" name="4[expdetails][amount]" maxlength="7"></td>
            <td><input type="text" class="form-control alphanumericval" placeholder="" name="4[expdetails][remarks]" maxlength="100"></td>
            <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows7(this.form);">+</span></td>
                </tr>
                @endif  
            </tbody>
            <tr>
            <td colspan="2">Total</td>
            <td>&nbsp;</td>
            <td><input type="text" class="form-control totalamtsch7 inputright" readonly="" /></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
        </table>
    </div>
</div><!-- ScheduleTab close8-->
	
	<div class="ScheduleTab">
      <div class="table-responsive">
	   <h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered" id="aknolwdgeForm-sch9">
              <thead class="text-center">
                <tr>
                  <th colspan="7" align="center"><h5><strong>Schedule - 8</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="7">Details of Lump sum amount received from the party (ies) in cash or cheque or DD or by Account Transfer</th>
                </tr>
                <tr>
                  <th>S. No.</th>
                  <th>Name of the Political Party</th>
                  <th>Date</th>
                  <th>Cash/DD/Cheque no etc. with details of drawee bank</th>
                  <th>Total Amount in Rs.</th>
                  <th>Remarks, If Any</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody class="addedRows8">
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                    <td>&nbsp;</td>
                </tr>
                @if(!empty($getSch8))
                	@php $i = 1; @endphp
                	@foreach($getSch8 as $getExp8)
              <tr>
                <td>@php echo $i++ @endphp
                	<input type="hidden" name="@php echo $getExp8->id @endphp['expdetails'][id]" value="@php echo $getExp8->id @endphp" />
                </td>
                <td><input type="text" maxlength="50" class="form-control" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][party_id]" value="@php echo !empty($getExp8->party_id)? $getExp8->party_id:""; @endphp"></td>
                <td><input type="date" class="form-control" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][submit_date]" value="@php echo !empty($getExp8->submit_date)? $getExp8->submit_date:""; @endphp" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                <td>
                	<select name="@php echo $getExp8->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp8->id @endphp">
            		<option>Select Payment Type</option>
            		<option value="cash" @if($getExp8->payment_type == "cash") selected="selected" @endif>Cash</option>
            		<option value="dd" @if($getExp8->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
            		<option value="cheque" @if($getExp8->payment_type == "cheque") selected="selected" @endif>Cheque</option>
                	</select>
                </td>
                <td><input type="text" class="form-control ackSch1input inputright candamtsch8" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][amount]" value="@php echo !empty($getExp8->amount)? $getExp8->amount:""; @endphp"></td>
                <td><input type="text" class="form-control alphanumericval" maxlength="100" placeholder="" name="@php echo $getExp8->id @endphp['expdetails'][remarks]" value="@php echo !empty($getExp8->remarks)? $getExp8->remarks:""; @endphp"></td>
                <td>&nbsp;</td>
            </tr>
        	@endforeach
        @else
        <tr >
            <td>1</td>
            <td><input type="text" class="form-control" maxlength="100" placeholder="" name="1[expdetails][party_id]" value="@php echo !empty($getExp8->party_id)? $getExp8->party_id:""; @endphp"></td>
            <td><input type="date" class="form-control" placeholder="" name="1[expdetails][submit_date]" value="@php echo !empty($getExp8->submit_date)? $getExp8->submit_date:""; @endphp" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="1[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype1">
        		<option>Select Payment Type</option>
        		<option value="cash">Cash</option>
        		<option value="dd">Demand Draft</option>
        		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td><input type="text" class="ackSch1input form-control inputright candamtsch8" placeholder="" name="1[expdetails][amount]"></td>
            <td><input type="text" class="form-control alphanumericval" placeholder="" name="1[expdetails][remarks]"></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>2</td>
			<td><input type="text" maxlength="100" class="form-control" placeholder="" name="2[expdetails][party_id]"></td>
			<td><input type="date" class="form-control" placeholder="" name="2[expdetails][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="2[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype2">
        		<option>Select Payment Type</option>
        		<option value="cash">Cash</option>
        		<option value="dd">Demand Draft</option>
        		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td><input type="text" class="ackSch1input form-control inputright candamtsch8" maxlength="7" placeholder="" name="2[expdetails][amount]"></td>
            <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="2[expdetails][remarks]"></td>
            <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><input type="text" maxlength="100" class="form-control" placeholder="" name="3[expdetails][party_id]"></td>
                    <td><input type="date" class="form-control" placeholder="" name="3[expdetails][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                    <td>
                    	<select name="3[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype3">
                		<option>Select Payment Type</option>
                		<option value="cash">Cash</option>
                		<option value="dd">Demand Draft</option>
                		<option value="cheque">Cheque</option>
                    	</select>
                    </td>
                    <td><input type="text" maxlength="7" class="ackSch1input form-control inputright candamtsch8" placeholder="" name="3[expdetails][amount]"></td>
                    <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="3[expdetails][remarks]"></td>
                    <td>&nbsp;</td>
                </tr>
                <tr id="rowId">
                    <td>4</td>
                    <td><input type="text" maxlength="100" class="form-control" placeholder="" name="4[expdetails][party_id]"></td>
                    <td><input type="date"  class="form-control" placeholder="" name="4[expdetails][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"/></td>
                    <td>
                    	<select name="4[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype4">
                		<option>Select Payment Type</option>
                		<option value="cash">Cash</option>
                		<option value="dd">Demand Draft</option>
                		<option value="cheque">Cheque</option>
                    	</select>
                    </td>
                    <td><input type="text" maxlength="7" class="ackSch1input inputright form-control candamtsch8" placeholder="" name="4[expdetails][amount]"></td>
                    <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="4[expdetails][remarks]"></td>
                    <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows8(this.form);">+</span></td>
              
                </tr>
                @endif
            </tbody>
             <tr>
                <td>&nbsp;</td>
                <td colspan="2">Total</td>
                <td>&nbsp;</td>
                <td><input type="text" class="form-control totalamtsch8 inputright" readonly /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
             </tr>
        </table>
    </div>
</div><!-- ScheduleTab close9-->
	
	<div class="ScheduleTab">
	 <div class="table-responsive">
		<h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
            <table class="table table-bordered" id="aknolwdgeForm-sch10">
              <thead class="text-center">
                <tr>
                  <th colspan="9" align="center"><h5><strong>Schedule - 9</strong></h5></th>
                </tr>
                <tr>
                  <th colspan="9">Details of  Lump sum amount received from any person/company/firm/associations/body of persons etc. as loan, gift or donation etc.</th>
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
                  <th>Action</th>
                </tr>
              </thead>
                <tbody class="addedRows9">
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                    <td>6</td>
                    <td>7</td>
                    <td>8</td>
                    <td>&nbsp;</td>
                </tr>
                @if(!empty($getSch9))
                @php $i = 1; @endphp
                	@foreach($getSch9 as $getExp9)
            <tr>
                <td>@php echo $i++ @endphp
                	<input type="hidden" value="@php echo $getExp9->id; @endphp" name="@php echo $getExp9->id; @endphp['expdetails'][id]" /></td>
                <td><input type="text" maxlength="50" class="form-control alphaval" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][name]" value="@php echo !empty($getExp9->name)? $getExp9->name:""; @endphp"></td>
                <td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][address]" value="@php echo !empty($getExp9->address)? $getExp9->address:""; @endphp"></td>
                <td><input type="date" class="form-control" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][submit_date]" value="@php echo !empty($getExp9->submit_date)? $getExp9->submit_date:""; @endphp" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
                <td>
                	<select name="@php echo $getExp9->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp9->id @endphp">
            		<option>Select Payment Type</option>
            		<option value="cash" @if($getExp9->payment_type == "cash") selected="selected" @endif>Cash</option>
            		<option value="dd" @if($getExp9->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
            		<option value="cheque" @if($getExp9->payment_type == "cheque") selected="selected" @endif>Cheque</option>
                	</select>
                </td>
                <td>
                	<select name="@php echo $getExp9->id; @endphp['expdetails'][amount_details]" class="form-control paymenttype">
            		<option>Select Amount Detail</option>
            		<option value="loan" value="loan" @if($getExp9->amount_details == "loan") selected="selected" @endif>Loan</option>
            		<option value="gift" value="gift" @if($getExp9->amount_details == "gift") selected="selected" @endif>Gift</option>
            		<option value="donation" value="donation" @if($getExp9->amount_details == "donation") selected="selected" @endif>Donation</option>
                	</select>
                </td>
                <td><input type="text" class="form-control ackSch1input candamtsch9 inputright" maxlength="7" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][amount]" value="@php echo $getExp9->amount; @endphp"></td>
                <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="@php echo $getExp9->id; @endphp['expdetails'][remarks]"  value="@php echo $getExp9->remarks; @endphp"></td>
                <td>&nbsp;</td>
            </tr>
        	@endforeach
        @else
        <tr>
            <td>1</td>
            <td><input type="text" maxlength="60" class="form-control alphaval" placeholder="" name="1['expdetails'][name]"></td>
            <td><input type="text"  maxlength="100" class="form-control alphanumericval" placeholder="" name="1['expdetails'][address]"></td>
            <td><input type="date" class="form-control" placeholder="" name="1['expdetails'][submit_date]" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="1['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype1">
        		<option>Select Payment Type</option>
        		<option value="cash">Cash</option>
        		<option value="dd">Demand Draft</option>
        		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td>
            	<select name="1['expdetails'][amount_details]" class="form-control paymenttype">
        		<option>Select Amount Detail</option>
        		<option value="loan">Loan</option>
        		<option value="gift">Gift</option>
        		<option value="donation">Donation</option>
            	</select>
            </td>
            <td><input type="text" maxlength="7" class="ackSch1input form-control inputright candamtsch9" placeholder="" name="1['expdetails'][amount]" ></td>
            <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="1['expdetails'][remarks]"></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td>2</td>
            <td><input type="text" maxlength="50" class="form-control alphaval" placeholder="" name="2['expdetails'][name]" ></td>
            <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="2['expdetails'][address]"></td>
            <td><input type="date" class="form-control" placeholder="" name="2['expdetails'][submit_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="2['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype2">
        		<option>Select Payment Type</option>
        		<option value="cash">Cash</option>
        		<option value="dd">Demand Draft</option>
        		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td>
            	<select name="2['expdetails'][amount_details]" class="form-control paymenttype">
        		<option>Select Amount Detail</option>
        		<option value="loan">Loan</option>
        		<option value="gift">Gift</option>
        		<option value="donation">Donation</option>
            	</select>
            </td>
            <td><input type="text" maxlength="7" class="ackSch1input form-control inputright candamtsch9" placeholder="" name="2['expdetails'][amount]" ></td>
            <td><input type="text"  maxlength="100" class="form-control alphanumericval" placeholder="" name="2['expdetails'][remarks]"></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td>3</td>
            <td><input type="text" maxlength="50" class="form-control alphaval" placeholder="" name="3['expdetails'][name]"></td>
            <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="3['expdetails'][address]"></td>
            <td><input type="date" class="form-control" placeholder="" name="3['expdetails'][submit_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="3['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype3">
        		<option>Select Payment Type</option>
        		<option value="cash">Cash</option>
        		<option value="dd">Demand Draft</option>
        		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td>
            	<select name="3['expdetails'][amount_details]" class="form-control paymenttype">
        		<option>Select Amount Detail</option>
        		<option value="loan">Loan</option>
        		<option value="gift">Gift</option>
        		<option value="donation">Donation</option>
            	</select>
            </td>
            <td><input type="text" maxlength="7" class="ackSch1input form-control inputright candamtsch9" placeholder="" name="3['expdetails'][amount]" ></td>
            <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="3['expdetails'][remarks]"></td>
            <td>&nbsp;</td>
        </tr>
         <tr id="rowId">
            <td>4</td>
            <td><input type="text" maxlength="50" class="form-control alphaval" placeholder="" name="4['expdetails'][name]"></td>
            <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="4['expdetails'][address]"></td>
            <td><input type="date" class="form-control" placeholder="" name="4['expdetails'][submit_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
            <td>
            	<select name="4['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype4">
        		<option>Select Payment Type</option>
        		<option value="cash">Cash</option>
        		<option value="dd">Demand Draft</option>
        		<option value="cheque">Cheque</option>
            	</select>
            </td>
            <td>
            	<select name="4['expdetails'][amount_details]" class="form-control paymenttype">
        		<option>Select Amount Detail</option>
        		<option value="loan">Loan</option>
        		<option value="gift">Gift</option>
        		<option value="donation">Donation</option>
            	</select>
            </td>
            <td><input type="text" maxlength="7" class="ackSch1input form-control inputright candamtsch9" placeholder="" name="4['expdetails'][amount]" ></td>
            <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="4['expdetails'][remarks]"></td>
            <td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows9(this.form);">+</span></td>
                </tr>
                @endif
            </tbody>
            <tr>
            <td>&nbsp;</td>
            <td colspan="2">Total</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="text" class="form-control totalamtsch9 inputright" readonly /></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
        </table>
    </div>
</div><!-- ScheduleTab close10-->
	
	<div class="ScheduleTab">
	 <div class="table-responsive">
	 	<h6 class="fs-title text-center pb-2"><strong>Details of Elections Funds and Expenditure of Candidate</strong></h6>
			<table class="table table-bordered" id="aknolwdgeForm-sch11">			 
			  <thead class="text-center">
			  <tr>
                  <th colspan="9" align="center"><h5><strong>Schedule - 10</strong></h5></th>
                </tr>
				<tr>
				  <th colspan="9" align="center" class="text-center">Details of expenditure incurred on publishing criminal antecedents, if any in newspaper and TV Channel </th>
				</tr>
				<tr>
				  <th align="center"  class="text-center">Sl. No.</th>						  
				  <th align="center"  class="text-center" colspan="3">Newspaper</th>		  
				  <th align="center"  class="text-center" colspan="3">Television</th>	
				  <th>Mode of payment (electronic/cheque/DD/Cash) (PI. specify)</th>
				  <th rowspan="2">Action</th>			  
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
			  <tbody class="addedRows10">
				<tr>
					<td>1</td>															
					<td>2</td>															
					<td>3</td>															
					<td>4</td>															
					<td>5</td>															
					<td>6</td>															
					<td>7</td>															
					<td>8</td>	
					<td>&nbsp;</td>														
				</tr>
				@if(!empty($getSch10))
				@php $i = 1; @endphp
                	@foreach($getSch10 as $getExp10)
            <tr>
				<td>@php echo $i++; @endphp</td>
				<td><input type="text" class="form-control alphanumericval" maxlength="50" placeholder="" name="@php echo $getExp10->id ; @endphp['expdetails'][newspaper_name]" value="@php echo $getExp10->newspaper_name @endphp"></td>
				<td><input type="date" class="form-control" placeholder="" name="@php echo $getExp10->id ; @endphp['expdetails'][news_publishing_date]" value="@php echo $getExp10->news_publishing_date @endphp"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
				<td><input type="text" class="form-control ackSch1input inputright newsExpSch10"  maxlength="7" placeholder="" name="@php echo $getExp10->id ; @endphp['expdetails'][expense_on_news]" value="@php echo $getExp10->expense_on_news @endphp"></td>
				<td><input type="text" class="form-control inputright" placeholder="" maxlength="50" name="@php echo $getExp10->id ; @endphp['expdetails'][channel_name]" value="@php echo $getExp10->channel_name @endphp"></td>
				<td><input type="datetime-local" class="form-control" placeholder="" name="@php echo $getExp10->id ; @endphp['expdetails'][telecost_dateTime]" value="@php echo $getExp10->telecost_dateTime @endphp"></td>
				<td><input type="text" class="form-control ackSch1input inputright channelExpSch10"  maxlength="7" placeholder="" name="@php echo $getExp10->id ; @endphp['expdetails'][expense_on_channel]" value="@php echo $getExp10->expense_on_channel @endphp"></td>
				<td>
					<select name="@php echo $getExp10->id @endphp['expdetails'][payment_type]" class="form-control paymenttype" id="paymenttype@php echo $getExp10->id @endphp">
					<option>Select Payment Type</option>
					<option value="cash" @if($getExp10->payment_type == "cash") selected="selected" @endif>Cash</option>
					<option value="dd" @if($getExp10->payment_type == "dd") selected="selected" @endif>Demand Draft</option>
					<option value="cheque" @if($getExp10->payment_type == "cheque") selected="selected" @endif>Cheque</option>
					</select>
				</td>
				<td>&nbsp;</td>													
			</tr>
           @endforeach
                @else					  
				<tr>
					<td>1</td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="1[expdetails][newspaper_name]"></td>
					<td><input type="date" class="form-control" placeholder="" name="1[expdetails][news_publishing_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
					<td><input type="text" maxlength="7" class="ackSch1input form-control inputright newsExpSch10" placeholder="" name="1[expdetails][expense_on_news]"></td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="1[expdetails][channel_name]"></td>
					<td><input type="datetime-local" class="form-control" placeholder="" name="1[expdetails][telecost_dateTime]"></td>
					<td><input type="text" class="ackSch1input form-control inputright channelExpSch10" maxlength="7" placeholder="" name="1[expdetails][expense_on_channel]"></td>
					<td>
						<select name="1[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype1">
                		<option value="">Select Payment Type</option>
                		<option value="cash">Cash</option>
                		<option value="dd">Demand Draft</option>
                		<option value="cheque">Cheque</option>
                    	</select>
					</td>
					<td>&nbsp;</td>													
				</tr>
				<tr>
					<td>2</td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="2[expdetails][newspaper_name]"></td>
					<td><input type="date" class="form-control" placeholder="" name="2[expdetails][news_publishing_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
					<td><input type="text" class="ackSch1input form-control inputright newsExpSch10" maxlength="7" placeholder="" name="2[expdetails][expense_on_news]"></td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="2[expdetails][channel_name]"></td>
					<td><input type="datetime-local" class="form-control" placeholder="" name="2[expdetails][telecost_dateTime]"></td>
					<td><input type="text" class="ackSch1input form-control inputright channelExpSch10" maxlength="7" placeholder="" name="2[expdetails][expense_on_channel]"></td>
					<td>
						<select name="2[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype2">
                		<option value="">Select Payment Type</option>
                		<option value="cash">Cash</option>
                		<option value="dd">Demand Draft</option>
                		<option value="cheque">Cheque</option>
                    	</select>
					</td>	
					<td>&nbsp;</td>						
				</tr>
				<tr>
					<td>3</td>
					<td><input type="text" maxlength="60" class="form-control alphanumericval" placeholder="" name="3[expdetails][newspaper_name]"></td>
					<td><input type="date" class="form-control" placeholder="" name="3[expdetails][news_publishing_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
					<td><input type="text" maxlength="7" class="ackSch1input form-control inputright newsExpSch10" placeholder="" name="3[expdetails][expense_on_news]"></td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="3[expdetails][channel_name]"></td>
					<td><input type="datetime-local" class="form-control" placeholder="" name="3[expdetails][telecost_dateTime]"></td>
					<td><input type="text" maxlength="7" class="ackSch1input form-control inputright channelExpSch10" placeholder="" name="3[expdetails][expense_on_channel]"></td>
					<td>
						<select name="3[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype3">
                    		<option value="">Select Payment Type</option>
                    		<option value="cash">Cash</option>
                    		<option value="dd">Demand Draft</option>
                    		<option value="cheque">Cheque</option>
                    	</select>
					</td>
					<td>&nbsp;</td>								
				</tr>
				<tr>
					<td>4</td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="4[expdetails][newspaper_name]"></td>
					<td><input type="date" class="form-control" placeholder="" name="4[expdetails][news_publishing_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
					<td><input type="text" class="ackSch1input form-control inputright newsExpSch10" maxlength="7" placeholder="" name="4[expdetails][expense_on_news]"></td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="4[expdetails][channel_name]"></td>
					<td><input type="datetime-local"  class="form-control" placeholder="" name="4[expdetails][telecost_dateTime]"></td>
					<td><input type="text" maxlength="7" class="ackSch1input form-control inputright channelExpSch10" placeholder="" name="4[expdetails][expense_on_channel]"></td>
					<td>
						<select name="4[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype4">
                    		<option value="">Select Payment Type</option>
                    		<option value="cash">Cash</option>
                    		<option value="dd">Demand Draft</option>
                    		<option value="cheque">Cheque</option>
                    	</select>
					</td>
					<td>&nbsp;</td>							
				</tr>
				<tr id="rowId">
					<td>5</td>
					<td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="5[expdetails][newspaper_name]"></td>
					<td><input type="date"  class="form-control" placeholder="" name="5[expdetails][news_publishing_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td>
					<td><input type="text" class="ackSch1input form-control inputright newsExpSch10" maxlength="7" placeholder="" name="5[expdetails][expense_on_news]"></td>
					<td><input type="text" maxlength="60" class="form-control alphanumericval" placeholder="" name="5[expdetails][channel_name]"></td>
					<td><input type="datetime-local" class="form-control" placeholder="" name="5[expdetails][telecost_dateTime]"></td>
					<td><input type="text" maxlength="7" class="ackSch1input form-control inputright channelExpSch10" placeholder="" name="5[expdetails][expense_on_channel]"></td>
					<td>
						<select name="5[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype5">
                    		<option value="">Select Payment Type</option>
                    		<option value="cash">Cash</option>
                    		<option value="dd">Demand Draft</option>
                    		<option value="cheque">Cheque</option>
                    	</select>
					</td>
					<td><span class="btn-circle_1 btn-info" style="cursor:pointer;" onclick="addMoreRows10(this.form);">+</span></td>
						
				</tr>
				@endif										
			  </tbody>
			  <tfoot>							
				<tr>								
					<td colspan="3"><strong>Grand Total</strong></td>
					<td><input type="text" name="" class="totalNewsExpSch10 form-control inputright"></td>
					<td colspan="2"></td>
					<td><input type="text" name="" class="totalChannelExpSch10 inputright form-control"></td>								
					<td></td>
					<td>&nbsp;</td>								
				</tr>
			  </tfoot>
			</table>						
		  </div>
		  <div class="clearfix"></div>					  
	</div><!-- ScheduleTab close11-->
	  
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

</form><!-- Form Close -->	
</div><!-- Tab 2 -->	

</div>
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
    //if (n == 1 && !validateForm()) return true; 
	  x[currentTab].style.display = "none"; 
	  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {


//alert('test');
/////////// form submitted //////////////////
	var data = jQuery("#annuxureForm").serialize();
	$.ajax({
	    data: data,
	    type: "post",
	    dataType: "json",
	    url: "{{url('/candidate/SaveAnnuxureData')}}",
	    success: function (response) {
	    	
	    }
	});
	//After all schedule completion
	window.location.replace(APP_URL + "/candidate/printEcrpStatusReport");
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
			
	    }
    });

  // if you have reached the end of the form...
if (Schedule_currentTab >= ScheduleTab.length) {

$("#openTab")[0].click();


$("#ScheduleTab_nextBtn").trigger("click");
// return false;
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

/*function validateForm() {
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
}*/
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
      amt1 = parseFloat($row.find('.amt1').val()),
      amt2 = parseFloat($row.find('.amt2').val()),
      amt3 = parseFloat($row.find('.amt3').val()),

      subTotal = amt1 + amt2 + amt3;
    $row.find('.subtotal').val(isNaN(subTotal) ? 0 : subTotal);
    totalIt()
});
/*$(document).on("keyup", ".priceofmedia, .commissionofmedia", function() {
    var $row = $(this).closest("tr"),
      price4a = parseFloat($row.find('.priceofmedia').val());
      commission4a = parseFloat($row.find('.commissionofmedia').val());

      subTotal = price4a + commission4a;
      subTotal = subTotal.toFixed(2);
    $row.find('.subtotal4').val(isNaN(subTotal) ? 0 : subTotal);
});*/

///////aknolwdge form//////////
function totalIt() {
  var total = 0;
  $(".subtotal").each(function() {
    var val = this.value;
    total += val == "" || isNaN(val) ? 0 : parseFloat(val);
  });
  $("#total").val(total);


  var total1 = 0;
  $(".amt1").each(function() {
    var val = this.value;
    total1 += val == "" || isNaN(val) ? 0 : parseFloat(val);
  });
  $("#grand_total_candidate_agent").val(total1);

  var total2 = 0;
  $(".amt2").each(function() {
    var val = this.value;
    total2 += val == "" || isNaN(val) ? 0 : parseFloat(val);
  });
  $("#grand_total_amt_incurred_by_pol_party").val(total2);

 var total3 = 0;
  $(".amt3").each(function() {
    var val = this.value;
    total3 += val == "" || isNaN(val) ? 0 : parseFloat(val);
  });
  $("#grand_total_amt_incurred_by_other").val(total3);

  }

function totalItsc1() {
  var total = 0;
  $(".s1amt").each(function() {
    var val = this.value;
    total += val == "" || isNaN(val) ? 0 : parseFloat(val);

  });
  $(".st1").val(total.toFixed(2));
  var schtotal1 = total.toFixed(2) ;
  if(schtotal1 > 7000000){
  	jQuery('.sch1totalerror').html('value should not be greater than 70 Lacs');
  }else{
  	jQuery('.sch1totalerror').html();
  }

  var total1 = 0;
  $(".s1amt1").each(function() {
    var val = this.value;
    total1 += val == "" || isNaN(val) ? 0 : parseFloat(val);
  });
  $(".st2").val(total1.toFixed(2));

  var total2 = 0;
  $(".s1amt2").each(function() {
    var val = this.value;
    total2 += val == "" || isNaN(val) ? 0 : parseFloat(val);
  });
  $(".st3").val(total2.toFixed(2));

 var total3 = 0;
  $(".s1amt3").each(function() {
    var val = this.value;
    total3 += val == "" || isNaN(val) ? 0 : parseFloat(val);
  });
  $(".st4").val(total3.toFixed(2));

  }

$(document).on("change", ".s1amt1, .s1amt2, .s1amt3", function() {

    var $row = $(this).closest("tr"),
      s1amt1 = $row.find('.s1amt1').val(),
      s1amt2 = $row.find('.s1amt2').val(),
      s1amt3 = $row.find('.s1amt3').val();

      if(s1amt1 == '' || s1amt1 == 0){
      	s1amt1 = 0;
      }
      if(s1amt2 == '' || s1amt2 == 0){
      	s1amt2 = 0;
      }
       if(s1amt3 == '' || s1amt3 == 0){
      	s1amt3 = 0;
      }
      var subTotal = parseFloat(s1amt1) + parseFloat(s1amt2) + parseFloat(s1amt3);
      var subtotalval = subTotal.toFixed(2);
    $row.find('.s1amt').val(isNaN(subtotalval) ? 0 : subtotalval);
    totalItsc1()

});

jQuery(document).ready(function(){

	//Schedule 1

	//Acknolegement Form Schedule 1 src_amt_total amount total
	//if(jQuery('.src_total_amt').val() != ''){
		var totalsum = 0;
	    $('.src_total_amt').each(function() {   
	        totalsum += +this.value;

	    });
	   	jQuery('.total_amt').val(totalsum.toFixed(2));    
	// }

	//Acknolegement Form Schedule 1 src_amt_incurred_cand amount total
	// if(jQuery('.src_amt_incurred_cand').val() != ''){
		var candsum = 0;
	    $('.src_amt_incurred_cand').each(function() {   
	        candsum += +this.value;
	    });
	    jQuery('.total_cand_amt').val(candsum.toFixed(2)); 
	//}

	//Acknolegement Form Schedule 1 src_amt_incurred_pp amount total
	// if(jQuery('.src_amt_incurred_pp').val() != ''){
		var ppsum = 0;
	    $('.src_amt_incurred_pp').each(function() {   
	        ppsum += +this.value;
	    });
	    jQuery('.total_pp_amt').val(ppsum.toFixed(2)); 
	// }

	//Acknolegement Form Schedule 1 src_amt_incurred_other amount total
	//if(jQuery('.src_amt_incurred_other').val() != ''){
		var othersum = 0;
	    $('.src_amt_incurred_other').each(function() {   
	        othersum += +this.value;
	    });

	    jQuery('.total_other_amt').val(othersum.toFixed(2)); 
	//}

	jQuery('.paymenttype').change(function(){
		var paymenttypeid   = jQuery(this).attr('id');
		var paymenttypeval  = jQuery(this).val();
		if(paymenttypeval == "cash"){
			jQuery("."+paymenttypeid).hide();
		}else{
			jQuery("."+paymenttypeid).show();
		}
	});


	/*------------Start Schedule 2 Total ------------*/

	//Cand Total
	if(jQuery('.amt_cand_sch2').val != ''){
		 var sum = 0;
	    $(".amt_cand_sch2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_cand_amt_sch2").val(sum.toFixed(2));
	}
	jQuery('.amt_cand_sch2').on('change',function(){
		var sum = 0;
	    $(".amt_cand_sch2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_cand_amt_sch2").val(sum.toFixed(2));
	});

	//Political Party Total
	if(jQuery('.amt_pp_sch2').val != ''){
		 var sum = 0;
	    $(".amt_pp_sch2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_pp_amt_sch2").val(sum.toFixed(2));
	}
	jQuery('.amt_pp_sch2').on('change',function(){
		var sum = 0;
	    $(".amt_pp_sch2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_pp_amt_sch2").val(sum.toFixed(2));
	});

	//Other Total
	if(jQuery('.amt_other_sch2').val != ''){
		 var sum = 0;
	    $(".amt_other_sch2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_other_amt_sch2").val(sum.toFixed(2));
	}
	jQuery('.amt_other_sch2').on('change',function(){
		var sum = 0;
	    $(".amt_other_sch2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_other_amt_sch2").val(sum.toFixed(2));
	});

	//Total Amt
	var sum_sch2 = 0;
	if(jQuery('.total_cand_amt_sch2').val() != '' || jQuery('.total_cand_amt_sch2').val() != 0){
		var totalCandSch2 = jQuery('.total_cand_amt_sch2').val() ;
		sum_sch2 +=  +totalCandSch2 ;
	}
	if(jQuery('.total_pp_amt_sch2').val() != '' || jQuery('.total_pp_amt_sch2').val() != 0){
		var totalPPSch2 = jQuery('.total_pp_amt_sch2').val() ;
		sum_sch2 +=  +totalPPSch2 ;
	}
	if(jQuery('.total_other_amt_sch2').val() != '' || jQuery('.total_other_amt_sch2').val() != 0){
		var totalOtherSch2 = jQuery('.total_other_amt_sch2').val() ;
		sum_sch2 +=  +totalOtherSch2 ;
	}
	jQuery('.total_amt_Sch2').val(sum_sch2.toFixed(2));

	/*------------End Schedule 2 Total ------------*/

	/*------------Start Schedule 3 Total ------------*/
	//Cand Total
	if(jQuery('.candamtsch3').val != ''){
		 var sum = 0;
	    $(".candamtsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".finalcandtotalsch3").val(sum.toFixed(2));
	}
	jQuery('.candamtsch3').on('change',function(){
		var sum = 0;
	    $(".candamtsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".finalcandtotalsch3").val(sum.toFixed(2));
	});

	//Political Party Total
	if(jQuery('.ppamtsch3').val != ''){
		 var sum = 0;
	    $(".ppamtsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".finalpptotalsch3").val(sum.toFixed(2));
	}
	jQuery('.ppamtsch3').on('change',function(){
		var sum = 0;
	    $(".ppamtsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".finalpptotalsch3").val(sum.toFixed(2));
	});

	//Other Total
	if(jQuery('.otheramtsch3').val != ''){
		 var sum = 0;
	    $(".otheramtsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".finalothertotalsch3").val(sum.toFixed(2));
	}
	jQuery('.otheramtsch3').on('change',function(){
		var sum = 0;
	    $(".otheramtsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".finalothertotalsch3").val(sum.toFixed(2));
	});

	//Total Amt
	//Cand Total
	if(jQuery('.totalsch3').val != ''){
		 var sum = 0;
	    $(".totalsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_amt_Sch3").val(sum.toFixed(2));
	}
	jQuery('.totalsch3').on('change',function(){
		var sum = 0;
	    $(".totalsch3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_amt_Sch3").val(sum.toFixed(2));
	});

	var sum_sch3 = 0;
	if(jQuery('.finalcandtotalsch3').val() != '' || jQuery('.finalcandtotalsch3').val() != 0){
		var totalCandSch3 = jQuery('.finalcandtotalsch3').val() ;
		sum_sch3 +=  +totalCandSch3 ;
	}
	if(jQuery('.finalpptotalsch3').val() != '' || jQuery('.finalpptotalsch3').val() != 0){
		var totalPPSch3 = jQuery('.finalpptotalsch3').val() ;
		sum_sch3 +=  +totalPPSch3 ;
	}
	if(jQuery('.finalothertotalsch3').val() != '' || jQuery('.finalothertotalsch3').val() != 0){
		var totalOtherSch3 = jQuery('.finalothertotalsch3').val() ;
		sum_sch3 +=  +totalOtherSch3 ;
	}
	jQuery('.total_amt_Sch3').val(sum_sch3);

	/*------------End Schedule 3 Total ------------*/

	/*------------Start Schedule 4 Total ------------*/

	//Price of Media Total
	if(jQuery('.priceofmediasch4').val != ''){
		 var sum = 0;
	    $(".priceofmediasch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalpriceofmediasch4").val(sum.toFixed(2));
	}
	jQuery('.priceofmediasch4').on('change',function(){
		var sum = 0;
	    $(".priceofmediasch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalpriceofmediasch4").val(sum.toFixed(2));
	});

	//Commission of Media Total
	if(jQuery('.commissionofmediasch4').val != ''){
		 var sum = 0;
	    $(".commissionofmediasch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalcommissionofmediasch4").val(sum.toFixed(2));
	}
	jQuery('.commissionofmediasch4').on('change',function(){
		var sum = 0;
	    $(".commissionofmediasch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalcommissionofmediasch4").val(sum.toFixed(2));
	});

	//Candidate Total
	if(jQuery('.candamtsch4').val != ''){
		 var sum = 0;
	    $(".candamtsch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalCandSch4").val(sum.toFixed(2));
	}
	jQuery('.candamtsch4').on('change',function(){
		var sum = 0;
	    $(".candamtsch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalCandSch4").val(sum.toFixed(2));
	});

	//Political Party Total
	if(jQuery('.ppamtsch4').val != ''){
		 var sum = 0;
	    $(".ppamtsch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalPPSch4").val(sum.toFixed(2));
	}
	jQuery('.ppamtsch4').on('change',function(){
		var sum = 0;
	    $(".ppamtsch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalPPSch4").val(sum.toFixed(2));
	});

	//Other Total
	if(jQuery('.otheramtsch4').val != ''){
		 var sum = 0;
	    $(".otheramtsch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalotheramtsch4").val(sum.toFixed(2));
	}
	jQuery('.otheramtsch4').on('change',function(){
		var sum = 0;
	    $(".otheramtsch4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalotheramtsch4").val(sum.toFixed(2));
	});

	//Total
	if(jQuery('.subtotal4').val() != ''){
		 var sum = 0;
	    $(".subtotal4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_amtsch4").val(sum.toFixed(2));
	}
	jQuery('.subtotal4').on('change',function(){
		var sum = 0;
	    $(".subtotal4").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_amtsch4").val(sum.toFixed(2));
	});

	//Total Amt
	var sum_sch4 = 0;
	if(jQuery('.totalCandSch4').val() != '' || jQuery('.totalCandSch4').val() != 0){
		var totalCandSch4 = jQuery('.totalCandSch4').val() ;
		sum_sch4 +=  +totalCandSch4 ;
	}
	if(jQuery('.totalPPSch4').val() != '' || jQuery('.totalppamtsch4').val() != 0){
		var totalPPSch4 = jQuery('.totalPPSch4').val() ;
		sum_sch4 +=  +totalPPSch4 ;
	}
	if(jQuery('.totalotheramtsch4').val() != '' || jQuery('.totalotheramtsch4').val() != 0){
		var totalOtherSch4 = jQuery('.totalotheramtsch4').val() ;
		sum_sch4 +=  +totalOtherSch4 ;
	}
	jQuery('.total_amt_Sch4').val(sum_sch4);

	/*------------End Schedule 4 Total ------------*/

	/*------------Start Schedule 4a Total ------------*/

	//Price of Media Total
	if(jQuery('.priceamt4a').val != ''){
		 var sum = 0;
	    $(".priceamt4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalpriceofmediasch4a").val(sum.toFixed(2));
	}
	jQuery('.priceamt4a').on('change',function(){
		var sum = 0;
	    $(".priceamt4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalpriceofmediasch4a").val(sum.toFixed(2));
	});

	//Commission of Media Total
	if(jQuery('.commissionamt4a').val != ''){
		 var sum = 0;
	    $(".commissionamt4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalcommissionofmediasch4a").val(sum.toFixed(2));
	}
	jQuery('.commissionamt4a').on('change',function(){
		var sum = 0;
	    $(".commissionamt4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalcommissionofmediasch4a").val(sum.toFixed(2));
	});

	//Candidate Total
	if(jQuery('.candamtsch4a').val != ''){
		 var sum = 0;
	    $(".candamtsch4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalCandSch4a").val(sum.toFixed(2));
	}
	jQuery('.candamtsch4a').on('change',function(){
		var sum = 0;
	    $(".candamtsch4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalCandSch4a").val(sum.toFixed(2));
	});

	//Political Party Total
	if(jQuery('.ppamtsch4a').val != ''){
		 var sum = 0;
	    $(".ppamtsch4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalPPSch4a").val(sum.toFixed(2));
	}
	jQuery('.ppamtsch4a').on('change',function(){
		var sum = 0;
	    $(".ppamtsch4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalPPSch4a").val(sum.toFixed(2));
	});

	//Other Total
	if(jQuery('.otheramtsch4a').val != ''){
		 var sum = 0;
	    $(".otheramtsch4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalotheramtsch4a").val(sum.toFixed(2));
	}
	jQuery('.otheramtsch4a').on('change',function(){
		var sum = 0;
	    $(".otheramtsch4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalotheramtsch4a").val(sum.toFixed(2));
	});

	//Total
	if(jQuery('.subtotal4a').val() != ''){
		 var sum = 0;
	    $(".subtotal4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_amtsch4a").val(sum.toFixed(2));
	}
	jQuery('.subtotal4a').on('change',function(){
		var sum = 0;
	    $(".subtotal4a").each(function(){
	        sum += +$(this).val();
	    });
	    $(".total_amtsch4a").val(sum.toFixed(2));
	});
	/*------------End Schedule 4a Total ------------*/

	/*------------Start Schedule 5 Total ------------*/

	//Candidate Total
	if(jQuery('.candamtsch5').val != ''){
		 var sum = 0;
	    $(".candamtsch5").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalcandamtsch5").val(sum.toFixed(2));
	}
	jQuery('.candamtsch5').on('change',function(){
		var sum = 0;
	    $(".candamtsch5").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalcandamtsch5").val(sum.toFixed(2));
	});

	//Political Party Total
	if(jQuery('.ppamtsch5').val != ''){
		 var sum = 0;
	    $(".ppamtsch5").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalppamtsch5").val(sum.toFixed(2));
	}
	jQuery('.ppamtsch5').on('change',function(){
		var sum = 0;
	    $(".ppamtsch5").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalppamtsch5").val(sum.toFixed(2));
	});

	//Other Total
	if(jQuery('.otheramtsch5').val != ''){
		 var sum = 0;
	    $(".otheramtsch5").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalotheramtsch5").val(sum.toFixed(2));
	}
	jQuery('.otheramtsch5').on('change',function(){
		var sum = 0;
	    $(".otheramtsch5").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalotheramtsch5").val(sum.toFixed(2));
	});

	//Total Amt
	var sum_sch5 = 0;
	if(jQuery('.totalCandSch4').val() != '' || jQuery('.totalcandamtsch5').val() != 0){
		var totalCandSch5 = jQuery('.totalcandamtsch5').val() ;
		sum_sch5 +=  +totalCandSch4 ;
	}
	if(jQuery('.totalppamtsch5').val() != '' || jQuery('.totalppamtsch5').val() != 0){
		var totalPPSch5 = jQuery('.totalppamtsch5').val() ;
		sum_sch5 +=  +totalPPSch5 ;
	}
	if(jQuery('.totalotheramtsch5').val() != '' || jQuery('.totalotheramtsch5').val() != 0){
		var totalOthersch5 = jQuery('.totalotheramtsch5').val() ;
		sum_sch5 +=  +totalOthersch5 ;
	}
	jQuery('.totalamtsch5').val(sum_sch5);

	/*------------End Schedule 5 Total ------------*/

	/*------------Start Schedule 6 Total ------------*/

	//Candidate Total
	if(jQuery('.candamtsch6').val != ''){
		 var sum = 0;
	    $(".candamtsch6").each(function(){
	        sum += +$(this).val();
	    });
	     $(".totalcandamtsch6").val(sum.toFixed(2));
	}
	jQuery('.candamtsch6').on('change',function(){
		var sum = 0;
	    $(".candamtsch6").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalcandamtsch6").val(sum.toFixed(2));
	});

	//Political Party Total
	if(jQuery('.ppamtsch6').val != ''){
		 var sum = 0;
	    $(".ppamtsch6").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalppamtsch6").val(sum.toFixed(2));
	}
	jQuery('.ppamtsch6').on('change',function(){
		var sum = 0;
	    $(".ppamtsch6").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalppamtsch6").val(sum.toFixed(2));
	});

	//Other Total
	if(jQuery('.otheramtsch6').val != ''){
		 var sum = 0;
	    $(".otheramtsch6").each(function(){
	        sum += +$(this).val();
	    });
	     $(".totalotheramtsch6").val(sum.toFixed(2));
	}
	jQuery('.otheramtsch6').on('change',function(){
		var sum = 0;
	    $(".otheramtsch6").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalotheramtsch6").val(sum.toFixed(2));
	});

	//Total Amt
	var sum_sch6 = 0;
	if(jQuery('.totalcandamtsch6').val() != '' || jQuery('.totalcandamtsch6').val() != 0){
		var totalCandSch6 = jQuery('.totalcandamtsch6').val() ;
		sum_sch6 +=  +totalCandSch4 ;
	}
	if(jQuery('.totalppamtsch6').val() != '' || jQuery('.totalppamtsch6').val() != 0){
		var totalPPSch6 = jQuery('.totalppamtsch6').val() ;
		sum_sch6 +=  +totalPPSch6 ;
	}
	if(jQuery('.totalotheramtsch6').val() != '' || jQuery('.totalotheramtsch6').val() != 0){
		var totalOthersch6 = jQuery('.totalotheramtsch6').val() ;
		sum_sch6 +=  +totalOthersch6 ;
	}
	jQuery('.totalamtsch6').val(sum_sch6);

	/*------------End Schedule 6 Total ------------*/


	/*-----------Start Schedule 7 ---------------*/

	// Total Amount
	if(jQuery('.candamtsch7').val() != ''){
		 var sum = 0;
	    $(".candamtsch7").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalamtsch7").val(sum.toFixed(2));
	}
	jQuery('.candamtsch7').on('change',function(){
		var sum = 0;
	    $(".candamtsch7").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalamtsch7").val(sum.toFixed(2));
	});

	/*-----------End Schedule 7 -----------------*/

	/*-----------Start Schedule 8 ---------------*/

	// Total Amount
	if(jQuery('.candamtsch8').val() != ''){
		 var sum = 0;
	    $(".candamtsch8").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalamtsch8").val(sum.toFixed(2));
	}
	jQuery('.candamtsch8').on('change',function(){
		var sum = 0;
	    $(".candamtsch8").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalamtsch8").val(sum.toFixed(2));
	});

	/*-----------End Schedule 8 -----------------*/


	/*-----------Start Schedule 9 ---------------*/

	// Total Amount
	if(jQuery('.candamtsch9').val != ''){
		 var sum = 0;
	    $(".candamtsch9").each(function(){
	        sum += +$(this).val();
	    });
	     $(".totalamtsch9").val(sum.toFixed(2));
	}
	jQuery('.candamtsch9').on('change',function(){
		var sum = 0;
	    $(".candamtsch9").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalamtsch9").val(sum.toFixed(2));
	});

	/*-----------End Schedule 9 -----------------*/

	/*-----------Start Schedule 10 ---------------*/
	// Total News Expenses
	if(jQuery('.newsExpSch10').val != ''){
		 var sum = 0;
	    $(".newsExpSch10").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalNewsExpSch10").val(sum.toFixed(2));
	}
	jQuery('.newsExpSch10').on('change',function(){
		var sum = 0;
	    $(".newsExpSch10").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalNewsExpSch10").val(sum.toFixed(2));
	});
	if(jQuery('.channelExpSch10').val != ''){
		 var sum = 0;
	    $(".channelExpSch10").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalChannelExpSch10").val(sum.toFixed(2));
	}
	jQuery('.channelExpSch10').on('change',function(){
		var sum = 0;
	    $(".channelExpSch10").each(function(){
	        sum += +$(this).val();
	    });
	    $(".totalChannelExpSch10").val(sum.toFixed(2));
	});
	/*-----------End Schedule 10 -----------------*/


	/*----------Start Annexure Form 2 -----------*/

	// Total Candidate Amount
	if(jQuery('.amt3').val != ''){
		 var sum = 0;
	    $(".amt3").each(function(){
	        sum += +$(this).val();
	    });
	    $(".grandtotalcandamt").val(sum.toFixed(2));
	}

	// Total Political Party Amount
	if(jQuery('.amt2').val != ''){
		 var sum = 0;
	    $(".amt2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".grandtotalppamt").val(sum.toFixed(2));
	}

	// Total Other Amount
	if(jQuery('.amt3').val != ''){
		 var sum = 0;
	    $(".amt2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".grandtotalotheramt").val(sum.toFixed(2));
	}

	// Total Other Amount
	if(jQuery('.subtotalann').val != ''){
		 var sum = 0;
	    $(".amt2").each(function(){
	        sum += +$(this).val();
	    });
	    $(".grandtotalamt").val(sum.toFixed(2));
	}
	
	if(jQuery('.candamtsch6').val() != '' || jQuery('.totalcandamtsch6').val() != ''){
		var sum = 0;
		var candamtsch1 = jQuery('.othercandexp').val() ;
		var totalcandamtsch6 = jQuery('.totalcandamtsch6').val();
		if(candamtsch1 == '' || candamtsch1 == 0){
			candamtsch1 = 0;
		}
		if(totalcandamtsch6 == '' || totalcandamtsch6 == 0){
			totalcandamtsch6 = 0;
		}

		sum = parseFloat(candamtsch1) + parseFloat(totalcandamtsch6);
		subTotal = sum.toFixed(2)
		jQuery('.annexcandtotalamt').val(isNaN(subTotal) ? 0.00 : subTotal);
		
	}
	if(jQuery('.otherppexp').val() != '' || jQuery('.totalppamtsch6').val() != ''){
		var sum = 0;
		var otherppexp = jQuery('.otherppexp').val() ;
		var totalppamtsch6 = jQuery('.totalppamtsch6').val();
		if(otherppexp == '' || otherppexp == 0){
			otherppexp = 0;
		}
		if(totalppamtsch6 == '' || totalppamtsch6 == 0){
			totalppamtsch6 = 0;
		}
		sum = parseFloat(otherppexp) + parseFloat(totalppamtsch6);
		subTotal = sum.toFixed(2)
		jQuery('.annexpptotalamt').val(isNaN(subTotal) ? 0.00 : subTotal);
	}
	if(jQuery('.otherexp').val() != '' || jQuery('.totalotheramtsch6').val() != ''){
		var sum = 0;
		var otherexp 		  = jQuery('.otherexp').val() ;
		var totalotheramtsch6 = jQuery('.totalotheramtsch6').val();
		if(otherexp == '' || otherexp == 0){
			otherexp = 0;
		}
		if(totalotheramtsch6 == '' || totalotheramtsch6 == 0){
			totalotheramtsch6 = 0;
		}
		sum = parseFloat(otherexp) + parseFloat(totalotheramtsch6);
		subTotal = sum.toFixed(2)
		jQuery('.annexothertotalamt').val(isNaN(subTotal) ? 0.00 : subTotal);
	}
	if(jQuery('.annexcandtotalamt').val() !='' || jQuery('.annexpptotalamt').val() != ''|| jQuery('.annexothertotalamt').val() != ''){
		var annexcandtotalamt = jQuery('.annexcandtotalamt').val();
		var annexpptotalamt   = jQuery('.annexpptotalamt').val();
		var annexothertotalamt= jQuery('.annexothertotalamt').val();
		var sum = 0 ;
		if(annexcandtotalamt == '' || annexcandtotalamt == 0){
			annexcandtotalamt = 0;
		}
		if(annexpptotalamt == '' || annexpptotalamt == 0){
			annexpptotalamt = 0;
		}
		if(annexothertotalamt == '' || annexothertotalamt == 0){
			annexothertotalamt = 0;
		}
		sum  = parseFloat(annexcandtotalamt) + parseFloat(annexpptotalamt) + parseFloat(annexothertotalamt);
		jQuery('.annextotalamt').val(sum.toFixed(2));
    }

	/*------------End Annexure Form 2 ----------------*/

	/*------------Start Annexure Form 3 ----------------*/

	// Total Candidate Amount
	if(jQuery('.annex3amt').val != ''){
		 var sum = 0;
	    $(".annex3amt").each(function(){
	        sum += +$(this).val();
	    });
	    $(".grandtotalannex3").val(sum.toFixed(2));

	}
	/*------------End Annexure Form 3 ----------------*/

});

// Total Candidate, Political Party, Other Amount Schedule 3
$(document).on("keyup", ".candamtsch3, .ppamtsch3, .otheramtsch3", function() {
  var $row      = $(this).closest("tr"),
  candamtsch3   = parseFloat($row.find('.candamtsch3').val()),
  ppamtsch3     = parseFloat($row.find('.ppamtsch3').val()),
  otheramtsch3  = parseFloat($row.find('.otheramtsch3').val()),

  subTotal = candamtsch3 + ppamtsch3 + otheramtsch3;
    //$row.find('.totalsch3').val(isNaN(subTotal) ? 0 : subTotal);
    totalItsc3()
});

jQuery('document').ready(function(){
	//called when key is pressed in textbox
    $('.ackSch1input').keypress(function (event) {
        return isNumber(this,event)
    });
});
function isNumber(el, evt) {

    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    //just one dot
    if(number.length>1 && charCode == 46){
         return false;
    }
    //get the carat position
    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if( caratPos > dotPos && dotPos>-1 && (number[1].length > 1)){
        return false;
    }
    return true;
}    

//thanks: http://javascript.nwbox.com/cursor_position/
function getSelectionStart(o) {
	if (o.createTextRange) {
		var r = document.selection.createRange().duplicate()
		r.moveEnd('character', o.value.length)
		if (r.text == '') return o.value.length
		return o.value.lastIndexOf(r.text)
	} else return o.selectionStart
}  

function totalItsc3() {
  var total = 0;
  $(".candamtsch3").each(function() {
    var val = this.value;
    total += val == "" || isNaN(val) ? 0 : parseFloat(val);

  });
 } 

 // Total Price of Media and Commission Amount Schedule 4
$(document).on("keyup", ".priceofmediasch4, .commissionofmediasch4", function() {
  var $row      = $(this).closest("tr"),
  priceofmediasch4   		= parseFloat($row.find('.priceofmediasch4').val()),
  commissionofmediasch4     = parseFloat($row.find('.commissionofmediasch4').val()),

  subTotal = priceofmediasch4 + commissionofmediasch4 ;
    $row.find('.totalamtsch4').val(isNaN(subTotal) ? 0 : subTotal);
    totalItsc4()
    
});

function totalItsc4() {
  var total = 0;
  $(".priceofmediasch4").each(function() {
    var val = this.value;
    total += val == "" || isNaN(val) ? 0 : parseFloat(val);

  });
 } 
/* -------Start Alphanumeric Validation -------*/

jQuery(document).ready(function($){
    $('.alphanumericval').keypress(function (e) {
        var regex = new RegExp("^[0-9a-zA-Z_\. ]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    });
});

jQuery(document).ready(function($){
    $('.alphaval').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z_\. ]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    });
});

 $('#upload_file').on('click', function() {
	 $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
var fd = new FormData();
var files = $('#file')[0].files[0];
 fd.append('file',files);    
    jQuery.ajax({
        url: APP_URL+"/candidate/UploadEcrpFile", // point to server-side PHP script 
		processData: false,
		contentType: false,
		data: fd,
		type: 'POST',
        success: function(res){
			
			res = res.trim();
			
			if(res=='1')
			{
				$('.UploadMsg').html("File Upload Successfully.");
				setTimeout("window.location='{{url('/candidate/annuxure')}}'",3000);

			}
			if(res=='0')
			{
				$('.UploadMsg').html("Some Error in uploading file Try again!");
			}            
        }
     });
});
 	    
/////upload affidavit
$('#upload_file_aff').on('click', function() {
	 $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
var fd = new FormData();
var files = $('#file_aff')[0].files[0];
 fd.append('file',files);    
    jQuery.ajax({
        url: APP_URL+"/candidate/UploadAffFile", // point to server-side PHP script 
		processData: false,
		contentType: false,
		data: fd,
		type: 'POST',
        success: function(res){
			
			res = res.trim();
			
			if(res=='1')
			{
				$('.UploadMsg_aff').html("File Upload Successfully.");
				setTimeout("window.location='{{url('/candidate/annuxure')}}'",3000);

			}
			if(res=='0')
			{
				$('.UploadMsg_aff').html("Some Error in uploading file Try again!");
			}
			
            
        }
     });
});
 		
/////upload acknowledgement
$('#upload_file_ack').on('click', function() {
	 $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
var fd = new FormData();
var files = $('#file_ack')[0].files[0];
 fd.append('file',files);    
    jQuery.ajax({
        url: APP_URL+"/candidate/UploadAckFile", // point to server-side PHP script 
		processData: false,
		contentType: false,
		data: fd,
		type: 'POST',
        success: function(res){
			
			res = res.trim();
			
			if(res=='1')
			{
				$('.UploadMsg_ack').html("File Upload Successfully.");
				setTimeout("window.location='{{url('/candidate/annuxure')}}'",3000);

			}
			if(res=='0')
			{
				$('.UploadMsg_ack').html("Some Error in uploading file Try again!");
			}
         }
     });
});
 		

/* -------End Alphanumeric Validation ---------*/
</script>
<script type="text/javascript">

////////schedule  - 2 /////////
var rowCount = 4;
function addMoreRows(frm) {
rowCount ++;
var recRow = '<tr id="rowCount'+rowCount+'"><td>'+rowCount+'</td><td><input type="date" class="form-control " name="'+rowCount+'[starcampaigner][meetingdate]" placeholder="" id="" min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td><td><input type="text" class="form-control alphanumericval" name="'+rowCount+'[starcampaigner][venue]" placeholder="" maxlength="100"></td><td><input type="text" class="form-control alphaval" name="'+rowCount+'[starcampaigner][name_of_start_and_party]" placeholder="" maxlength="100"></td><td><input type="text" class="form-control ackSch1input inputright amt_cand_sch2" pattern="^[0–9]$" name="'+rowCount+'[starcampaigner][src_amt_by_cand]" placeholder="" maxlength="7"></td><td><input type="text" class="form-control ackSch1input inputright amt_pp_sch2" pattern="^[0–9]$" name="'+rowCount+'[starcampaigner][src_amt_by_pp]" placeholder="" maxlength="7"></td><td><input type="text" class="form-control ackSch1input inputright amt_other_sch2" pattern="^[0–9]$" name="'+rowCount+'[starcampaigner][src_amt_by_other]" placeholder="" maxlength="7"></td><td><input type="text" class="form-control alphanumericval"  name="'+rowCount+'[starcampaigner][remarks]" placeholder="" maxlength="100"></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount+');">Delete</a></td></tr>';
jQuery('.addedRows').append(recRow);
}
///////// schedule - 2///////////////



////////schedule  - 3 /////////
var rowCount3 = 4;
function addMoreRows3(frm) {
rowCount3 ++;
var recRow = '<tr id="rowCount'+rowCount3+'"><td>'+rowCount3+'</td><td><input type="text" class="form-control alphaval" name="'+rowCount3+'[campaigmaterial][nature_of_expense]" placeholder="" value=""></td><td><input type="text" class="form-control ackSch1input inputright totalsch3" name="'+rowCount3+'[campaigmaterial][total_amt]" placeholder="" value=""></td><td><input type="text" maxlength="7" class="form-control ackSch1input candamtsch3 inputright" name="'+rowCount3+'[campaigmaterial][src_amt_by_cand]" placeholder="" value=""></td><td><input type="text" maxlength="7" class="form-control ackSch1input ppamtsch3 inputright" name="'+rowCount3+'[campaigmaterial][src_amt_by_pp]" placeholder="" value=""></td><td><input type="text" maxlength="7" class="form-control ackSch1input otheramtsch3 inputright" name="'+rowCount3+'[campaigmaterial][src_amt_by_other]" placeholder="" value=""></td><td><input type="text" maxlength="" class="form-control alphanumericval" name="'+rowCount3+'[campaigmaterial][remarks]" placeholder="" value=""></td>><td><a href="javascript:void(0);" onclick="removeRow('+rowCount3+');">Delete</a></td></tr>';
jQuery('.addedRows3').append(recRow);
}
///////// schedule - 3 ///////////////



////////schedule  - 4 /////////
var rowCount4 = 4;
function addMoreRows4(frm) {
rowCount4 ++;
var recRow = '<tr id="rowCount'+rowCount4+'"><td>'+rowCount4+'</td><td><select class="form-control" name="'+rowCount4+'[expdetails][nature_of_medium]"><option value="">Select Media</option><option value="Electronic Media">Electronic Media</option><option value="Print Media">Print Media</option></select></td><td><input type="text" class="form-control alphaval" name="'+rowCount4+'[expdetails][name_of_media]" placeholder="" maxlength="100"></td><td><input type="text" class="form-control alphanumericval" name="'+rowCount4+'[expdetails][address_of_media]" placeholder="" maxlength="100"></td><td><input type="text" class="form-control ackSch1input priceofmedia priceofmediasch4 inputright" name="'+rowCount4+'[expdetails][price_of_the_media]"placeholder=""  maxlength="7"></td><td><input type="text" class="form-control alphanumericval" name="'+rowCount4+'[expdetails][name_address_of_agency]" placeholder=""  maxlength="100"></td><td><input type="text" class="form-control ackSch1input inputright commissionofmedia commissionofmediasch4" name="'+rowCount4+'[expdetails][commission_of_agency]" maxlength="7"></td> <td><input type="text" class="form-control inputright ackSch1input subtotal4 total_amtsch4" name="'+rowCount4+'[expdetails][total_amt]" placeholder=""></td><td><input type="text" class="form-control ackSch1input inputright candamtsch4" name="'+rowCount4+'[expdetails][src_amt_by_cand]" placeholder="" maxlength="7"></td><td><input type="text" class="form-control ackSch1input inputright ppamtsch4" name="'+rowCount4+'[expdetails][src_amt_by_pp]" placeholder="" maxlength="7"></td><td><input type="text" class="form-control ackSch1input inputright otheramtsch4" name="'+rowCount4+'[expdetails][src_amt_by_other]" maxlength="7" placeholder=""></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount4+');">Delete</a></td></tr>';
jQuery('.addedRows4').append(recRow);
}
///////// schedule - 4 ///////////////



////////schedule  - 4a /////////
var rowCount4a = 4;
function addMoreRows4a(frm) {
rowCount4a ++;
var recRow = '<tr id="rowCount'+rowCount4a+'"><td>'+rowCount4a+'</td><td><select class="form-control" name="'+rowCount4a+'[expdetails][nature_of_media]"><option value="">Select Media</option><option value="Electronic Media">Electronic Media</option><option value="Print Media">Print Media</option></select></td><td><input type="text" class="form-control alphaval" name="'+rowCount4a+'[expdetails][name_of_media]" placeholder="" maxlength="100"></td><td><input type="text" class="form-control alphanumericval" name="'+rowCount4a+'[expdetails][address_of_media]" placeholder="" maxlength="100"></td><td><input type="text" class="form-control inputright ackSch1input priceofmedia priceamt4a" name="'+rowCount4a+'[expdetails][price_of_the_media]" maxlength="7" placeholder=""></td><td><input type="text" class="form-control alphanumericval" name="'+rowCount4a+'[expdetails][name_address_of_agency]" placeholder=""  maxlength="100"></td><td><input type="text" class="form-control ackSch1input commissionamt4a inputright commissionofmedia" name="'+rowCount4a+'[expdetails][commission_of_agency]" maxlength="7"></td><td><input type="text" class="form-control ackSch1input totalamt4a inputright subtotal4a" name="'+rowCount4a+'[expdetails][total_amt]" placeholder=""></td><td><input type="text" class="form-control ackSch1input candamtsch4a inputright" name="'+rowCount4a+'[expdetails][src_amt_by_cand]" maxlength="7" placeholder=""></td><td><input type="text" class="form-control ackSch1input ppamtsch4a inputright" name="'+rowCount4a+'[expdetails][src_amt_by_pp]" maxlength="7" placeholder=""></td><td><input type="text" class="form-control ackSch1input otheramtsch4a inputright" name="'+rowCount4a+'[expdetails][src_amt_by_other]" maxlength="7" placeholder=""></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount4a+');">Delete</a></td></tr>';
jQuery('.addedRows4a').append(recRow);
}
///////// schedule - 4a ///////////////



////////schedule  - 5 /////////
var rowCount5 = 4;
function addMoreRows5(frm) {
rowCount5 ++;
var recRow = '<tr id="rowCount'+rowCount5+'"><td>'+rowCount5+'</td><td><input type="text" class="form-control alphanumericval" maxlength="20" placeholder="" name="'+rowCount5+'[expdetails][regn_no_of_vehicle]" value=""></td><td><input type="text" class="form-control ackSch1input inputright" maxlength="7" placeholder="" name="'+rowCount5+'[expdetails][hir_rate_for_vehicle]" value=""></td><td><input type="text" class="form-control ackSch1input inputright" maxlength="7" placeholder="" name="'+rowCount5+'[expdetails][hir_fuel_charges]" value=""></td><td><input type="text" class="form-control inputright ackSch1input" maxlength="7" placeholder="" name="'+rowCount5+'[expdetails][hir_driver_charges]" value=""></td> <td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="'+rowCount5+'[expdetails][no_of_days]" value=""></td><td><input type="text" class="form-control inputright ackSch1input" placeholder="" name="'+rowCount5+'[expdetails][total_amt_incurred]" value=""></td><td><input type="text" class="form-control inputright ackSch1input candamtsch5" maxlength="7" placeholder="" name="'+rowCount5+'[expdetails][src_amt_by_cand]" value=""></td><td><input type="text" class="form-control inputright ackSch1input ppamtsch5" maxlength="7" placeholder="" name="'+rowCount5+'[expdetails][src_amt_by_pp]" value=""></td><td><input type="text" class="form-control inputright ackSch1input otheramtsch5" maxlength="7" placeholder="" name="'+rowCount5+'[expdetails][src_amt_by_other]" value=""></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount5+');">Delete</a></td></tr>';
jQuery('.addedRows5').append(recRow);
}

///////// schedule - 5 ///////////////


////////schedule  - 6 /////////
var rowCount6 = 5;
function addMoreRows6(frm) {   
rowCount6 ++;
var recRow = '<tr id="rowCount'+rowCount6+'"><td>'+rowCount6+'</td><td><input type="date" class="form-control" placeholder="" name="'+rowCount6+'[expdetails][venu_date]" value=""  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td> <td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="'+rowCount6+'[expdetails][venu_details]" value=""></td><td>Others<input type="hidden" name="'+rowCount6+'[expdetails][expense_nature]" value="Others"></td><td><input type="text" maxlength="7" class="ackSch1input inputright form-control" placeholder="" name="'+rowCount6+'[expdetails][expense_nature_rate]" value=""></td><td><input type="text" class="form-control ackSch1input inputright" maxlength="7" placeholder="" name="'+rowCount6+'[expdetails][worker_agents_count]" value=""></td><td><input type="text" class="form-control ackSch1input inputright" placeholder="" name="'+rowCount6+'[expdetails][total_amnt]" value=""></td><td><input type="text" class="form-control inputright ackSch1input candamtsch6" maxlength="7" placeholder="" name="'+rowCount6+'[expdetails][source_amnt_by_cand]" value=""></td><td><input type="text" class="form-control inputright ppamtsch6 ackSch1input" maxlength="7" placeholder="" name="'+rowCount6+'[expdetails][source_amnt_by_polparty]" value=""></td><td><input type="text" class="form-control inputright otheramtsch6 ackSch1input" maxlength="7" placeholder="" name="'+rowCount6+'[expdetails][source_amnt_by_others]" value=""></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount6+');">Delete</a></td></tr>';
jQuery('.addedRows6').append(recRow);
}


///////// schedule - 6 ///////////////



////////schedule  - 7 /////////
var rowCount7 = 4;
function addMoreRows7(frm) {
rowCount7 ++;
var recRow = '<tr id="rowCount'+rowCount7+'"><td>'+rowCount7+'</td><td><input type="date" class="form-control" placeholder="" name="'+rowCount7+'[expdetails][submit_date]"  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td><td><select name="'+rowCount7+'[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype4"><option>Select Payment Type</option><option value="cash">Cash</option><option value="dd">Demand Draft</option><option value="cheque">Cheque</option></select></td><td><input type="text" class="form-control ackSch1input candamtsch7 inputright" placeholder="" name="'+rowCount7+'[expdetails][amount]" maxlength="7"></td><td><input type="text" class="form-control alphanumericval" placeholder="" name="'+rowCount7+'[expdetails][remarks]" maxlength="100"></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount7+');">Delete</a></td></tr>';
jQuery('.addedRows7').append(recRow);  
}

///////// schedule - 7 ///////////////



////////schedule  - 8 /////////
var rowCount8 = 4;
function addMoreRows8(frm) {
rowCount8 ++;
var recRow = '<tr id="rowCount'+rowCount8+'"><td>'+rowCount8+'</td><td><input type="text" maxlength="50" class="form-control" placeholder="" name="'+rowCount8+'[expdetails][party_id]" value=""></td><td><input type="date" class="form-control" placeholder="" name="'+rowCount8+'[expdetails][submit_date]" value=""  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td><td><select name="'+rowCount8+'[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype148"><option>Select Payment Type</option><option value="cash">Cash</option><option value="">Demand Draft</option><option value="cheque" selected="selected">Cheque</option></select></td><td><input type="text" class="form-control ackSch1input inputright candamtsch8" placeholder="" name="'+rowCount8+'[expdetails][amount]" value=""></td><td><input type="text" class="form-control alphanumericval" maxlength="100" placeholder="" name="'+rowCount8+'[expdetails][remarks]" value=""></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount8+');">Delete</a></td></tr>';
jQuery('.addedRows8').append(recRow);
}

///////// schedule - 8 ///////////////




////////schedule  - 9 /////////
var rowCount9 = 4;
function addMoreRows9(frm) {
rowCount9 ++;
var recRow = '<tr id="rowCount'+rowCount9+'"><td>'+rowCount9+'</td><td><input type="text" maxlength="50" class="form-control alphaval" placeholder="" name="'+rowCount9+'[expdetails][name]" value=""></td><td><input type="text" maxlength="50" class="form-control alphanumericval" placeholder="" name="'+rowCount9+'[expdetails][address]" value=""></td><td><input type="date" class="form-control" placeholder="" name="'+rowCount9+'[expdetails][submit_date]" value=""  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td><td><select name="'+rowCount9+'[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype132"><option>Select Payment Type</option><option value="cash">Cash</option><option value="">Demand Draft</option><option value="cheque" >Cheque</option></select></td><td><select name="'+rowCount9+'[expdetails][amount_details]" class="form-control paymenttype"><option>Select Amount Detail</option><option value="loan">Loan</option><option value="gift">Gift</option><option value="donation" selected="selected">Donation</option></select></td> <td><input type="text" class="form-control ackSch1input candamtsch9 inputright" maxlength="7" placeholder="" name="'+rowCount9+'[expdetails][amount]" value=""></td><td><input type="text" maxlength="100" class="form-control alphanumericval" placeholder="" name="'+rowCount9+'[expdetails][remarks]" value=""></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount9+');">Delete</a></td></tr>';
jQuery('.addedRows9').append(recRow);
}

///////// schedule - 9 ///////////////



////////schedule  - 10 /////////
var rowCount10 = 5;
function addMoreRows10(frm) {
rowCount10 ++;
var recRow = '<tr id="rowCount'+rowCount10+'"><td>'+rowCount10+'</td><td><input type="text" class="form-control alphanumericval" maxlength="50" placeholder="" name="'+rowCount10+'[expdetails][newspaper_name]" value=""></td><td><input type="date" class="form-control" placeholder="" name="'+rowCount10+'[expdetails][news_publishing_date]" value=""  min="@php echo $electionSch[0]->DT_PRESS_ANNC @endphp" max="@php echo $electionSch[0]->DATE_COUNT @endphp"></td><td><input type="text" class="form-control ackSch1input newsExpSch10 inputright" maxlength="7" placeholder="" name="'+rowCount10+'[expdetails][expense_on_news]" value=""></td><td><input type="text" class="form-control inputright" placeholder="" maxlength="50" name="'+rowCount10+'[expdetails][channel_name]" value=""></td><td><input type="datetime-local" class="form-control" placeholder="" name="'+rowCount10+'[expdetails][telecost_dateTime]" value=""></td><td><input type="text" class="form-control ackSch1input inputright channelExpSch10" maxlength="7" placeholder="" name="'+rowCount10+'[expdetails][expense_on_channel]" value=""></td><td><select name="'+rowCount10+'[expdetails][payment_type]" class="form-control paymenttype" id="paymenttype45"><option>Select Payment Type</option><option value="cash">Cash</option><option value="">Demand Draft</option><option value="cheque">Cheque</option></select></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount10+');">Delete</a></td></tr>';
jQuery('.addedRows10').append(recRow);
}

///////// schedule - 10 ///////////////


////////schedule  - 1 /////////
var rowCount1 = 16;
function addMoreRows1(frm) {
rowCount1 ++;
var recRow = '<tr id="rowCount'+rowCount1+'"><td>'+rowCount1+'<input type="hidden" name="16[fund][nature_of_exp_id]" value="16"></td><td><label>other expenses</label><td><input type="text" name="'+rowCount1+'[fund][total_amt]" pattern="\d*" maxlength="7" class="form-control inputright src_total_amt s1amt ackSch1input" placeholder="0" value="" readonly=""></td><td><input type="text" name="'+rowCount1+'[fund][src_amt_incurred_cand]" id="'+rowCount1+'[fund][src_amt_incurred_cand]" pattern="\d*" maxlength="7" class="form-control inputright ackSch1input s1amt1 src_amt_incurred_cand" placeholder="0" value=""></td><td><input type="text" name="'+rowCount1+'[fund][src_amt_incurred_pp]" pattern="\d*" maxlength="7" class="form-control inputright s1amt2 ackSch1input src_amt_incurred_pp" placeholder="0" value=""></td><td><input type="text" name="'+rowCount1+'[fund][src_amt_incurred_other]" pattern="\d*" maxlength="7" class="form-control inputright s1amt3 ackSch1input src_amt_incurred_other" placeholder="0" value=""></td><td><a href="javascript:void(0);" onclick="removeRow('+rowCount1+');">Delete</a></td></tr>';
jQuery('.addedRows1').append(recRow);
}
/////////////////////schedule - 1////////////////////



function removeRow(removeNum) {
jQuery('#rowCount'+removeNum).remove();
}
</script>

<!--**********FORM VALIDATION ENDS*************--> 
@endsection