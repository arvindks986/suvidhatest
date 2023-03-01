@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Contested Candidate</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="left" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="">
                    <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>
                   </th>
                    <th  style="width:50%" align="right" style="font-weight:normal;">
                        SECRETARIAT OF THE<br>
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: left;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%;" border="0" align="left">  
                <tr>
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>Final By DEO Candidate List</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name: PC General-2014</strong> </td>
                         </tr>
                         <!--<tr>  
                           <td><strong>Phase:</strong>   aa</td>
                         </tr>
                          <tr>  
                           <td><strong>Assembly:</strong> SNAME</td>
                         </tr>  --> 
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
                       <!-- <tr>  
                           <td align="right"><strong>Phase Starts:</strong> </td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Phase Ends:</strong>  </td>
                         </tr>  -->
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>
<table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
<thead>
<tr>
 <th>State</th>
<th>PC No & Name</th>
<th>Candidate Name</th>
<th>Party Name</th>
<th>Last Date of Lodging</th>
<th>Date of Scrutiny Report Submission</th>
<th>Date of Lodging A/C By Candidate</th>
<th>Date of Sending to the CEO</th>
<th>Date of Receipt By CEO</th>
</tr>
</thead>
            <tbody>
     @php  
     $count = 1; 
        $TotalUsers = 0;
        @endphp
         @forelse ($getcandidateListfinalbyECI as $candDetails)

         @php
           
         $TotalUsers =count($getcandidateListfinalbyECI);
         $pcDetails=getpcbypcno($candDetails->st_code,$candDetails->pc_no);
        $date = new DateTime($candDetails->created_at);
		//echo $date->format('d.m.Y'); // 31.07.2012
		$lodgingDate = $date->format('d-m-Y'); // 31-07-2012
		$report_submitted_data = new DateTime($candDetails->report_submitted_date);
	   //echo $date->format('d.m.Y'); // 31.07.2012
	   $report_submitted_date=$report_submitted_data->format('d-m-Y'); // 31-07-2012
		  $stDetails=getstatebystatecode($candDetails->st_code);
         @endphp
<tr>
<td>@if(!empty($stDetails->ST_NAME)) {{ $stDetails->ST_NAME}} @endif</td>
<td>@if(!empty($pcDetails->PC_NO)) {{ $pcDetails->PC_NO}} - {{ $pcDetails->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '16-06-2014'}} @endif</td>
<td>@if(!empty($candDetails->report_submitted_date)) {{ date('d-m-Y',strtotime($candDetails->report_submitted_date))}}  @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($lodgingDate) && $lodgingDate > '04-12-2019') {{ $lodgingDate}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_sending_deo)) {{  date('d-m-Y',strtotime($candDetails->date_of_sending_deo))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_receipt) && ($candDetails->date_of_receipt !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->date_of_receipt))}}  @else {{ 'N/A'}} @endif</td>  
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Record Found </td>                 
              </tr>
            @endforelse
            <!-- <tr><td><b>Total</b></td><td></td><td><b></b></td><td><b></b></td></tr> -->
            </tbody>
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>