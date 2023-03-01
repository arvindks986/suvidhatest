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
                           <td><strong>Officer's MIS Regarding DEO's Scrutiny Report On Account Of Contesting Candidates</strong></td>
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
<th colspan="">XI</th> 
<!-- <th colspan="">XII</th>  -->
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
<!--<th>Started</th> 
<th>Not Started</th> -->
<th>Finalised By DEO</th> 
<th>Pending - DEO <BR /> IV-V</th> 
<th>Notice - DEO</th>
<th>Pending - CEO <BR /> V-(X+XI+XII) </th> 
<th>Notice - CEO</th>
<th>Pending - ECI </th> 
<th>Closed/Case Dropped</th> 
<!-- <th>Disqualified</th> -->
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
		$pendingatRO=0;
		$pendingatCEO=0;
		$Totaldisqualifiedcount=0;

        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php

        $TotalUsers +=$listdata->totalcandidate;
         
         $stdetails=getstatebystatecode($listdata->st_code);

         $pcbystate=getpcbystate($listdata->st_code);

         $pccount=count($pcbystate);
         $Totalpc += $pccount;
		     $pcdetails=getpcbypcno($listdata->st_code,$listdata->pc_no);
       
		 $finalbyDEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO2014('PC',$listdata->st_code,$cons_no);

         $TotalFinalByDEO += $finalbyDEO;
		 
       
       
        // $pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('PC',$listdata->st_code,$cons_no);
		
		

		       // $pendingatECI=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyeci('PC',$listdata->st_code,$cons_no);
         //$TotalPendingatECI += $pendingatECI;
		 
		 
		   
		 
         $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart2014('PC',$listdata->st_code,$cons_no);
		   
         $TotalfiledData +=  $filedcount;
		  
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $TotalnotfiledData += $notfiledcount;
		 
        $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyDEO2014('PC',$listdata->st_code,$cons_no);
        
         $Totalfinalcompletedcount +=  $finalcompletedcount;
         if($finalcompletedcount>0){
           $pendingatECI= $finalcompletedcount-$finalbyDEO;
         }
        
         $TotalPendingatECI += $pendingatECI;
		 
		// $disqualifiedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalDisqualifiedbyEci('PC',$listdata->st_code,$cons_no);
       //  $Totaldisqualifiedcount += $disqualifiedcount;
		 
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO2014('PC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO2014('PC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
		 //pending at DEO
		  if($finalbyDEO >= 0 ){
			$pendingatRO=$listdata->totalcandidate-($finalbyDEO);
			if($pendingatRO >= 0 ){$TotalPendingatRO += $pendingatRO;}
			}  
		  //pending at CEO	
		if($finalbyDEO >=  0 && $pendingatECI >=0 && $finalcompletedcount >=0){
		// $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount + $disqualifiedcount);
      $pendingatCEO = $finalbyDEO-($pendingatECI + $finalcompletedcount);
		 if($pendingatCEO >= 0) { $TotalPendingatCEO += $pendingatCEO; }
		}
         @endphp
          <tr>    
          <td>{{ $count }}</td>
            <td>{{ $stdetails->ST_NAME }}</td>
			<td align="right">@if(empty($cons_no))   {{  $pccount }}  @else <b>{{$pcdetails->PC_NAME}}</b> @endif</td>
            <td> @if(empty($listdata->totalcandidate) || $listdata->totalcandidate < 1)     0  @else  <b>{{ $listdata->totalcandidate }}</b> @endif</td>
			<td> @if( empty($finalbyDEO) || $finalbyDEO < 1)     0  @else <b>{{  $finalbyDEO }}</b> @endif</td>
            <td> @if( empty($pendingatRO) || $pendingatRO < 1)     0  @else <b>{{  $pendingatRO }}</b> @endif</td>
			 <td> @if( empty($noticeatDEOCount) || $noticeatDEOCount < 1)     0  @else <b>{{  $noticeatDEOCount }}</b> @endif</td>
            <td> @if( empty($pendingatCEO) || $pendingatCEO < 1)     0  @else <b>{{  $pendingatCEO }}</b> @endif</td>
			 <td>@if( empty($noticeatCEOCount)  || $noticeatCEOCount < 1)     0  @else <b>{{  $noticeatCEOCount }}</b> @endif</td>			
            <td> @if( empty($pendingatECI) || $pendingatECI < 1)     0  @else <b>{{  $pendingatECI }}</b> @endif</td> 
            <td> @if( empty($finalcompletedcount) || $finalcompletedcount < 1 )     0  @else <b>{{  $finalcompletedcount }}</b> @endif</td>	
			
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td></td><td align="right"><b> @if(empty($cons_no)) {{$Totalpc}} @endif</b></td><td><b>{{$TotalUsers}}</b></td><td><b>{{$TotalFinalByDEO}}</b></td><td><b>{{$TotalPendingatRO}}</b></td> <td><b>{{$TotalDEONotice}}</b></td> <td><b>{{$TotalPendingatCEO}}</b></td><td><b>{{$TotalCEONotice}}</b></td><td><b>{{$TotalPendingatECI}}</b></td><td><b>{{$Totalfinalcompletedcount}}</b></td></tr>
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