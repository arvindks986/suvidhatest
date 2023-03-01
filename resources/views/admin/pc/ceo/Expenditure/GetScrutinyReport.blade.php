<style type="text/css">
	.row.mis_gap {
    padding: 10px;
    border: 1px solid #6666;
}
</style>

<?php 
//echo "dd";die;
//print_r($expenseunderstated);die;
$htmlData ="";
if(!empty($scrutinyReportData[0]))
{?>
 <script>
$(document).ready(function(){
  $("#commentDatas").click(function(){
  	  var comment = $("#comments").val();
  	  var candidate_id = $('#candidate_id').val();
  	  	$.ajax({
         data: {candidate_id:candidate_id,comment:comment,"_token": "{{ csrf_token() }}"},
         type: "post",
         url: "{{url('/pcceo/saveComment')}}",
         success: function(response){
             response = response.trim();
            if(response==1){
            	$('#commentMsg').text('Comment saved successfully').css("color","green");
            }

          	if(response==0){
          		$('#commentMsg').text('Internal error occured').css("color","red");;
            }
              
         }

     });
  });
});
</script>
<?php } 
$htmlData .="<div class='col'><center><h5>Candidate Accoussnt Detail</h5></center></div>
                    <br>
                    <div class='row mis_gap'>
                        <div class='col'>Name : </div>
                        <div class='col'>{$scrutinyReportData[0]->contensting_candiate}</div>
                    </div>
 
                    <div class='row mis_gap'>
                        <div class='col'>Address of the Candidate  : </div>
                        <div class='col'>{$scrutinyReportData[0]->candidate_residence_address}</div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>Political Party Affliation, If Any  : </div>
                        <div class='col'>Independent</div>
                    </div>

                    
                    <div class='row mis_gap'>
                        <div class='col'>Date of Declaration of Result : </div>
                        <div class='col'>{$scrutinyReportData[0]->date_of_declaration}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Date of Account Reconciliation Meeting   : </div>
                        <div class='col'>{$scrutinyReportData[0]->date_of_account_rec_meetng}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(i) Whether the Candidate or his Agent had been informed about the Date of Account Reconciliation Meeting in writing  : </div>
                        <div class='col'>{$scrutinyReportData[0]->reconciliation_meeting_writing}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(ii) Whether he or his Agent has attended the Meeting  : </div>
                        <div class='col'>{$scrutinyReportData[0]->agent_attend_meeting}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Whether all the defects Reconciled by the Candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 15) : </div>
                        <div class='col'>{$scrutinyReportData[0]->defect_reconciliation_meeting}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Last Date Prescribed for Lodging Account : </div>
                        <div class='col'>{$scrutinyReportData[0]->last_date_prescribed_acct_lodge}</div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>Whether the Candidate has Lodged the Account : </div>
                        <div class='col'>{$scrutinyReportData[0]->candidate_lodged_acct}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>If the Candidate has Lodged the Account, Date of Lodging of Account by the Candidate <br> (i) Original Account </div>
                        <div class='col'>{$scrutinyReportData[0]->date_orginal_acct}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(ii) Revised Account after the Account Reconciliation Meeting : </div>
                        <div class='col'>{$scrutinyReportData[0]->date_revised_acct}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Whether Account Lodged in Time : </div>
                        <div class='col'>{$scrutinyReportData[0]->account_lodged_time}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>If Account not Lodged or not Lodged in Time, Whether DEO called for Explanation from the Candidate. If not, reason thereof. : </div>
                        <div class='col'>{$scrutinyReportData[0]->reason_lodged_not_lodged}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Explanation, if any, given by the Candidate :</div>
                        <div class='col'>{$scrutinyReportData[0]->explaination_by_candidate}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Comments of the DEO on the Explanation if any, of the Candidate : </div>
                        <div class='col'>{$scrutinyReportData[0]->comment_by_deo}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Grand Total of all Election Expenses Reported by the Candidate in Part-II of the Abstract Statement :</div>
                        <div class='col'>{$scrutinyReportData[0]->grand_total_election_exp_by_cadidate}</div>
                    </div>
                    <br><br>
                    <div class='col'><center><h5>Defects In Formats</h5></center></div>
                    <br>
                    <div class='row mis_gap'>
                        <div class='col'>Whether in the RO's Opinion, the Account of Election Expenses of the Candidate has been Lodged 
in the manner required by the R.P. Act 1951 and C.E. Rules, 1961. : </div>
                        <div class='col'>{$scrutinyReportData[0]->rp_act}</div>
                    </div>
 
                    <div class='row mis_gap'>
                        <div class='col'> If No, then please mention the following defects with details <br>
                        (i) Whether Election Expenditure Register Comprising of the Day to Day Account Register,
                                                <br />Cash Register, Bank Register, Abstract Statement has been Lodged </div>
                        <div class='col'>{$scrutinyReportData[0]->comprising} <br> {$scrutinyReportData[0]->comprising_comment}</div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>(ii) Whether duly sworn in affidavit has been submitted by the Candidate : </div>
                        <div class='col'>{$scrutinyReportData[0]->duly_sworn} <br>{$scrutinyReportData[0]->duly_sworn_comment}</div>
                    </div>

                    
                    <div class='row mis_gap'>
                        <div class='col'>(iii) Whether requisite Vouchers in respect of items of Election Expenditure Submited :</div>
                        <div class='col'>{$scrutinyReportData[0]->Vouchers} <br> {$scrutinyReportData[0]->Vouchers_comment}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'> (iv) Whether  seprate Bank Account Opened by for Election : </div>
                        <div class='col'>{$scrutinyReportData[0]->seprate} <br> {$scrutinyReportData[0]->seprate_comment}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(v) Whether all Expenditure (Except petty Expenditure) routed through bank Account : </div>
                        <div class='col'>{$scrutinyReportData[0]->routed} <Br> {$scrutinyReportData[0]->routed_comment}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(i) Whether the RO had issued a notice to the Candidate for Rectifying the Defect : </div>
                        <div class='col'>{$scrutinyReportData[0]->rectifying} <br>{$scrutinyReportData[0]->rectifying_comment}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(ii) Whether the Candidate Rectified the Defect :  </div>
                        <div class='col'>{$scrutinyReportData[0]->rectified}

                                        <br> {$scrutinyReportData[0]->rectified_comment}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(iii) Comments of the RO on the above, i.e. whether the defect was rectified or not. : </div>
                        <div class='col'>{$scrutinyReportData[0]->comment_of_deo}</div>
                    </div>";

                    if(!empty($expenseunderstated)){
                    	?>

				<?php

                        $htmlData .="<br><br>
                    <div class='col'><center><h5>Expenses Understated</h5></center></div>
                    <br>
                    <div class='row mis_gap'>
                        <div class='col'>Whether the items of Election Expenses Reported by the Candidate correspond with the Expenses shown in the Shadow Observation Register and Folder of Evidance. If no then mention the following.     </div>
                        <div class='col'>{$expenseunderstated[0]->status}</div>
                    </div>";

                   $htmlData .="<table class='table' width='100%' cellpadding='0' id='tblEntAttributes'>
                                <thead>
                                    <tr>
                                        <th width='200'>Item of Expenditure</th>
                                        <th width='140'>Date</th>   
                                        <th width='100'>Page no of Shadow Observation Register / folder of evidence</th>  
                                        <th width='130'>Mention amount as per the shadow observation register/ folder of evidence</th>
                                        <th width='130'>Amount as per the account submitted by the candidate</th>   
                                        <th width='130'>Amount understated by the Candidate </th> 
                                        <th>Description</th>
                                    </tr>
                                </thead>
                            <tbody>";

                  if(!empty($expenseunderstatedbyitem))
                  {

                    foreach ($expenseunderstatedbyitem as $item) {

                        $htmlData .="<tr>
                                    <td>{$item->expenditure_type}</td>
                                    <td>{$item->date_understated}</td>
                                    <td>{$item->page_no_observation}</td>
                                    <td>{$item->amt_as_per_observation}</td>
                                    <td>{$item->amt_as_per_candidate}</td>
                                    <td>{$item->amt_understated_by_candidate}</td>
                                    <td>{$item->description}</td>

                                    </tr>";
                    }
                  }  


                  $htmlData .="</tbody>
                        </table>";

            
             $htmlData .="<div class='row mis_gap'>
                        <div class='col'> Did the Candidate produce his Register of the Accounting Election Expenditure Register for Inspection by the Observer/RO/Authorized persons 3 times during Campaign Period    </div>
                        <div class='col'>{$expenseunderstated[1]->status} </div>
                    </div>

                    <div class='row mis_gap'>
                        <div class='col'>If RO does not agree with the facts Mentioned aginast Row No. 15 referred to above, give the following Details : <Br> (i) Were the defects notice by the RO brought to the notice of the Candidate during Campaign Period or during the Account Reconcialation Meeting </div>
                        <div class='col'>{$expenseunderstated[2]->status}</div>
                    </div>

                    
                    <div class='row mis_gap'>
                        <div class='col'>(ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice.  :</div>
                        <div class='col'>PDF FILE</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'> (iii) Did the Candidate give any reply to the Notice ?  : </div>
                        <div class='col'>{$expenseunderstated[4]->status}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>(iv) If Yes, please Annex copies of such Explanation received, (With the English translation of the same, if it is in regional language) and mention Date of Reply : </div>
                        <div class='col'>PDF FILE</div>
                    </div>
                   
                    <div class='row mis_gap'>
                        <div class='col'>(V) RO's Comments/Observations on the Candidate's Explanation :</div>
                        <div class='col'>{$expenseunderstated[6]->comment}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'>Whether the RO Agrees that the Expenses are correctly Reported by the Candidate. should be similar to Column no. 8 of Summary Repods of RO : </div>
                        <div class='col'>{$expenseunderstated[7]->status}<br>{$expenseunderstated[7]->comment}</div>
                    </div>
                    <div class='row mis_gap'>
                        <div class='col'> Comments, If Any by the Expenditure Observer : </div>
                        <div class='col'>{$expenseunderstated[8]->comment}</div>
                    </div>";

                             }

                    $htmlData .="<br><br><div class='col'><center><h5>Fund Given by Political Party</h5></center></div>
                    <br>
                    
                    <table id='fundParty' class='table table-striped table-bordered' style='width:100%'>
                <thead>
                    <tr>
                        <th colspan='7' class='text-center' color='#ffffff'>Fund Given By Political Party</th>
                    </tr><tr>    
                </tr></thead>
                    <tbody>
                    <tr>
                        <td width='190'><label>By Cash</label></td>
                        <td>{$scrutinyReportData[0]->political_fund_cash}</td>
                    </tr>
                    <tr>
                        <td width='190'><label>By Cheque</label></td>
                        <td width='120'>
                           {$scrutinyReportData[0]->political_fund_checque} 
                        </td>
                        <td>
                           {$scrutinyReportData[0]->political_fund_checque_date}
                        </td>
                        <td>
                            {$scrutinyReportData[0]->political_fund_bank_name}
                        </td>
                        <td>
                            {$scrutinyReportData[0]->political_fund_acct_no}
                        </td>
                        <td>
                        {$scrutinyReportData[0]->political_fund_ifsc}
                        </td>
                        <td>    
                        {$scrutinyReportData[0]->political_fund_checque_num}

                        </td>
                    </tr>
                    <tr>
                        <td width='190'><label>In Kind</label></td>
                      <td> {$scrutinyReportData[0]->political_fund_kind}</td>
                    </tr>
                    
                    </tbody>
                </table>";
 if(!empty($expensesourecefundbyitem))
                        {

                $htmlData .="<br><br><div class='col'><center><h5>Fund Given by Political Party</h5></center></div>
                    <br>
                    
                    <table class='table table-bordered'>
                    <thead>                                                
                        <tr>
                            <th>Name</th>
                            <th>Mode of Payment</th>
                            <th>Amount</th>
                        </tr>    
                    </thead>
                    <tbody>";
                        
                    foreach ($expensesourecefundbyitem as $items) {

                        $htmlData .="<tr>
                                    <td>{$items->other_souce_name}</td>
                                    <td>{$items->other_source_payment_mode}</td>
                                    <td>{$items->other_source_amount}</td>
                                    </tr>";
                                }
                              
                                                                        
                   $htmlData .="</tbody>    
                            </table>";

                        }   

                    $htmlData .="<br><br><div class='col'><center><h5>Comment By ECI/CEO/DEO/RO </h5></center></div><Br>
                                <div class='row mis_gap'>
                                 <div class='col comhead'> Comment By Eci </div>
                                 <div class='col'>{$scrutinyReportData[0]->comment_by_eci} </div>
                                </div>
                                <div class='row mis_gap'>
                                 <div class='col comhead'> Comment By You </div><br><br>
                                 <div class='col'>{$scrutinyReportData[0]->comment_by_ceo}</div>
                                </div>

                                 <form method='post' id='commentData'>
                                 <input type='hidden' name='candidate_id' id='candidate_id' value='{$scrutinyReportData[0]->candidate_id}'>
                                 <table border='' width='100%''>
                                 <tr><td><textarea name='comment_by_ceo' id='comments' rows='4' cols='130' value='{$scrutinyReportData[0]->comment_by_ceo}'>{$scrutinyReportData[0]->comment_by_ceo}</textarea></td></tr> 
                                 <tr><td><center><input type='button' name='submit' id='commentDatas' value='Submit'></center></td></tr>
								 </form></table>
								 <center><span id='commentMsg'></span></center>
                                 ";    



                    echo $htmlData;
                    exit;

                    ?> 


