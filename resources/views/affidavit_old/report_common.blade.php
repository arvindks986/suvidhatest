<table width="100%" border="0">
	<caption style="caption-side: top;" >
		<h3 style="text-align:center; margin:9px auto 0; font-size:22px; line-height:25px; font-weight:bold; color:black;">{{Lang::get('affidavit.form26') }} <br>{{Lang::get('affidavit.see_rule_4a')}}</h3>						
	</caption>
		<tr>
			<td align="right">
				<div style="width: 110px; height: 130px; text-align:right;">
					
					@if(@$data['cand_details']->cimage)
						@if(@$data['pdf'] == '1')
							<img style="width: 100px;" src="{!! @$data['cand_details']->cimage !!}">
						@else
							<img style="width: 100px;" src="{{asset(@$data['cand_details']->cimage)}}">
						@endif
					@endif
				</div>
			</td>
		</tr>											
	</table>
	<table width="100%" bgcolor="#fff" border="0">


		<?php if(session()->get('locale') == 'hi') { ?>
			<tr>
			<th  class="justify">			
				<p> <u class="inputLine">@if(@$data['cand_details']->st_code  && @$data['cand_details']->pc_no )
				{{getpcbypcno(@$data['cand_details']->st_code,@$data['cand_details']->pc_no)->PC_NAME}} @endif</u> (निर्वाचन क्षेत्र का नाम)   निर्वाचन-क्षेत्र  से   <u class="inputLine"><span style="width:350px;">{{Lang::get('affidavit.general_election_to_legislative_assembly')}}</span></u> (सदन का नाम) के निर्वाचन के लिए रिटर्निंग आफिसर के समक्ष अभ्‍यर्थी द्वारा नाम-निर्देशन पत्र के साथ प्रस्‍तुत किया जाने वाला शपथ पत्र 
				</p>
			</th>
			</tr>
		<?php } else { ?>

	
		<tr>
			<th  class="justify">			
				<p> {{Lang::get('affidavit.affidavit_to_be_giled_by_the_candidate_alogwith_nomination_paper_before_the_returning_officer_for_election')}} {{Lang::get('affidavit.to')}} <u class="inputLine"><span style="width:350px;">{{Lang::get('affidavit.general_election_to_legislative_assembly')}}</span></u> <span style="text-align:right!important; display:inline-block">{{Lang::get('affidavit.name_of_the_house')}}</span> {{Lang::get('affidavit.from')}} <u class="inputLine">@if(@$data['cand_details']->st_code  && @$data['cand_details']->pc_no )
				{{getpcbypcno(@$data['cand_details']->st_code,@$data['cand_details']->pc_no)->PC_NAME}} @endif</u>{{Lang::get('affidavit.constituency')}}</p>
			</th>
		</tr>  
		<?php } ?>
		
 	</table>
 	<table width="100%" class="top-20 top" bgcolor="#fff" border="0">	
		<tr>
		<th align="center" style="text-align:center; margin:20px auto 0;  line-height:25px"><h3 style="text-decoration: underline; font-weight:bold; color:black;font-size:22px;">{{Lang::get('affidavit.part_a')}}</h3>	
		</th>
		</tr>
	</table>
 	<table width="100%" border="0" class="top padd-0" >						
		<tr>
			<td colspan="2" style="line-height: 1.8; margin-bottom: 20px;">
			<p>{{Lang::get('affidavit.i')}}<u class="inputLine"> {{@$data['cand_details']->cand_name}} </u>**
			@if(@$data['cand_details']->relation_name == 2)
				<del>{{Lang::get('affidavit.son')}}/</del>{{Lang::get('affidavit.daughter')}}<del>/{{Lang::get('affidavit.wife')}}</del>
			@elseif(@$data['cand_details']->relation_name == 3)
				<del>{{Lang::get('affidavit.son')}}/{{Lang::get('affidavit.daughter')}}/</del>{{Lang::get('affidavit.wife')}}
			@else
				{{Lang::get('affidavit.son')}}<del>/{{Lang::get('affidavit.daughter')}}/{{Lang::get('affidavit.wife')}}/</del>
			@endif 
			 @if(session()->get('locale') == 'hi') @else {{Lang::get('affidavit.of')}} @endif <u class="inputLine">{{$data['cand_details']->son_daughter_wife_of}} </u> {{Lang::get('affidavit.aged')}} <u class="inputLine">{{@$data['cand_details']->age}}</u> {{Lang::get('affidavit.years_resident_of')}} <u class="inputLine">{{@$data['cand_details']->postal_address}} </u> {{Lang::get('affidavit.mention_full_postal_address')}}</p>
			</td>
		</tr>
		
		
		<?php if(session()->get('locale') == 'hi') { ?>
		
		<tr>
			<td width="4"><b>(1)</b></td>
			
			
			<td>
				    	मैं. <u class="inputLine">@if(@$data['cand_details']->partyabbre){{getpartybyid(@$data['cand_details']->partyabbre)->PARTYNAME}}@endif</u>  @if(@$data['cand_details']->partytype) (**राजनैतिक दल का नाम) द्वारा खड़ा किया गया अभ्‍यर्थी  <del> /**एक स्‍वतंत्र अभ्‍यर्थी </del> @else <del> (**राजनैतिक दल का नाम) द्वारा खड़ा किया गया अभ्‍यर्थी / </del> **एक स्‍वतंत्र अभ्‍यर्थी  @endif के रूप में लड़ रहा हूं। 
				<br />	(**जो लागू न हो उसे काट दें) 

		    </td>

			
			
		</tr>
		
		<?php } else { ?>
		<tr>
			<td width="4"><b>(1)</b></td>
			
				<td>
				 {{Lang::get('affidavit.i_am_a_candidate_set_up_by')}} <u class="inputLine">@if(@$data['cand_details']->partyabbre){{getpartybyid(@$data['cand_details']->partyabbre)->PARTYNAME}}@endif</u>
				</td>
			
		</tr>
		
		<tr>
			<td></td>
			<td>
				<span style="display: block; line-height: 30px;" class="block">@if(@$data['cand_details']->partytype)(<b>**</b> {{Lang::get('affidavit.name_of_the_political_party')}}) / <b>**</b> <del>{{Lang::get('affidavit.am_contesting_as_an_independent_candidate')}} </del>
				
				@else
					
				<del>(<b>**</b> {{Lang::get('affidavit.name_of_the_political_party')}})</del> / <b>**</b> {{Lang::get('affidavit.am_contesting_as_an_independent_candidate')}}
				@endif
				.</span>
				<p style="paddint-top:20px; display:block">(<b>**</b>{{Lang::get('affidavit.strike_out_whichever_is_not_applicable')}})</p>
			</td>
		</tr>
		
		<?php } ?>
		
			
			<?php if(session()->get('locale') == 'hi') { ?>
			
			<tr>
				<td width="4"><b>(2)</b></td>
				<td>
				<p>
						मेरा नाम  <u class="inputLine">
					@if(@$data['cand_details']->state_enrolled && @$data['cand_details']->constituency_enrolled)
					{{getpcbypcno(@$data['cand_details']->state_enrolled,@$data['cand_details']->constituency_enrolled)->PC_NAME}},
					@endif
					@if(@$data['cand_details']->state_enrolled)
					{{getstatebystatecode(@$data['cand_details']->state_enrolled)->ST_NAME}} @endif 
				</u>(निर्वाचन-क्षेत्र और राज्‍य का नाम) में भाग सं <u class="inputLine">{{@$data['cand_details']->part_no_enrolled}}</u> के क्रम सं <u class="inputLine">{{@$data['cand_details']->serial_no_enrolled}}</u> पर प्रविष्‍ट है। </p>
				</td>
			</tr>
			
			
			
			<?php } else { ?>
			
			
			<tr>
				<td width="4"><b>(2)</b></td>
				<td>
				<p>
					<span> {{Lang::get('affidavit.my_name_is_enrolled_in')}} <u class="inputLine">
					@if(@$data['cand_details']->state_enrolled && @$data['cand_details']->constituency_enrolled)
					{{getacbyacno(@$data['cand_details']->state_enrolled,@$data['cand_details']->constituency_enrolled)->AC_NAME}},
					@endif
					@if(@$data['cand_details']->state_enrolled)
					{{getstatebystatecode(@$data['cand_details']->state_enrolled)->ST_NAME}} @endif 
				</u> {{Lang::get('affidavit.at_serial_no')}} <u class="inputLine">{{@$data['cand_details']->serial_no_enrolled}}</u> {{Lang::get('affidavit.in_part_no')}} <u class="inputLine">{{@$data['cand_details']->part_no_enrolled}}</u></span></p>
				</td>
			</tr>	
			
			<?php } ?>
			
				<?php if(session()->get('locale') == 'hi') { ?>
			
			<tr>
				<td width="4"><b>(3)</b></td>
				<td>
					<p>(3)	मेरा/मेरे <u class="inputLine">{{@$data['cand_details']->phoneno_1}}   @if(@$data['cand_details']->phoneno_2) ,{{@$data['cand_details']->std_code}}-{{@$data['cand_details']->phoneno_2}} @endif</u>  संपर्क दूरभाष संख्‍या/संख्‍याएं है/हैं और <u class="inputLine">{{@$data['cand_details']->emailid}}</u>  मेरा ईमेल पता (यदि कोई हो) है तथा मेरा/मेरे सोशल मीडिया खाता/खाते (यदि कोई हो) निम्‍नलिखित है/हैं।</p>
				</td>
			</tr>
			
			
			
			<?php } else { ?>
			<tr>
				<td width="4"><b>(3)</b></td>
				<td>
					<p>{{Lang::get('affidavit.my_contact_telephone_number')}} <u class="inputLine">{{@$data['cand_details']->phoneno_1}}   @if(@$data['cand_details']->phoneno_2) ,{{@$data['cand_details']->std_code}}-{{@$data['cand_details']->phoneno_2}} @endif</u> {{Lang::get('affidavit.and_my_e_mail_id')}} <u class="inputLine">{{@$data['cand_details']->emailid}}</u> {{Lang::get('affidavit.and_my_social_media_account')}}</p>
				</td>
			</tr>

			<?php } ?>
			
	</table>
	
	
					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<td><b>{{Lang::get('affidavit.sr_no')}}</b></td>
								<td><b>{{Lang::get('affidavit.social_media')}}</b></td>
								<td><b> {{Lang::get('affidavit.account')}} </b></td>
								
							</tr>
							@if(count($data['social_media'])>0)
							@foreach($data['social_media'] as $key => $raw)
							
							<tr>
								<td>{{$key+1}}</td>
								<td>{{$raw->media_account}}</td>
								<td>{{$raw->other_account_name}}</td>									
							</tr>	
							
							@endforeach
							@else
								<tr>
								<td>1</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>	
							</tr>
							@endif


						</tbody>
					</table>			
					
		
		
			<table width="100%" class="top-20 top" bgcolor="#fff" border="0">
				<tr>
					<th align="left">(4) {{Lang::get('affidavit.details_of_permanent_account_number_and_status_of_filing_of_income_tax_return')}}</th>
				</tr>
			</table>

	<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
		<tbody>
			<tr class="thHeading">
				<th>{{Lang::get('affidavit.name')}}</th>
				<th>{{Lang::get('affidavit.relation')}}</th>
				<th>{{Lang::get('affidavit.pan')}} </th>
				<th>{{Lang::get('affidavit.the_financial_year_for_which_the_last_incometax_return_has_been_filed')}}</th>
				<th>{{Lang::get('affidavit.total_income_shown_in_income_tax_return_for_the_last_five_financial_years_completed')}}
				</th>
			</tr>				
			@foreach($data['pan_details'] as $key => $raw)				
			<tr>
				<td>{{$raw->name}}</td>
				<td>{{$raw->relation_type}}</td>
				<td>@if($raw->pan)
					{{$raw->pan}}
					@else
						{{Lang::get('affidavit.no_pan_allotted')}}
					@endif
					</td>
				<td>
				@if($raw->financial_year)
					{{$raw->financial_year}}
				@else
					{{Lang::get('affidavit.nil')}}
				@endif
				</td>
				<td class="padd-0">
					<table class="bdrLeass" align="left" width="100%" border="1">
						<tr><td width="10">(i)</td><td> 
						@if($raw->financialyr1) &#8377; {{$raw->financialyr1}}
						@else {{Lang::get('affidavit.nil')}} @endif
						
						@if($raw->financial_year)
						({{$raw->financial_year}})
						@endif
						</td></tr>
						<tr><td>(ii)</td> <td>
						@if($raw->financialyr2) &#8377; {{$raw->financialyr2}}
						@else {{Lang::get('affidavit.nil')}} @endif
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -2}} - {{$financial_year -1}})
							@endif
						@endif
						</td></tr>
						<tr><td>(iii)</td> <td>
						@if($raw->financialyr3) &#8377; {{$raw->financialyr3}}
						@else {{Lang::get('affidavit.nil')}} @endif
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -3}} - {{$financial_year -2}})
							@endif
						@endif
						</td></tr>
						
						<tr><td>(iv)</td> <td>
						@if($raw->financialyr4) &#8377; {{$raw->financialyr4}}
						@else {{Lang::get('affidavit.nil')}} @endif 
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -4}} - {{$financial_year -3}})
							@endif
						@endif
						</td></tr>
						
						<tr><td>(v)</td> <td>
						@if($raw->financialyr5) &#8377; {{$raw->financialyr5}}
						@else {{Lang::get('affidavit.nil')}} @endif 
						
						@if($raw->financial_year) 
						@php $financial_year = substr($raw->financial_year,-4) @endphp
							@if(is_numeric($financial_year))
							({{$financial_year -5}} - {{$financial_year -4}})
							@endif
						@endif
						</td></tr>
					</table>
				</td>
			</tr>							
			@endforeach
		</tbody>
	</table>	

		<table width="100%;" class="top top-20">
			<tr>
				<th>{{Lang::get('affidavit.note')}}: </th>
				<th>{{Lang::get('affidavit.it_is_mandatory_for_pan_holder_to_mention_pan')}} </th>
			</tr>			
			@php $not_applicable = ''; @endphp
			@foreach($data['pending_cases'] as $key => $raw)
				@php $not_applicable = $raw->not_applicable; @endphp			
			@endforeach	
			<tr align="left">
				<th>(5)</th>
				<th align="left">{{Lang::get('affidavit.pending_criminal_cases')}}</th>
			</tr>
			<tr>
				<th>(i)</th>
				@if(($not_applicable == 'NOT APPLICABLE') || (count($data['pending_cases'])  == 0)  )
				<td>{{Lang::get('affidavit.i_declare_that_there_is_no_pending_criminal_case_against_me')}}</td>
				@else
				<td><del>{{Lang::get('affidavit.i_declare_that_there_is_no_pending_criminal_case_against_me')}} </del></td>	
				@endif
			</tr>
			<tr>
				<td colspan="2" align="center"><span class="bold">{{Lang::get('affidavit.or')}}</span></td>
			</tr>
			<tr>
				<th>(ii)</th>
				<td> {{Lang::get('affidavit.the_following_criminal_cases_are_pending_against')}}
				@if(($not_applicable == 'NOT APPLICABLE') || (count($data['pending_cases'])  == 0)  )
				<span class="bold">&nbsp;&nbsp;&nbsp;{{Lang::get('affidavit.not_applicable')}}</span>
				@endif
				</td>				
			</tr>
		</table>

		@if(($not_applicable == 'NOT APPLICABLE') || (count($data['pending_cases'])  == 0)  )		
		<table width="100%" class="top-20" border="0">
			<tr>
				<td colspan="2"><del>{{Lang::get('affidavit.if_there_are_pending_criminal_cases_against_the_candidate')}}</del></td>
			</tr>

		</table>		
		@else			
		<table>
			<tr>
				<td colspan="2">{{Lang::get('affidavit.if_there_are_pending_criminal_cases_against_the_candidate')}}</td>
			</tr>
		</table>
					
				@foreach($data['pending_cases'] as $key => $raw)	
							
					<table width="100%" class="top-20 top" style="border:1px solid black;">
						<thead style="border:1px solid black!important;">
							<tr class="thHeading" align="left" style="border:1px solid black!important;">
								<th style="border:1px solid black!important;" width="55">{{Lang::get('affidavit.sr_no')}}</th>
								<th style="border:1px solid black!important;" colspan="8" class="bordered">{{Lang::get('affidavit.details')}}</th>
							</tr>
						</thead>
						<tbody>
						    <tr align="left" style="border:1px solid black!important;" class="bordered">
						    	<td style="border:1px solid black!important;" class="bordered" rowspan="6">{{$key+1}}</td>
						    	<th align="left" style="border-left:1px solid black!important;"   class="n-bordered bdrLeass">{{Lang::get('affidavit.fir_no')}}: </th>
			    				<td align="left" class="n-bordered bdrLeass">{{$raw->fir_no}}</td>

			    				<th align="left"  class="n-bordered bdrLeass">{{Lang::get('affidavit.state')}}: </th>
								<td align="left" style="border:0px solid black!important;" class="n-bordered bdrLeass">{{ getstatebystatecode($raw->st_code)->ST_NAME }}</td>

								<th align="left"  class="n-bordered bdrLeass">{{Lang::get('affidavit.district')}}: </th>
								<td align="left" style="border:0px solid black!important;" class="n-bordered bdrLeass">{{ getdistrictbydistrictno($raw->st_code,$raw->dist_no)->DIST_NAME }}</td>

								<th align="left" class="n-bordered bdrLeass">{{Lang::get('affidavit.police_station')}}: </th>
								<td align="left" style="border:0px solid black!important;" class="n-bordered bdrLeass">{{$raw->police_station}}</td>
							</tr>
							<tr class="n-bordered bdrLeass">
								<th align="left" style="border-left:1px solid black!important;" class="n-bordered bdrLeass">{{Lang::get('affidavit.police_station_address')}}: </th>
			    				<td align="left" class="n-bordered bdrLeass">{{$raw->police_station_address}}</td>

			    				<th align="left" class="n-bordered bdrLeass">{{Lang::get('affidavit.case_number')}}: </th>
								<td align="left" class="n-bordered bdrLeass">{{$raw->case_no}}</td>

								<th align="left" class="n-bordered bdrLeass">{{Lang::get('affidavit.name_of_court')}}: </th>
								<td align="left" class="n-bordered bdrLeass">{{$raw->name_court_cognizance}}</td>

								<th align="left" class="n-bordered bdrLeass">{{Lang::get('affidavit.acts')}}: </th>
								<td align="left" class="n-bordered bdrLeass">{{$raw->acts}}</td>								
							</tr>
							<tr  class="">
								<th align="left" style="border-left:1px solid black!important;"  class="n-bordered bdrLeass">{{Lang::get('affidavit.sections')}}:</th>
			    				<td align="left" colspan="2" class="n-bordered bdrLeass">{{$raw->sections}}</td>

			    				<th align="left" colspan="2" class="n-bordered bdrLeass">{{Lang::get('affidavit.brief_description_of_the_offense')}}: </th>
								<td align="left" colspan="3" class="n-bordered bdrLeass">{{$raw->offence_description}}</td>
							</tr>
							
							<tr  class="">
								<th align="left" colspan="2" style="border-left:1px solid black!important;"  class="n-bordered bdrLeass">{{Lang::get('affidavit.whether_charges_have_been_framed')}}: </th>
								<td align="left" colspan="2" class="n-bordered bdrLeass">@if($raw->framed_charge == 1) {{Lang::get('affidavit.yes')}} @else {{Lang::get('affidavit.no')}} @endif</td>

								<th align="left" colspan="2" class="n-bordered bdrLeass">{{Lang::get('affidavit.date')}}: </th>
								<td align="left" colspan="2" class="n-bordered bdrLeass">@if($raw->framed_charge == 1) {{\Carbon\Carbon::parse($raw->date_charges)->format('d/m/Y')}} @endif</td>																	
							</tr>
							<tr>
								<th colspan="6" style="border-left:1px solid black!important;"  class="n-bordered bdrLeass" align="left">{{Lang::get('affidavit.whether_any_appeal_application_for_revision')}}: </th>	
								<td colspan="2" class="n-bordered bdrLeass" align="left">@if($raw->appeal_application == 1) {{Lang::get('affidavit.yes')}} @else {{Lang::get('affidavit.no')}} @endif</td>	
							</tr>								
						</tbody>
					</table>				
					@endforeach				
					@endif

					<table width="100%;" class="top top-20">
					
					@php $not_applicable = ''; @endphp
					@foreach($data['imprisonment_criminal'] as $key => $raw)
						@php $not_applicable = $raw->not_applicable; @endphp
					
					@endforeach
					
						<tr align="left">
							<th width="4">(6)</th>
							<th>{{Lang::get('affidavit.cases_of_conviction')}}</th>
						</tr>						
						<tr>
							<th width="4">(i)</th>
							@if(($not_applicable == 'NOT APPLICABLE') || (count($data['imprisonment_criminal'])  == 0)  )
							<td>{{Lang::get('affidavit.i_declare_that_i_have_not_been_convicted_for_any_criminal_offence')}}</td>
							@else
							<td><del>{{Lang::get('affidavit.i_declare_that_i_have_not_been_convicted_for_any_criminal_offence')}}</del></td>	
							@endif
						</tr>
						<tr>
							<td colspan="2" align="center"><span class="bold">{{Lang::get('affidavit.or')}}</span></td>
						</tr>
						<tr>
							<th width="3">(ii)</th>
							<td>
								{{Lang::get('affidavit.i_have_been_convicted_for_the_offences_mentioned')}}
								@if(($not_applicable == 'NOT APPLICABLE') || (count($data['imprisonment_criminal'])  == 0)  )
								<b class="bold"> {{Lang::get('affidavit.not_applicable')}}</b> 
								@endif
							 </td>							
						</tr>
					</table>					
					@if(($not_applicable == 'NOT APPLICABLE') || (count($data['imprisonment_criminal'])  == 0)  )					
					<table width="100%" class="top-20" border="0">
						<tr>
							<td colspan="2"><del>{{Lang::get('affidavit.if_the_candidate_has_been_convicted')}}</del></td>
						</tr>
					</table>					
					@else						
					<table width="100%" class="top-20" border="0">
						<tr>
							<td colspan="2">{{Lang::get('affidavit.if_the_candidate_has_been_convicted')}}</td>
						</tr>
					</table>

				@foreach($data['imprisonment_criminal'] as $key => $raw)	
					<table width="100%" class="top-20 top" style="border:1px solid black;">
						    <tr class="thHeading">
						    <th style="border:1px solid black!important;" width="55">{{Lang::get('affidavit.sr_no')}}</th>
						    	<th style="border:1px solid black!important;" colspan="6">{{Lang::get('affidavit.details')}}</th>
						    </tr>
							<tr align="left">
								<th style="border:1px solid black!important;" class="bordered" rowspan="5">{{$key+1}}</th>
								<th style="border-left:1px solid black!important;" align="left">{{Lang::get('affidavit.case_number')}}:</th>
								<td align="left" style="border:0px solid black!important;">{{$raw->case_no}}</td>
								<th align="left">{{Lang::get('affidavit.name_of_court')}}:</th>
								<td align="left" style="border:0px solid black!important;">{{$raw->convicting_court}}</td>
								<th align="left">{{Lang::get('affidavit.acts')}}:</th>
								<td align="left" style="border:0px solid black!important;">{{$raw->acts}}</td>
								
							</tr>
							<tr>
								<th style="border-left:1px solid black!important;" align="left">{{Lang::get('affidavit.sections')}}:</th>
								<td align="left">{{$raw->sections}}</td>								
								<th align="left">{{Lang::get('affidavit.brief_description_of_the_offence_for_which_conviction')}}:</th>
								<td align="left">{{$raw->offence_description}}</td>
								<th align="left">{{Lang::get('affidavit.date_of_order')}}:</th>
								<td align="left">{{\Carbon\Carbon::parse($raw->order_date)->format('d/m/Y')}}</td>								
							</tr>	
							<tr align="left">							
								<th style="border-bottom:1px solid black!important; border-left:1px solid black!important;" align="left">{{Lang::get('affidavit.punishment_imposed')}}:</th>
								<td align="left" style="border-bottom:1px solid black!important;">{{$raw->punish}}</td>
								<th align="left" style="border-bottom:1px solid black!important;">{{Lang::get('affidavit.whether_any_appeal_has_been_filed_against_conviction_order')}}:</th>
								<td align="left" style="border-bottom:1px solid black!important;">@if($raw->appeal_filed == 1) {{Lang::get('affidavit.yes')}} @else {{Lang::get('affidavit.no')}} @endif</td>
								<th align="left" style="border-bottom:1px solid black!important;">{{Lang::get('affidavit.details_and_present_status_of_appeal')}}:</th>
								<td align="left" style="border-bottom:1px solid black!important;"> @if($raw->appeal_filed == 1) {{$raw->appeal}} @endif </td>
							</tr>											
					</table>
					@endforeach						
					@endif
					<table width="100%" class="top top-20">
						<tr>  
							<th width="4">(6A)</th>							
							<td>
								<span>{{Lang::get('affidavit.i_have_given_full_and_up_to_date_information_to_my_political_party')}}</span>
							</td>
						</tr>
						<tr>
							<th colspan="2" align="left">{{Lang::get('affidavit.note')}} :</th>
						</tr>	
						<tr>
							<th width="4" align="right">1.</th>
							<td >{{Lang::get('affidavit.details_should_be_entered_clearly_and_legibly_in_bold_letters')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">2.</th>
							<td >{{Lang::get('affidavit.details_to_be_given_separately_for_each_case_under_different_columns_against_eachitem')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">3.</th>
							<td >{{Lang::get('affidavit.details_should_be_given_in_reverse_chronological_order')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">4.</th>
							<td>{{Lang::get('affidavit.additional_sheet_may_be_added_if_required')}}</td>
						</tr>
						<tr>
							<th width="4" align="right">5.</th>
							<td>{{Lang::get('affidavit.candidate_is_responsible_for_supplying_all_information_in_compliance_of')}}</td>
						</tr>
                    </table>

                    <table width="100%" class="top top-20">
                    	<tr>
                    		<th width="4">(7)</th>
                    		<th>                    			
                    			{{Lang::get('affidavit.that_i_give_herein_below_the_details_of_the_assets')}}
                    		</th>
                    	</tr>
                    	<tr align="left">
                    		<th colspan="2" align="left"><span class="block"><u> {{Lang::get('affidavit.details_of_movable_assets')}}:</u></span></th>
                    	</tr>
                    	<tr>
                    		<td colspan="2" class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="95">{{Lang::get('affidavit.note')}}: 1. </th>
                    					<td>{{Lang::get('affidavit.assets_in_joint_name_indicating_the_extent_of_joint_ownership_will_also_have_to_be_given')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 2. </th>
                    					<td>{{Lang::get('affidavit.in_case_of_deposit_investment_the_details_including_serial_number')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>	
                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 3. </th>
                    					<td>{{Lang::get('affidavit.value_of_bonds_share_debentures_as_per_the_current_market_value_in_stock_exchange')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 4. </th>
                    					<td>{{Lang::get('affidavit.dependent_means_parents_son_daughter_of_candidate')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>

                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 5. </th>
                    					<td>{{Lang::get('affidavit.details_including_amount_is_to_be_given_separately_in_respect_of_each_investment')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>	

                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.note')}}: 6. </th>
                    					<td >{{Lang::get('affidavit.details_should_include_the_interest_in_or_ownership_of_offshore_assets')}} </td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>

                    	<tr>
                    		<td class="padd-0" colspan="2">
                    			<table class="top top-20" width="100%">
                    				<tr valign="top">
                    					<th width="95">{{Lang::get('affidavit.explanation')}},-  </th>
                    					<td>{{Lang::get('affidavit.for_the_purpose_of_this_form_the_expression')}} </td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>                    	
                    </table>
						<table width="100%" class="top-20" border="0">
						<tr>
							<th width="6">(i)</th>
                    		<td colspan="4" align="left">{{Lang::get('affidavit.cash_in_hand')}}</td>
                    	</tr>
						</table>
                    <table width="100%" class="top-20" border="1">                    	
                    	<tr>
                    		<th colspan="2">{{Lang::get('affidavit.relation_type')}}</th>
                    		<th colspan="2">{{Lang::get('affidavit.name')}}</th>
                    		<th>{{Lang::get('affidavit.cash_in_hand')}}</th>
                    	</tr>						
						@foreach($data['cash_in_hand'] as $key => $raw)
                    	<tr>
                    		<td colspan="2">{{$raw->relation_type}}</td>
                    		<td colspan="2">{{$raw->name}}</td>
                    		<td>@if($raw->cash_in_hand)
							&#8377; {{$raw->cash_in_hand}}
							@else {{Lang::get('affidavit.nil')}} @endif
							</td>
                    	</tr>
						@endforeach	
					</table>					
					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th width="6">(ii)</th>
                    		<td colspan="4" align="left">{{Lang::get('affidavit.details_of_deposit_in_bank_accounts')}}</td>
                    		<!-- <th colspan="5" align="left"></th> -->
                    	</tr>						
					</table>					
					<table width="100%" class="top-20 top" border="1">	
                    	<tr class="thHeading">
							<th>{{Lang::get('affidavit.relation_type')}}</th>
							<th>{{Lang::get('affidavit.name')}}</th>
							<th>{{Lang::get('affidavit.bank_company_name')}}</th>
							<th>{{Lang::get('affidavit.deposit_details')}}</th>
							<th>{{Lang::get('affidavit.account_details')}}</th>
						</tr>
						@foreach($data['bank_details'] as $key => $raw)
						<tr>
							<td class="bordered">{{$raw->relation_type}} </td>
							<td class="bordered">{{$raw->name}}</td>
							<td class="padd-0 bordered">
								<table width="100%" align="left" class="" border="0">
									<tr>
										<td align="left">
										@if($raw->bank_name) {{$raw->bank_name}} @else {{Lang::get('affidavit.nil')}} @endif </td>	
									</tr>
								</table>									
							</td>
							<td class="padd-0 bordered">
								<table width="100%" class="top" align="left" border="0">
									<tr>
										<th align="left">{{Lang::get('affidavit.deposit_type')}}:</th>
										<td align="right"> @if($raw->deposit_type) {{$raw->deposit_type}} @else {{Lang::get('affidavit.nil')}} @endif</td>
									</tr>
									<tr>
										<th align="left">{{Lang::get('affidavit.deposit_date')}}:</th>	
										<td align="right">@if($raw->deposit_date) {{\Carbon\Carbon::parse($raw->deposit_date)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
										</td>
									</tr>
								</table>									
							</td>
							<td class="padd-0 bordered">
								<table width="100%" align="left" class="top" border="0">
									<tbody><tr>
										<th align="left">{{Lang::get('affidavit.account_type')}}:</th>
										<td align="right"> 
										@if($raw->account_type) 
											@if($raw->account_type == 'Joint')												
												{{$raw->account_type}} {{$raw->joint_account_with_name}} 
											@else
												{{$raw->account_type}} 
											@endif
										@else {{Lang::get('affidavit.nil')}} 
										@endif</td>
									</tr>
									<tr>
										<th align="left">{{Lang::get('affidavit.amount')}}:</th>	
										<td align="right">@if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif</td>
									</tr>
								</tbody></table>									
							</td>
						</tr>
						@endforeach
						</table>					
					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th width="6">(iii)</th>
                    		<td colspan="4" align="left">{{Lang::get('affidavit.details_of_investment_in_bonds')}}</th>
                    	</tr>						
					</table>
					<table width="100%" class="top-20" border="1">
						<tr class="thHeading">
							<th>{{Lang::get('affidavit.relation_type')}}</th>
							<th>{{Lang::get('affidavit.name')}}</th>
							<th>{{Lang::get('affidavit.bank_company_name')}}</th>
							<th>{{Lang::get('affidavit.deposit_details')}}</th>
							<th>{{Lang::get('affidavit.account_details')}}</th>
						</tr>					
						@foreach($data['investment_details'] as $key => $raw)
						<tr>
							<td class="bordered">{{$raw->relation_type}}</td>
							<td class="bordered">{{$raw->name}}</td>
							<td class="padd-0 bordered">
								<table width="100%" align="left" border="0" class="top">
									<tbody>
									<tr>
										<td align="left">@if($raw->company) {{$raw->company}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
									</tr>
								</tbody>
							</table>									
							</td>
							<td class="padd-0 bordered">
								<table width="100%" align="left" class="top" border="0">
									<tr>
										<th align="left">{{Lang::get('affidavit.account_type')}}:</th>
										<td align="right"> @if($raw->company_investment_type) {{$raw->company_investment_type}} @else {{Lang::get('affidavit.nil')}} @endif</td>
									</tr>									
								</table>									
							</td>
							<td class="padd-0 bordered">
								<table width="100%" class="top" align="left" border="0" >
									<tr>
										<th align="left">{{Lang::get('affidavit.account_type')}}:</th>
										<td align="right">@if($raw->account_type) 
											@if($raw->account_type == 'Joint')												
												{{$raw->account_type}} {{$raw->joint_account_with_name}} 
											@else
												{{$raw->account_type}} 
											@endif
										@else {{Lang::get('affidavit.nil')}} 
										@endif</td>
									</tr>
									<tr>
										<th align="left">{{Lang::get('affidavit.amount')}}:</th>	
										<td align="right"> @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif</td>
									</tr>								
								</table>									
							</td>
						</tr>
						@endforeach						
						</table>
					
					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th width="6">(iv)</th>
                    		<td> {{Lang::get('affidavit.details_of_investment_in_nss')}}</td>
                    	</tr>						
					</table>	
					
					<table width="100%" class="top top-20" border="1">
						<tr class="thHeading">
							<th>{{Lang::get('affidavit.relation_type')}}</th>
							<th>{{Lang::get('affidavit.name')}}</th>
							<th>{{Lang::get('affidavit.bank_company_name')}}</th>
							<th>{{Lang::get('affidavit.deposit_details')}}</th>
							<th>{{Lang::get('affidavit.account_details')}}</th>
						</tr>						
						@foreach($data['savings_and_policies'] as $key => $raw)
						<tr>
							<td class="bordered">{{$raw->relation_type}}</td>
							<td class="bordered">{{$raw->name}}</td>
							<td class="padd-0 bordered">
								<table width="100%" align="left" border="0">
									<tr>
										<td align="left">@if($raw->company) {{$raw->company}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
									</tr>
								</table>									
							</td>
							<td class="padd-0 bordered">
								<table width="100%" align="left" border="0">
									<tr>
										<th align="left">{{Lang::get('affidavit.deposit_type')}}:</th>
										<td align="right">@if($raw->saving_type) {{$raw->saving_type}} @else {{Lang::get('affidavit.nil')}} @endif</td>
									</tr>									
								</table>									
							</td>
							<td class="padd-0 bordered">
								<table width="100%" align="left" border="0">
									<tr>
										<th align="left">{{Lang::get('affidavit.account_type')}}:</th>
										<td align="right">@if($raw->account_type) 
											@if($raw->account_type == 'Joint')												
											{{$raw->account_type}} {{$raw->joint_account_with_name}} 
											@else
												{{$raw->account_type}} 
											@endif
										@else {{Lang::get('affidavit.nil')}} 
										@endif</td>
									</tr>
									<tr>
										<th align="left">{{Lang::get('affidavit.amount')}}:</th>	
										<td align="right">@if($raw->amount) &#8377; {{$raw->amount}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
									</tr>
								</table>									
							</td>
						</tr>
						@endforeach						
					</table>
					
					<table width="100%" class="top-20 top" border="0">
						<tr>
                    		<th width="6">(v)</th>
                    		<td align="left">{{Lang::get('affidavit.personal_loans_advance_given_to_any_person_or_entity_including_firm')}}</td>
                    	</tr>						
					</table>	
					
					<table width="100%" class="top top-20" border="1">	
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.relation_type')}}</th>
							<th>{{Lang::get('affidavit.name')}}</th>
							<th>{{Lang::get('affidavit.bank_company_name')}}</th>
							<th>{{Lang::get('affidavit.nature_of_loan')}}</th>
							<th>{{Lang::get('affidavit.account_details')}}</th>
							</tr>
							@foreach($data['loan_details'] as $key => $raw)
							<tr>
								<td class="bordered">{{$raw->relation_type}}</td>
								<td class="bordered">{{$raw->name}}</td>
								<td class="bordered">@if($raw->loan_to) {{$raw->loan_to}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
								<td class="bordered">@if($raw->nature_of_loan) {{$raw->nature_of_loan}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
								<td class="padd-0 bordered">
									<table width="100%" align="left" border="0">
										<tr>
											<th align="left">{{Lang::get('affidavit.account_type')}}:</th>
											<td align="right">
											@if($raw->loan_account_type) 
											@if($raw->loan_account_type == 'Joint')												
											{{$raw->loan_account_type}} {{$raw->joint_account_with_name}} 
											@else
												{{$raw->loan_account_type}} 
											@endif
										@else {{Lang::get('affidavit.nil')}} 
										@endif											
											</td>
										</tr>
										<tr>
											<th align="left">{{Lang::get('affidavit.amount')}}:</th>	
											<td align="right">@if($raw->outstanding_amount) &#8377; {{$raw->outstanding_amount}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
									</table>									
								</td>
							</tr>
							@endforeach							
					</table>
					
					<table width="100%" class="top top-20" border="0"  >
						<tr>
                    		<th width="6">(vi)</th>
                    		<td align="left">{{Lang::get('affidavit.motor_vehicles_aircrafts_yachts_ships')}}</td>
                    	</tr>						
					</table>	
					
					<table width="100%" class="top top-20" border="1">
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.relation_type')}}</th>
								<th>{{Lang::get('affidavit.name')}}</th>
								<th colspan="2">{{Lang::get('affidavit.vehicle_details')}}</th>
								<th>{{Lang::get('affidavit.amount_details')}}</th>
							</tr>
							@foreach($data['vehicle_details'] as $key => $raw)
							<tr>
								<td class="bordered">{{$raw->relation_type}}</td>
								<td class="bordered">{{$raw->name}}</td>
								<td colspan="2" class="padd-0 bordered">
									<table width="100%" class="top">
										<tr>
											<th align="left">{{Lang::get('affidavit.vehicle_type')}}:</th>
											<td align="right">@if($raw->vehicle_type) {{$raw->vehicle_type}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
										<tr>
											<th align="left">{{Lang::get('affidavit.make')}}:</th>
											<td align="right">@if($raw->make) {{$raw->make}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
										<tr>
											<th align="left">{{Lang::get('affidavit.registration_no')}}:</th>
											<td align="right">@if($raw->registration_no) {{$raw->registration_no}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
										<tr>
											<th align="left">{{Lang::get('affidavit.year_of_purchase')}}:</th>
											<td align="right">@if($raw->year_of_purchase) {{$raw->year_of_purchase}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
									</table>
								</td>
								<td class="bordered">@if($raw->amount) &#8377; {{$raw->amount}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach							
					</table>	

					<table width="100%" class="top top-20" border="0" >
						<tr>
                    		<th width="6">(vii)</th>
                    		<td align="left">{{Lang::get('affidavit.jewellery_bullion_and_valuable_thing')}}</td>
                    	</tr>						
					</table>					
					<table width="100%" class="top top-20" border="1">
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.relation_type')}}</th>
								<th>{{Lang::get('affidavit.name')}}</th>
								<th colspan="2">{{Lang::get('affidavit.valuable_thing_details')}}</th>
								<th>{{Lang::get('affidavit.amount_details')}}</th>
							</tr>
							@foreach($data['valuable_things_details'] as $key => $raw)
							<tr>
								<td class="bordered">{{$raw->relation_type}} </td>
								<td class="bordered">{{$raw->name}}</td>
								<td colspan="2" class="padd-0 bordered">
									<table width="100%" class="top ">
										<tr>
											<th align="left">{{Lang::get('affidavit.valuable_things_type')}}:</th>
											<td align="right">@if($raw->valuable_type) {{$raw->valuable_type}} @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
										<tr>
											<th align="left">{{Lang::get('affidavit.weight')}}:</th>
											<td align="right"> @if($raw->weight) {{$raw->weight}} {{$raw->valuable_weight}}  @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>										
									</table>
								</td>
								<td class="bordered"> @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach							
							</table>
							<table width="100%" class="top-20" border="0">
								<tr>
									<th width="6">(viii)</th>
                    				<td align="left"> {{Lang::get('affidavit.any_other_assets_such_as_value_of_claims_interest')}}</td>
								</tr>								
							</table>								
							<table width="100%" class="top top-20" border="1">
							<tr>
								<th>{{Lang::get('affidavit.relation_type')}}</th>
								<th>{{Lang::get('affidavit.name')}}</th>
								<th colspan="2">{{Lang::get('affidavit.asset_details')}}</th>
								<th>{{Lang::get('affidavit.amount_details')}}</th>
							</tr>
							@foreach($data['other_assets'] as $key => $raw)
							<tr>
								<td class="bordered">{{$raw->relation_type}}</td>
								<td class="bordered">{{$raw->name}}</td>
								<td colspan="2" class="padd-0 bordered">
									<table width="100%" class="">
										<tbody><tr>
											<th align="left">{{Lang::get('affidavit.asset_type')}}:</th>
											<td align="right">@if($raw->asset_type) {{$raw->asset_type}} @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
										<tr>
											<th align="left">{{Lang::get('affidavit.brief_details')}}:</th>
											<td align="right"> @if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>										
									</tbody></table>
								</td>
								<td class="bordered"> @if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach							
							<!--<tr>
								<th>(ix)</th>
								<th colspan="4" align="left">Gross Total value</th>
							</tr>-->
                    </table>


					<table width="100%" class="top top-20" border="0">
						<tr align="left">
                    		<th align="left">{{Lang::get('affidavit.details_of_immovable_assets')}}</th>
                    	</tr>
                    	<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="105" class="pad-35">{{Lang::get('affidavit.note')}}: 1. </th>
                    					<td>{{Lang::get('affidavit.properties_in_joint_ownership_indicating_the_extent_of_joint_ownership_will_also_have_to_be_indicated')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="105" class="pad-35">{{Lang::get('affidavit.note')}}: 2. </th>
                    					<td>{{Lang::get('affidavit.each_land_or_building_or_apartment_should_be_mentioned_separately_in_this_format')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="105" class="pad-35">{{Lang::get('affidavit.note')}}: 3. </th>
                    					<td>{{Lang::get('affidavit.details_should_include_the_interest_in_or_ownership_of_offshore_assets')}}</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr> 
					</table>

					<table width="100%" class="top-20" border="0">
						<tr>
							<th width="6">(i)</th>
							<td align="left">{{Lang::get('affidavit.agricultural_land')}}</td>
						</tr>						
					</table>						

					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no')}}</th>								
								<th>{{Lang::get('affidavit.name')}}</th>								
								<th colspan="2" align="center">{{Lang::get('affidavit.asset_details')}}</th>								
							</tr>
							@foreach($data['agricultural_land'] as $key => $raw)
														
							<tr>
								<td rowspan="8"><span class="block">{{$key+1}}</span></td>
								<td rowspan="8"><span class="block">{{ucfirst($raw->relation_type)}} <br /> {{$raw->name}}</span></td>
							<th align="left">{{Lang::get('affidavit.location')}}:</th>
								<td>@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.survey_no')}}:</th>
								<td>@if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.area')}}</th>
								<td>@if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.whether_inherited_property')}} ({{Lang::get('affidavit.yes')}} {{Lang::get('affidavit.or')}} {{Lang::get('affidavit.no')}})</th>
								<td>@if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
								<td> @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</th>
								<td> @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</th>
								<td> @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.approximate_current_market_value')}}</th>
								<td> @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					
					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(ii)</th>
							<td align="left">{{Lang::get('affidavit.non_agricultural_land')}}</td>
						</tr>						
					</table>

					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no')}}</th>								
								<th>Name</th>								
								<th colspan="2" align="center">{{Lang::get('affidavit.asset_details')}}</th>								
							</tr>
							@foreach($data['non_agricultural_land'] as $key => $raw)
													
							<tr>
								<td rowspan="8"><span class="block">{{$key+1}}</span></td>
								<td rowspan="8"><span class="block">{{ucfirst($raw->relation_type)}} <br /> {{$raw->name}}</span></td>
							<th align="left">{{Lang::get('affidavit.location')}}</th>
								<td>@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.survey_no')}}</th>
								<td>@if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.area')}}</th>
								<td>@if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.whether_inherited_property')}} ({{Lang::get('affidavit.yes')}} {{Lang::get('affidavit.or')}} {{Lang::get('affidavit.no')}})</th>
								<td>@if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
								<td> @if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.cost_of_land_at_the_time_of_purchase')}}</th>
								<td> @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.any_investment_on_the_land_by_way_of_development')}}</th>
								<td> @if($raw->investment_on_land) &#8377; {{$raw->investment_on_land}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.approximate_current_market_value')}}</th>
								<td> @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							
							@endforeach
							
						</tbody>
					</table>
					
					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th width="6">(iii)</th>
							<td align="left">{{Lang::get('affidavit.commercial_buildings')}}</td>
						</tr>						
					</table>

					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no')}}</th>
								<th>{{Lang::get('affidavit.name')}}</th>								
								<th colspan="2" align="center">{{Lang::get('affidavit.asset_details')}}</th>								
							</tr>
							@foreach($data['commercial_buildings'] as $key => $raw)
														
							<tr>
								<td rowspan="9"><span class="block">{{$key+1}}</span></td>
								<td rowspan="9"><span class="block">{{ucfirst($raw->relation_type)}} <br /> {{$raw->name}}</span></td>
							<th align="left">{{Lang::get('affidavit.location')}}</th>
								<td>@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.survey_no')}}</th>
								<td>@if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.area')}}</th>
								<td>@if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.built_up_area')}}</th>
								<td>@if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.whether_inherited_property')}} ({{Lang::get('affidavit.yes')}} {{Lang::get('affidavit.or')}} {{Lang::get('affidavit.no')}})</th>
								<td>@if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
								<td>@if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
								</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.cost_of_property_at_the_time_of_purchase')}}</th>
								<td> @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.any_investment_on_the_property_by_way_of_development')}}</th>
								<td>@if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.approximate_current_market_value')}}</th>
								<td> @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							
							@endforeach
							
						</tbody>
					</table>
					
					<table width="100%" class="top-20" border="0">
						<tr>
							<th align="left" width="6">(iv)</th>
							<td align="left">{{Lang::get('affidavit.residential_buildings')}}</td>
						</tr>						
					</table>

					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no')}}</th>
								<th>{{Lang::get('affidavit.name')}}</th>								
								<th colspan="2" align="center">{{Lang::get('affidavit.asset_details')}}</th>								
							</tr>
							@foreach($data['residential_buildings'] as $key => $raw)
														
							<tr>
								<td rowspan="9"><span class="block">{{$key+1}}</span></td>
								<td rowspan="9"><span class="block">{{ucfirst($raw->relation_type)}} <br /> {{$raw->name}}</span></td>
							<th align="left">{{Lang::get('affidavit.location')}}</th>
								<td>@if($raw->location) {{$raw->location}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.survey_no')}}</th>
								<td>@if($raw->survey_number) {{$raw->survey_number}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.area')}}</th>
								<td>@if($raw->area) {{$raw->area}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.built_up_area')}}</th>
								<td>@if($raw->built_up_area) {{$raw->built_up_area}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.whether_inherited_property')}} ({{Lang::get('affidavit.yes')}} {{Lang::get('affidavit.or')}} {{Lang::get('affidavit.no')}})</th>
								<td>@if($raw->inherited_property) {{$raw->inherited_property}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.date_of_purchase_in_case_of_self_acquired_property')}}</th>
								<td>@if((@$raw->date_of_purchase) && (@$raw->date_of_purchase != '0000-00-00 00:00:00')) {{\Carbon\Carbon::parse($raw->date_of_purchase)->format('d/m/Y')}} @else {{Lang::get('affidavit.nil')}} @endif
								</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.cost_of_property_at_the_time_of_purchase')}}</th>
								<td> @if($raw->cost_at_purchase_time) &#8377; {{$raw->cost_at_purchase_time}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.any_investment_on_the_property_by_way_of_development')}}</th>
								<td>@if($raw->investment_on_buildings) &#8377; {{$raw->investment_on_buildings}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.approximate_current_market_value')}}</th>
								<td> @if($raw->approx_current_market_value) &#8377; {{$raw->approx_current_market_value}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							
							@endforeach
							
						</tbody>
					</table>

					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(v)</th>
							<td align="left">{{Lang::get('affidavit.other_assets')}}</td>
						</tr>
					</table>

					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no')}}</th>								
								<th>{{Lang::get('affidavit.name')}}</th>								
								<th colspan="2" align="center">{{Lang::get('affidavit.asset_details')}}</th>								
							</tr>								
							@foreach($data['other_immovable'] as $key => $raw)
							<tr>
								<td rowspan="2"><span class="block">{{$key+1}}</span></td>
								<td rowspan="2"><span class="block">{{ucfirst($raw->relation_type)}} <br /> {{$raw->name}}</span></td>
								<th align="left">{{Lang::get('affidavit.brief_details')}}</th>
								<td>@if($raw->brief_details) {{$raw->brief_details}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							<tr>
								<th align="left">{{Lang::get('affidavit.amount')}}</th>
								<td>@if($raw->amount) &#8377; {{$raw->amount}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach		
						</tbody>
					</table>						

                    <table width="100%" class="top-20 top">
                    	<tr>
                    		<td>
                    			<span class="w-20 bold">(8)</span>
                    			<span class="inBlock bold">{{Lang::get('affidavit.i_give_herein_below_the_details_of_liabilities_dues_to_public_financial_institutions_and_government')}}-</span>
                    		</td>
                    	</tr>
                    	<tr>
                    		<td>
                    			<span class="w-20"></span>
                    			<span class="inBlock">({{Lang::get('affidavit.note')}}: {{Lang::get('affidavit.please_give_separate_details_of_name_of_bank_institution_entity_or_individual_and_amount_before_each_item')}}) </span>
                    		</td>
                    	</tr>
                    	                 	
                    	<!--<tr>
                    		<td class="padd-0">
                    			<table width="100%">
                    				<tr>
                    					<th width="95">{{Lang::get('affidavit.note')}}: 1. </th>
                    					<td align="left">Assets in joint name indicating the extent of joint ownership will also have to be given.</td>
                    				</tr>
                    			</table>
                    		</td>
                    	</tr>-->
                    </table>


					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th>(i)</th>
                    		<td align="left">{{Lang::get('affidavit.loan_or_dues_to_bank_financial_institution')}}<br /><br />{{Lang::get('affidavit.name_of_bank_or_financial_institution_amount_outstanding_nature_of_loan')}}</td>
                    		<th>
                    	</tr>						
					</table>
                     
                    <table class="top top-20" width="100%" border="1">
							<tbody>								
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.name')}}</th>
								<th colspan="2">{{Lang::get('affidavit.name_of_bank_or_financial_institution')}}</th>
								<th>{{Lang::get('affidavit.nature_of_loan')}}</th>
								<th>{{Lang::get('affidavit.account_details')}}</th>
							</tr>
							@foreach($data['l_loan_details'] as $key => $raw)
							<tr>
								<td class="bordered"><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td class="bordered" colspan="2"> @if($raw->bank_inst_name){{$raw->bank_inst_name}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td class="bordered"><span class="block">@if($raw->loan_type){{$raw->loan_type}}@else {{Lang::get('affidavit.nil')}} @endif</span></td>
								<td class="padd-0 bordered">
									<table width="100%">
										<tr>
											<th align="left">{{Lang::get('affidavit.account_type')}}:</th>
											<td>@if($raw->loan_account_type) 
											@if($raw->loan_account_type == 'Joint')												
											{{$raw->loan_account_type}} {{$raw->joint_account_with_name}} 
											@else
												{{$raw->loan_account_type}} 
											@endif
										@else {{Lang::get('affidavit.nil')}} 
										@endif											
											</td>
										</tr>
										<tr>
											<th align="left">{{Lang::get('affidavit.amount')}}:</th>
											<td> @if($raw->outstanding_amount)&#8377; {{$raw->outstanding_amount}}@else {{Lang::get('affidavit.nil')}} @endif</td>
										</tr>
									</table>
								</td>
							</tr>
							@endforeach							
						</tbody>
					</table>
					
					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th>(ii)</th>
                    		<td align="left">{{Lang::get('affidavit.loan_or_dues_to_any_other_individuals_entity_other_than_mentioned_above')}}</td>
                    	</tr>						
					</table>
					
					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.name')}}</th>
								<th>{{Lang::get('affidavit.name_of_individual_entity') }}</th>
                                <th>{{Lang::get('affidavit.nature_of_loan') }}</th>
                                <th>{{Lang::get('affidavit.loan_account_type') }}</th>
                                <th>{{Lang::get('affidavit.amount_outstanding') }}</th>
							</tr>
							@foreach($data['l_loan_individual'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td>@if($raw->individual_entity_name) {{$raw->individual_entity_name}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->loan_type) {{$raw->loan_type}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->loan_account_type) 
											@if($raw->loan_account_type == 'Joint')												
											{{$raw->loan_account_type}} {{$raw->joint_account_with_name}} 
											@else
												{{$raw->loan_account_type}} 
											@endif
										@else {{Lang::get('affidavit.nil')}} 
										@endif</td>
								<td>@if($raw->outstanding_amount)&#8377; {{$raw->outstanding_amount}}@else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach
						</tbody>
					</table>					
					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(iii)</th>
                    		<td align="left"><u><span class="bold"> {{Lang::get('affidavit.government_dues')}}:</span></u> &nbsp;&nbsp;{{Lang::get('affidavit.dues_to_departments_dealing_with_government_accommodation')}}</td>
                    	</tr>						
					</table>					
					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.government_department_name') }}</th>
                                <th>{{Lang::get('affidavit.due_details') }}</th>
                                <th>{{Lang::get('affidavit.amount') }}</th>							
							</tr>
							@foreach($data['l_govt_dues'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td>
								@if($raw->govt_dept_name) 
								
								{{$raw->govt_dept_name}}<br>
                                            @if(!empty($raw->other_dept))
                                                    {{$raw->other_dept}}
                                                @endif
								@else {{Lang::get('affidavit.nil')}} @endif				
								</td>
								<td>
                                            @if($raw->govt_dept_name_code==1)
                                                @if($raw->is_government_accomodation==0)
                                                    {{"No"}}
                                                @else
                                                    <label>{{Lang::get('affidavit.address_of_the_government_accommodation') }}:</label><br>
                                                     <strong>{{$raw->government_accomodation_address}}</strong>
                                                      <label>{{Lang::get('affidavit.there_is_no_dues_payable') }}</label>
                                                    <ol type="A">
                                                      <li>{{Lang::get('affidavit.rent') }}</li>
                                                      <li>{{Lang::get('affidavit.electricity_charges') }}</li>
                                                      <li>{{Lang::get('affidavit.water_charges') }}</li>
                                                      <li>{{Lang::get('affidavit.telephone_charges_as_on') }} : <strong>{{\Carbon\Carbon::parse($raw->telephone_charges)->format('d/m/Y')}}</strong><br>
													  <label>{{Lang::get('affidavit.attached_no_dues_certificate') }}</label>
													  
                                                       <!-- @if(!empty($raw->no_dues_file))
                                                        <label>No dues file:</label> 
                                                        <a href="{{ url('/').'/affidavit/uploads/govt_dues_liabitilies/'.$raw->no_dues_file}}" target="_new">Click here to open the file</a>
                                                        @endif--> </li>
                                                    </ol>
                                                @endif
                                            @else
                                                @if($raw->due_details) {{$raw->due_details}}@else {{Lang::get('affidavit.nil')}} @endif
                                            @endif</td>
								<td>@if($raw->amount)&#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach
						</tbody>
					</table>				
					
					<!--<table width="100%" class="top-20" border="0">
						<tr>
                    		<th colspan="5">(iii) Dues to department dealing with Government transport (including aircrafts and helicopters)</th>
                    	</tr>
						
					</table>

					<table width="100%" class="top-20" border="1">
							<tbody>
							<tr>
								<th>Name</th>
								<th><span class="block">Government transport</span> (including aircrafts and helicopters)</th>
								<th>Due Details</th>
								<th>Amount</th>								
							</tr>
							<tr>								
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
						</tbody>
					</table>

					<table width="100%" class="top-20" border="0">
						<tr>
                    		<th colspan="5">(iv) Income Tax dues</th>
                    	</tr>
						
					</table>
					<table width="100%" class="top-20" border="1">
							<tbody>
							
							<tr>
								<th>Name</th>
								<th>Government Department Name</th>
								<th>Due Details</th>
								<th>Amount</th>								
							</tr>
							<tr>								
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
						</tbody>
					</table>

					
					<table width="100%" class="top-20" border="0">
						<tr>
                    		<th colspan="5">(v) GST dues</th>
                    	</tr>						
					</table>

					<table width="100%" class="top top-20" border="1">
							<tbody>
							
							<tr>
								<th>Name</th>
								<th>GST No.</th>
								<th>Due Details</th>
								<th>Amount</th>								
							</tr>
							<tr>								
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
						</tbody>
					</table>
					
					<table width="100%" class="top-20" border="0">
						<tr>
                    		<th colspan="5">(vi) Municipal/Property tax dues</th>
                    	</tr>						
					</table>

					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr>
								<th>Name</th>
								<th>Municipal/Property tax</th>
								<th>Due Details</th>
								<th>Amount</th>								
							</tr>
							<tr>								
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
						</tbody>
					</table>
					
					<table width="100%" class="top-20" border="0">
						<tr>
                    		<th colspan="5">(vii) Any other dues</th>
                    	</tr>						
					</table>

					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr>
								<th>Name</th>
								<th>Dues Name</th>
								<th>Due Details</th>
								<th>Amount</th>								
							</tr>
							<tr>								
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
						</tbody>
					</table>-->

					<!--<table width="100%" class="top-20" border="0">
						<tr>
                    		<th colspan="5">(viii) Grand total of all Government dues</th>
                    	</tr>						
					</table>-->					
					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(iv)</th>
                    		<td align="left">{{Lang::get('affidavit.any_other_liabilities') }}</td>
                    	</tr>						
					</table>
					<table width="100%" class="top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>Name</th>
								<th colspan="2">{{Lang::get('affidavit.authority_name') }}</th>
                                <th>{{Lang::get('affidavit.brief_details') }}</th>
                                <th>{{Lang::get('affidavit.amount') }}</th>							
							</tr>
							@foreach($data['l_other_liabilities'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td colspan="2">@if($raw->authority_name) {{$raw->authority_name}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->details) {{$raw->details}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->amount) &#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<table width="100%" class="top top-20" border="0">
						<tr>
                    		<th width="6">(v)</th>
                    		<td align="left">{{Lang::get('affidavit.whether_any_other_liabilities_are_dispute') }}</td>
                    	</tr>						
					</table>
					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.authority_name') }}</th>
                                <th>{{Lang::get('affidavit.brief_details') }}</th>
                                <th>{{Lang::get('affidavit.amount') }}</th>							
							</tr>
							@foreach($data['l_liabilities_disputes'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td>@if($raw->authority_name) {{$raw->authority_name}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->details) {{$raw->details}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->amount) &#8377; {{$raw->amount}}@else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach
						</tbody>
					</table>
                     <table class="top top-20" width="100%">
                    	<tr>
                    		<th width="4">(9)</th>
                    		<th align="left">
                    			 {{Lang::get('affidavit.details_of_profession_or_occupation') }}: 
                    			<!-- <ul class="list">
                    			 	<li>(a) Self <input type="text" readonly  name=""> </li>
                    			 	<li>(b) Spouse <input type="text" readonly  name=""></li>
                    			 </ul>-->	
                    		</th>
                    	</tr>
                    	<!--<tr>
                    		<td width="4"><b>(9A)</b></td>
                    		<td>
                    			  Details of source(s) of income:
                    			 <ul class="list">
                    			 	<li>(a) Self <input type="text" readonly  name=""> </li>
                    			 	<li>(b) Spouse <input type="text" readonly  name=""></li>
                    			 	<li>(c) Source of income, if any, of dependents,<input type="text" readonly  name=""></li>
                    			 </ul>	
                    		</td>
                    	</tr>-->						
					</table>					
					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(i)</th>
							<td align="left">{{Lang::get('affidavit.occupation_of_self_and_spouse') }}</td>
						</tr>						
					</table>	
					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no') }}</th>
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.occupation') }}</th>								
							</tr>
							@foreach($data['occupation'] as $key => $raw)							
							<tr>
								<td>{{$key+1}}</td>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td>@if($raw->occupation) {{$raw->occupation}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
							</tr>								
							@endforeach
						</tbody>
					</table>
					
					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(ii)</th>
							<td align="left">{{Lang::get('affidavit.source_of_income_of_all_dependants') }}</td>
						</tr>						
					</table>

					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no') }}</th>
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.source_of_income') }}</th>								
							</tr>
							@foreach($data['source_of_income'] as $key => $raw)							
							<tr>
								<td>{{$key+1}}</td>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td>@if($raw->source_of_income) {{$raw->source_of_income}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
							</tr>								
							@endforeach
						</tbody>
					</table>
					
					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th width="6">(iii)</th>
							<td align="left">{{Lang::get('affidavit.details_of_contract_with_govt_or_public_company') }}</th>
						</tr>						
					</table>

					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no') }}</th>
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.name_of_government_or_public_company') }}</th>
                                <th>{{Lang::get('affidavit.details_of_contract_entered') }}</th>
							</tr>
							@foreach($data['govt_public_company'] as $key => $raw)							
							<tr>
								<td>{{$key+1}}</td>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td>@if($raw->govt_public_company) {{$raw->govt_public_company}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
								<td>@if($raw->details) {{$raw->details}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
							</tr>								
							@endforeach
						</tbody>
					</table>
					
					<table width="100%" class="top-20 top" border="0">
						<tr>
							<th width="6">(iv)</th>
							<td align="left"> {{Lang::get('affidavit.details_of_contracts_entered_into_by_hindu') }}</td>
						</tr>						
					</table>
					
					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no') }}</th>
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.name_of_hindu_undivided_family_or_trust') }}</th>
                                <th>{{Lang::get('affidavit.details_of_contract_entered') }}</th>
							</tr>
							@foreach($data['huf_trust'] as $key => $raw)							
							<tr>
								<td>{{$key+1}}</td>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>	
								<td>@if($raw->huf_trust_contracts) {{$raw->huf_trust_contracts}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
								<td>@if($raw->details) {{$raw->details}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
							</tr>								
							@endforeach
						</tbody>
					</table>
					
					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(v)</th>
							<td align="left"> {{Lang::get('affidavit.details_of_contracts_entered_into_by_partnership_firms') }}</td>
						</tr>						
					</table>

					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no') }}</th>
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.name_of_partnership_firms') }}</th>
                                <th>{{Lang::get('affidavit.details_of_contract_entered') }}</th>	
							</tr>
							@foreach($data['partnership_firm'] as $key => $raw)	
							<tr>
								<td>{{$key+1}}</td>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>	
								<td>@if($raw->name_partnership_firm) {{$raw->name_partnership_firm}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
								<td>@if($raw->details) {{$raw->details}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>								
							@endforeach
						</tbody>
					</table>	
					<table width="100%" class="top top-20" border="0">
						<tr>
							<th width="6">(vi)</th>
							<td align="left"> {{Lang::get('affidavit.details_of_contracts_entered_into_by_private_companies') }}</td>
						</tr>						
					</table>					
					<table width="100%" class="top-20 top" bgcolor="#fff" border="1">
						<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.sr_no') }}</th>
								<th>{{Lang::get('affidavit.name') }}</th>
								<th>{{Lang::get('affidavit.name_of_private_company') }}</th>
                                <th>{{Lang::get('affidavit.details_of_contract_entered') }}</th>	
							</tr>
							@foreach($data['private_company'] as $key => $raw)							
							<tr>
								<td>{{$key+1}}</td>
								<td><span class="block">{{ucfirst($raw->relation_type)}} <br />{{$raw->name}}</span></td>
								<td>@if($raw->name_private_company) {{$raw->name_private_company}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
								<td>@if($raw->details) {{$raw->details}} @else {{Lang::get('affidavit.nil')}} @endif</td>	
							</tr>								
							@endforeach
						</tbody>
					</table>
					<table class="top top-20" width="100%" border="0">	
						
                    	<!--<tr>
                    		<td width="4"><b>(9B)</b></td>
                    		<td>
                    			   Contracts with appropriate Government and any public company or companies
                    			 <ul class="list">
                    			 	<li>(a) details of contracts entered by the candidate <input type="text" readonly  name=""> </li>
                    			 	<li>(b) details of contracts entered into by spouse <input type="text" readonly  name=""></li>
                    			 	<li>(c) details of contracts entered into by dependents<input type="text" readonly  name=""></li>
                    			 	<li>(d) details of contracts entered into by Hindu Undivided Family or trust in which the candidate or spouse or dependents have interest <input type="text" readonly  name=""></li>
                    			 	<li>(e) details of contracts, entered into by Partnership Firms in which candidate or spouse or dependents are partners <input type="text" readonly  name=""></li>
                    			 	<li>(f) details of contracts, entered into by private companies in which candidate or spouse or dependents have share <input type="text" readonly  name=""></li>
                    			 </ul>	
                    		</td>
                    	</tr> -->
						
                    	<tr>
                    		<th width="4" align="left">(10)</th>
                    		<th align="left">{{Lang::get('affidavit.my_education_qualification_is_as_under')}}</th>
                    	</tr>
                    	<!--<tr>
                    		<td colspan="2"><b><input type="text" readonly  name="" class="w-100"></b></td>
                    	</tr>	-->
                    	<tr>
                    		<td colspan="2">
                    		{{Lang::get('affidavit.give_details_of_highest_school_university_education_mentioning')}}
                    	    </td>
                        </tr>
                    </table>
					
					<table width="100%" class="top top-20" border="1">
							<tbody>
							<tr class="thHeading">
								<th>{{Lang::get('affidavit.qualification')}}</th>
								<th>{{Lang::get('affidavit.full_form_certificate')}}</th>
								<th>{{Lang::get('affidavit.school_college')}}</th>
								<th>{{Lang::get('affidavit.board_university')}}</th>
								<th>{{Lang::get('affidavit.year_of_completion')}}</th>								
							</tr>
							@if(count($data['education']) > 0)
							@foreach($data['education'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->qualification)}}</span></td>
								<td>{{$raw->full_form_course}}</td>
								<td>{{$raw->school_college}}</td>
								<td>{{$raw->board_univ}}</td>
								<td>{{$raw->q_year}}</td>
							</tr>
							@endforeach
							@else
							<tr>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
							@endif
						</tbody>
					</table>

					<div style="page-break-after:always;"></div>
					
                     <table width="100%" class="top top-20" border="0">
                    	<caption align="center" align="center" style="width:100%; text-align:center; margin:20px auto 0;  line-height:25px; display: block;">
						<h3 style="text-decoration: underline; font-weight:bold; color:black;font-size:22px;">{{Lang::get('affidavit.part_b')}}</h3>	
					    </caption>					    
					 </table>   
					 <table width="100%" border="0">					 	
						<tr>
							<th width="4" align="left"><b>(11).</b></th>
							<td align="left">{{Lang::get('affidavit.abstract_of_the_details_given_in')}}</td>
						</tr>					    
					 </table>

                    <table width="100%" class="top top-20" border="1">				    
					    <tbody>
					    	<tr>
					    		<td>1.</td>
					    		<td colspan="4">{{Lang::get('affidavit.name_of_the_candidate')}} </td>
					    		<td colspan="4">{{Lang::get('affidavit.sh_smt_kum')}} {{@$data['cand_details']->cand_name}}</td>
					    	</tr>
					    	<tr>
					    		<td>2.</td>
					    		<td colspan="4">{{Lang::get('affidavit.full_postal_address')}} </td>
					    		<td colspan="4">{{@$data['cand_details']->postal_address}}</td>
					    	</tr>
					    	<tr>
					    		<td>3.</td>
					    		<td colspan="4">{{Lang::get('affidavit.number_and_name_of_the_constituency_and_state')}}</td>
					    		<td colspan="4">@if(@$data['cand_details']->pc_no && @$data['cand_details']->st_code)
								{{@$data['cand_details']->pc_no}}-{{getpcbypcno(@$data['cand_details']->st_code,@$data['cand_details']->pc_no)->PC_NAME}},@endif
							@if(@$data['cand_details']->st_code){{getstatebystatecode(@$data['cand_details']->st_code)->ST_NAME}}@endif</td>
					    	</tr>
					    	<tr>
					    		<td>4.</td>
					    		<td colspan="4">{{Lang::get('affidavit.name_of_the_political_party_which_set_up_the_candidate')}}</td>
					    		<td colspan="4">@if(@$data['cand_details']->partyabbre){{getpartybyid(@$data['cand_details']->partyabbre)->PARTYNAME}} @else Independent @endif</td>
					    	</tr>
					    	<tr>
					    		<td>5.</td>
					    		<td colspan="4">{{Lang::get('affidavit.total_number_of_pending_criminal_cases')}}</td>
					    		<td colspan="4">{{count($data['pending_cases_count'])}}</td>
					    	</tr>
					    	<tr>
					    		<td>6.</td>
					    		<td colspan="4">{{Lang::get('affidavit.total_number_of_cases_in_which_convicted')}} </td>
					    		<td colspan="4">{{count($data['imprisonment_criminal_count'])}}</td>
					    	</tr>

					    	<tr>
					    		<td rowspan="{{count($data['pan_details'])+1}}" >7.</td>
					    		<td colspan="2"></td>
					    		<td colspan="2">{{Lang::get('affidavit.pan_of')}}</td>
					    		<td colspan="3">{{Lang::get('affidavit.year_for_which_last_income_tax_return_filed')}}</td>
					    		<td>{{Lang::get('affidavit.total_income_shown')}}</td>
					    	</tr>
							
							
							@foreach($data['pan_details'] as $key => $raw)
							
							<tr>
								<td colspan="2">{{$key+1}}. @if($raw->relation_type == 'self' || $raw->relation_type == 'Self') {{Lang::get('affidavit.candidate')}} @else {{$raw->relation_type}} @endif</td>
								<td colspan="2">{{$raw->name}}</td>
								<td colspan="3"> @if($raw->financial_year) {{$raw->financial_year}} @else {{Lang::get('affidavit.nil')}} @endif</td>
								<td>@if($raw->financialyr1) &#8377;{{$raw->financialyr1}} @else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							
							@endforeach
							<tr><td rowspan="{{count($data['movable_assets_total'])+count($data['immoveable_assets_total']) +6}}">8</td><th colspan="8" >{{Lang::get('affidavit.details_of_assets_and_liabilities')}}</th></tr>					    	
					    	<tr>
					    		<td colspan="1" rowspan="{{count($data['movable_assets_total'])+2}}">{{Lang::get('affidavit.a')}}</td>
					    		<th colspan="7">{{Lang::get('affidavit.movable_assets_total_value')}}</th>
					    	</tr>	
					    		
							<tr>
					    		<th colspan="4">{{Lang::get('affidavit.name')}}</th>
					    		<th colspan="3">{{Lang::get('affidavit.total_value')}}</th>
					    	</tr>	
							@foreach($data['movable_assets_total'] as $raw)
							<tr>
					    		<td colspan="4">{{$raw->NAME}} ({{$raw->RELATION_TYPE}})</td>
					    		<td colspan="3">@if($raw->total) &#8377;{{$raw->total}} @else {{Lang::get('affidavit.nil')}} @endif</td>
					    	</tr>	
							@endforeach
								
					    	<tr>
					    		<td colspan="1" rowspan="{{count($data['immoveable_assets_total'])+3}}">{{Lang::get('affidavit.b')}}</td>
					    		<th colspan="7">{{Lang::get('affidavit.immovable_assets')}}</th>
					    		
					    	</tr>
							
					    	<tr>
					    		<td colspan="1" rowspan="2">{{Lang::get('affidavit.name')}}</td>
					    		<td colspan="1" rowspan="2">{{Lang::get('affidavit.purchase_price_of_self_acquired_immovable_property')}}</td>
					    		<td colspan="2" rowspan="2">{{Lang::get('affidavit.development_construction_cost_of_immovable_property_after_purchase')}}</td>
					    		<td colspan="2">{{Lang::get('affidavit.approximate_current_market_price')}}</td>
								<td rowspan="2">{{Lang::get('affidavit.other')}}</td>
					    	</tr>

					    	<tr>
					    		<td colspan="1">{{Lang::get('affidavit.self_acquired_assets_total_value')}}</td>
					    		<td colspan="1">{{Lang::get('affidavit.inherited_assets_total_value')}}</td>
					    	</tr>

							@foreach($data['immoveable_assets_total'] as $raw)
					    	<tr>
					    		<td colspan="1">{{$raw->NAME}} ({{$raw->RELATION_TYPE}})</td>
					    		<td colspan="1">{{$raw->purcahse_price_self_acquired_immov}}</td>
					    		<td colspan="2">{{$raw->Investment_Immov}}</td>
					    		<td colspan="1">{{$raw->self_acquired_Assets_Value}}</td>
					    		<td colspan="1">{{$raw->Inherited_assets_Value}}</td>
					    		<td colspan="1">{{$raw->Other_Immov_Asset}}</td>
					    	</tr>
					    	@endforeach

					    	<tr>
					    		<td rowspan="{{count($data['liabilites_total'])+2}}">9</td>
					    		<th colspan="8">{{Lang::get('affidavit.liabilities')}}</th>
					    		
					    	</tr>
					    	<tr>
					    		<th>{{Lang::get('affidavit.name')}}</th>
					    		<th colspan="3">{{Lang::get('affidavit.government_dues_total')}}</th>
					    		<th colspan="4">{{Lang::get('affidavit.loan_from_bank_financial_other')}}</th>
					    	</tr>
					    	@foreach($data['liabilites_total'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->RELATION_TYPE)}} <br />{{$raw->NAME}}</span></td>
								<td colspan="3"> @if($raw->Govt_dues) &#8377; {{$raw->Govt_dues}}@else {{Lang::get('affidavit.nil')}} @endif</td>
								<td colspan="4">@if($raw->Total_Loan + $raw->Other_Amt) &#8377; {{$raw->Total_Loan + $raw->Other_Amt}}@else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach

					    	<tr>
					    		<td rowspan="{{count($data['l_liabilities_disputes'])+2}}">10</td>
					    		<th colspan="8">{{Lang::get('affidavit.liabilities_that_are_under_dispute')}}</th>
					    		
					    	</tr>
					    	<tr>
					    		<th>{{Lang::get('affidavit.name')}}</th>
					    		<th colspan="3">{{Lang::get('affidavit.government_dues_total')}}</th>
					    		<th colspan="4">{{Lang::get('affidavit.loan_from_bank_financial_other')}}</th>
					    	</tr>
					    	@foreach($data['liabilites_total'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->RELATION_TYPE)}} <br />{{$raw->NAME}}</span></td>
								<td colspan="3"> {{Lang::get('affidavit.nil')}}</td>
								<td colspan="4">@if($raw->Other_Amt_Dispute) &#8377; {{$raw->Other_Amt_Dispute}}@else {{Lang::get('affidavit.nil')}} @endif</td>
							</tr>
							@endforeach
					    	<tr>
					    		<td rowspan="2"><b>11.</b></td>
					    		<td colspan="8"><b class="block">{{Lang::get('affidavit.highest_educational_qualification')}}</b><br>
								{{Lang::get('affidavit.give_details_of_highest_school_university_education_mentioning')}}

								</td>
					    	</tr>
							<tr>
						<td class="padd-0 bdrLeass" colspan="8">
							<table width="100%" class="" border="1">
							<tbody>
							<tr>
								<th>{{Lang::get('affidavit.qualification')}}</th>
								<th>{{Lang::get('affidavit.full_form_certificate')}}</th>
								<th>{{Lang::get('affidavit.school_college')}}</th>
								<th>{{Lang::get('affidavit.board_university')}}</th>
								<th>{{Lang::get('affidavit.year_of_completion')}}</th>								
							</tr>
							@if(count($data['education']) > 0)
							@foreach($data['education'] as $key => $raw)
							<tr>
								<td><span class="block">{{ucfirst($raw->qualification)}}</span></td>
								<td>{{$raw->full_form_course}}</td>
								<td>{{$raw->school_college}}</td>
								<td>{{$raw->board_univ}}</td>
								<td>{{$raw->q_year}}</td>
							</tr>
							@break;
							@endforeach
							@else
							<tr>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
								<td>{{Lang::get('affidavit.nil')}}</td>
							</tr>
							@endif
						</tbody>
					</table>
				</td>
							</tr>
					    </tbody>
                    </table>
					
					<div style="page-break-after:always;"></div>
					
					

                    <table width="100%" class="top top-20" border="0">
                    	
						<tr>
                    		<th  colspan="3" align="center" style="text-align:center">{{Lang::get('affidavit.verification')}}</th>
						</tr>
                    	<tbody>
                    		<tr>
                    			<td colspan="3"><span class="pad-20">&nbsp;&nbsp;&nbsp;{{Lang::get('affidavit.i_the_deponent_above_named')}}</span></td>
                    		</tr>
                    		<tr>
                    			<td colspan="3">{{Lang::get('affidavit.there_is_no_case_of_conviction_or_pending_case_against_me')}}</td>
                    		</tr>
                    		<tr>
                    			<td colspan="3">{{Lang::get('affidavit.i_my_spouse_or_my_dependents_do_not_have_any_asset')}}</td>
                    		</tr>
							<?php if(session()->get('locale') == 'hi') { ?>
								<tr>
									<td colspan="2"> आज तारीख.......................... को सत्‍यापित किया गया। </td>                   			
								</tr>
							<?php } else { ?>
                    		<tr>
                    			<td colspan="2"> Verified at <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>                    				
								<td align="right"> this the <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>                    			
                    		</tr>
                    		<tr>
                    			<td colspan="3">day of <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td>
                    		</tr>
							<?php } ?>
							
							
                    		<tr> 
                    			<td colspan="3" align="right"><span class="bold"> {{Lang::get('affidavit.deponent')}}</span></b>
                    			
								<hr style="height:1px;border-width:0;color:#101010;background-color:#101010; margin-top: 10px;">
								</td>
                    		</tr>
                    	</tbody>
                    </table>
					

                   <table width="100%" class="top-20 top" border="0">
                   	<tbody>
                   	<tr>
                   		<th width="90">{{Lang::get('affidavit.note')}}: 1.</th>
                   		<td align="left">{{Lang::get('affidavit.affidavit_should_be_filed_latest_by_3_pm_on_the_last_day_of_filing_nominations')}}
						</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 2.</th>
                   		<td>{{Lang::get('affidavit.affidavit_should_be_sworn_before_an_oath_commissioner_or_magistrate_of_the_first_class_or_before_a_notary_public')}}</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 3.</th>
                   		<td>{{Lang::get('affidavit.all_columns_should_be_filled_up_and_no_column_to_be_left_blank')}}</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 4.</th>
                   		<td>{{Lang::get('affidavit.the_affidavit_should_be_either_typed_or_written_legibly_and_neatly')}}
						</td>
                   	</tr>
                   	<tr>
                   		<th>{{Lang::get('affidavit.note')}}: 5.</th>
                   		<td>{{Lang::get('affidavit.each_page_of_the_affidavit_should_be_signed_by_the_deponent')}}</td>
                   	</tr>
                   	</tbody>	
                   </table>