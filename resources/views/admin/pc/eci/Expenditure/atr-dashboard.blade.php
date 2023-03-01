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
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp
<main role="main" class="inner cover mb-3">
    <div class="card-header pt-2" id="expenditure_section">
        <div class="container-fluid">
            <div class="row text-center">
                <div class="col-sm-12"><h4><b>ELECTION EXPENDITURE MONITORING SYSTEM GENERAL PC ELECTION-2019</b></h4></div>
            </div> 
			<div class="col-sm-12 mt-3">
              <!--FILTER STARTS FROM HERE-->
              <form method="post" action="{{url('/eci-expenditure/atr-dashboard')}}" id="EcidashboardFilter">           
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
						   <a href="{{url('/eci-expenditure/mis-officer')}}"><input type="button" value="Clear Filter" id="Filter" class="btn btn-primary"></a>
            	</p>
                        </div>
                    </div>
                </form> 
                 <!--FILTER ENDS HERE-->
				</div>
        </div>
    </div>
   <section class="breadcrumb-section">
	<div class="container-fluid">
		<div class="row pt-2">
		<!-- <div class="col">
		  <ul id="breadcrumb" class="">
			<li><a href="#">Election Expenditure Monitoring System</a></li>
		  </ul>
		 </div>-->
     <div class="col"><p class="mb-0 text-right">
			<b>State Name:</b> 
			<span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
			<b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
			<b>PC:</b> <span class="badge badge-info">{{$pcName}}</span>
      <!-- <b></b> <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button> --></p>
    </div>
		</div>
	</div>
  </section>

  <section class="atrdashboard color-grey">
    <div class="container-fluid">
          <div class="row d-flex">
            <div class="col-sm-12 text-center mb-3"><h5><strong>Action Taken Report(Quick View)</strong></h5></div>
            <div class="col-md-4">
              <div class="card atrBox text-center">
                <div class="icon"><img src="{{ asset('admintheme/img/icon/atr-total.png') }}" alt=""></div>
                <div class="number yellow">{{ $totalatr ?: '0'}}</div>
                <p><strong>Total ATR</strong></p>
              </div>
            </div>
      
            <div class="col-md-4">
              <div class="card atrBox text-center">
                   <div class="icon"><img src="{{ asset('admintheme/img/icon/atr-closed.png') }}" alt=""></div>
                <div class="number red">{{ $closedatr ?: '0'}}</div>
                <p><strong>Closed ATR</strong></p> 
              </div>
            </div>

            <div class="col-md-4">
              <div class="card atrBox text-center">
                  <div class="icon"><img src="{{ asset('admintheme/img/icon/atr-live.png') }}" alt=""></div>
                <div class="number green">{{ $liveatr ?: '0'}}</div>
                <p><strong>Live ATR</strong></p>
              </div>
            </div> 

      </div>
    </div>
  </section>  <!-- End of atr dashboard section -->
  
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


