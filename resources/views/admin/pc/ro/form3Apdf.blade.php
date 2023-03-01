<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>NOTICE OF NOMINATIONS</title>        
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
      <!--HEADER STARTS HERE-->
      <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
          <thead>
           <tr>
              <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img style="width:20%;" src="{{ asset('admintheme/img/logo/eci-logo.png') }}"></th>
               <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                   SECRETARIAT OF THE<br>
                   ELECTION COMMISSION OF INDIA<br>
                   Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
               </th>
           </tr>
         </thead>
       </table>
   <!--HEADER ENDS HERE-->
      
       <?php $i=0;   $totalag=0;  $totalvg=0; $totalrecg=0; $totalwg=0; $totalaccg=0; $totalrg=0; $totalg=0; ?>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:49%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                         <td><strong>State: {{$state->ST_NAME}}</strong></td>
                         </tr>
                         <tr>  
                         <td><strong>PC Name: {{$const_name}}</strong></td>
                         </tr>
                  
                         
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:49%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d.m.Y') }}</td>
                         </tr>
                         <tr>  
                           <td align="right">
                               <?php
                                if($date!='all'){
                                    $date_range = explode('~', $date);
                                    $fromDate=$date_range[0] ;
                                    $toDate=$date_range[1];                                                
                                    if($fromDate==$toDate){
                                      echo $gettimeInterval= '<span id=""><b>Report Of: </b>'.$fromDate.'</span>';
                                    }else {
                                      echo $gettimeInterval= '<span id=""><b>Report From: </b>'.$fromDate.'<b> To: </b>'.$toDate.'</span>';
                                    }
                                }
                                ?>
                               
                           </td>
                         </tr>
                        
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>FORM 3A--(See Rule-7)</strong></td>
               </tr>
			   <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>Election to the *House of the People/Legislative Assembly from the {{ $const_name}} constituency.</strong></td>
               </tr>
			   <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>Notice is hereby given that the following nominations in respect of the above election have been received today:_.</strong></td>
               </tr>
            </table>
        <table class="table-strip" style="width: 98%;" border="1" align="center">
            <thead>
          <tr> 
          <th>S.No</th>
          <th>Candidate Name</th>
          <th>Father/Mother/Husband </th>
          <th>Age</th>
          <th>Address</th>
          <th>Party Affilation</th>
          <th>Caste</th>
          <th>Electoral Roll No</th>  
          <th>Proposer Name</th>
          <th>Electoral Roll Number Of Proposers</th>  
          </tr>
           </thead>
            </thead>
            <tbody>
            <?php $j=1; ?>
            @if(!empty($datewiseform3alist))
      @foreach($datewiseform3alist as $listform3a)
			<?php
        // dd($listform3a);
        $canddetailsArray=\app(App\adminmodel\CandidateModel::class)->where(['candidate_id' =>$listform3a->candidate_id])->get();
        $nominationArray=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['candidate_id' =>$listform3a->candidate_id])->get();
       //dd($nominationArray);
      // echo '<pre>';  print_r($canddetailsArray); echo '<pre>'; exit;
				?>
<tr>
  <td>{{$j++}}</td>
  <td>@if(!empty($canddetailsArray[0]->cand_name)) {{$canddetailsArray[0]->cand_name}} @endif </td>
  <td>@if(!empty($canddetailsArray[0]->candidate_father_name)) {{$canddetailsArray[0]->candidate_father_name}} @endif </td>
  <td>@if(!empty($canddetailsArray[0]->cand_age)) {{$canddetailsArray[0]->cand_age}} @endif</td>
  <td>@if(!empty($canddetailsArray[0]->candidate_residence_address)) {{$canddetailsArray[0]->candidate_residence_address}} @endif</td>
  <td>@if(!empty($listform3a->party_id)) {{$listform3a->party_id}} @endif</td>
  <td>@if(!empty($canddetailsArray[0]->cand_category)) {{$canddetailsArray[0]->cand_category}} @endif</td>
  <td>@if(!empty($listform3a->party_id)) {{$listform3a->party_id}} @endif</td>
  <td>@if(!empty($nominationArray[0]->proposer_name)) {{$nominationArray[0]->proposer_name}} @endif</td>
  <td>@if(!empty($nominationArray[0]->proposer_partno)) {{$nominationArray[0]->proposer_partno}} @endif</td>
</tr>

@endforeach 
@endif 
            </tbody> 
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            
			<tr>
              <td colspan="2" align="left"><strong>Place......................... Returning Officer------------</strong></td>  
            </tr>
			<tr>
              <td colspan="2" align="left"><strong>Date .......................</strong></td>  
            </tr>
			<tr>
              <td colspan="2" align="left"><strong>*Strike off the inappropriate alternative</strong></td>  
            </tr>
			<tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>