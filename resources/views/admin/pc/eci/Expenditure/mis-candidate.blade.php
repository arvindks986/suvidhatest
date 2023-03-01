@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
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
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp

<main role="main" class="inner cover mb-3">
<section class="mt-5">
<div class="container-fluid">
  <div class="row text-center pt-2 pb-1">
  <div class="col-sm-12"><h4><b> ECI ELECTION EXPENDITURE MONITORING SYSTEM GENERAL PC ELECTION-2019</b></h4></div>
				         <div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/eci-expenditure/mis-candidate')}}" id="EcidashboardFilter">           
                       <div class="row justify-content-center">
                    {{ csrf_field() }}
                      <!--STATE LIST DROPDOWN STARTS-->
                        <div class="col-sm-3">
                        <label for="" class="mr-3">Select State</label>    
                        <select name="state" id="state" class="form-control">
                     <?php if($stateName=='ALL') { ?> <option value="">All States</option> <?php } ?>
                     <?php //$statelist = getallstate(); ?>
                      @foreach ($statelist as $state_List ))
                        @if ($st_code == $state_List->ST_CODE)
                              <option value="{{ $state_List->ST_CODE }}" selected>{{$state_List->ST_NAME}}</option>
                        @else
                              <option value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>
                        @endif
                      @endforeach

                      @if ($errors->has('state'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('state') }}</strong>
                      </span>
                      @endif
                      <div class="stateerrormsg errormsg errorred"></div>
                  </select> 
                        </div>
                          <!--STATE LIST DROPDOWN ENDS-->
					       	<div class="col-sm-3">
                        <label for="" class="mr-3">Select PC</label>    
                        <select name="pc" id="pc" class="consttype form-control" >
								<option value="">-- All PC --</option>
                @if (!empty($all_pc))
                <?php //dd($all_pc);?>
								@foreach($all_pc as $getPc)
								 @if ($cons_no ==  $getPc->PC_NO)
                              <option value="{{ $getPc->PC_NO }}" selected>{{$getPc->PC_NO }} - {{$getPc->PC_NAME }}- {{$getPc->PC_NAME_HI}}</option>
                              @else
									<option value="{{ $getPc->PC_NO }}" > 
									{{$getPc->PC_NO }} - {{$getPc->PC_NAME }} - {{$getPc->PC_NAME_HI}}</option>
									 @endif
								@endforeach 
                @endif
							</select>
					    @if ($errors->has('pc'))
                  		  <span style="color:red;">{{ $errors->first('pc') }}</span>
               			@endif
                     
							<div class="acerrormsg errormsg errorred"></div>
                        </div>
					  	<div class="col-sm-2 mt-2">
							<p class="mt-4 text-left">
							<!-- <button type="button" id="Back" class="btn btn-primary">Filter</button> -->
						  <input type="submit" value="Filter" id="Filter" class="btn btn-primary">
						   <a href="{{url('/eci-expenditure/mis-candidate')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a>
            	</p>
                        </div>
                    </div>
                </form> 
                 <!--FILTER ENDS HERE-->
				</div> 
  <div class="card text-left mt-3" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4>Candidate MIS Report</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; 
              <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/EciCandidateMISPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci-expenditure/EciCandidateMISEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body"> 
<div class="table-responsive">
<table id="examples" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>State</th> 
		   @if(empty($cons_no)) 
          <th>Total PC</th> 
	      @else
		  <th>PC Name</th> 
		  @endif
          <th>Total Filed Candidate</th> 
          <th>Not Filed Candidate</th> 
          <th>Not In Time Candidate</th> 
          <th>Defaulter Candidate</th> 
         </tr>
        </thead>
       
        @php  
        $count = 1; 
        $TotalUsers = 0;
        $TotalfiledData = 0;
        $TotalnotfiledData = 0;
        $TotalDefaulter= 0;
        $TotalNotinTime= 0;
        $Totalpc = 0;
		
        @endphp
		
         @forelse ($totalContestedCandidate as $key=>$listdata)
         @php
         //dd($listdata);
         $TotalUsers =$listdata->totalcandidate;
        
         $stdetails=getstatebystatecode($listdata->st_code);
		 $pcbystate=getpcbystate($listdata->st_code);
         $pccount=count($pcbystate);
         $Totalpc += $pccount;
		 $pcdetails=getpcbypcno($listdata->st_code,$listdata->pc_no);
         $filedcount=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotaldataentryStart('PC',$listdata->st_code,$cons_no);
       
         // Get Pending Data Count 
         $notfiledcount= $TotalUsers- $filedcount;

         $defaulter=\app(App\models\Expenditure\EciExpenditureModel::class)->getdefaulter('PC',$listdata->st_code,$cons_no);
         //dd($defaulter);
         $defaultercount=!empty($defaulter) ? count($defaulter) : '0';
         $notinTime=\app(App\models\Expenditure\EciExpenditureModel::class)->gettotalNotinTime('PC',$listdata->st_code,$cons_no);
         $TotalfiledData +=  $filedcount;
         $TotalnotfiledData += $notfiledcount;
         $TotalDefaulter += $defaultercount;
         $TotalNotinTime += $notinTime;
         @endphp
          <tr>
            <td>{{ $count }}</td>
            <td>{{ $stdetails->ST_NAME }}</td>
			<td align="right">@if(empty($cons_no))   {{  $pccount }}  @else <b>{{$pcdetails->PC_NAME}}</b> @endif</td>
            <td align="right"><a href="{{url('/')}}/eci-expenditure/filedcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($filedcount =='' )     0  @else  <b>{{ $filedcount }}</b> @endif</a></td>
			
            <td align="right"> <a href="{{url('/')}}/eci-expenditure/notfiledcandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($notfiledcount =='' )     0  @else <b>{{  $notfiledcount }}</b> @endif </a></td>
            <td align="right"><a href="{{url('/')}}/eci-expenditure/notintimecandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($notinTime =='')     0  @else <b>{{  $notinTime }}</b> @endif</a></td>
            <td align="right"><a href="{{url('/')}}/eci-expenditure/defaultercandidate/{{base64_encode($listdata->st_code)}}/{{base64_encode($cons_no)}}" > @if($defaultercount =='')     0  @else <b>{{ $defaultercount }}</b> @endif</a></td>
          </tr>
           @php  $count++;  @endphp
          
           @empty
                <tr>
                  <td colspan="6">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
          <tr><td><b>Total</b></td><td></td> <td align="right"><b> @if(empty($cons_no)) {{$Totalpc}} @endif</b></td><td align="right"><b>{{$TotalfiledData}}</b></td><td align="right"><b>{{$TotalnotfiledData}}</b></td><td align="right"><b>{{$TotalNotinTime}}</b></td><td align="right"><b>{{$TotalDefaulter}}</b></td></tr>
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


