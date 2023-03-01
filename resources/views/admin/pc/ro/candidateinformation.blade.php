@extends('admin.layouts.theme')
@section('content') 
 
<link href="{{ asset('theme/main.css') }}" rel="stylesheet">

<div class="container-fluid">
  <!-- Start parent-wrap div -->  
   <div class="parent-wrap">
    <!-- Start child-area Div --> 
    <div class="child-area">
   <div class="nw-crte-usr">
         <div class="head-title">
          <h3><i><img src="{{ asset('theme/images/icons/tab-icon-010.png')}}" /></i>Verify Nomination</h3>
         </div>
                <ul class="steps" id="progressbar">
                  <li class="step">QR SCAN</li>
                  <li class="step active">Verify Nomination</li>
                  <li class="step">Decision by RO</li>
                  <li class="step">Final Receipt</li>
                  <li class="step">Print Receipt</li>
                </ul>

    <div class="col-lg-12">
      
                     
          <form class="form-horizontal" id="election_form" method="POST"  action="{{url('ro/candidatevalidation') }}" >
                {{ csrf_field() }} 
            <input type="hidden" name="candidate_id" value="{{$caddata->candidate_id}}">
            <input type="hidden" name="qrcode" value="{{$caddata->qrcode}}">
            <div class="nomination-fieldset">
              <div class="nomination-parts1">
              <div class="nomination-heading"> NOMINATION PAPER<br/> <em>Election to the House of the People</em> </div>
               
             
              </div><!-- Close nomination parts1 -->
              <div class="nomination-parts box recognised">
                
                <div class="nomination-form-heading">
                  <!--STRIKE OFF PART I OR PART II BELOW WHICHEVER IS NOT APPLICABLE<br/>-->
                  <strong>PART I</strong><br/>
                  (To be used by candidate set up by recognised political party)
                </div>
               <?php  
                 if(!empty($caddata->candidate_residence_pcno))
                   $candrpc = app(App\commonModel::class)->getpcname($caddata->candidate_residence_stcode,$caddata->candidate_residence_pcno);
                 else
                   $candrpc='';
                  $candrac = app(App\commonModel::class)->getacname($caddata->candidate_residence_stcode,$caddata->candidate_residence_acno);
                  //dd($caddata);
                ?>
                 
                <div class="nomination-detail">

                   <p>  @if(!empty($pc->PC_NAME)) I nominate as a candidate for election to the House of the People from the &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$pc->PC_NAME}}  </span> &nbsp;&nbsp;&nbsp; Parliamentary constituency. @endif <br/>
                   
                   
                  <span class="{{ $errors->has('cand_name') ? ' has-error' : '' }}" > 
                  Candidate's name &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_name}}  </span> &nbsp;&nbsp;&nbsp; 

                  Father's/Mother's/Husband's name &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->candidate_father_name}}</span> &nbsp;&nbsp;&nbsp;
                  
                  His postal address &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->candidate_residence_address}} </span> &nbsp;&nbsp;&nbsp;
                   <br/> His name is entered at S.No &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_sl_no}} </span> &nbsp;&nbsp;&nbsp; in Part No &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->candidate_residance_part_no}} </span> &nbsp;&nbsp;&nbsp; of the electoral roll for &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$candrac->AC_NAME}} </span> &nbsp;&nbsp;&nbsp; (Assembly constituency comprised within) 
                   @if(!empty($candrpc->PC_NAME)) &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$candrpc->PC_NAME}} </span> &nbsp;&nbsp;&nbsp; Parliamentary Constituency. @endif <br/>
                  
                  My name is &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_name}} </span> &nbsp;&nbsp;&nbsp; and it is entered at S.No &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_slno}} </span> &nbsp;&nbsp;&nbsp; in Part No &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_partno}} </span> &nbsp;&nbsp;&nbsp; of the electoral roll for &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_assembly}} </span> &nbsp;&nbsp;&nbsp; *(Assembly constituency comprised within) &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_parlimentry}} </span> &nbsp;&nbsp;&nbsp; Parliamentary constituency.</p>
                </div>


                <div class="nomination-signature">
                  <span class="nomination-date left">Date &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{Carbon\Carbon::parse($caddata->cand_apply_date)->format('d-m-Y')  }} </span> &nbsp;&nbsp;&nbsp;
                  </span>
                  <span class="nomination-sign right">Signature of Proposer &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_name}} </span> &nbsp;&nbsp;&nbsp;</span>
                </div>
              </div><!-- Nomination recognised part -->
               
                
                <div class="nomination-form-heading">
                  <strong>PART II</strong><br/>
                  (To be used by candidate NOT set up by recognised political party) 
                </div>
                  
                <div class="nomination-detail">
                   <p>We hereby nominate as candidate for election to the House of the People from the  &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$pc->PC_NAME}}  </span> &nbsp;&nbsp;&nbsp; Parliamentary Constituency<br/>
                   
                  Candidate's name  &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_name}} </span> &nbsp;&nbsp;&nbsp;
                   
                  Father's/Mother's/Husband's name  &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->candidate_father_name}} </span> &nbsp;&nbsp;&nbsp;
                  His postal address  &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->candidate_residence_address}} </span> &nbsp;&nbsp;&nbsp;
                  <br/>
                   
                  His name is entered at S.No &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_sl_no}} </span> &nbsp;&nbsp;&nbsp; in Part No &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->candidate_residance_part_no}} </span> &nbsp;&nbsp;&nbsp; of the electoral roll for &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$candrac->AC_NAME}} </span> &nbsp;&nbsp;&nbsp; (Assembly constituency comprised within) &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$candrpc->PC_NAME }} </span> &nbsp;&nbsp;&nbsp; Parliamentary constituency.<br/> 
                   
                   We declare that we are electors of the above Parliamentary Constituency and our names are entered in the electoral roll for that Parliamentary Constituency as indicated below and we append our signatures below in token of subscribing to this nomination:—</p>
                   
                   <div class="table-heading">Particulars of the proposers and their signatures</div>
                   <table class="table table-bordered proposers-table">
                      <thead>
                        <tr>
                        <th>Sr No.</th>
                        <th>Name of component Assembly Constituency</th>
                        <th colspan="2">Elector Roll No. of Proposer</th>
                        <th>Full Name</th>
                         
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Part No. of Electoral Roll</td>
                        <td>S.No. in that part</td>
                        <td>&nbsp;</td></tr>
                        
                        <tr> <td>1</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_name1}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_slno1}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_partno1}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_partno1}}  </span> &nbsp;&nbsp;&nbsp;</td></tr>
                       <tr> <td>2</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_name2}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_slno2}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_partno2}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_partno2}}  </span> &nbsp;&nbsp;&nbsp;</td></tr>

                        <tr> <td>3</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_name3}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_slno3}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_partno3}}  </span> &nbsp;&nbsp;&nbsp;</td>
                        <td>&nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->proposer_partno3}}  </span> &nbsp;&nbsp;&nbsp;</td></tr>
                        
                      </tbody>
                    </table> 
               <!-- </div>
              </div> 
            
             
            <div class="nomination-fieldset">
              <div class="nomination-parts">-->
                <div class="nomination-form-heading">
                  <strong>PART III</strong>
                </div>
           <?php 
                  $party = app(App\commonModel::class)->getparty($caddata->party_id);
                  $symbole = app(App\commonModel::class)->getsymbol($caddata->symbol_id);
                ?>        
                <div class="nomination-detail">
                  <p>I, the candidate mentioned in Part I/Part II (Strike out which is not applicable) assent to this nomination and hereby declare—</p>
                  <ul>
                    <li>(a) that I am a citizen of India and have not acquired the citizenship of any foreign State/country.</li>
                    <li>(b) that I have completed &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_age}} </span> &nbsp;&nbsp;&nbsp; years of age; <br/>
                    
                    [STRIKE OUT c(i) or c(ii) BELOW WHICHEVER IS NOT APPLICABLE]</li>
                    
                <div class="nomination-options strikeout">
                  <div class="checkbox">
                    <label><input type="radio" class="strikeout-applicable" name="applicable" value="applicable">(c) (i) that I am set up at this election by the &nbsp;&nbsp;&nbsp; <span class="nominationvalue">@if(!empty($party)) {{$party->PARTYNAME}} @endif</span> &nbsp;&nbsp;&nbsp; party, which is recognised National Party/State Party in this State and that the symbol reserved for the above party be allotted to me.</label>
                  </div>
                  <div class="or">OR</div>
                  <div class="checkbox">
                    <label><input type="radio" class="strikeout-applicable" name="applicable" value="not-applicable">(c) (ii) that I am set up at this election by the <input type="text" name="cand_name" class="nomination-field-2"/> party, which is a registered-unrecognised political party/that I am contesting this election as an independent candidate. (Strike out which is not applicable) and that the symbols I have chosen, in order of preference, are:—
                  (i)<input type="text" name="cand_name" class="nomination-field-2"/>(ii)<input type="text" name="cand_name" class="nomination-field-2"/>(iii)<input type="text" name="cand_name" class="nomination-field-2"/></label>
                  </div>
                </div>
                
                  <li>(d) that my name and my father's/mother's/husband's name have been correctly spelt out above in &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_nickname}} </span> &nbsp;&nbsp;&nbsp; (name of the language);</li>
                  <li>(e) that to the best of my knowledge and belief, I am qualified and not also disqualified for being chosen to fill the seat in the House of the People.</li></ul>
                </div>
                
                <div class="nomination-detail">
                  <p>*I further declare that I am a member of the &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_category}} </span> &nbsp;&nbsp;&nbsp; **Caste/tribe which is a scheduled **caste/tribe of the State of &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_cast_state}} </span> &nbsp;&nbsp;&nbsp; in relation to &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->cand_cast_area}} </span> &nbsp;&nbsp;&nbsp;(area) in that State. 
                
                  I also declare that I have not been, and shall not be nominated as a candidate at the present general election/the bye-elections being held simultaneously, to the House of the People from more than two Parliamentary Constituencies. </p>
                </div>
                
                <div class="nomination-signature">
                  <span class="nomination-date left">Date &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{Carbon\Carbon::parse($caddata->cand_apply_date)->format('d-m-Y')  }} </span> &nbsp;&nbsp;&nbsp; </span>
                  <span class="nomination-sign right">Signature of Candidate </span>
                </div>
                
                <div class="nomination-note">
                  *Score out the words "assembly constituency comprised within" in the case of Jammu and Kashmir, Andaman and Nicobar Islands, Chandigarh, Dadra and Nagar Haveli, Daman and Diu and Lakshadweep.<br/>

                  *Score out this paragraph, if not applicable.<br/>

                  **Score out the words not applicable. N.B.—A "recognised political party" means a political party recognised by the Election Commission under the Election Symbols (Reservation and Allotment) Order, 1968 in the State concerned. 
                </div>
              </div><!-- Nomination Parts -->
              
              <!--<input type="button" name="previous" class="pre_btn" value="Previous" />
              <input type="button" name="next" class="next_btn" value="Next" />-->
            <!--</fieldset>
            <fieldset>-->
            </div><!-- Nomination Fieldset -->
            <div class="nomination-fieldset">
              <div class="nomination-parts">
                <div class="nomination-form-heading">
                  <strong>PART III A </strong><br/>
                  (To be filled by the candidate) 
                </div>
                  
                <div class="nomination-detail">
                  <p>(1) Whether the candidate— </p>
                    <ul><li>(i) has been convicted—  @if($caddata->is_criminal==1) &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> Yes </span> &nbsp;&nbsp;&nbsp;   @else &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> No </span> &nbsp;&nbsp;&nbsp; </li></ul>  @endif 
                    @if($caddata->is_criminal==1)
                  
                      <ul><li>(a) of any offense(s) under sub-section (1); or</li>
                      <li>(b) for contravention of any law specified in sub-section (2), of section 8 of the Representation of the People Act, 1951 (43 of 1951); or -</li> 
                      <li>(ii) has been convicted for any other offense(s) for which he has been sentenced to imprisonment for two years or more. <br/>
                    If the answer is “Yes”, the candidate shall furnish the following information:</li>

                    <li>(i) Case/first information report No./Nos &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->case_no}} </span> &nbsp;&nbsp;&nbsp;</li></ul>
                    
                    <div id="case-records"> <!--style="display:none;"-->
                      <ul><li>(ii) Police station(s) &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->police_station}} </span> &nbsp;&nbsp;&nbsp;  District(s) &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->district_no}} </span> &nbsp;&nbsp;&nbsp; State(s) &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->state_code}} </span> &nbsp;&nbsp;&nbsp;</li>
                      <li>(iii) Section(s) of the concerned Act(s) and brief description of the offense(s) for which he has been convicted &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->convicted_des}} </span> &nbsp;&nbsp;&nbsp; </li>
                      <li>(iv)Date(s) of conviction(s) &nbsp;&nbsp;&nbsp; <span class="nominationvalue">{{Carbon\Carbon::parse($caddata->date_of_conviction)->format('d-m-Y')  }} </span> &nbsp;&nbsp;&nbsp;  </li>

                      <li>(v) Court(s) which convicted the candidate &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->court_name}} </span> &nbsp;&nbsp;&nbsp;</li>
                      <li>(vi)Punishment(s) imposed [indicate period of imprisonment(s) and/or quantum offine(s)] &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->punishment_imposed}} </span> &nbsp;&nbsp;&nbsp;</li>
                      <li>(vii) Date(s) of release from prison &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{Carbon\Carbon::parse($caddata->Date_of_release)->format('d-m-Y')  }} </span> &nbsp;&nbsp;&nbsp;</li>
                      <li>(viii)Was/were any appeal(s)/revision(s) filed against above conviction(s)  &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->revision_against_conviction}} </span> &nbsp;&nbsp;&nbsp;</li></ul>
                      </div>
                      <div class="revisedfiled"> <!--style="display:none;"-->
                        <ul><li>(ix) Date and particulars of appeal(s)/application(s) for revision filed  &nbsp;&nbsp;&nbsp; <span class="nominationvalue">{{Carbon\Carbon::parse($caddata->revision_appeal_date)->format('d-m-Y')  }}</span> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->revision_appeal_description}} </span> &nbsp;&nbsp;&nbsp;</li>
                        <li>(x) Name of the court(s) before which the appeal(s)/application(s) for revision filed &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->revision_appeal_court}} </span> &nbsp;&nbsp;&nbsp;</li>
                        <li>(xi) Whether the said appeal(s)/application(s) for revision has/have been disposed of or is/are pending &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->revision_status}} </span> &nbsp;&nbsp;&nbsp;</li>
                        <li>(xii) If the said appeal(s)/application(s) for revision has/have been disposed of—<br/>
                          <ul><li>(a) Date(s) of disposal &nbsp;&nbsp;&nbsp; <span class="nominationvalue">{{Carbon\Carbon::parse($caddata->revision_disposal_date)->format('d-m-Y')  }} </span> &nbsp;&nbsp;&nbsp;</li>
                          <li>(b) Nature of order(s) passed &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{$caddata->revision_order_description}} </span> &nbsp;&nbsp;&nbsp;</li></ul>
                         </li> </ul>
                      </div>
                    @endif
                  <div class="nomination-signature">
                  <span class="nomination-date left"> Date: &nbsp;&nbsp;&nbsp; <span class="nominationvalue"> {{Carbon\Carbon::parse($caddata->cand_apply_date)->format('d-m-Y')  }} </span> &nbsp;&nbsp;&nbsp;</span>
                  <span class="nomination-sign right">Signature of Candidate  </span>
                </div>
                  <div class="btns-actn"> 
                    <span>Details Verified &nbsp;<input type="checkbox" name="Verify" value="1">  </span>
                   <input type="submit" value="Next">
                    
                            
                    </div>
                </div>
                  
                
                  
              </div><!-- Nomination Parts -->
            </div><!-- Nomination Fieldset -->
            <!--</fieldset>-->
            </form>
             
         
      </div><!-- End Of nw-crte-usr Div -->
  
     </div><!-- End Of nw-crte-usr Div -->
    </div> <!-- End Of child-area Div -->     
  </div><!-- End Of parent-wrap Div -->
  </div> 
 
 
@endsection