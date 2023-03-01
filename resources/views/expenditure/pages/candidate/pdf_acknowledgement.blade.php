<?php
 
$partyname = getpartybyid($candData->party_id);
$partyname = !empty($partyname) ? $partyname->PARTYNAME : 'N/A';

//schedule-1
$sum_src_amt_incurred_cand =  array_sum(array_column($getSch1Data, 'src_amt_incurred_cand'));
$sum_src_amt_incurred_pp =  array_sum(array_column($getSch1Data, 'src_amt_incurred_pp'));
$sum_src_amt_incurred_other =  array_sum(array_column($getSch1Data, 'src_amt_incurred_other'));
$sum_total_amt = array_sum(array_column($getSch1Data, 'total_amt'));

///schedule-2
if(count($getSch2Data)>0){
$sum_src_amt_by_cand = array_sum(array_column($getSch2Data, 'src_amt_by_cand'));
$sum_src_amt_by_pp = array_sum(array_column($getSch2Data, 'src_amt_by_pp'));
$sum_src_amt_by_other = array_sum(array_column($getSch2Data, 'src_amt_by_other'));
}
else
{
$sum_src_amt_by_cand = "0";
$sum_src_amt_by_pp = "0";
$sum_src_amt_by_other = "0";
}

//schedule-3
if(count($getSch3Data)>0){
$sum3_src_amt_by_cand = array_sum(array_column($getSch3Data, 'src_amt_by_cand'));
$sum3_src_amt_by_pp = array_sum(array_column($getSch3Data, 'src_amt_by_pp'));
$sum3_src_amt_by_other = array_sum(array_column($getSch3Data, 'src_amt_by_other'));
$sum3_total_amt = array_sum(array_column($getSch3Data, 'total_amt'));
}else
{
$sum3_src_amt_by_cand = "0";
$sum3_src_amt_by_pp = "0";
$sum3_src_amt_by_other = "0";
$sum3_total_amt="0";
}

//schedule-4
if(count($getSch4Data)>0){
$sum4_total_amt = array_sum(array_column($getSch4Data, 'total_amt'));
$sum4_src_amt_by_cand = array_sum(array_column($getSch4Data, 'src_amt_by_cand'));
$sum4_src_amt_by_pp = array_sum(array_column($getSch4Data, 'src_amt_by_pp'));
$sum4_src_amt_by_other = array_sum(array_column($getSch4Data, 'src_amt_by_other'));
$sum4_commission_of_agency = array_sum(array_column($getSch4Data, 'commission_of_agency'));
$sum4_price_of_the_media = array_sum(array_column($getSch4Data, 'price_of_the_media'));

}else
{
$sum4_total_amt = "0";
$sum4_src_amt_by_cand = "0";
$sum4_src_amt_by_pp = "0";
$sum4_src_amt_by_other = "0";
$sum4_commission_of_agency="0";
$sum4_price_of_the_media="0";
}
//schedule-4a
if(count($getSch4aData)>0){
$sum4a_total_amt = array_sum(array_column($getSch4aData, 'total_amt'));
$sum4a_src_amt_by_cand = array_sum(array_column($getSch4aData, 'src_amt_by_cand'));
$sum4a_src_amt_by_pp = array_sum(array_column($getSch4aData, 'src_amt_by_pp'));
$sum4a_src_amt_by_other = array_sum(array_column($getSch4aData, 'src_amt_by_other'));
$sum4a_commission_of_agency = array_sum(array_column($getSch4aData, 'commission_of_agency'));
$sum4a_price_of_the_media = array_sum(array_column($getSch4aData, 'price_of_the_media'));
}
else
{
$sum4a_total_amt = "0";
$sum4a_src_amt_by_cand = "0";
$sum4a_src_amt_by_pp = "0";
$sum4a_src_amt_by_other = "0";
$sum4a_commission_of_agency="0";
$sum4a_price_of_the_media="0";
}

///schedule-5
if(count($getSch5Data)>0){
$sum5_total_amt_incurred = array_sum(array_column($getSch5Data, 'total_amt_incurred'));
$sum5_src_amt_by_cand = array_sum(array_column($getSch5Data, 'src_amt_by_cand'));
$sum5_src_amt_by_pp = array_sum(array_column($getSch5Data, 'src_amt_by_pp'));
$sum5_src_amt_by_other = array_sum(array_column($getSch5Data, 'src_amt_by_other'));
$sum5_hir_rate_for_vehicle = array_sum(array_column($getSch5Data, 'hir_rate_for_vehicle'));
$sum5_hir_fuel_charges = array_sum(array_column($getSch5Data, 'hir_fuel_charges'));
$sum5_hir_driver_charges = array_sum(array_column($getSch5Data, 'hir_driver_charges'));
}else
{
$sum5_total_amt_incurred = "0";
$sum5_src_amt_by_cand = "0";
$sum5_src_amt_by_pp = "0";
$sum5_src_amt_by_other = "0";
$sum5_hir_rate_for_vehicle = "0";
$sum5_hir_fuel_charges = "0";
$sum5_hir_driver_charges = "0";
}

//schedule-6
if(count($getSch6Data)>0){
$sum6_total_amnt = array_sum(array_column($getSch6Data, 'total_amnt'));
$sum6_source_amnt_by_cand = array_sum(array_column($getSch6Data, 'source_amnt_by_cand'));
$sum6_source_amnt_by_polparty = array_sum(array_column($getSch6Data, 'source_amnt_by_polparty'));
$sum6_source_amnt_by_others = array_sum(array_column($getSch6Data, 'source_amnt_by_others'));
$sum6_expense_nature_rate = array_sum(array_column($getSch6Data, 'expense_nature_rate'));

}else
{
$sum6_total_amnt = "0";
$sum6_source_amnt_by_cand = "0";
$sum6_source_amnt_by_polparty = "0";
$sum6_source_amnt_by_others = "0";
$sum6_expense_nature_rate="0";
}


if(count($getSch7Data)>0){
$sum7_total_amnt =  array_sum(array_column($getSch7Data, 'amount'));
}else
{
$sum7_total_amnt = "0";
}


if(count($getSch8Data)>0){
$sum8_total_amnt = array_sum(array_column($getSch8Data, 'amount'));
}else{
$sum8_total_amnt ="0";
}

if(count($getSch9Data)>0){
$sum9_total_amnt = array_sum(array_column($getSch9Data, 'amount'));
}else{
$sum9_total_amnt ="0";
}


if(count($getSch10Data)>0){
$sum10_expense_on_news = array_sum(array_column($getSch10Data, 'expense_on_news'));
$sum10_expense_on_channel = array_sum(array_column($getSch10Data, 'expense_on_channel'));

}else{
$sum10_expense_on_news ="0";
$sum10_expense_on_channel ="0";
}

 //$amt_own_funds_election_compaign = $getannuxureData[0]->amt_own_funds_election_compaign[] + $getannuxureData[0]->lump_sum_amt_from_party + $getannuxureData[0]->lump_sum_amt_from_other; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tracking Status</title>
    <!--HEADER STARTS HERE-->

    <!--HEADER ENDS HERE-->
    <style type="text/css">
    .table-strip{border-collapse: collapse;}
    .table-strip th,.table-strip td{text-align: left;}
    .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
    </style>
</head>
<body>
    <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
    <thead>
        <tr>
        <th  style="width:49%" align="left" style="border-bottom: 1px solid #d7d7d7;">
            <img src="<?php echo url('/'); ?>/admintheme/images/logo/suvidha-logo.png" alt=""  width="100" border="0"/></th>  
        <th  style="width:49%" align="right" style="border-bottom: 1px solid #d7d7d7;">
            SECRETARIAT OF THE<br> ELECTION COMMISSION OF INDIA<br> Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
        </th>
        </tr>
    </thead>
    </table><br /><br />
    <table style="width:100%; border: 1px solid #000;" border="0" align="center">
       <tr>
           <td>
               <table style="width:100%" align="">
               <tbody>
                   <tr>
 <th style="font-size: 21px;" align="left"><strong>FORM ID: {{$candData->st_code}}{{$candData->district_no}}{{date('Ymd')}}{{rand(100,999)}}</strong></th>
                        <th style="width:49%" align="right">
                          @php /*<img src="<?php echo url('/'); ?>/admintheme/images/qr_code.png" alt=""  width="100" border="0"/> */ @endphp
                             <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{$candData->st_code}}{{$candData->district_no}}{{date('Ymd')}}{{rand(100,999)}}&choe=UTF-8" title="Form ID" />
                        </th>
                   </tr>
               </tbody>
               </table>
           </td>
       </tr>
   </table> <br><br>
    <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
        <tr>
            <td>
                <table style="width:100%" align="center">
                <tbody>
                    <tr>
                        <th><strong>Annexure-E2</strong></th>
                    </tr>
                </tbody>
                </table>  
            </td>
        </tr>
    </table><br />        
    <div class=" text-left" style="width:100%;">
<!-- Summary Report of DEO --> 
    <!--<div class="collapse show">
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SUMMARY REPORT OF DEO FOR EACH CONSTITUENCY ON LODGING OF ELECTION EXPENSES ACCOUNTS BY CANDIDATES</b></p>

    <div class="row"> 
        <div class="col">
         <p style="font-size: 11pt; font-family: Arial;">(a) No. and Name of Assembly/Parliamentary Constituency:</p>
        <p style="font-size: 11pt; font-family: Arial;">(b) Total No. Contesting Candidates:</p>
        <p style="font-size: 11pt; font-family: Arial;">(c) State and District:</p>
        <p style="font-size: 11pt; font-family: Arial;">(d) Date of Declaration of Result of Election/Bye-election:</p>
        <p style="font-size: 11pt; font-family: Arial;">(e) Last Date of Lodging Accounts:</p>
        <p style="font-size: 11pt; font-family: Arial;">(f) Name of the Elected Candidate:</p>
        </div>
    </div>
    <br />
    <table style="width:100%; font-size: 8pt; font-family: Arial; border-collapse: collapse; border: 1px solid #7c7d80; color: #000000" border="0" align="center" cellpadding="5" bgcolor="#f5f6f8">
         <thead class="text-center">
        <tr>
          <th style="border: 1px solid #363637; padding: 10px;">1.</th>
          <th style="border: 1px solid #363637; padding: 10px;">2.</th>
          <th style="border: 1px solid #363637; padding: 10px;">3.</th>
          <th style="border: 1px solid #363637; padding: 10px;">4.</th>
          <th style="border: 1px solid #363637; padding: 10px;">5.</th>
          <th style="border: 1px solid #363637; padding: 10px;">6.</th>
          <th style="border: 1px solid #363637; padding: 10px;">7.</th>
          <th style="border: 1px solid #363637; padding: 10px;">8.</th>
          <th style="border: 1px solid #363637; padding: 10px;" colspan="2">9.</th>
          <th style="border: 1px solid #363637; padding: 10px;" colspan="2">10.</th>
          <th style="border: 1px solid #363637; padding: 10px;">11.</th>
        </tr>
        <tr>  
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Sr .No.</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Name of the Candidate and Party Affiliation</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Due Date of Lodging of Account</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Date of Lodging of Account by the Candidate</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Whether Lodged in the Prescribed Format (Yes or No)</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Whether Lodged in the manner required by Law (Yes or No)</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Grand Total of the Expenses Incurred/Authorized by the Candidate/Agent (as mentioned in <em>Part-II</em> of Abstract Statement)</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Whether the DEO agrees with the amount shown by the candidate against all items of expenditure (Should be similar to point no. <b>22</b> of DEO's Scrutiny Report i.e. Annexure -C3)</th>
          <th style="border: 1px solid #363637; padding: 10px;" colspan="2">Total Expenses incurred by the Party (As reported in Part-III of Abstract Statement)</th>
          <th style="border: 1px solid #363637; padding: 10px;" colspan="2">Total Expenses incurred by others/entities as reported in Part-III of Abstract Statement</th>
          <th style="border: 1px solid #363637; padding: 10px;" rowspan="2">Remarks of the Expenditure Observer</th>          
        </tr>
        <tr>
          <th style="border: 1px solid #363637; padding: 10px;">Lump Sum Amount in cash or cheque given to candidate by each Political Party</th>
          <th style="border: 1px solid #363637; padding: 10px;">Grand Total of other Expenses kind by the Political Party</th>
          <th style="border: 1px solid #363637; padding: 10px;">Lump Sum Amount in cash/cheque given to the candidate (and mention names of donors)</th>
          <th style="border: 1px solid #363637; padding: 10px;">Grand Total of other expenses in kind incurred for the candidate</th>    
        </tr>
      </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
            </tr>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
            </tr>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
            </tr>
        </tbody>
    </table><br />
     <table style="width:100%; font-size: 11pt; font-family: Arial; border-collapse: collapse; border: 1px solid #7c7d80; color: #000000" border="0" align="center" cellpadding="5">
      <tr>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="50%">Comments of the Expenditure Observer, If any</td>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="50%">Signature of the DEO</td>
      </tr>
      <tr>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #f5f6f8;" width="50%">&nbsp;</td>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #f5f6f8;" width="50%">&nbsp;</td>
      </tr>
    </table>
    <br />
</div><br /> -->
<!-- End of Summary Report of DEO -->

<div  class="collapse show">
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>PART-I ABSTRACT STATEMENT OF ELECTION EXPENSES</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
        <tbody>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="10%">I</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="60%">Name of the Candidate</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="30%">{{!empty($candData->cand_name)?$candData->cand_name:"N/A"}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="10%">II</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="60%">Number and name of Constituency</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="30%">{{$candData->pc_no}} - {{$candData->PC_NAME}}  </td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="10%">III</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="60%">Name of State/Union Territory</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="30%">{{$candData->ST_NAME}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="10%">IV</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="60%">Nature of Election (Please mention whether General Election to State Assembly / Lok Sabha / Bye- Election)</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="30%">General</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="10%">V</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="60%">Date of declaration of result</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="30%">23-05-2019</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="10%">VI</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="60%">Name and address of the Election Agent</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="30%">{{!empty($candData->candidate_residence_address)?$candData->candidate_residence_address:"N/A"}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="10%">VII</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="60%">If candidate is set up by a political party, Please mention the name of the political party</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="30%">{{$partyname}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="10%">VIII</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="60%">Whether the party is a recognised Yes/No political party</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="30%">Yes</td>
            </tr>
        </tbody>
    </table><br />
     <table style="width:100%; font-size: 10pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
        <tbody>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Date</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Signature of the Candidate</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Place</td>
                 <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="25%">Name</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="25%">03-06-2019</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="25%">&nbsp;</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="25%">{{$candData->ST_NAME}}</td>
                 <td style="border: 1px solid #454546; padding: 10px;" width="25%">{{$candData->cand_name}}</td>
            </tr>
        </tbody>
    </table> 
</div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<div  class="collapse show">
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>PART-II ABSTRACT STATEMENT OF ELECTION EXPENSES EXPENDITURE OF CANDIDATE</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
        <thead>
            <tr>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Sr. No.</th>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="35%">Particulars</th>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Amt. Incurred / Auth. by Candidate Election Agent (in Rs.)</th>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Amt. Incurred authorized by Pol. Party(in Rs.)</th>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Amt. Incurred / authorized by Other(in Rs.)</th>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Total Expenditure (3) + (4) + (5)</th>
            </tr>
            <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">I</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Expenses in public meeting, rally,procession etc.:-l.a.: Expences in public meeting , rally, procession etc. (i.e. other than once with the Star Campaigners of the political party)(Enclose as per Schedule-3)</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getannuxureData[0]->public_expenses_meeting_star_3)?$getannuxureData[0]->public_expenses_meeting_star_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getannuxureData[0]->public_expenses_meeting_star_4)?$getannuxureData[0]->public_expenses_meeting_star_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getannuxureData[0]->public_expenses_meeting_star_5)?$getannuxureData[0]->public_expenses_meeting_star_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getannuxureData[0]->public_expenses_meeting_star_6)?$getannuxureData[0]->public_expenses_meeting_star_6:"N/A"}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">l. b. expenditure in public meeting rally, procession etc. with the star Campaigner(s) (ie other than those for general party propaganda) (Enclose as per Schedule-2)</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->public_expenses_meeting_general_3)?$getannuxureData[0]->public_expenses_meeting_general_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->public_expenses_meeting_general_4)?$getannuxureData[0]->public_expenses_meeting_general_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->public_expenses_meeting_general_5)?$getannuxureData[0]->public_expenses_meeting_general_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->public_expenses_meeting_general_6)?$getannuxureData[0]->public_expenses_meeting_general_6:"N/A"}}</td>
            
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">II</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Campaign materials other,rally, procession etc. mentioned in S. No.I above(Enclose as per Schedule-3)</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->compaign_material_3)?$getannuxureData[0]->compaign_material_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->compaign_material_4)?$getannuxureData[0]->compaign_material_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->compaign_material_5)?$getannuxureData[0]->compaign_material_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->compaign_material_6)?$getannuxureData[0]->compaign_material_6:"N/A"}}</td>
            
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">III</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Campaign, through print and electronic media including cable network, bulk SMS or internet and Social media (Enclose as per Schedule-4)</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->compaign_through_print_media_3)?$getannuxureData[0]->compaign_through_print_media_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->compaign_through_print_media_4)?$getannuxureData[0]->compaign_through_print_media_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->compaign_through_print_media_5)?$getannuxureData[0]->compaign_through_print_media_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->compaign_through_print_media_6)?$getannuxureData[0]->compaign_through_print_media_6:"N/A"}}</td>
            
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">IV</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Expenditure on campaign vehicle(s), used by candidate(Enclose as per schedule-5)</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_vehicle_3)?$getannuxureData[0]->expenditure_on_compaign_vehicle_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_vehicle_4)?$getannuxureData[0]->expenditure_on_compaign_vehicle_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_vehicle_5)?$getannuxureData[0]->expenditure_on_compaign_vehicle_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_vehicle_6)?$getannuxureData[0]->expenditure_on_compaign_vehicle_6:"N/A"}}</td>
            
            </tr>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">V</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Expenses of campaign workers / agents (Enclose as per Schedule —6)</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_workers_3)?$getannuxureData[0]->expenditure_on_compaign_workers_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_workers_4)?$getannuxureData[0]->expenditure_on_compaign_workers_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_workers_5)?$getannuxureData[0]->expenditure_on_compaign_workers_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenditure_on_compaign_workers_6)?$getannuxureData[0]->expenditure_on_compaign_workers_6:"N/A"}}</td>
            
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">VI</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Any other campaign expenditure</td>
               <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->any_other_compaign_expenditure_3)?$getannuxureData[0]->any_other_compaign_expenditure_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->any_other_compaign_expenditure_4)?$getannuxureData[0]->any_other_compaign_expenditure_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->any_other_compaign_expenditure_5)?$getannuxureData[0]->any_other_compaign_expenditure_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->any_other_compaign_expenditure_6)?$getannuxureData[0]->any_other_compaign_expenditure_6:"N/A"}}</td>
            
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">VII</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Expenses incurred on publishing of declaration regarding criminal cases (Enclose as per Schedule-10)*</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenses_incurred_on_publishing_3)?$getannuxureData[0]->expenses_incurred_on_publishing_3:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenses_incurred_on_publishing_4)?$getannuxureData[0]->expenses_incurred_on_publishing_4:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->expenses_incurred_on_publishing_5)?$getannuxureData[0]->expenses_incurred_on_publishing_5:"N/A"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenses_incurred_on_publishing_6)?$getannuxureData[0]->expenses_incurred_on_publishing_6:"N/A"}}</td>
            
            </tr>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong>Grand Total</strong></td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->grand_total_candidate_agent)?$getannuxureData[0]->grand_total_candidate_agent:"0"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->expenses_incurred_on_publishing_4)?$getannuxureData[0]->expenses_incurred_on_publishing_4:"0"}}</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->grand_total_amt_incurred_by_pol_party)?$getannuxureData[0]->grand_total_amt_incurred_by_pol_party:"0"}}</td>
                <td style="border: 1px solid #454546; padding: 10px; " width="">{{!empty($getannuxureData[0]->total_expenditure)?$getannuxureData[0]->total_expenditure:"0"}}</td>
            
            </tr>
        </tbody>
    </table>
</div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<div  class="collapse show">
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>PART-III: ABSTRACT OF SOURCE OF FUNDS RAISED BY CANDIDATE</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
        <thead>
            <tr>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Sr. No.</th>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="65%">Particulars</th>
                <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="25%">Amount (in Rs.)</th>
            </tr>
            <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">I</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Amount of own funds used for the election campaign ( Enclose as per Schedule-7)</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getannuxureData[0]->amt_own_funds_election_compaign)?$getannuxureData[0]->amt_own_funds_election_compaign:"N/A"}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">II</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Lump sum amount received from the party (ies) in cash or cheque etc. (Enclose as per Schedule-8)</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getannuxureData[0]->lump_sum_amt_from_party)?$getannuxureData[0]->lump_sum_amt_from_party:"N/A"}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">III</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Lump sum amount received from any persion/company/firm/association/body of persons etc. as loan. gift or donation etc. (Enclose as per Schedule-9)</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getannuxureData[0]->lump_sum_amt_from_other)?$getannuxureData[0]->lump_sum_amt_from_other:"N/A"}}</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>Grand Total</strong></td>
                <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{!empty($getannuxureData[0]->grand_total_source_funds)?$getannuxureData[0]->grand_total_source_funds:"N/A"}}</strong></td>
            </tr>
        </tbody>
    </table>
</div>
</div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<table style="width:100%; border: 1px solid #000;" border="0" align="center">  
        <tr>
            <td>
                <table style="width:100%" align="center">
                <tbody>
                    <tr>
                        <th><strong>Acknowledegement Form</strong></th>
                    </tr>
                </tbody>
                </table>  
            </td>
        </tr>
    </table>  

    <div class=" text-left" style="width:100%;">
    <div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 1</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="6">Expenses in public meeting, rally, procession etc, (ie: other than those with Star Campaigners of the Political party)</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="35%" rowspan="2">Nature of Expenditure</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="45%" colspan="3">Source of Expenditure</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Amt. Incurred / Auth. by Candidate / Agent</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Amt. Incurred / by Pol. Party with Name</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Amt. Incurred by Others</th>
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
        </tr>
      </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Vehicles for Transporting Visitors</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[0]->total_amt)?$getSch1Data[0]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[0]->src_amt_incurred_cand)?$getSch1Data[0]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[0]->src_amt_incurred_pp)?$getSch1Data[0]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[0]->src_amt_incurred_other)?$getSch1Data[0]->src_amt_incurred_other:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Erecting Stage, Pandal & Furniture, Fixtures, Poles etc.</td>
          <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[1]->total_amt)?$getSch1Data[0]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[1]->src_amt_incurred_cand)?$getSch1Data[1]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[1]->src_amt_incurred_pp)?$getSch1Data[1]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[1]->src_amt_incurred_other)?$getSch1Data[1]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">  Arches & Barricades etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[2]->total_amt)?$getSch1Data[2]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[2]->src_amt_incurred_cand)?$getSch1Data[2]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[2]->src_amt_incurred_pp)?$getSch1Data[2]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[2]->src_amt_incurred_other)?$getSch1Data[2]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Flowers/ Garlands</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[3]->total_amt)?$getSch1Data[3]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[3]->src_amt_incurred_cand)?$getSch1Data[3]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[3]->src_amt_incurred_pp)?$getSch1Data[3]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[3]->src_amt_incurred_other)?$getSch1Data[3]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">5</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Hiring Loud Speakers, Microphone, Amplifiers, Comparers etc.</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[4]->total_amt)?$getSch1Data[4]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[4]->src_amt_incurred_cand)?$getSch1Data[4]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[4]->src_amt_incurred_pp)?$getSch1Data[4]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[4]->src_amt_incurred_other)?$getSch1Data[4]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">6</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Posters Land Bills, Pamphlets, Banners, Cut-outs, Hoardings</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[5]->total_amt)?$getSch1Data[5]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[5]->src_amt_incurred_cand)?$getSch1Data[5]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[5]->src_amt_incurred_pp)?$getSch1Data[5]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[5]->src_amt_incurred_other)?$getSch1Data[5]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">7</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Beverages like Tea, Water, Cold drink, Juice etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[6]->total_amt)?$getSch1Data[6]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[6]->src_amt_incurred_cand)?$getSch1Data[6]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[6]->src_amt_incurred_pp)?$getSch1Data[6]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[6]->src_amt_incurred_other)?$getSch1Data[6]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">8</td>
            <td style="border: 1px solid #454546; padding: 10px;" width=""> Digital TV-boards display, Projector display, Tickers boards, 3D display</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[7]->total_amt)?$getSch1Data[7]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[7]->src_amt_incurred_cand)?$getSch1Data[7]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[7]->src_amt_incurred_pp)?$getSch1Data[7]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[7]->src_amt_incurred_other)?$getSch1Data[7]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">9</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Expenses on celebrities, payment to Musicians, other Artists Remuneration etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[8]->total_amt)?$getSch1Data[8]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[8]->src_amt_incurred_cand)?$getSch1Data[8]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[8]->src_amt_incurred_pp)?$getSch1Data[8]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[8]->src_amt_incurred_other)?$getSch1Data[8]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">10</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Illumination items like Serial Lights, boards etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[9]->total_amt)?$getSch1Data[9]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[9]->src_amt_incurred_cand)?$getSch1Data[9]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[9]->src_amt_incurred_pp)?$getSch1Data[9]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[9]->src_amt_incurred_other)?$getSch1Data[9]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">11</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Expenses on transport, Helicopter/ aircraft / vehicles/ boats ete. charges (for self, celebrity or any other campaigner other than Star Campaigner)</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[10]->total_amt)?$getSch1Data[10]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[10]->src_amt_incurred_cand)?$getSch1Data[10]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[10]->src_amt_incurred_pp)?$getSch1Data[10]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[10]->src_amt_incurred_other)?$getSch1Data[10]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">12</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Power Consumption/ Generator charges</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[11]->total_amt)?$getSch1Data[11]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[11]->src_amt_incurred_cand)?$getSch1Data[11]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[11]->src_amt_incurred_pp)?$getSch1Data[11]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[11]->src_amt_incurred_other)?$getSch1Data[11]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">13</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Rent for Venue</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[12]->total_amt)?$getSch1Data[12]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[12]->src_amt_incurred_cand)?$getSch1Data[12]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[12]->src_amt_incurred_pp)?$getSch1Data[12]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[12]->src_amt_incurred_other)?$getSch1Data[12]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">14</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Guards & Security charges</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[13]->total_amt)?$getSch1Data[13]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[13]->src_amt_incurred_cand)?$getSch1Data[13]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[13]->src_amt_incurred_pp)?$getSch1Data[13]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[13]->src_amt_incurred_other)?$getSch1Data[13]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">15</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Boarding & lodging expenses of self, celebrity, party functionary or any other campaigner including Star Campaigner</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[14]->total_amt)?$getSch1Data[14]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[14]->src_amt_incurred_cand)?$getSch1Data[14]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[14]->src_amt_incurred_pp)?$getSch1Data[14]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[14]->src_amt_incurred_other)?$getSch1Data[14]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">16</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Others expenses</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[15]->total_amt)?$getSch1Data[15]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[15]->src_amt_incurred_cand)?$getSch1Data[15]->src_amt_incurred_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[15]->src_amt_incurred_pp)?$getSch1Data[15]->src_amt_incurred_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch1Data[15]->src_amt_incurred_other)?$getSch1Data[15]->src_amt_incurred_other:"N/A"}}</td>
        
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>Grand Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum_total_amt}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum_src_amt_incurred_cand}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum_src_amt_incurred_pp}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum_src_amt_incurred_other}}</strong></td>
    
    </tr>
    </tbody>
 </table>
</div>
<br><br><br>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 2</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="8">
      Expenditure in public meeting rally, procession etc. with the Star Campaigner(s) as apportioned to candidate (ie: other than those for general party propaganda)</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Date</th>
    <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Venue</th>

          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Name of the Star Campaigner(s) &amp; Name of the Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="60%" colspan="3">Amount of Expenditure on public meeting rally, procession etc. with the Star Campaigner(s) apportioned to the candidate (As other than for general party propaganda) in Rs. </th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%">Remarks If Any </th>
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">3</th>
      <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" colspan="3">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">6</th>
        </tr>
        <tr> 
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" colspan="3">Source of Expenditure </th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amount by Candidate/Agent </th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amount by Political Party </th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amount by Others </th>                  
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[0]->meetingdate)?$getSch2Data[0]->meetingdate:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[0]->venue)?$getSch2Data[0]->venue:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[0]->name_of_start_and_party)?$getSch2Data[0]->name_of_start_and_party:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[0]->src_amt_by_cand)?$getSch2Data[0]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[0]->src_amt_by_pp)?$getSch2Data[0]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[0]->src_amt_by_other)?$getSch2Data[0]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[0]->remarks)?$getSch2Data[0]->remarks:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[1]->meetingdate)?$getSch2Data[1]->meetingdate:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[1]->venue)?$getSch2Data[1]->venue:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[1]->name_of_start_and_party)?$getSch2Data[1]->name_of_start_and_party:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[1]->src_amt_by_cand)?$getSch2Data[1]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[1]->src_amt_by_pp)?$getSch2Data[1]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[1]->src_amt_by_other)?$getSch2Data[1]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[1]->remarks)?$getSch2Data[1]->remarks:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[2]->meetingdate)?$getSch2Data[2]->meetingdate:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[2]->venue)?$getSch2Data[2]->venue:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[2]->name_of_start_and_party)?$getSch2Data[2]->name_of_start_and_party:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[2]->src_amt_by_cand)?$getSch2Data[2]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[2]->src_amt_by_pp)?$getSch2Data[2]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[2]->src_amt_by_other)?$getSch2Data[2]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[2]->remarks)?$getSch2Data[2]->remarks:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[3]->meetingdate)?$getSch2Data[3]->meetingdate:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[3]->venue)?$getSch2Data[3]->venue:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[3]->name_of_start_and_party)?$getSch2Data[3]->name_of_start_and_party:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[3]->src_amt_by_cand)?$getSch2Data[3]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[3]->src_amt_by_pp)?$getSch2Data[3]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[3]->src_amt_by_other)?$getSch2Data[3]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[3]->remarks)?$getSch2Data[3]->remarks:"N/A"}}</td>
        
        </tr>
    <!--<tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">5</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[4]->meetingdate)?$getSch2Data[4]->meetingdate:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[4]->venue)?$getSch2Data[4]->venue:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[4]->name_of_start_and_party)?$getSch2Data[4]->name_of_start_and_party:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[4]->src_amt_by_cand)?$getSch2Data[4]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[4]->src_amt_by_pp)?$getSch2Data[4]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[4]->src_amt_by_other)?$getSch2Data[4]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch2Data[4]->remarks)?$getSch2Data[4]->remarks:"N/A"}}</td>
        
        </tr>-->
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="4"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong>{{$sum_src_amt_by_cand}}</strong></td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong>{{$sum_src_amt_by_pp}}</strong></td>

        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong>{{$sum_src_amt_by_other}}</strong></td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong></strong></td>


        </tr>
    </tbody>
    </table>
</div>
<br><br><br><br><br>
<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 3</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="7">Details of expenditure on campaign material, like handbills, pamphlets, posters, hoardings, banners, cut-outs, gates & arches, video and audio cassettes, CDs/DVDs, Loud Speakers, amplifiers, digital TV/board display, 3 D display etc. for candidate’s election campaign (ie: other than those covered in Schedule- 1 & 2)</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Nature of Expenses</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="56%" colspan="3">Source of Expenditure</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Remarks If Any </th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By Candidate / Agent</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By Pol. Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. by Others</th>                  
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[0]->nature_of_expense)?$getSch3Data[0]->nature_of_expense:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[0]->total_amt)?$getSch3Data[0]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[0]->src_amt_by_cand)?$getSch3Data[0]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[0]->src_amt_by_pp)?$getSch3Data[0]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[0]->src_amt_by_other)?$getSch3Data[0]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[0]->remarks)?$getSch3Data[0]->remarks:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[1]->nature_of_expense)?$getSch3Data[1]->nature_of_expense:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[1]->total_amt)?$getSch3Data[1]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[1]->src_amt_by_cand)?$getSch3Data[1]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[1]->src_amt_by_pp)?$getSch3Data[1]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[1]->src_amt_by_other)?$getSch3Data[1]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[1]->remarks)?$getSch3Data[1]->remarks:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[2]->nature_of_expense)?$getSch3Data[2]->nature_of_expense:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[2]->total_amt)?$getSch3Data[2]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[2]->src_amt_by_cand)?$getSch3Data[2]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[2]->src_amt_by_pp)?$getSch3Data[2]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[2]->src_amt_by_other)?$getSch3Data[2]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[2]->remarks)?$getSch3Data[2]->remarks:"N/A"}}</td>
        
        </tr>
    
    <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">4</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[3]->nature_of_expense)?$getSch3Data[3]->nature_of_expense:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[3]->total_amt)?$getSch3Data[3]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[3]->src_amt_by_cand)?$getSch3Data[3]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[3]->src_amt_by_pp)?$getSch3Data[3]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[3]->src_amt_by_other)?$getSch3Data[3]->src_amt_by_other:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch3Data[3]->remarks)?$getSch3Data[3]->remarks:"N/A"}}</td>
        
        </tr>
        
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="2"><strong>Total</strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong>{{$sum3_total_amt}}</strong></td>

        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong>{{$sum3_src_amt_by_cand}}</strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong>{{$sum3_src_amt_by_pp}}</strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong>{{$sum3_src_amt_by_other}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan=""><strong></strong></td>

        </tr>
    </tbody>
    </table><br /><br />
</div>
<br /><br /><br /><br /><br /><br /><br /><br />


<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 4</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="11">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in privately owned newspapers/TV/radio channels etc.</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Nature of medium (electronic/print) and duration</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Name and address of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
           <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">Address of Media Provider</th>
           <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">Price of Media</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="14%" rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Commission of Agency</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Total Amount in Rs. Col. (6)+(8)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="22%" colspan="3">Sources of Expenditure</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By candidate/agent</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By Pol. Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By others</th>                  
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">8</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">9</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">10</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">11</th>
        </tr>
      </thead>
      <tbody>
    
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->nature_of_medium)?$getSch4Data[0]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->name_of_media)?$getSch4Data[0]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->address_of_media)?$getSch4Data[0]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->price_of_the_media)?$getSch4Data[0]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->name_address_of_agency)?$getSch4Data[0]->name_address_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->commission_of_agency)?$getSch4Data[0]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->total_amt)?$getSch4Data[0]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->src_amt_by_cand)?$getSch4Data[0]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->src_amt_by_pp)?$getSch4Data[0]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[0]->src_amt_by_other)?$getSch4Data[0]->src_amt_by_other:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->nature_of_medium)?$getSch4Data[1]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->name_of_media)?$getSch4Data[1]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->address_of_media)?$getSch4Data[1]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->price_of_the_media)?$getSch4Data[1]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->name_address_of_agency)?$getSch4Data[1]->name_address_of_agency:"N/A"}}</td><td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->commission_of_agency)?$getSch4Data[1]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->total_amt)?$getSch4Data[1]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->src_amt_by_cand)?$getSch4Data[1]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->src_amt_by_pp)?$getSch4Data[1]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[1]->src_amt_by_other)?$getSch4Data[1]->src_amt_by_other:"N/A"}}</td>
         </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->nature_of_medium)?$getSch4Data[2]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->name_of_media)?$getSch4Data[2]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->address_of_media)?$getSch4Data[2]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->price_of_the_media)?$getSch4Data[2]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->name_address_of_agency)?$getSch4Data[2]->name_address_of_agency:"N/A"}}</td><td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->commission_of_agency)?$getSch4Data[2]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->total_amt)?$getSch4Data[2]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->src_amt_by_cand)?$getSch4Data[2]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->src_amt_by_pp)?$getSch4Data[2]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[2]->src_amt_by_other)?$getSch4Data[2]->src_amt_by_other:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->nature_of_medium)?$getSch4Data[3]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->name_of_media)?$getSch4Data[3]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->address_of_media)?$getSch4Data[3]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->price_of_the_media)?$getSch4Data[3]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->name_address_of_agency)?$getSch4Data[3]->name_address_of_agency:"N/A"}}</td><td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->commission_of_agency)?$getSch4Data[3]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->total_amt)?$getSch4Data[3]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->src_amt_by_cand)?$getSch4Data[3]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->src_amt_by_pp)?$getSch4Data[3]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4Data[3]->src_amt_by_other)?$getSch4Data[3]->src_amt_by_other:"N/A"}}</td>
        </tr>
    
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="4"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4_price_of_the_media}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4_commission_of_agency}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4_total_amt}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4_src_amt_by_cand}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4_src_amt_by_pp}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4_src_amt_by_other}}</strong></td>
        </tr>
    </tbody>
    </table>
</div>
<br /><br /><br /><br /><br /><br /><br /><br />

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 4A</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="11">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in newspapers/TV/radio channels, owned by the candidate or by the political party sponsoring the candidate
.</th>
        </tr>
    
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Nature of medium (electronic/print) and duration</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Name and address of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
           <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">Address of Media Provider</th>
           <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">Price of Media</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="14%" rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Commission of Agency</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Total Amount in Rs. Col. (6)+(8)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="22%" colspan="3">Sources of Expenditure</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By candidate/agent</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By Pol. Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By others</th>                  
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">8</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">9</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">10</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">11</th>
        </tr>
      </thead>
      <tbody>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->nature_of_medium)?$getSch4aData[0]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->name_of_media)?$getSch4aData[0]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->address_of_media)?$getSch4aData[0]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->price_of_the_media)?$getSch4aData[0]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->name_address_of_agency)?$getSch4aData[0]->name_address_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->commission_of_agency)?$getSch4aData[0]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->total_amt)?$getSch4aData[0]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->src_amt_by_cand)?$getSch4aData[0]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->src_amt_by_pp)?$getSch4aData[0]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[0]->src_amt_by_other)?$getSch4aData[0]->src_amt_by_other:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->nature_of_medium)?$getSch4aData[1]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->name_of_media)?$getSch4aData[1]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->address_of_media)?$getSch4aData[1]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->price_of_the_media)?$getSch4aData[1]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->name_address_of_agency)?$getSch4aData[1]->name_address_of_agency:"N/A"}}</td><td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->commission_of_agency)?$getSch4aData[1]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->total_amt)?$getSch4aData[1]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->src_amt_by_cand)?$getSch4aData[1]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->src_amt_by_pp)?$getSch4aData[1]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[1]->src_amt_by_other)?$getSch4aData[1]->src_amt_by_other:"N/A"}}</td>
         </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->nature_of_medium)?$getSch4aData[2]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->name_of_media)?$getSch4aData[2]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->address_of_media)?$getSch4aData[2]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->price_of_the_media)?$getSch4aData[2]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->name_address_of_agency)?$getSch4aData[2]->name_address_of_agency:"N/A"}}</td><td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->commission_of_agency)?$getSch4aData[2]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->total_amt)?$getSch4aData[2]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->src_amt_by_cand)?$getSch4aData[2]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->src_amt_by_pp)?$getSch4aData[2]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->src_amt_by_other)?$getSch4aData[2]->src_amt_by_other:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->nature_of_medium)?$getSch4aData[3]->nature_of_medium:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->name_of_media)?$getSch4aData[3]->name_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->address_of_media)?$getSch4aData[3]->address_of_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->price_of_the_media)?$getSch4aData[2]->price_of_the_media:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[2]->name_address_of_agency)?$getSch4aData[2]->name_address_of_agency:"N/A"}}</td><td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->commission_of_agency)?$getSch4aData[3]->commission_of_agency:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->total_amt)?$getSch4aData[3]->total_amt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->src_amt_by_cand)?$getSch4aData[3]->src_amt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->src_amt_by_pp)?$getSch4aData[3]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch4aData[3]->src_amt_by_other)?$getSch4aData[3]->src_amt_by_other:"N/A"}}</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4a_price_of_the_media}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>

        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4a_commission_of_agency}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4a_total_amt}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4a_src_amt_by_cand}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4a_src_amt_by_pp}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum4a_src_amt_by_other}}</strong></td>
        </tr>
    </tbody>
    </table><br /><br /><br />
</div>


      

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 5</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="10">Details of expenditure on campaign vehicle (s) and poll expenditure on vehicle (s) for candidate's election campaign</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Regn. No. of Vehicle & Type of vehicle</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="30%" colspan="3">Hiring Charges of vehicle</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">No. of Days for which used</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Total amt. incurred/auth in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="30%" colspan="3">Source of Expenditure</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Rate for Hiring of vehicle/ Maintenance</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Fuel Charges (If not covered under hiring)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Driver;s Charges (If not covered under hiring)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By candidate/agent</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By Pol. Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By others</th>                  
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3a</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3b</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3c</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">8</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->regn_no_of_vehicle)?$getSch5Data[0]->regn_no_of_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->hir_rate_for_vehicle)?$getSch5Data[0]->hir_rate_for_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->hir_fuel_charges)?$getSch5Data[0]->hir_fuel_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->hir_driver_charges)?$getSch5Data[0]->hir_driver_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->no_of_days)?$getSch5Data[0]->no_of_days:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->total_amt_incurred)?$getSch5Data[0]->total_amt_incurred:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->src_amt_by_cand)?$getSch5Data[0]->src_amt_by_cand:"N/A"}}</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->src_amt_by_pp)?$getSch5Data[0]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[0]->src_amt_by_other)?$getSch5Data[0]->src_amt_by_other:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->regn_no_of_vehicle)?$getSch5Data[1]->regn_no_of_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->hir_rate_for_vehicle)?$getSch5Data[1]->hir_rate_for_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->hir_fuel_charges)?$getSch5Data[1]->hir_fuel_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->hir_driver_charges)?$getSch5Data[1]->hir_driver_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->no_of_days)?$getSch5Data[1]->no_of_days:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->total_amt_incurred)?$getSch5Data[1]->total_amt_incurred:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->src_amt_by_cand)?$getSch5Data[1]->src_amt_by_cand:"N/A"}}</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->src_amt_by_pp)?$getSch5Data[1]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[1]->src_amt_by_other)?$getSch5Data[1]->src_amt_by_other:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->regn_no_of_vehicle)?$getSch5Data[2]->regn_no_of_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->hir_rate_for_vehicle)?$getSch5Data[2]->hir_rate_for_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->hir_fuel_charges)?$getSch5Data[2]->hir_fuel_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->hir_driver_charges)?$getSch5Data[2]->hir_driver_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->no_of_days)?$getSch5Data[2]->no_of_days:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->total_amt_incurred)?$getSch5Data[2]->total_amt_incurred:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->src_amt_by_cand)?$getSch5Data[2]->src_amt_by_cand:"N/A"}}</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->src_amt_by_pp)?$getSch5Data[2]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[2]->src_amt_by_other)?$getSch5Data[2]->src_amt_by_other:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->regn_no_of_vehicle)?$getSch5Data[3]->regn_no_of_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->hir_rate_for_vehicle)?$getSch5Data[3]->hir_rate_for_vehicle:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->hir_fuel_charges)?$getSch5Data[3]->hir_fuel_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->hir_driver_charges)?$getSch5Data[3]->hir_driver_charges:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->no_of_days)?$getSch5Data[3]->no_of_days:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->total_amt_incurred)?$getSch5Data[3]->total_amt_incurred:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->src_amt_by_cand)?$getSch5Data[3]->src_amt_by_cand:"N/A"}}</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->src_amt_by_pp)?$getSch5Data[3]->src_amt_by_pp:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch5Data[3]->src_amt_by_other)?$getSch5Data[3]->src_amt_by_other:"N/A"}}</td>
        
        </tr>
    <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="2"><strong>Total</strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum5_hir_rate_for_vehicle}}</strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum5_hir_fuel_charges}}</strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum5_hir_driver_charges}}</strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>

        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum5_total_amt_incurred}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum5_src_amt_by_cand}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum5_src_amt_by_pp}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum5_src_amt_by_other}}</strong></td>
        </tr>
    </tbody>
    </table><br /><br /><br />
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 6</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="10">Details of expenditure on campaign workers/agents and on candidate's booths (kiosks) outside polling stations for distribution of voter's slips</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Venue Date</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Venue ADD.</th>
       
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="40%" colspan="3">Expenses on campaign workers/agents</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" rowspan="2">Total amt. incurred/auth. in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="30%" colspan="3">Source of Expenditure</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="20">Nature of Expenses</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10">Rate</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10">No. Of Workers / agents No. of Kiosks</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By candidate/agent</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By Pol. Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Amt. By others</th>                  
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
      <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
      <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4a</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4b</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4c</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">8</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->venu_date)?$getSch6Data[0]->venu_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->venu_details)?$getSch6Data[0]->venu_details:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->expense_nature)?$getSch6Data[0]->expense_nature:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->expense_nature_rate)?$getSch6Data[0]->expense_nature_rate:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->worker_agents_count)?$getSch6Data[0]->worker_agents_count:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->total_amnt)?$getSch6Data[0]->total_amnt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->source_amnt_by_cand)?$getSch6Data[0]->source_amnt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->source_amnt_by_polparty)?$getSch6Data[0]->source_amnt_by_polparty:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[0]->source_amnt_by_others)?$getSch6Data[0]->source_amnt_by_others:"N/A"}}</td>

        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->venu_date)?$getSch6Data[1]->venu_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->venu_details)?$getSch6Data[1]->venu_details:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->expense_nature)?$getSch6Data[1]->expense_nature:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->expense_nature_rate)?$getSch6Data[1]->expense_nature_rate:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->worker_agents_count)?$getSch6Data[1]->worker_agents_count:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->total_amnt)?$getSch6Data[1]->total_amnt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->source_amnt_by_cand)?$getSch6Data[1]->source_amnt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->source_amnt_by_polparty)?$getSch6Data[1]->source_amnt_by_polparty:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[1]->source_amnt_by_others)?$getSch6Data[1]->source_amnt_by_others:"N/A"}}</td>

      
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->venu_date)?$getSch6Data[2]->venu_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->venu_details)?$getSch6Data[2]->venu_details:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->expense_nature)?$getSch6Data[2]->expense_nature:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->expense_nature_rate)?$getSch6Data[2]->expense_nature_rate:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->worker_agents_count)?$getSch6Data[2]->worker_agents_count:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->total_amnt)?$getSch6Data[2]->total_amnt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->source_amnt_by_cand)?$getSch6Data[2]->source_amnt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->source_amnt_by_polparty)?$getSch6Data[2]->source_amnt_by_polparty:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[2]->source_amnt_by_others)?$getSch6Data[2]->source_amnt_by_others:"N/A"}}</td>

      
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->venu_date)?$getSch6Data[3]->venu_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->venu_details)?$getSch6Data[3]->venu_details:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->expense_nature)?$getSch6Data[3]->expense_nature:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->expense_nature_rate)?$getSch6Data[3]->expense_nature_rate:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->worker_agents_count)?$getSch6Data[3]->worker_agents_count:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->total_amnt)?$getSch6Data[3]->total_amnt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->source_amnt_by_cand)?$getSch6Data[3]->source_amnt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->source_amnt_by_polparty)?$getSch6Data[3]->source_amnt_by_polparty:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[3]->source_amnt_by_others)?$getSch6Data[3]->source_amnt_by_others:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">5</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->venu_date)?$getSch6Data[4]->venu_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->venu_details)?$getSch6Data[4]->venu_details:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->expense_nature)?$getSch6Data[4]->expense_nature:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->expense_nature_rate)?$getSch6Data[4]->expense_nature_rate:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->worker_agents_count)?$getSch6Data[4]->worker_agents_count:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->total_amnt)?$getSch6Data[4]->total_amnt:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->source_amnt_by_cand)?$getSch6Data[4]->source_amnt_by_cand:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->source_amnt_by_polparty)?$getSch6Data[4]->source_amnt_by_polparty:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch6Data[4]->source_amnt_by_others)?$getSch6Data[4]->source_amnt_by_others:"N/A"}}</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="4"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum6_expense_nature_rate}}</strong></td>

    <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum6_total_amnt}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum6_source_amnt_by_cand}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum6_source_amnt_by_polparty}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum6_source_amnt_by_others}}</strong></td>
        </tr>
    </tbody>
    </table><br />
</div>
<br /><br /><br /><br /><br /><br /><br /><br />
<div  class="collapse show">
     <p 
style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 7</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="5">Details of Amount of own fund used for the election campaign</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">S. No</th>
      <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="20%">Date</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="20%" >Cash/DD/Cheque no. etc. with details of drawee bank</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="35%">Remarks</th>
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
      
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch7Data[0]->submit_date)?$getSch7Data[0]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch7Data[0]->payment_type)?$getSch7Data[0]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch7Data[0]->amount)?$getSch7Data[0]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch7Data[0]->remarks)?$getSch7Data[0]->remarks:"N/A"}}</td>
        </tr>
     
       
    <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >{{!empty($getSch7Data[1]->submit_date)?$getSch7Data[1]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">{{!empty($getSch7Data[1]->payment_type)?$getSch7Data[1]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >{{!empty($getSch7Data[1]->amount)?$getSch7Data[1]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >{{!empty($getSch7Data[1]->remarks)?$getSch7Data[1]->remarks:"N/A"}}</td>
        </tr>
     
        
    <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch7Data[2]->submit_date)?$getSch7Data[2]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch7Data[2]->payment_type)?$getSch7Data[2]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch7Data[2]->amount)?$getSch7Data[2]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch7Data[2]->remarks)?$getSch7Data[2]->remarks:"N/A"}}</td>
        </tr>
     
        
    <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >{{!empty($getSch7Data[3]->submit_date)?$getSch7Data[3]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >{{!empty($getSch7Data[3]->payment_type)?$getSch7Data[3]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >{{!empty($getSch7Data[3]->amount)?$getSch7Data[3]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >{{!empty($getSch7Data[3]->remarks)?$getSch7Data[3]->remarks:"N/A"}}</td>
        </tr>
    
    <tr>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" colspan="2"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong></strong></td>
      
    <td style="border: 1px solid #454546; padding: 10px;background-color: #e8e8ea;" width=""><strong>{{$sum7_total_amnt}}</strong></td>
    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong></strong></td>
        </tr>
      
         
       
    
    </tbody>
    </table><br /><br /><br /><br /><br /><br />
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 8</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="9">Details of Lump sum amount received from the party (ies) in cash or cheque or DD or by Account Transfer</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="25%">Name of the Political Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Date</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%" >Cash/DD/Cheque no etc. with details of drawee bank</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="25%">Remarks, If Any</th>
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
           
        </tr>
      </thead>
      <tbody>
        <tr>
           <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[0]->party_id)?$getSch8Data[0]->party_id:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch8Data[0]->submit_date)?$getSch8Data[0]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[0]->payment_type)?$getSch8Data[0]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[0]->amount)?$getSch8Data[0]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[0]->remarks)?$getSch8Data[0]->remarks:"N/A"}}</td>
        </tr>
        </tr>
         
    <tr>
           <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch8Data[1]->party_id)?$getSch8Data[1]->party_id:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch8Data[1]->submit_date)?$getSch8Data[1]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[1]->payment_type)?$getSch8Data[1]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[1]->amount)?$getSch8Data[1]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[1]->remarks)?$getSch8Data[1]->remarks:"N/A"}}</td>
        </tr>
        </tr>
    <tr>
           <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[2]->party_id)?$getSch8Data[2]->party_id:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[2]->submit_date)?$getSch8Data[2]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[2]->payment_type)?$getSch8Data[2]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch8Data[2]->amount)?$getSch8Data[2]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[2]->remarks)?$getSch8Data[2]->remarks:"N/A"}}</td>
        </tr>
        </tr>
        
    <tr>
           <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >4</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[3]->party_id)?$getSch8Data[3]->party_id:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[3]->submit_date)?$getSch8Data[3]->submit_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[3]->payment_type)?$getSch8Data[3]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch8Data[3]->amount)?$getSch8Data[3]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch8Data[3]->remarks)?$getSch8Data[3]->remarks:"N/A"}}</td>
        </tr>
        </tr>
       
    <tr>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" colspan="3"><strong>Total</strong></td>
        
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong></strong></td>
    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong>{{$sum8_total_amnt}}</strong></td>
    <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong></strong></td>
        </tr>
    </tbody>     
        
    </table><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 9</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="8">Details of Lump sum amount received from any person/company/firm/associations/body of persons etc. as loan, gift or donation etc.</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%">Name</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="22%">Address</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Date</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Cash/DD/Cheque no. etc. with details of drawee bank</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Mention whether loan, gift or donation etc.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="30%">Remarks</th>
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">8</th>
    

        </tr>
      </thead>
      <tbody>
    
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch9Data[0]->name)?$getSch9Data[0]->name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[0]->address)?$getSch9Data[0]->address:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[0]->submit_date)?$getSch9Data[0]->submit_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[0]->payment_type)?$getSch9Data[0]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[0]->amount_details)?$getSch9Data[0]->amount_details:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[0]->amount)?$getSch9Data[0]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[0]->remarks)?$getSch9Data[0]->remarks:"N/A"}}</td>
        </tr>
        
    <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >2</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[1]->name)?$getSch9Data[1]->name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[1]->address)?$getSch9Data[1]->address:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[1]->submit_date)?$getSch9Data[1]->submit_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[1]->payment_type)?$getSch9Data[1]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[1]->amount_details)?$getSch9Data[1]->amount_details:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[1]->amount)?$getSch9Data[1]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[1]->remarks)?$getSch9Data[1]->remarks:"N/A"}}</td>
        
        </tr>
    
    <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[2]->name)?$getSch9Data[2]->name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[2]->address)?$getSch9Data[2]->address:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[2]->submit_date)?$getSch9Data[2]->submit_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[2]->payment_type)?$getSch9Data[2]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[2]->amount_details)?$getSch9Data[2]->amount_details:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[2]->amount)?$getSch9Data[2]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[2]->remarks)?$getSch9Data[2]->remarks:"N/A"}}</td>
        
        </tr>
        
    <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="" >4</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[3]->name)?$getSch9Data[3]->name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[3]->address)?$getSch9Data[3]->address:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[3]->submit_date)?$getSch9Data[3]->submit_date:"N/A"}}</td>
      <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[3]->payment_type)?$getSch9Data[3]->payment_type:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[3]->amount_details)?$getSch9Data[3]->amount_details:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[3]->amount)?$getSch9Data[3]->amount:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" >{{!empty($getSch9Data[3]->remarks)?$getSch9Data[3]->remarks:"N/A"}}</td>
        
        </tr>
        
        <tr>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="" colspan="2"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong></strong></td>
        <td style="border: 1px solid #454546; padding: 10px;background-color: #e8e8ea;" width=""><strong></strong></td>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong></strong></td>
        <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong></strong></td>
       
    <td style="border: 1px solid #454546; padding: 10px;background-color: #e8e8ea;" width=""><strong>{{$sum9_total_amnt}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;background-color: #e8e8ea;" width=""><strong></strong></td>

        </tr>
    </tbody>     
        
    </table><br /><br /><br />
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 10</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="8">Details of expenditure incurred on publishing criminal antecedents, if any in newspaper and TV Channel</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="40%" colspan="3">Newspaper</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="40%" colspan="3">Television</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Mode of payment (electronic/cheque/DD/Cash) (PI. specify)</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Name of Newspaper</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Date of publishing</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Expenses that may have been incurred (in Rs.)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Name of channel</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Date & Time of insertion/telecast</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">Expenses that may have been incurred (in Rs.)</th>                 
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">8</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[0]->newspaper_name)?$getSch10Data[0]->newspaper_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[0]->news_publishing_date)?$getSch10Data[0]->news_publishing_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[0]->expense_on_news)?$getSch10Data[0]->expense_on_news:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[0]->channel_name)?$getSch10Data[0]->channel_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[0]->telecost_dateTime)?$getSch10Data[0]->telecost_dateTime:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[0]->expense_on_channel)?$getSch10Data[0]->expense_on_channel:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[0]->payment_type)?$getSch10Data[0]->payment_type:"N/A"}}</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
       <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[1]->newspaper_name)?$getSch10Data[0]->newspaper_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[1]->news_publishing_date)?$getSch10Data[1]->news_publishing_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[1]->expense_on_news)?$getSch10Data[1]->expense_on_news:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[1]->channel_name)?$getSch10Data[1]->channel_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[1]->telecost_dateTime)?$getSch10Data[1]->telecost_dateTime:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[1]->expense_on_channel)?$getSch10Data[1]->expense_on_channel:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[1]->payment_type)?$getSch10Data[1]->payment_type:"N/A"}}</td>
        
            
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
           <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[2]->newspaper_name)?$getSch10Data[2]->newspaper_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[2]->news_publishing_date)?$getSch10Data[2]->news_publishing_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[2]->expense_on_news)?$getSch10Data[2]->expense_on_news:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[2]->channel_name)?$getSch10Data[2]->channel_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[2]->telecost_dateTime)?$getSch10Data[2]->telecost_dateTime:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[2]->expense_on_channel)?$getSch10Data[2]->expense_on_channel:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[2]->payment_type)?$getSch10Data[2]->payment_type:"N/A"}}</td>
        
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[3]->newspaper_name)?$getSch10Data[3]->newspaper_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[3]->news_publishing_date)?$getSch10Data[3]->news_publishing_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[3]->expense_on_news)?$getSch10Data[3]->expense_on_news:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[3]->channel_name)?$getSch10Data[3]->channel_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[3]->telecost_dateTime)?$getSch10Data[3]->telecost_dateTime:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[3]->expense_on_channel)?$getSch10Data[3]->expense_on_channel:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[3]->payment_type)?$getSch10Data[3]->payment_type:"N/A"}}</td>
        
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">5</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[4]->newspaper_name)?$getSch10Data[4]->newspaper_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[4]->news_publishing_date)?$getSch10Data[4]->news_publishing_date:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[4]->expense_on_news)?$getSch10Data[4]->expense_on_news:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[4]->channel_name)?$getSch10Data[4]->channel_name:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[4]->telecost_dateTime)?$getSch10Data[4]->telecost_dateTime:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[4]->expense_on_channel)?$getSch10Data[4]->expense_on_channel:"N/A"}}</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">{{!empty($getSch10Data[4]->payment_type)?$getSch10Data[4]->payment_type:"N/A"}}</td>
        
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>Grand Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>{{$sum10_expense_on_news}}</strong></td>

    <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>
    <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="1"><strong>{{$sum10_expense_on_channel}}</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong></strong></td>
        </tr>
    </tbody>
    </table>


</div>

</div>
    <table style="width:100%; border-collapse: collapse; margin-top: 30px;" align="center" border="1" cellpadding="5">
        <tbody>
            <tr>
                <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
        </tbody>
    </table>
    
    </body>
</html>