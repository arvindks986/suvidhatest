@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
      @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

#acViewBody a{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}

#acViewBody a:hover{
    text-decoration: none !important;
    color: #000 !important;
    cursor: default !important;
}
.bold{font-weight:bold;}

.swatch-yellow {
   color: #fff;
    background-color: #17a2b8; padding: 10px;
}

</style>

<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
  <div class="row">
  <div class="col-md-9 pull-left">
   <h4>Round Wise Report</h4>
  </div>
   <div class="col-md-3  pull-right text-right">   
      <span style="display:none;" onclick="return downloadExcel();" class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="#" title="Download Excel">Export Excel</a></span>    
      <span style="display:none;" onclick="return downloadPdf();" class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="#" title="Download PDF" >Export PDF</a></span>  
  </div> 
  </div>
</div>  
</section>

<div class="btns-actn">
{{ Form::model('', ['action' => 'Admin\RoundWiseReport\RoundWiseReportController@getCompleteResult', 'name'=>'pdfForm' ]) }}
<input type="hidden" name="download" value="2">		
<input type="hidden" name="st_code[]" id="pdfstate">	
<input type="hidden" name="pc[]" id="pdfpc">	
<input type="hidden" name="ac[]" id="pdfac">	
<input type="hidden" name="condidate[]" id="pdfcondidate">		
</form>	
</div> 

<div class="btns-actn">
{{ Form::model('', ['action' => 'Admin\RoundWiseReport\RoundWiseReportController@csvDownload', 'name'=>'csvDownload' ]) }}
<input type="hidden" name="download" value="2">		
<input type="hidden" name="st_code[]" id="csvstate">	
<input type="hidden" name="pc[]" id="csvpc">	
<input type="hidden" name="ac[]" id="csvac">	
<input type="hidden" name="condidate[]" id="csvcondidate">		
</form>	
</div> 

<section class="dashboard-header p-2 mb-0">
  <div class="container-fluid">  
    <form id="generate_report_id" class="row" method="get" onsubmit="return false;"> 
		  <input type="hidden" name="user_type" id="user_type_a" value="{{$user_type}}">
          <div class="form-group col-md-4"> <label>Select State <span style="color:red;">*</span></label> 
            <select name="state" id="state" class="form-control" onchange ="return getPcByStateId(this.value)" {{$disabled}}>
			<option value="">Select State</option>
           @foreach($state as $st)
			<option value="{{$st->ST_CODE}}" @if(isset($state_id) && ($state_id==$st->ST_CODE)){{'selected'}}@endif>{{$st->ST_NAME}}</option>
          @endforeach
            </select>
          </div>

		 @if((!isset($state_id) && (empty($pc_no)))) <!-- For ECI -->
		  <div class="form-group col-md-4"> <label>Select PC <span style="color:red;">*</span></label> 
            <select name="pc" id="pc" class="form-control" onchange ="return getAcByStateAndPcId(this.value)">
            <option value="">Select PC</option>
           <option> </option>
            </select>
          </div>
		 @endif	
		
		
		 @if((isset($state_id) && (empty($pc_no)))) <!-- For CEO -->
		  <div class="form-group col-md-4"> <label>Select PC <span style="color:red;">*</span></label> 
            <select name="pc" id="pc" class="form-control" onchange ="return getAcByStateAndPcId(this.value)">
            <option value="">Select PC</option>
               @foreach($get_Pc_data as $pcdata)
				<option value="{{$pcdata->PC_NO}}"  @if(isset($state_id) && ($pc_no==$pcdata->PC_NO)){{'selected'}}@endif>{{$pcdata->PC_NO}}- {{$pcdata->PC_NAME}}</option>
			 @endforeach
            </select>
          </div>
		 @endif	

		 @if((isset($state_id) && (!empty($pc_no)))) <!-- For ARO(DEO) -->
		  <div class="form-group col-md-4"> <label>Select PC <span style="color:red;">*</span></label> 
            <select name="pc" id="pc" class="form-control" onchange ="return getAcByStateAndPcId(this.value)" {{$disabled}}>
            <option value="">Select PC</option>
               @foreach($get_Pc_data as $pcdata)
				<option value="{{$pcdata->PC_NO}}"  @if(isset($state_id) && ($pc_no==$pcdata->PC_NO)){{'selected'}}@endif>{{$pcdata->PC_NO}}- {{$pcdata->PC_NAME}}</option>
			 @endforeach
            </select>
          </div>
		 @endif	


		@if(empty($pc_no))  
		  <div class="form-group col-md-4"> <label>Select AC <span style="color:red;">*</span></label> 
            <select name="ac" id="ac" class="form-control">
            <option value="">Select AC</option>
           <option> </option>
            </select>
          </div>
		@endif  
		
		@if(!empty($pc_no) && (empty($ac_no)))  
		  <div class="form-group col-md-4"> <label>Select AC <span style="color:red;">*</span></label> 
            <select name="ac" id="ac" class="form-control">
			<option value="">Select AC</option>
			<option value="000" selected>All AC</option>
             @foreach($acData as $adata)
				<option value="{{$adata->AC_NO}}" @if(isset($ac_no) && ($ac_no==$adata->AC_NO)){{'selected'}}@endif>{{$adata->AC_NO}}- {{$adata->AC_NAME}}</option>
			 @endforeach
            </select>
          </div>
		@endif  
		
		@if(!empty($pc_no) && (!empty($ac_no)))
		  <div class="form-group col-md-4"> <label>Select AC <span style="color:red;">*</span></label> 
            <select name="ac" id="ac" class="form-control" {{$disabled}}>
			<option value="">Select AC</option>
			<option value="000" selected>All AC</option>
             @foreach($acData as $adata)
				<option value="{{$adata->AC_NO}}" @if(isset($ac_no) && ($ac_no==$adata->AC_NO)){{'selected'}}@endif>{{$adata->AC_NO}} - {{$adata->AC_NAME}}</option>
			 @endforeach
            </select>
          </div>
		@endif  		
		@if(empty($pc_no))  
		   <div class="form-group col-md-4"> <label>Select Candidate <span style="color:red;">*</span></label> 
            <select name="condidate" id="condidate" class="form-control">
            <option value="">Select Candidate</option>
           <option> </option>
            </select>
          </div>
		@endif  		
		@if(!empty($pc_no))  
		   <div class="form-group col-md-4"> <label>Select Candidate <span style="color:red;">*</span></label> 
            <select name="condidate" id="condidate" class="form-control">
            <option value="">Select Candidate</option>
			<option value="000" selected>All Candidate</option>
            @foreach($condidateData as $cData)
				<option value="{{$cData->candidate_id}}">{{$cData->nom_id}}-{{$cData->candidate_name}}({{$cData->party_abbre}})</option>
			 @endforeach
            </select>
          </div>
		@endif  
		<div class="form-group col-md-4"> <label>Round</label> 
            <select name="phase" id="phase" class="form-control" disabled>
            <option value="All" selected>All</option>
           <option> </option>
            </select>
          </div>
		  <div class="form-group col-md-4"  id="resultDiv">
			<div class="row"><label class="col" for="">&nbsp;</label></div>		
			<a class="btn btn-primary btn-block" href="#" onclick="return getResultECI();" title="Download Excel" >Search</a>
		
		</div> 
		<div class="col-md-12" style="text-align: center; margin-top: 47px; margin-left: -9em;display:none;" id="loading">   
		<span class="report-btn">
			<a class="btn btn-primary" href="#" title="Download Excel" >Loading... Please Wait</a>
		</span> 
		</div> 
        </form>  
</div>
</section>
<div id="valShow" class="container-fluid pr-5 pl-5 pt-2 pb-2 swatch-yellow" style="display:none;"> 
<div class="row">
<div class="col-md-3"><span> State : </span><span id="statename" class="bold"> State : Delhi </span></div>
<div class="col-md-3"><span> PC : </span><span id="pcname" class="bold"> Delhi </span> </div>
<div class="col-md-3"><span> AC : </span><span id="acname" class="bold"> Delhi </span> </div>
<div class="col-md-3"><span> Candidate : </span><span id="candidatename" class="bold">  </span> </div>
</div>
</div>

<div class="container-fluid">
<div class="row">
	<div id="showResult" class="col mt-5"></div>
</div>


 </div>

 

   

<script> 
function downloadPdf(){ 
	document.pdfForm.submit();
}
function downloadExcel(){ 
	document.csvDownload.submit();
}
function getResultECI(){ 
	
	var state = [];    
		$("#state :selected").each(function(){
		state.push($(this).val()); 
	});
	
	var pc = [];    
		$("#pc :selected").each(function(){
		pc.push($(this).val()); 
	});
	var ac = [];    
		$("#ac :selected").each(function(){
		ac.push($(this).val()); 
	});
	var condidate = [];    
		$("#condidate :selected").each(function(){
		condidate.push($(this).val()); 
	});

    if(state==''){
		alert("Please select state");
		$('#state').focus();
		return false;
	}
	
	if(pc==''){
		alert("Please select pc");
		$('#pc').focus();
		return false;
	}

	if(ac==''){
		alert("Please select ac");
		$('#ac').focus();
		return false;
	}

	if(condidate==''){
		alert("Please select candidate");
		$('#condidate').focus();
		return false;
	}
	var user_type_a = $('#user_type_a').val();
	$('#loading').show();
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/eci/get-all-result-eci", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": state,
				"pc": pc,
				"ac": ac,
				"condidate": condidate,
				"user_type": user_type_a
				},
			dataType: "html",
			success: function(msg){ 
				$('#showResult').show();
				$('#pdfstate').val(state);
				$('#pdfpc').val(pc);
				$('#pdfac').val(ac);
				$('#pdfcondidate').val(condidate);
				$('#csvstate').val(state);
				$('#csvpc').val(pc);
				$('#csvac').val(ac);
				$('#csvcondidate').val(condidate); 
				$('#resultDiv').show();
				$('#loading').hide();
				$('#export-csv-btn').show();
				$('#export-pdf-btn').show();
				$('#showResult').html(msg); 
				$('#valShow').show();
				$('#statename').text($("#state option:selected").text());
				$('#pcname').text($("#pc option:selected").text());
				$('#acname').text($("#ac option:selected").text());
				$('#candidatename').text($("#condidate option:selected").text());

			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
}


function getPcByStateId(st_code){ 
	$('#showResult').hide();
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/eci/get-pc-by-state-id-eci", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": st_code
				},
			dataType: "html",
			success: function(msg){ 
			var jsonText = $.parseJSON(msg); 
			var text = [];
			text.push('<option value="">Select PC </option>');
			for (i=0; i<jsonText.PC_NO.length; i++) {
				text.push('<option value=' + jsonText.PC_NO[i] + '>' + jsonText.PC_NO[i] +'-'+ jsonText.PC_NAME[i]  + '</option>');
			}
			$('#pc').html(text);
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
}

function getAcByStateAndPcId(pcId){ 
   $('#showResult').hide();
	var state = [];    
		$("#state :selected").each(function(){
		state.push($(this).val()); 
	});
	//alert(state+'-'+pcId);
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/eci/get-ac-by-state-and-pc-id-eci", 
			data: {
				"_token": "{{ csrf_token() }}",
				"st_code": state,
				"pcId": pcId
				},
			dataType: "html",
			success: function(msg){ 
			var jsonText = $.parseJSON(msg); 
			var text = [];
			text.push('<option value="">Select AC </option>');
			text.push('<option value="000" selected>All AC</option>');
			for (i=0; i<jsonText.AC_NO.length; i++) {
				text.push('<option value=' + jsonText.AC_NO[i] + '>' + jsonText.AC_NO[i] +'-'+ jsonText.AC_NAME[i]  + '</option>');
			}
			$('#ac').html(text);
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
	
	$.ajax({
			type: "POST",
			url: "<?php echo url('/'); ?>/eci/get-condidate-details-eci", 
			data: {
				"_token": "{{ csrf_token() }}",
				"stateok": state,
				"pcId": pcId
				},
			dataType: "html",
			success: function(msg){
			var jsonText = $.parseJSON(msg); 
			var text = [];
			
			if(msg.length>1){
					text.push('<option value="">Select Candidate </option>');
					text.push('<option value="000" selected>All Candidate </option>');
					for (i=0; i<jsonText.candidate_id.length; i++) {
						text.push('<option value=' + jsonText.candidate_id[i] + '>' + jsonText.cParty[i]  + '</option>');
					}
					$('#condidate').html(text);
			} else { 
					text.push('<option value="" selected>No candidate found</option>');
					$('#condidate').html(text);
			}
			},
			error: function(msg){ alert('Else'+msg);
				console.log(msg);
			}
	});
	
}
</script>
@endsection




