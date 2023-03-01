@extends('admin.layouts.pc.dashboard-theme')
@section('content')
<style type="text/css">
.Pdf-container {width:800px; background: #fff; margin: 0 auto;}
section.pdfDoc table { font-family: serif;}
section.pdfDoc table.table td{padding:5px;border: 1px solid #000;border-top: 0;}
section.pdfDoc table td {padding: 4px 0;font-size: 18px;}
section.pdfDoc table h1{font-size: 36px;  font-weight: 700;}
section.pdfDoc table h2 {font-size: 24px;  font-weight: 800;   color: #000;}
section.pdfDoc table th {font-size: 18px;  font-weight: 800;}
</style>



<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
 <div class="row">
 <div class="col-md-9 pull-left">
  <h4>Form 21</h4>
 </div>
     <div class="col-md-3  pull-right text-right report_section">
         <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="Javascript:;" title="Download Excel" onclick="DownloadExcel();" target="_blank">Export Excel</a></span>
         <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="Javascript:;" onclick="DownloadPdf();" title="Download PDF" target="_blank">Export PDF</a></span>
     </div>

 </div>
</div>
</section>
<div class="loader" style="display:none;"></div>






<section class="dashboard-header section-padding" style="display: none;">
    <div class="container-fluid">
        <div class="row">
            <div id="errMsg" class="red" style="margin-left: 16px;">&nbsp;</div>
            
        </div>
        <form id="generate_report_id" class="row">
            
            <div class="form-group col-md-4"> <label>State </label>
                @if($user_data->role_id=='7')
                <select name="state" id="state" class="form-control" required="">
                    <option value="">Select State</option>
                    <?php foreach ($list_state as $k => $v) { ?>
                        <option value="<?php echo $v->ST_CODE; ?>"><?php echo $v->ST_NAME; ?></option>
                    <?php } ?>
                </select>
                @else
                <select name="state" id="state" class="form-control" required="" disabled="">
                    <option value="">Select State</option>
                    <?php foreach ($list_state as $k => $v) { ?>
                        <option value="<?php echo $v->ST_CODE; ?>" <?php if(count($list_state)==1){echo 'selected';}?>><?php echo $v->ST_NAME; ?></option>
                    <?php } ?>
                </select>
                @endif
            </div>
            <div class="form-group col-md-4"> <label>PC </label>
                @if($user_data->role_id=='7')
                <select name="pcval[]" id="pcval" class="form-control" data-actions-box="true" required="" multiple="multiple" disabled="">
                </select>
                @else
                <select name="pcval" id="pcval" class="form-control" data-actions-box="true" required="" disabled="">
                    <?php foreach ($pc_list as $k => $v) { ?>
                        <option value="<?php echo $v->PC_NO; ?>"<?php if(count($pc_list)==1){echo 'selected';}?>><?php echo $v->PC_NAME; ?></option>
                    <?php } ?>
                </select>
                @endif
            </div>
            <input type="hidden" name="acval[]" id="acval">
<!--            <div class="form-group col-md-3"> <label>Party Name </label> 
                @if($user_data->role_id=='7')
                <select name="party_id[]" id="party_id" class="form-control selectpicker" required="" multiple="" data-actions-box="true">
                </select>
                @else
                <select name="party_id[]" id="party_id" class="form-control selectpicker" data-actions-box="true" required="" multiple="multiple">
                    <?php //foreach ($list_party as $k => $v) { ?>
                        <option value="<?php //echo $v->party_id; ?>"><?php //echo $v->party_abbre.' - '.$v->party_name; ?></option>
                    <?php //} ?>
                </select>
                @endif
            </div>-->
            <div class="form-group col-md-1 text-right">
                <div class="loadingIcon" style="margin-top:25px;display: none;"><img src="{{ asset('/img/loading-icon.gif')}}"/></div>
            </div>
            <div class="form-group col-md-2 text-right">
                <button type="button" name="search" id="search_record" class="btn btn-success" style="margin-top:31px;">Search </button>
            </div>

        </form>   


    </div>
</section>
<section class="pdfDoc">
	<div class="container-fluid">
		<div class="row">
		<div class="Pdf-container card pt-4 pb-4">
		<div class="card-body">
			<table width="100%" align="center" class="">
				<tbody>
					<tr><td colspan="2" align="center"><h1>FORM-21E</h1></td></tr>
					<tr><td colspan="2" align="center">Return of Election</td></tr>
					<tr><td colspan="2" align="center"><i>[See rule 64 of the Conduct of Elections Rules, 1961]</i></td></tr>
				</tbody>
			</table>
			<br>
			<table align="center" width="100%"> 
				<tbody>
					<tr>
						<td width="115px">Election to the</td>
						<td style="border-bottom: 2px solid #000;" text-align:=""><?php echo $state;?></td>
						<td width="100px">From the</td>
						<td style="border-bottom: 2px solid #000;width: 250px;text-align: left;"><?php echo $pcname;?></td>
					</tr>

					<tr>
						<td colspan="2" style="font-size: 16px;border-bottom: 2px solid #000;"></td>
						<td style="/* font-size: 16px; */">Constituency</td> 
						<td></td>
					</tr>
				
				</tbody>
			</table>
			<br>
			<table align="center">
				<tbody><tr><td align="center"><h2>RETURN OF ELECTION</h2></td></tr>
			</tbody></table>
			<table width="100%" align="center" cellspacing="0" cellpadding="8" class="table table-bordered">
    <thead>
        <tr>
            <th>Serial No.</th>
            <th>Name of Candidate</th>
            <th>Party Affiliation</th>
            <th>Number of votes polled</th>
        </tr>
    </thead>
    <tbody>
		 @php $i=0;
        @endphp
        @if(count($array)>0)
        @foreach($array as $data)
        @php $i++;
        @endphp
                <tr>
            <td>{{$i}}</td>
            <td>{{$data['candidate_name']}}</td>
            <td>{{$data['party_name']}}</td>
            <td>{{$data['total_vote']}}</td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
        @endif
        </tbody>

</table>

<table width="80%" align="right" class="ml-auto">
	<tbody><tr>
		<td align="left">
			<table style="width: 100%;margin-bottom: 10px;">
				<tbody>
				<tr>
					<td style="" width="32%">Total numbers of electors</td>
					<td style="border-bottom: 2px solid #000;"><?php echo number_format($tot_electrol);?></td>
				</tr>
			</tbody></table>
			
			<table style="
    width: 100%;
    margin-bottom: 10px;
">
				<tbody>
				<tr>
					<td style="" width="45%">Total numbers of valid votes polled</td>
					<td style="border-bottom: 2px solid #000;"><?php echo number_format($total_validpol);?></td>
				</tr>
			</tbody>
			</table>
			
			<table style="width: 100%; margin-bottom: 10px;">
				<tbody>
					<tr>
						<td style="" width="39%">Total numbers of rejected votes</td>
						<td style="border-bottom: 2px solid #000;"><?php echo $array[0]['rejectedvote'];?></td>
					</tr>
				</tbody>
			</table>
			
			<table style="margin-bottom: 10px;  width: 100%;">
				<tbody>
					<tr>
						<td style="" width="40%">Total numbers of tendered votes</td>
						<td style="border-bottom: 2px solid #000;">0</td>
					</tr>
				</tbody>
			</table>
			
			<table style="margin-bottom: 10px;  width: 100%;">
				<tbody>
					<tr>
						<td style=" " width="41%">I declare that :-</td>
						
					</tr>
				</tbody>
			</table>
		</td>	
		</tr>
</tbody>
</table>

<table width="100%" align="left">
	<tbody><tr>
		<td style="border-bottom: 2px solid #000;"> {{$win_can->cand_name}} </td>
		<td style=" " width="80px" align="right"> (Name)</td>
	</tr>
</tbody></table>
<table width="100%" align="left">
	<tbody><tr>
		<td width="50px" align="left">of</td>
		<td style="border-bottom: 2px solid #000;"> {{$win_can->candidate_residence_address}} </td>
		<td width="80px" align="right"> (address)</td>
	</tr>
</tbody></table>
<table width="100%" align="left">
	<tbody><tr align="left">
		<td>has been duly elected to fill the seat.</td>
		
	</tr>
</tbody>
</table>
<?php  date_default_timezone_set("Asia/Calcutta");  ?>
<br>
<table width="100%" align="left" class="mt-2"> 
<tbody><tr><td style="text-align: left;width: 70px;">Place:-</td><td>{{$user_data->placename}}</td></tr>   
</tbody></table>
<br>
<table width="100%">
	 <tbody><tr><td style=" width: 70px;">Date:- </td><td> {{ date('d-m-Y H:i:s') }}</td> <td style=" width: 40%;">Returning Officer</td> <td></td></tr></tbody></table>
		</div>	
		</div>	
		</div>	
	</div>	
</section>
<form id="exportFrm" method="post">
    {{ csrf_field() }}											
    <input type ="hidden" name="statevalue" id='statevalue' value = "">
    <input type ="hidden" name="pcvalue" id='pcvalue' value = "">
</form>
</div> 


<script type="text/javascript">

//    setTimeout(function (e) {
//        referesh_page();
//    }, 300000);
    var role_id = '<?php echo $user_data->role_id ?>';
    var prefix = '';
    if(role_id !=''){
        switch(role_id){
            case '7':
                prefix = 'eci';
            break;
            case '4':
                prefix = 'pcceo';
            break;
            case '18':
                prefix = 'ropc';
            break;
            case '20':
                prefix = 'aro';
            break;
        }
    }

//    //Select pc list by state start 
//    $("#state").change(function(){
//       $("#errMsg").html("&nbsp;");
//       var state = $(this).val();
//       if(state !=''){
//            $.ajax({
//                 url: '<?php //echo url('/'); ?>/'+prefix+'/voter-type-wise-report-getpc-state/'+encodeURI(state),
//                type: "GET",
//                dataType: "html",
//               success: function(msg){ 
//			  
//                    var jsonText = $.parseJSON(msg); 
//                    var pc_arrval = jsonText.pc_arr;
//                    var party_arrval = jsonText.party_arr;
//                  
//                    var text = [];
//                    var text1 = [];
//                    if(msg.length > 0){
//                            for (var i=0; i<pc_arrval.id.length; i++) { 
//                                    text.push('<option value=' + pc_arrval.id[i] + ' >' + pc_arrval.val[i]  +'-'+ pc_arrval.id[i] +'</option>');   
//                            }						
//                            $('#pcval').html(text).selectpicker('refresh');
//                    } else {
//                            text.push('<option selected value="No_Dis">PC Not Found</option>');
//                            $('#pcval').html('');
//                    }
//                    if(msg.length > 0){
//                            for (var i=0; i<party_arrval.id.length; i++) { 
//                                    text1.push('<option value=' + party_arrval.id[i] + ' >' + party_arrval.val[i]+'</option>');   
//                            }						
//                            $('#party_id').html(text1).selectpicker('refresh');
//                    } else {
//                            text1.push('<option selected value="No_Dis">Party Not Found</option>');
//                            $('#party_id').html('');
//                    }
//                    },
//            });
//       }
//    });
//    
//    //Select pc list by state ends 
//    
//    //Select ac list by pc start 
//    $("#pcval").change(function(){
//       $("#errMsg").html("&nbsp;");
//       var pc = $(this).val();
//       var state = $("#state").val();
//       if(state==''){
//           $("#errBox").show();
//           $("#errMsg").text("Please select state first.");
//           $("#state").focus();
//           return false;
//       }else{
//           //$("#errBox").hide();
//       }
//       if(pc==''){
//           $("#errBox").show();
//           $("#errMsg").text("Please select PC.");
//           $("#pcval").focus();
//           return false;
//       }else{
//           //$("#errBox").hide();
//       }
//        var state_code = $("#state").val();
//        var selectedValues = [];    
//            $("#pcval :selected").each(function(){
//            selectedValues.push($(this).val()); 
//        });	
//       
//       if(pc !='' && state !=''){
//            $.ajax({
//                 url: '<?php //echo url('/'); ?>/'+prefix+'/voter-type-wise-report-get-party/'+encodeURI(selectedValues)+'/'+encodeURI(state),
//                type: "GET",
//                dataType: "html",
//               success: function(msg){ 
//			  
//                    var jsonText = $.parseJSON(msg); 
//                    var text = [];
//                    if(msg.length > 2){
//                            for (var i=0; i<jsonText.id.length; i++) { 
//                                    text.push('<option value=' + jsonText.id[i] + ' >' + jsonText.val[i]  +'-'+ jsonText.id[i] +'</option>');   
//                            }						
//                            $('#party_id').html(text).selectpicker('refresh');
//                    } else {
//                            text.push('<option selected value="No_Dis">Party Not Found</option>');
//                            $('#party_id').html('');
//                    }
//                    },
//            });
//       }
//    });
//    
//    //Select ac list by pc ends 
//    $("#party_id").change(function(){
//        $("#errMsg").html("&nbsp;");
//        $("#party_id").selectpicker('refresh');
//    });
//    
    //Searching start here
    $("#search_record").click(function(){
        var state_code = $("#state").val();
        var pcno = $("#pcval").val();

        if(state_code !='' && pcno !=''){
            $.ajax({
                    type: "POST",
                    url: '<?php echo url('/'); ?>/'+prefix+'/form-21-report-view', 
                    data: {
                            "_token": "{{ csrf_token() }}",
                            "stateid": state_code,
                            "pcno":pcno
                            },
                    dataType: "html",
                    beforeSend: function (xhr) {
                        $('#search_record').prop("disabled",true);
                        $('#datashow').hide();
                        $(".loadingIcon").show();
                    },
                    success: function(msg){	
                            $(".loadingIcon").hide();
                            $('#datashow').show();
                            $('#search_record').prop("disabled",false);
                            var tview = msg.split("|||")[0];
                            if(tview!=""){
                                $("#datashow").show();
                                $("#datashow").html(tview);
                            }else{
                                $("#datashow").hide();
                                $(".report_section").hide();
                            }
                            
                            var rowCount = parseInt(msg.split("|||")[1]);
                            if(rowCount >0){
                                $(".report_section").show();
                            }else{
                                $(".report_section").hide();
                            }
                    }, 
                    error: function(msg){ console.log(msg); 
                            //console.log("Error");
                    }
            });
        }
    });
    
//    $("#reset").click(function(){
//        setTimeout(function(){ 
//            $("#datashow").hide();
//            $('.selectpicker').selectpicker('refresh');
//        }, 100);
//        
//    });
    
    function DownloadExcel(){
        var state_code = $("#state").val();
        var pcno = $("#pcval").val();
        
        var acurl = '<?php echo url('/'); ?>/'+prefix+'/form-21-report-excel';
        
        $('#exportFrm').attr('action', acurl);
        $("#statevalue").val(state_code);
        $("#pcvalue").val(pcno);
        //$("#partyvalue").val(party_id);
        $("#exportFrm").submit();
    }
    function DownloadPdf(){
        var state_code = $("#state").val();
        var pcno = $("#pcval").val();
        
        var acurl = '<?php echo url('/'); ?>/'+prefix+'/form-21-report-pdf';
        $('#exportFrm').attr('action', acurl);
        $("#statevalue").val(state_code);
        $("#pcvalue").val(pcno);
        //$("#partyvalue").val(party_id);
        $("#exportFrm").submit();
    }

    function referesh_page() {
        location.reload();
    }
</script>
<script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.stickytable.min.js') }}"></script>
@endsection