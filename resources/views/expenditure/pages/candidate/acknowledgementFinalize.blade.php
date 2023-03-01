<?php
$pc_no = !empty($profileData[0]) ? $profileData[0]->pc_no : '';
$st_code = !empty($profileData[0]) ? $profileData[0]->st_code : '';
$party_id = !empty($profileData[0]) ? $profileData[0]->party_id : '';

$candiatePcName = getpcbypcno($st_code, $pc_no);
$candiatePcName = !empty($candiatePcName) ? $candiatePcName->PC_NAME : '---';
$stateName = getstatebystatecode($st_code);
$stateName = !empty($stateName) ? $stateName->ST_NAME : '---';
$partyname = getpartybyid($party_id);
$partyname = !empty($partyname) ? $partyname->PARTYNAME : '---';

$candidateId = !empty($profileData[0]) ? $profileData[0]->candidate_id : 0;
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
        <th  style="width:49%" align="left" style="border-bottom: 1px solid #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/images/logo/suvidha-logo.png" alt=""  width="100" border="0"/></th>
        <th  style="width:49%" align="right" style="border-bottom: 1px solid #d7d7d7;">
            SECRETARIAT OF THE<br> ELECTION COMMISSION OF INDIA<br> Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
        </th>
        </tr>
    </thead>
    </table><br /><br />
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
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="30%">Anumula Vamsikrishna</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="10%">II</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="60%">Number and name of Constituency</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="30%">1 - Aruku  </td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="10%">III</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="60%">Name of State/Union Territory</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="30%">Andhra Pradesh</td>
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
                <td style="border: 1px solid #454546; padding: 10px;" width="30%">D. No. 6-129/A/1,Vaddera Colony</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="10%">VII</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="60%">If candidate is set up by a political party, Please mention the name of the political party</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="30%">Independent</td>
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
                <td style="border: 1px solid #454546; padding: 10px;" width="25%">Andhra Pradesh</td>
                 <td style="border: 1px solid #454546; padding: 10px;" width="25%">Anumula Vamsikrishna</td>
            </tr>
        </tbody>
    </table> 
</div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
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
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">l. b. expenditure in public meeting rally, procession etc. with the star Campaigner(s) (ie other than those for general party propaganda) (Enclose as per Schedule-2)</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">II</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Campaign materials other,rally, procession etc. mentioned in S. No.I above(Enclose as per Schedule-3)</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">III</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Campaign, through print and electronic media including cable network, bulk SMS or internet and Social media (Enclose as per Schedule-4)</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">IV</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Expenditure on campaign vehicle(s), used by candidate(Enclose as per schedule-5)</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            </tr>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">V</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Expenses of campaign workers / agents (Enclose as per Schedule —6)</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">VI</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Any other campaign expenditure</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">VII</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Expenses incurred on publishing of declaration regarding criminal cases (Enclose as per Schedule-10)*</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            </tr>
            <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong>Grand Total</strong></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong>0</strong></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong>0</strong></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong>0</strong></td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""><strong>0</strong></td>
            </tr>
        </tbody>
    </table>
</div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
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
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width="">II</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">Lump sum amount received from the party (ies) in cash or cheque etc. (Enclose as per Schedule-8)</td>
                <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">III</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Lump sum amount received from any persion/company/firm/association/body of persons etc. as loan. gift or donation etc. (Enclose as per Schedule-9)</td>
                <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            </tr>
             <tr>
                <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
                <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>Grand Total</strong></td>
                <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
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
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Erecting Stage, Pandal & Furniture, Fixtures, Poles etc.</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">  Arches & Barricades etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Flowers/ Garlands</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">5</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Hiring Loud Speakers, Microphone, Amplifiers, Comparers etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">6</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Posters Land Bills, Pamphlets, Banners, Cut-outs, Hoardings</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">7</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Beverages like Tea, Water, Cold drink, Juice etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">8</td>
            <td style="border: 1px solid #454546; padding: 10px;" width=""> Digital TV-boards display, Projector display, Tickers boards, 3D display</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">9</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Expenses on celebrities, payment to Musicians, other Artists Remuneration etc.</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">10</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Illumination items like Serial Lights, boards etc.</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">11</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Expenses on transport, Helicopter/ aircraft / vehicles/ boats ete. charges (for self, celebrity or any other campaigner other than Star Campaigner)</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">12</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Power Consumption/ Generator charges</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">13</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Rent for Venue</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">14</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Guards & Security charges</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">15</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Boarding & lodging expenses of self, celebrity, party functionary or any other campaigner including Star Campaigner</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">16</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Others expenses</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>Grand Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
    </tr>
    </tbody>
 </table>
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 2</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="7">Expenditure in public meeting rally, procession etc. with the Star Campaigner(s) as apportioned to candidate (ie: other than those for general party propaganda)</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Date and Venue</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">Name of the Star Campaigner(s) &amp; Name of the Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="60%" colspan="3">Amount of Expenditure on public meeting rally, procession etc. with the Star Campaigner(s) apportioned to the candidate (As other than for general party propaganda) in Rs. </th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%">Remarks If Any </th>
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" colspan="3">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" rowspan="3">5</th>
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
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>
    </table>
</div>

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
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>
    </table><br /><br />
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 4</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="8">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in privately owned newspapers/TV/radio channels etc.</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%" rowspan="2">Nature of medium (electronic/print) and duration</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%" rowspan="2">Name and address of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="17%" rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%" rowspan="2">Total Amount in Rs. Col. (3)+(4)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="36%" colspan="3">Sources of Expenditure</th>
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
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="4"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>
    </table><br /><br />
</div>


<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 4A</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="8">Details of expenditure on campaign through print and electronic media including cable network, buld SMS or Internet or social media, news items/TV/radio channel etc, including the paid news so decided by MCMC or voluntarily admitted by the candidate. The details should include the expenditure incurred on all such news items appearing in newspapers/TV/radio channels, owned by the candidate or by the political party sponsoring the candidate.</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%" rowspan="2">Nature of medium (electronic/print) and duration</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%" rowspan="2">Name and address of media provider (print/electronic/SMS/Voice/cable TV, social media etc.)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="17%" rowspan="2">Name and address of agency, reporter, stringer, company or any person to whom charges/commission etc. paid/payable, if any</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%" rowspan="2">Total Amount in Rs. Col. (3)+(4)</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="36%" colspan="3">Sources of Expenditure</th>
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
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="4"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>
    </table><br /><br />
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
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="6"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
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
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="9">Details of expenditure on campaign workers/agents and on candidate's booths (kiosks) outside polling stations for distribution of voter's slips</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="8%" rowspan="2">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%" rowspan="2">Date and Venue</th>
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
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3a</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3b</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3c</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">7</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Candidate's booths (Kiosks) set up for distribution of voter's slips</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Campaign workers honorarium/salary etc.</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Boarding</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">Lodging</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">5</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">Others</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
             <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="5"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>
    </table><br />
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 7</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">     
        <thead class="text-center">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="7">Details of Amount of own fund used for the election campaign</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Date</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Cash</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="30%">DD/Cheque no. etc. with details of drawee bank</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Remarks</th>
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
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>
    </table><br /><br />
</div>

<div  class="collapse show">
     <p style="text-align: center; font-size: 11pt; font-family: Arial;"><b>Details of Elections Funds and Expenditure of Candidate</b></p>
    <p style="text-align: center; font-size: 11pt; font-family: Arial; background-color: #b22682; color: #ffffff; padding: 6px;"><b>SCHEDULE - 8</b></p>
    <table style="width:100%; font-size: 9pt; font-family: Arial; border-collapse: collapse; border: 1px solid #dfe4ea;" border="0" align="center" cellpadding="5" bgcolor="#f8f9f9">
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="100%" colspan="8">Details of Lump sum amount received from the party (ies) in cash or cheque or DD or by Account Transfer</th>
        </tr>
        <tr>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="10%">S. No</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Name of the Political Party</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Date</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Cash</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">DD/Cheque no etc. with details of drawee bank</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Remarks, If Any</th>
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
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>     
        
    </table><br /><br />
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
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%">Name and Address</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">Date</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="13%">Cash</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="15%">DD/Cheque no. etc. with details of drawee bank</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%">Mention whether loan, gift or donation etc.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%">Total Amount in Rs.</th>
          <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff" width="12%">Remarks</th>
        </tr>
        <tr>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">1</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">2</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">3</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">4</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">5</th>
            <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color: #ffffff">6</th>
             <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color:#ffffff">7</th>
             <th style="border: 1px solid #ffffff; padding: 10px; background-color: #f0587e; color:#ffffff">8</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">1</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">2</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">3</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width="">4</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>     
        
    </table><br /><br /><br /><br />
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
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
         <tr>
            <td style="border: 1px solid #454546; padding: 10px;" width=""></td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px;" width="">0</td>
        </tr>
        <tr>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width=""></td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
            <td style="border: 1px solid #454546; padding: 10px; background-color: #e8e8ea;" width="">0</td>
        </tr>
        <tr>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="4"><strong>Grand Total</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width="" colspan="3"><strong>0</strong></td>
        <td style="border: 1px solid #454546; padding: 10px;" width=""><strong>0</strong></td>
        </tr>
    </tbody>
    </table><br /><br /><br/><br/><br/> <br/><br/><br/><br/> <br/> <br/><br/><br/> <br/> 
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