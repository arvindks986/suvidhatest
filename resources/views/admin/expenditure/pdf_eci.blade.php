    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Candidate Scrutiny Reports</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
   
   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/images/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br> 
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:49%;">
                    <table  style="width:100%">ST_NAME
                      <tbody>
                         <tr>
                           <td><strong>Candidate Scrutiny Reports</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->state:'--'}}</td>
                         </tr>
                  
                         
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:49%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d.m.Y h:i a') }}</td>
                         </tr>
                         
                        
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <
            </table>
       <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
        <tr>
           
          <td style="border: 1px solid #ddd; padding: 12px;" width="60%">Name :</td>
           <td style="border: 1px solid #ddd; padding: 12px;" width="40%">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->contensting_candiate:'--'}}</td>
        </tr>
        <tr>
            
          <td style="border: 1px solid #ddd; padding: 12px;">Address of the Candidate :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->candidate_residence_address:'--'}}</td>
         </tr>
         <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">PC Name :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->PC_NAME:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Political Party Affliation, If Any :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->PARTYNAME:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Date of Declaration of Result :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->date_of_declaration:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Date of Account Reconciliation Meeting :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->date_of_account_rec_meetng:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">
            (i) Whether the Candidate or his Agent had been informed about the Date of Account Reconciliation Meeting in writing :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->date_of_account_rec_meetng:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(ii) Whether he or his Agent has attended the Meeting :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->reconciliation_meeting_writing:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Whether all the defects Reconciled by the Candidate after Account Reconciliation Meeting (Yes or No). (If not, defects that could not be reconciled be shown in Column No. 15) :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->defect_reconciliation_meeting:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Last Date Prescribed for Lodging Account :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->last_date_prescribed_acct_lodge:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Whether the Candidate has Lodged the Account :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->candidate_lodged_acct:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">If the Candidate has Lodged the Account, Date of Lodging of Account by the Candidate (i) Original Account</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->date_orginal_acct:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(ii) Revised Account after the Account Reconciliation Meeting :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->date_revised_acct:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Whether Account Lodged in Time:</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->account_lodged_time:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">If Account not Lodged or not Lodged in Time, Whether DEO called for Explanation from the Candidate. If not, reason thereof :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->reason_lodged_not_lodged:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Explanation, if any, given by the Candidate :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->explaination_by_candidate:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Comments of the DEO on the Explanation if any, of the Candidate :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->comment_by_deo:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Grand Total of all Election Expenses Reported by the Candidate in Part-II of the Abstract Statement :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->grand_total_election_exp_by_cadidate:'--'}}</td>
        </tr>
      </table>
      </font>

       <p style="text-align: center; font-size: 14pt; font-family: Arial;"><b>Defects In Formats</b></p>
        <font face="Arial" size="2pt">
      <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;" width="60%">Whether in the RO's Opinion, the Account of Election Expenses of the Candidate has been Lodged in the manner required by the R.P. Act 1951 and C.E. Rules, 1961. :</td>
           <td style="border: 1px solid #ddd; padding: 12px;" width="40%">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->rp_act:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">If No, then please mention the following defects with details (i) Whether Election Expenditure Register Comprising of the Day to Day Account Register, Cash Register, Bank Register, Abstract Statement has been Lodgeds</td>
          <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->comprising:'--'}}<br/>{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->comprising_comment:'--'}}</td>
         </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(ii) Whether duly sworn in affidavit has been submitted by the Candidate :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->duly_sworn:'--'}}{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->duly_sworn_comment:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(iii) Whether requisite Vouchers in respect of items of Election Expenditure Submited :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->Vouchers:'--'}}{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->Vouchers_comment:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(iv) Whether separate Bank Account Opened by for Election :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->seprate_comment:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(v) Whether all Expenditure (Except petty Expenditure) routed through bank Account :</td>
           <td style="border: 1px solid #ddd; padding: 12px">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->routed_comment:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(i) Whether the RO had issued a notice to the Candidate for Rectifying the Defect :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->rectifying_comment:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(ii) Whether the Candidate Rectified the Defect :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->rectified:'--'}}{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->rectified_comment:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(iii) Comments of the RO on the above, i.e. whether the defect was rectified or not. :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->comment_of_deo:'--'}}</td>
        </tr>
      </table>
    </font>

    <p style="text-align: center; font-size: 14pt; font-family: Arial;"><b>Expenses Understated</b></p>
      <font face="Arial" size="2pt">
      <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;" width="60%">Whether the items of Election Expenses Reported by the Candidate correspond with the Expenses shown in the Shadow Observation Register and Folder of Evidance. If no then mention the following.</td>
           <td style="border: 1px solid #ddd; padding: 12px;" width="40%">{{!empty($expenseunderstated[0])?$expenseunderstated[0]->status:'--'}}</td>
        </tr>
      </table>
    </font>

     <font face="Arial" size="2pt">
      <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
        <tr>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: left; color: white;">Item of Expenditure</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: left; color: white;">Date</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: left; color: white;">Page no of Shadow Observation Register / folder of evidence</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: left; color: white;">Mention amount as per the shadow observation register/ folder of evidence</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: left; color: white;">Amount as per the account submitted by the candidate</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: left; color: white;">Amount understated by the Candidate</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: left; color: white;">Description</th>
        </tr>
        @if(count($expenseunderstatedbyitem)>0)
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($item->expenditure_type)? $item->expenditure_type:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($item->date_understated)? $item->date_understated:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($item->page_no_observation)? $item->page_no_observation:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($item->amt_as_per_observation)? $item->amt_as_per_observation:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($item->amt_understated_by_candidate)? $item->amt_understated_by_candidate:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($item->description)? $item->description:'--'}}</td>
           
        </tr>
        @endif  
      </table>
    </font>

    <font face="Arial" size="2pt">
      <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;" width="60%">Did the Candidate produce his Register of the Accounting Election Expenditure Register for Inspection by the Observer/RO/Authorized persons 3 times during Campaign Period.</td>
           <td style="border: 1px solid #ddd; padding: 12px;" width="40%" colspan="2">{{!empty($expenseunderstated[1])? $expenseunderstated[1]->status:''}}</td>
        </tr>
         <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">If RO does not agree with the facts Mentioned aginast Row No. 15 referred to above, give the following Details :
          (i) Were the defects notice by the RO brought to the notice of the Candidate during Campaign Period or during the Account Reconcialation Meeting</td>
          <td style="border: 1px solid #ddd; padding: 12px;" colspan="2">{{!empty($expenseunderstated[2])? $expenseunderstated[2]->status:''}}</td>
        </tr>
         <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(ii) If Yes, then Annexe copies of all the notices issued relating to Discrepancies with English Translation (If it is in regional language) and mention Date of Notice. :</td>
           <td style="border: 1px solid #ddd; padding: 12px;" colspan="2">PDF File</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(iii) Did the Candidate give any reply to the Notice ? :</td>
           <td style="border: 1px solid #ddd; padding: 12px;" colspan="2">{{!empty($expenseunderstated[4])? $expenseunderstated[4]->status:''}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(iv) If Yes, please Annex copies of such Explanation received, (With the English translation of the same, if it is in regional language) and mention Date of Reply :</td>
           <td style="border: 1px solid #ddd; padding: 12px;" colspan="2">PDF File</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">(v) RO's Comments/Observations on the Candidate's Explanation :</td>
           <td style="border: 1px solid #ddd; padding: 12px;" colspan="2">{{!empty($expenseunderstated[6])? $expenseunderstated[6]->comment:''}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Whether the RO Agrees that the Expenses are correctly Reported by the Candidate. should be similar to Column no. 8 of Summary Repods of RO :</td>
           <td style="border: 1px solid #ddd; padding: 12px;">No</td>
           <td style="border: 1px solid #ddd; padding: 12px;">{{!empty($expenseunderstated[7])? $expenseunderstated[7]->comment:''}}</td>   
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px;">Comments, If Any by the Expenditure Observer :</td>
           <td style="border: 1px solid #ddd; padding: 12px;" colspan="2">{{!empty($expenseunderstated[8])? $expenseunderstated[8]->comment:''}}</td>
        </tr>
      </table>
    </font>

    <p style="text-align: center; font-size: 14pt; font-family: Arial;"><b>Fund Given by Political Party</b></p>
     <font face="Arial" size="2pt">
      <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
        <tr>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; text-align: center; color: white;" colspan="7">Fund Given By Political Party</th>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">By Cash</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;" colspan="6">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_cash:'--'}}</td>
        </tr>
         <tr>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">By Cheque</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_checque:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_checque_date:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_bank_name:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_acct_no:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_ifsc:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_checque_num:'--'}}</td>
        </tr>
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">In Kind</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;" colspan="6">{{!empty($scrutinyReportData[0])?$scrutinyReportData[0]->political_fund_kind:'--'}}</td>
        </tr>
      </table>
    </font>

    <p style="text-align: center; font-size: 14pt; font-family: Arial;"><b>Fund Given By Other Sources</b></p>
     <font face="Arial" size="2pt">
      <table id="customers" style="border-collapse: collapse; width: 100%; color: #36393c;">
        <tr>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; color: white;">Name</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; color: white;">Mode of Payment</th>
          <th bgcolor="#e83e8c" style="border: 1px solid #ddd; padding: 12px; padding-top: 12px; padding-bottom: 12px; color: white;">Amount</th>
        </tr>
        @if(count($expensesourecefundbyitem)>0)
        @foreach ($expensesourecefundbyitem as $items)
        <tr>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($items->other_souce_name)?$items->other_souce_name:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($items->other_source_payment_mode)?$items->other_source_payment_mode:'--'}}</td>
          <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{!empty($items->other_source_amount)?$items->other_source_amount:'--'}}</td>
        </tr>
        @endforeach
        @endif
      </table>
              
    </font>
      <table style="width:98%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>