@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
 //echo $st_code.'cons_no'.$cons_no; die;
  $countingDate=\app(App\models\Expenditure\ExpenditureModel::class)->getResultDeclarationDate();

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
                           <td><strong>Officer MIS Report</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name: PC General</strong> </td>
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
          <th>Serial No</th>
		  <th>PC Name</th> 
          <th>Total Candidates</th> 
		  <th>Started</th> 
          <th>Not Started</th> 
		  <!--<th>Not In Time</th> -->
		  <th>Finalised By DEO</th> 
          <th>Pending - DEO</th> 
		  <!--<th>Notice At DEO</th> -->
          <th>Pending - CEO</th> 
		  <th>Notice At CEO</th>
           </tr>
            </thead>
            <tbody>
     @php  
      $count = 1; 
        $TotalUsers = 0;
        $TotalPendingatRO = 0;
        $TotalPendingatCEO = 0;
        $TotalPendingatECI= 0;
        $TotalfiledData = 0;
        $TotalnotfiledData = 0;
        $Totalfinalcompletedcount= 0;
        $Totalpc = 0;
		$TotalDEONotice = 0;
		$TotalCEONotice = 0;
		$TotalFinalByDEO = 0;
		$TotalNotinTime= 0;
		$pendingatRO=0;
		$Totaldisqualifiedcount =0

        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php

         $TotalUsers +=$listdata->totalcandidate;
          $cons_no=$listdata->pc_no;
         $stdetails=getstatebystatecode($listdata->st_code);
         $pcbystate=getpcbystate($listdata->st_code);
         $pccount=count($pcbystate);
         $Totalpc += $pccount;
		 $pcdetails=getpcbypcno($listdata->st_code,$listdata->pc_no);
       
		 $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO('PC',$listdata->st_code,$cons_no);
         $TotalFinalByDEO += $finalbyDEO;
		 
         //$pendingatRO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalpartiallypending('PC',$listdata->st_code,$cons_no);
         //$TotalPendingatRO += $pendingatRO;
		 
         //$pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('PC',$listdata->st_code,$cons_no);
        // $TotalPendingatCEO += $pendingatCEO;
		 
		
		 
         $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('PC',$listdata->st_code,$cons_no);
         $TotalPendingatECI += $pendingatECI;
		 
          $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('PC',$listdata->st_code,$cons_no);
         $TotalfiledData +=  $filedcount;
		  
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('PC',$listdata->st_code,$cons_no);
         $Totalfinalcompletedcount += $finalcompletedcount;
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('PC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('PC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
		 $notinTime=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalNotinTime('PC',$listdata->st_code,$cons_no);
		 $TotalNotinTime += $notinTime;
		 
		 $disqualifiedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalDisqualifiedbyEci('PC',$listdata->st_code,$cons_no);
         $Totaldisqualifiedcount += $disqualifiedcount;
		 
		 //pending at DEO
		 if($finalbyDEO > 0 ){
			$pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
			} 
			
	   //pending at CEO	
		if($finalbyDEO >=  0 && $pendingatECI >=0 && $finalcompletedcount >=0){
		 $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount + $disqualifiedcount);
		 if($pendingatCEO >= 0) { $TotalPendingatCEO += $pendingatCEO; }
		}

         @endphp
          <tr>    
          <td>{{ $count }}</td>
			  <td align="right">@if(!empty($pcdetails->PC_NAME))   {{ $pcdetails->PC_NAME }}  @else <b> N/A </b> @endif</td>
            <td align="right"> @if($listdata->totalcandidate =='' )     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
			<td align="right"> @if( $filedcount =='' )     0  @else <b>{{  $filedcount }}</b> @endif</td>
			<td align="right"> @if($notfiledcount =='' )     0  @else  <b>{{ $notfiledcount }}</b> @endif</td>
		   <!--<td align="right">@if($notinTime =='')     0  @else <b>{{  $notinTime }}</b> @endif</td>-->
			<td align="right"> @if( $finalbyDEO =='' )     0  @else <b>{{  $finalbyDEO }}</b> @endif</td>
            <td align="right"> @if( $pendingatRO =='' )     0  @else <b>{{  $pendingatRO }}</b> @endif</td>
            <td align="right"> @if( $pendingatCEO =='' )     0  @else <b>{{  $pendingatCEO }}</b> @endif</td>
		    <td align="right">@if($noticeatCEOCount =='')     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td>
          <td align="right"><b> </b></td>
          <td align="right"><b>{{$TotalUsers}}</b>
          </td>
	      <td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>
		  {{$TotalFinalByDEO}}</b></td><td align="right"><b>{{$TotalPendingatRO}}</b></td><td align="right"><b>{{$TotalPendingatCEO}}</b></td><td align="right"><b>{{$TotalDEONotice}}</b></td></tr>
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