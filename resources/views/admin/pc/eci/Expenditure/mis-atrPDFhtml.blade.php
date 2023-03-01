@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
  $countingDate=\app(App\models\Expenditure\ExpenditureModel::class)->getResultDeclarationDate();
  $DB_MONTH=Session::get('DB_MONTH');
  $DB_MONTH=!empty($DB_MONTH) ? $DB_MONTH : '';
  $DB_YEAR=Session::get('DB_YEAR');
  $DB_CONS_TYPE=Session::get('DB_CONS_TYPE');
  $DB_ELE_TYPE=Session::get('DB_ELE_TYPE');
  $monthName = date("F", mktime(0, 0, 0, $DB_MONTH, 10));
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Active Users</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="left" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="">
                   <!-- <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>-->
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
                           <td><strong>ATR MIS Regarding DEO's Scrutiny Report On Account Of Contesting Candidates</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name: {{ $DB_CONS_TYPE.' '.$DB_ELE_TYPE.' '.'ELECTION-'.' '.$monthName.' '.$DB_YEAR }}</strong> </td>
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
						  <tr>
						   <td align="right"><strong>Counting Date:</strong> {{ !empty($countingDate['start_result_declared_date']) ? date('d-M-Y',strtotime($countingDate['start_result_declared_date'])) :'NA'}}</td>
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
           <thead class="text-center">
		  <tr>
          <th>I</th>
          <th>II</th>
          <th>III</th>
          <th>IV</th> 
		   <th>V</th>
          <th>VI</th>
		  <th>VII</th>
          <th>VIII</th> 
          <th>IX</th> 
          <th colspan="">X</th> 
         </tr>
         <tr>
            <th>Serial No</th>
			<th>State</th> 
			@if(empty($cons_no)) 
			<th>Total PC</th> 
			@else
			<th>PC Name</th> 
			@endif
			<th>Total Candidates</th> 
			<th>Total ATR <BR /></th> 
			<th>Notice At DEO <BR /></th> 
			<th>Reply By DEO</th>
			<th>Notice At CEO</th>
			<th>Reply By CEO</th>
			<th>Closed ATR</th> 
         </tr>
        </thead>
            <tbody>
     @php  
       $count = 1; 
        $TotalUsers = 0;
        $TotalPendingatRO = 0;
        $TotalFinalByDEO  = 0;
		$TotalPendingatECI =0;
        $Totalfinalcompletedcount= 0;
        $Totalpc = 0;
		$grandtotalATR = 0;
		$TotalDEONotice = 0;
		$TotalCEONotice = 0;
		$TotalreplybyDEO=0;
		$TotalreplybyCEO=0;
	    $TotalCloseAtr=0;
        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php
         //dd($listdata);
         $TotalUsers +=$listdata->totalcandidate;
         
         $stdetails=getstatebystatecode($listdata->st_code);
         $pcbystate=getpcbystate($listdata->st_code);
         $pccount=count($pcbystate);
         $Totalpc += $pccount;
		 $pcdetails=getpcbypcno($listdata->st_code,$listdata->pc_no);
       
		 $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('PC',$listdata->st_code,$cons_no);
         $TotalFinalByDEO += $finalbyDEO;
		

		 
         $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('PC',$listdata->st_code,$cons_no);
         $TotalPendingatECI += $pendingatECI;
      
		 
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('PC',$listdata->st_code,$cons_no);
         $Totalfinalcompletedcount += $finalcompletedcount;
		 
		 $totalATR=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalATR($listdata->st_code,$cons_no);
         $grandtotalATR += $totalATR;
		 
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('PC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('PC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
		 //Reply by CEO to ECI on ATR (Role Id-4)
		 $ReplybyCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalReplyByroleid(4,$listdata->st_code,$cons_no);
         $TotalreplybyCEO += $ReplybyCEO;
		 
		 //Reply by DEO to CEO on ATR (Role Id-18)
		 $ReplybyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalReplyByroleid(18,$listdata->st_code,$cons_no);
         $TotalreplybyDEO += $ReplybyDEO;
		 
		 $CloseAtr=\app(App\models\Expenditure\EciExpenditureModel::class)->getclosedATR($listdata->st_code,$cons_no);
         $TotalCloseAtr += $CloseAtr;

         @endphp
          <tr>    
          <td>{{ $count }}</td>
            <td>{{ $stdetails->ST_NAME }}</td>
			<td align="right">@if(empty($cons_no))   {{  $pccount }}  @else <b>{{$pcdetails->PC_NAME}}</b> @endif</td>
            <td> @if(empty($listdata->totalcandidate) || $listdata->totalcandidate < 1)     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
			<td> @if( empty($totalATR) || $totalATR < 1)     0  @else <b>{{  $totalATR }}</b> @endif</td>
            <td> @if( empty($noticeatDEOCount) || $noticeatDEOCount < 1)     0  @else <b>{{  $noticeatDEOCount }}</b> @endif</td>
			 <td> @if( empty($ReplybyDEO) || $ReplybyDEO < 1)     0  @else <b>{{  $ReplybyDEO }}</b> @endif</td>
			 <td>@if( empty($noticeatCEOCount)  || $noticeatCEOCount < 1)     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</td>			
            <td> @if( empty($ReplybyCEO) || $ReplybyCEO < 1)     0  @else <b>{{  $ReplybyCEO }}</b> @endif</td> 
            <td> @if( empty($CloseAtr) || $CloseAtr < 1 )     0  @else <b>{{  $CloseAtr }}</b> @endif</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td></td><td align="right"><b> @if(empty($cons_no)) {{$Totalpc}} @endif</b></td><td><b>{{$TotalUsers}}</b></td><td><b>{{$grandtotalATR}}</b></td><td><b>{{$TotalDEONotice}}</b></td> <td><b>{{$TotalreplybyDEO}}</b></td> <td><b>{{$TotalCEONotice}}</b></td><td><b>{{$TotalreplybyCEO}}</b></td><td><b>{{$TotalCloseAtr}}</b></td></tr>
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