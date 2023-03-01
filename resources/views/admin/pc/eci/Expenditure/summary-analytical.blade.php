@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'EXPENDITURE')
@section('bradcome', 'MIS')
@section('description', '')
@section('content')
@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
$nameSuffix=$nameSuffix;
 //echo $st_code.'cons_no'.$cons_no; 
@endphp

<main role="main" class="inner cover mb-3">
<section class="mt-5">
<div class="container-fluid">
  <div class="row text-center pt-2 pb-1">
  <div class="col-sm-12"><h4><b>ELECTION EXPENDITURE MONITORING SYSTEM GENERAL PC ELECTION-2019</b></h4></div>
				         <div class="col-sm-12 mt-3"></div> 
  <div class="card text-left mt-3" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4></h4></div> 
              <div class="col"><p class="mb-0 text-right">
			  &nbsp;&nbsp;
			<a href="{{url('/eci-expenditure/EciExpdashboard')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
              </p>
              </div>
            </div>
			 <div class="row" style="width:100%;"><h4> Analytical Summary</h4></div> 
      </div>
   
 <div class="card-body"> 
<div class="table-responsive">
<table id="examples" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead class="text-center">
         <tr>
          <th>Serial No</th>
          <th>State</th> 
		  @if(empty($cons_no)) 
          <th>Total PC</th> 
	      @else
		  <th>PC Name</th> 
		  @endif
          <th>Total Candidates</th> 
		   <th>Total Elected Candidates</th> 
		  @if($nameSuffix=='dataentry') 
          <th>Data Entry Started</th>
	      @elseif($nameSuffix=='finalize')
		  <th>Scrutiny Report Finalized </th>
		   @elseif($nameSuffix=='logedaccount')
		  <th>Account Loged by Candidate</th>
		  @elseif($nameSuffix=='notintime')
		  <th>Not in Time</th>
		  @elseif($nameSuffix=='formatedefects')
		  <th>Defects in Formats</th>
		  @elseif($nameSuffix=='understatedexpense')
		  <th>Under Stated Expenses</th>
		   @elseif($nameSuffix=='partyfund')
		  <th>Party Funds (Rs.)</th>
		   @elseif($nameSuffix=='othersfund')
		  <th>Othersfund Funds (Rs.)</th>
		   @elseif($nameSuffix=='return')
		  <th>Return Type</th>
		   @elseif($nameSuffix=='non-return')
		  <th>Non-Return Type</th>
		  @endif
         </tr>
        </thead>
       
        @php  
        $count = 1; 
        $TotalUsers = 0;
        $TotalfiledData = 0;
        $TotalnotfiledData = 0;
        $Totalfinalcompletedcount= 0;
        $Totalpc = 0;
		$TotalDEONotice = 0;
		$TotalCEONotice = 0;
		$Totaldisqualifiedcount=0;
		$Totaldataentryfinal=0;
		$Totallogedaccount=0;
		$TotalNotinTime=0;
		$Totalunderstated=0;
		$Totalformatedefects=0;
		$Totalpartyfund=0;
		$Totalothersfund=0;
		$Totalreturn=0;
		$Totalnonreturn=0;
		$Totalelectedcand=0;
        @endphp
         @forelse ($totalContestedCandidatedata as $key=>$listdata)
         @php
         
         $TotalUsers +=$listdata->totalcandidate;
         
         $stdetails=getstatebystatecode($listdata->st_code);
         $pcbystate=getpcbystate($listdata->st_code);
         $pccount=count($pcbystate);
         $Totalpc += $pccount;
		 $pcdetails=getpcbypcno($listdata->st_code,$listdata->pc_no);
       
		 $electedcand=\app(App\models\Expenditure\EciExpenditureModel::class)->getTotalelectedcandbystate('PC',$listdata->st_code);
		
         $Totalelectedcand += $electedcand;
		 
         $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('PC',$listdata->st_code,$cons_no);
		 
         $TotalfiledData +=  $filedcount;
		 
		 $dataentryfinal=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryFinal('PC',$listdata->st_code,$cons_no);
		 
         $Totaldataentryfinal +=  $dataentryfinal;
		 
		 $logedaccount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotallogedAccount('PC',$listdata->st_code,$cons_no);
		 
         $Totallogedaccount +=  $logedaccount;
		 
		 $notintime=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalNotinTime('PC',$listdata->st_code,$cons_no);
		  
		 $TotalNotinTime += $notintime;
		  
		  $formatedefects=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalDefectformats('PC',$listdata->st_code,$cons_no);
		  $Totalformatedefects += $formatedefects;
		  
		  $understated=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalexpenseUnderStated('PC',$listdata->st_code,$cons_no);
		  $Totalunderstated += $understated;
		 
		 $partyfund=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalPartyfund('PC',$listdata->st_code,$cons_no);
		 $Totalpartyfund += $partyfund->total_partyfund;
		 
		 $othersfund=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalOtherSourcesfund('PC',$listdata->st_code,$cons_no);
		 
		 $Totalothersfund += $othersfund->total_otherSourcesfund;;
		 
		 $returncount=\app(App\models\Expenditure\ExpenditureModel::class)->gettotalreturn('PC', $listdata->st_code, $cons_no,'Returned');
		 $return=!empty($returncount)?count($returncount):0;
		 $Totalreturn += $return;
		 
		 $returncount=\app(App\models\Expenditure\ExpenditureModel::class)->gettotalreturn('PC', $listdata->st_code, $cons_no,'Non-Returned');
		 $nonreturn=!empty($returncount)?count($returncount):0;
		 $Totalnonreturn += $nonreturn;
		  
         // Get Pending Data Count 
         $notfiledcount= $listdata->totalcandidate - $filedcount;
         $TotalnotfiledData += $notfiledcount;
		 
         $finalcompletedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalCompletedbyEci('PC',$listdata->st_code,$cons_no);
         $Totalfinalcompletedcount += $finalcompletedcount;
		 
		 $disqualifiedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalDisqualifiedbyEci('PC',$listdata->st_code,$cons_no);
         $Totaldisqualifiedcount += $disqualifiedcount;
		 
		 $noticeatCEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatCEO('PC',$listdata->st_code,$cons_no);
         $TotalCEONotice += $noticeatCEOCount;
		 
		 $noticeatDEOCount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalnoticeatDEO('PC',$listdata->st_code,$cons_no);
         $TotalDEONotice += $noticeatDEOCount;
		 
         @endphp
          <tr>
            <td>{{ $count }}</td>
            <td>@if($stdetails->ST_NAME =='' )   'N/A'  @else <b>{{  $stdetails->ST_NAME }}</b> @endif</td>

            <td align="right">@if(empty($cons_no))   {{  $pccount }}  @else <b>{{$pcdetails->PC_NAME}}</b> @endif</td>
			
            <td align="right">@if(empty($listdata->totalcandidate) || $listdata->totalcandidate <1 )     0  @else <a href="{{url('/')}}/eci-expenditure/allcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" >  <b>{{ $listdata->totalcandidate }}</b> @endif</a></td>
			
		   <td align="right">@if(empty($electedcand) || $electedcand <1 )     0  @else <a href="{{url('/')}}/eci-expenditure/electedcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" >  <b>{{ $electedcand }}</b> @endif</a></td>
			
		  @if($nameSuffix=='dataentry') 
          <td align="right"><a href="{{url('/')}}/eci-expenditure/Ecistartedcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($filedcount =='' )     0  @else  <b>{{ $filedcount }}</b> @endif</a></td>
	  
	      @elseif($nameSuffix=='finalize')
		  
		 <td align="right"> @if(empty($dataentryfinal) || $dataentryfinal <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/finalizeData/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $dataentryfinal }}</b> @endif </a></td>
		 
		  @elseif($nameSuffix=='logedaccount')
		  
		 <td align="right"> @if(empty($logedaccount) || $logedaccount <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/logedaccount/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $logedaccount }}</b> @endif </a></td>
		   
		  @elseif($nameSuffix=='notintime')
		  
		 <td align="right"> @if(empty($notintime) || $notintime < 1)     0  @else  <a href="{{url('/')}}/eci-expenditure/notintime/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $notintime }}</b> @endif </a></td>
		  
		  @elseif($nameSuffix=='formatedefects')
		  <td align="right"> @if(empty($formatedefects) || $formatedefects <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/formatedefects/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $formatedefects }}</b> @endif </a></td>
		  
		  @elseif($nameSuffix=='understatedexpense')
		  
		 <td align="right"> @if(empty($understated) || $understated <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/understatedexpense/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $understated }}</b> @endif </a></td>
		 
		 @elseif($nameSuffix=='partyfund')
		  
		 <td align="right"> @if(empty($partyfund->total_partyfund) || $partyfund->total_partyfund <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/partyfund/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $partyfund->total_partyfund }}</b> @endif </a></td>
		 
		@elseif($nameSuffix=='othersfund')
		  
		 <td align="right"> @if(empty($othersfund->total_otherSourcesfund) || $othersfund->total_otherSourcesfund <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/othersfund/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{ $othersfund->total_otherSourcesfund }}</b> @endif </a></td>
		 
		 @elseif($nameSuffix=='return')
		 <td align="right"> @if(empty($return) || $return <1)     0  @else  <a href="{{url('/')}}/eci-expenditure/return/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > <b>{{  $return }}</b> @endif </a></td>
		  
		 @elseif($nameSuffix=='non-return')
		 <td align="right"> @if(empty($nonreturn) || $nonreturn <1) 0  @else <a href="{{url('/')}}/eci-expenditure/non-return/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" ><b>{{ $nonreturn}}</b> @endif </a></td>
		  
		  @endif
		   
          </tr>
           @php  $count++;  @endphp
          
           @empty
                <tr>
                  <td colspan="6">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td></td>
          <td align="right"><b> @if(empty($cons_no)) {{$Totalpc}} @endif</b>
          </td>
          <td align="right"><b>{{$TotalUsers}}</b></td>
		  <td align="right"><b>{{$Totalelectedcand}}</b>
          </td>
		   @if($nameSuffix=='dataentry') 
         <td align="right"><b>{{$TotalfiledData}}</b></td>
	      @elseif($nameSuffix=='finalize')
		 <td align="right"><b>{{$Totaldataentryfinal}}</b></td>
		   @elseif($nameSuffix=='logedaccount')
		 <td align="right"><b>{{$Totallogedaccount}}</b></td>
		  @elseif($nameSuffix=='notintime')
		  <td align="right"><b>{{$TotalNotinTime}}</b></td>
		  @elseif($nameSuffix=='formatedefects')
		 <td align="right"><b>{{$Totalformatedefects}}</b></td>
		  @elseif($nameSuffix=='understatedexpense')
		  <td align="right"><b>{{$Totalunderstated}}</b></td>
		  @elseif($nameSuffix=='partyfund')
		  <td align="right"><b>{{$Totalpartyfund}}</b></td>
		   @elseif($nameSuffix=='othersfund')
		  <td align="right"><b>{{$Totalothersfund}}</b></td>
		   @elseif($nameSuffix=='return')
		  <td align="right"><b>{{$Totalreturn}}</b></td>
		   @elseif($nameSuffix=='non-return')
		  <td align="right"><b>{{$Totalnonreturn}}</b></td>
		  @endif
		  </tr>
        <tbody> </tbody>
    </table>
	</div> 
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

@endsection

@section('script')

<script>
jQuery(document).ready(function(){ 
	jQuery("select[name='state']").change(function(){
		var state = jQuery(this).val();  
   // alert(state);
        jQuery.ajax({ 
        	url: '<?php echo url('/') ?>/eci-expenditure/getpcbystate',
            type: 'GET',
            data: {state:state},
         
            success: function(result){  
							console.log(result); 
                var stateselect = jQuery('form select[name=pc]');
                stateselect.empty();
                var pchtml = '';
                pchtml = pchtml + '<option value="">-- All PC --</option> ';
                jQuery.each(result,function(key, value) { 
                    pchtml = pchtml + '<option value="'+value.PC_NO+'">'+value.PC_NO+' - '+value.PC_NAME + ' - ' +value.PC_NAME_HI+'</option>';
                    jQuery("select[name='pc']").html(pchtml);
                });
                var pchtml_end = '';
                jQuery("select[name='pc']").append(pchtml_end)
            }
        });
    });
	/*
	//Check Validation
    jQuery('#psinfo').click(function(){
		var distt = jQuery('select[name="state"]').val();
		var acname = jQuery('select[name="pc"]').val();
		
		if(distt == ''){
			jQuery('.errormsg').html('');
			jQuery('.stateerrormsg').html('Please select district');
			jQuery( "input[name='district']" ).focus();
			return false;
		}
		if(acname == ''){
            jQuery('.errormsg').html('');
			jQuery('.acerrormsg').html('Please select ac');
			jQuery( "input[name='ac']" ).focus();
			return false;
		}
	});
  */
	
});

</script>
@endsection


