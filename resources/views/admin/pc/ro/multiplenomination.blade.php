@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Multiple Candidate Nomintion')
@section('content')	
 <?php  $st=app(App\commonModel::class)->getstatebystatecode($stcode);  
        $pc=app(App\commonModel::class)->getpcbypcno($stcode,$constno); 
        $partyd=getallpartylist();
        $symb=getsymbollist();
		$symb1=getsymboltypelist('T');
	?>
<link rel="stylesheet" href="{{ asset('admintheme/css/nomination.css') }}" id="theme-stylesheet">
 <style type="text/css">
     html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 
<main role="main" class="inner cover mb-3">
<section>
	 
	 <form enctype="multipart/form-data" id="election_form" method="POST"  action="{{url('ropc/newmultiplenomination') }}" autocomplete='off' enctype="x-www-urlencoded">
	  {{ csrf_field() }}
 
  <div class="container">
  <div class="row">
  
  <div class="card text-left mt-3" style="width:100%; margin:0 auto 10px auto;">
                <div class=" card-header">
                <div class=" row">
                 <div class="col"><h4>Candidate Multiple Nomintion Details</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b> 
            <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;  </p></div>
         
                </div>
                </div>
     
    <div class="card-body">  
 		<div class="container p-0">
 			<div class="row">
	    @if (session('error_mes'))
          <div class="alert alert-danger">{{session('error_mes') }}</div>
        @endif
        
	</div> 
			<div class="row">
			<div class="col">
					<label class="">Select Candidate Name <sup>*</sup></label>
			 <select name="candidate_name" class="form-control candidate_id">
				<option value="">-- Select Candidate Name--</option>
					 
					@foreach($lists as $list)
					<option value="{{$list->candidate_id}}" @if($list->candidate_id==old('candidate_name')) selected="selected" @endif >{{$list->candidate_id}}- {{$list->cand_name}}-C/O.:-{{$list->candidate_father_name}} </option>
					@endforeach
					 
			</select>
		 		@if ($errors->has('candidate_name'))
                  		  <span style="color:red;">{{ $errors->first('candidate_name') }}</span>
               			@endif
			<div class="nameerrormsg errormsg errorred"></div>
		  </div>
		  <div class="col"> </div>
		</div>
		</div>				 
	 
	
<div class="form-group row">

<div class="col">
<label class="">Party Name <sup>*</sup></label>
		 
			

			<select name="party_id" class="form-control party_id">
				<option value="">-- Select Party --</option>
					 
					@foreach($partyd as $Party)
					<option value="{{ $Party->CCODE }}" @if($Party->CCODE==old('party_id')) selected="selected" @endif > {{$Party->PARTYABBRE}}-{{$Party->PARTYNAME}} </option>
					@endforeach
					 
			</select>
		 		@if ($errors->has('party'))
                  		  <span style="color:red;">{{ $errors->first('party') }}</span>
               			@endif
			<div class="perrormsg errormsg errorred"></div>
	
</div>
		
		<div class="col">
		<label class="">Symbol <sup>*</sup></label>
				<select name="symbol_id" class="form-control">
					<option value="">-- Select Symbol --</option>
					@foreach($symb as $symbolDetails)
					<option value="{{ $symbolDetails->SYMBOL_NO }}" @if($symbolDetails->SYMBOL_NO==old('symbol_id')) selected="selected" @endif> {{$symbolDetails->SYMBOL_NO}}-{{$symbolDetails->SYMBOL_DES}}</option>
					@endforeach
				</select>
		    @if ($errors->has('symbol_id'))
                <span style="color:red;">{{ $errors->first('symbol_id') }}</span>
            @endif
				<div class="serrormsg errormsg errorred"></div>
				<div id="mysysDiv" style="display: none;"> <input type="checkbox" name="nosymb" id="nosymb" value="200" checked="checked"> Symbole Not Alloted</div>
		</div>
			
		
		
		
		 		 
	</div><!-- end COL-->
	<div class="form-group row float-right">       
					  <div class="col">
						<button type="submit" id="candnomination" class="btn btn-primary">Submit</button>
					  </div>
				 </div>
	</div><!-- end row-->
	 
					</div>
				</div>
				</div>
			</div>
		</div>	  
	  </section>
	   
	  </form>
	</main>
@endsection
		 
@section('script')

<script>
  
jQuery(document).ready(function(){  
			 
	  
	jQuery('select[name="party_id"]').change(function(){ 
		var partyid = jQuery(this).val();   
		$('#mysysDiv').hide();  
		jQuery.ajax({
            url: "{{url('/ropc/getSymbol')}}",
            type: 'GET',
            data: {partyid:partyid},
            success: function(result){  
            	jQuery("select[name='symbol_id']").html(result);
			 },
		       error: function (data, textStatus, errorThrown) {
		         var symbolselect = jQuery('form select[name=symbol_id]');
			        symbolselect.empty();
					 var symbolhtml = '';
					 	symbolhtml = symbolhtml + '<option value="200">200 - Not Alloted</option>';
					 jQuery("select[name='symbol_id']").html(symbolhtml);
					  var symbolhtml_end = '';
					  jQuery("select[name='symbol_id']").append(symbolhtml_end);
		       }
        });
	});
	 
	 
	 
	 
    jQuery('#candnomination').click(function(){
		var partyid = jQuery('select[name="party_id"]').val();
		var symbolid = jQuery('select[name="symbol_id"]').val();
		var candidate_name = jQuery('select[name="candidate_name"]').val();
		 
		if(candidate_name == ''){
            jQuery('.errormsg').html('');
			jQuery('.nameerrormsg').html('Please select candidate name');
			jQuery( "input[name='candidate_name']" ).focus();
			return false;
		} 
		
		if(partyid == ''){
            jQuery('.errormsg').html('');
			jQuery('.perrormsg').html('Please select party');
			jQuery( "input[name='party_id']" ).focus();
			return false;
		}
		 
		if(symbolid == ''){
            jQuery('.errormsg').html('');
			jQuery('.serrormsg').html('Please select symbol');
			jQuery( "input[name='symbol_id']" ).focus();
			return false;
		}
		
	  
	});
	 
});
 
</script>
@endsection