@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'EXPENDITURE')
@section('bradcome', 'MIS')
@section('description', '')
@section('content')
@php 
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($user_data->st_code);

$pcdetails=getpcbypcno($user_data->st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
@endphp

<main role="main" class="inner cover mb-3">
<section class="mt-5">
<div class="container-fluid">
  <div class="row text-center pt-2 pb-1">
  <div class="col-sm-12"><h4><b>ELECTION EXPENDITURE MONITORING SYSTEM GENERAL PC ELECTION-2019</b></h4></div>
				         <div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/pcceo/mis-officer')}}" id="EcidashboardFilter">           
                     <div class="row justify-content-center">
                    {{ csrf_field() }}
					       	<div class="col-sm-3">
                  <label for="" class="mr-3">Select PC</label>    
                  <select name="pc" id="pc" class="consttype form-control" >
                    <option value="">-- All PC --</option>
                    @php $all_pc = getpcbystate($user_data->st_code); @endphp
                    @foreach($all_pc as $getPc)
                    @if ( $cons_no == $getPc->PC_NO)
                      <option value="{{ $getPc->PC_NO }}" selected>{{$getPc->PC_NO}}-{{$getPc->PC_NAME}} - {{$getPc->PC_NAME_HI}}</option>
                      @else
                      <option value="{{ $getPc->PC_NO }}">{{$getPc->PC_NO}}-{{$getPc->PC_NAME}} - {{$getPc->PC_NAME_HI}}</option>
                    @endif
								@endforeach 
							</select>
					    @if ($errors->has('pc'))
                  		  <span style="color:red;">{{ $errors->first('pc') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-1 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
            	</p>
                        </div>
                    </div>
                </form>  
                 <!--FILTER ENDS HERE-->
				</div> 
  <div class="card text-left mt-3" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4></h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; 
              <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;
              <a href="{{url('/pcceo/OfficerMISPDF')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/pcceo/OfficerMISEXL')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
             <!-- <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->
              </p>
              </div>
            </div>
			 <div class="row" style="width:100%;"><h4> Officer's MIS Regarding DEO's Scrutiny Report On Account Of Contesting Candidates.</h4></div> 
      </div>
   
 <div class="card-body"> 
<div class="table-responsive">
<table id="examples" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
		  <th>PC Name</th> 
          <th>Total Candidates</th> 
		  <th>Started</th> 
          <th>Not Started</th> 
		  <!--<th>Not In Time</th>--> 
		  <th>Finalised By DEO</th> 
          <th>Pending - DEO</th> 
		  <!--<th>Notice At DEO</th> -->
          <th>Pending - CEO</th> 
		  <th>Notice At CEO</th>
         </tr>
        </thead>
       
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
		$TotalfiledData = 0;
		$TotalFinalByDEO = 0;
		$TotalNotinTime= 0;
		$pendingatRO= 0;
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
		 
         //$pendingatCEO=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalfinalbyceo('PC',$listdata->st_code,$cons_no);
         //$TotalPendingatCEO += $pendingatCEO;
		 
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
		  if($finalbyDEO >= 0 ){
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
            <!-- <td align="right">{{ $pcdetails->PC_NO}} &nbsp;&nbsp;{{$pcdetails->PC_NAME}}</td> -->
			
            <td align="right">@if($listdata->totalcandidate =='' )     0  @else <a href="{{url('/')}}/pcceo/expallcandidate/{{base64_encode($cons_no)}}" >   <b>{{ $listdata->totalcandidate }}</b> @endif</a></td>
			
			<td align="right">@if($filedcount =='' )     0  @else  <a href="{{url('/')}}/pcceo/expstartedcandidate/{{base64_encode($cons_no)}}" >  <b>{{ $filedcount }}</b> @endif</a></td>
			
            <td align="right"> @if($notfiledcount =='' )     0  @else <a href="{{url('/')}}/pcceo/expnotstarted/{{base64_encode($cons_no)}}" > <b>{{  $notfiledcount }}</b> @endif </a></td>
			
			<!-- <td align="right"> @if($notinTime =='')     0  @else<a href="{{url('/')}}/pcceo/expnotintimecandidate/{{base64_encode($cons_no)}}" > <b>{{  $notinTime }}</b> @endif</a></td>-->
			
			<td align="right">  @if($finalbyDEO =='' )     0  @else<a href="{{url('/')}}/pcceo/expfinalbyDEO/{{base64_encode($cons_no)}}" > <b>{{  $finalbyDEO }}</b> @endif </a></td>
			
            <td align="right" > @if($pendingatRO =='' )     0 @else <a href="{{url('/')}}/pcceo/exppendingatro/{{base64_encode($cons_no)}}" title="toalcandidate-pendingatCEO">   <b>{{  $pendingatRO }}</b> @endif </a></td>
			
			<!--<td align="right"> <a href="{{url('/')}}/pcceo/noticeatdeo/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($noticeatDEOCount =='' )     0  @else <b>{{  $noticeatDEOCount }}</b> @endif </a></td>-->
			
            <td align="right"> @if($pendingatCEO =='')     0  @else<a href="{{url('/')}}/pcceo/exppendingatceo/{{base64_encode($cons_no)}}" > <b>{{  $pendingatCEO }}</b> @endif</a></td>
			
			<td align="right">@if($noticeatCEOCount =='')     0  @else <a href="{{url('/')}}/pcceo/noticeatceo/{{base64_encode($cons_no)}}" > <b>{{  $noticeatCEOCount }}</b> @endif</a></td>
			 
          
          </tr>
           @php  $count++;  @endphp
          
           @empty
                <tr>
                  <td colspan="6">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td>
          <td align="right"><b> </b></td>
          <td align="right"><b>{{$TotalUsers}}</b>
          </td>
	      <td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>
		  {{$TotalFinalByDEO}}</b></td><td align="right"><b>{{$TotalPendingatRO}}</b></td><td align="right"><b>{{$TotalPendingatCEO}}</b></td><td align="right"><b>{{$TotalDEONotice}}</b></td></tr>
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
        	url: '<?php echo url('/') ?>/pcceo/getpcbystate',
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


