@extends('admin.layouts.pc.dashboard-theme')
@section('title', 'Suvidha')
@section('bradcome', 'Candidate Wise Report')
@section('content')
<link rel="stylesheet" href="{{ asset('public/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/css/jquery.stickytable.min.css') }}">
<style>
    .bootstrap-select>.dropdown-toggle {
    background-color: #fff;
    border: 1px solid #ced4da;
    padding: 0.6rem 1rem;
}
</style>

<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
 <div class="row">
 <div class="col-md-9 pull-left">
  <h4>Candidate Wise Report</h4>
 </div>
     <div class="col-md-3  pull-right text-right report_section" style="display: none;">
         <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="Javascript:;" title="Download Excel" onclick="DownloadExcel();" target="_blank">Export Excel</a></span>
<!--     <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="Javascript:;" onclick="DownloadPdf();" title="Download PDF" target="_blank">Export PDF</a></span>-->
 </div>

 </div>
</div>
</section>
<div class="loader" style="display:none;"></div>
<section class="dashboard-header section-padding">
    <div class="container-fluid">
        <form id="generate_report_id" class="row">
            
            <div class="form-group col-md-4"> <label>State* </label>
                @if($user_data->role_id=='7')
                <select name="state" id="state" class="form-control" required="">
                    <option value="">Select State</option>
                    <option value="all">All</option>
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
                <div id="errMsg1" class="red errMsg">&nbsp;</div>
            </div>
            <div class="form-group col-md-4"> <label>PC* </label>
                @if($user_data->role_id=='7')
                <select name="pcval" id="pcval" class="form-control">
                    <option value="">Select PC</option>
                </select>
                @else
                <select name="pcval" id="pcval" class="form-control">
                    @if($user_data->role_id=='4')
                    <option value="all">All</option>
                     @endif
                    <?php foreach ($pc_list as $k => $v) { ?>
                        <option value="<?php echo $v->PC_NO; ?>"<?php if(count($pc_list)==1){echo 'selected';}?>><?php echo $v->PC_NO.'-'.$v->PC_NAME; ?></option>
                    <?php } ?>
                </select>
                @endif
                <div id="errMsg2" class="red errMsg">&nbsp;</div>
            </div>
            <input type="hidden" name="acval[]" id="acval">
            <div class="form-group col-md-4"> <label>Candidate Name* </label> 
                @if($user_data->role_id=='7')
                <select name="party_id[]" id="party_id" class="form-control">
                    <option value="">Select candidate</option>
                </select>
                @else
                <select name="party_id[]" id="party_id" class="form-control">
                    <option value="all">All</option>
                    <?php foreach ($list_party as $k => $v) { ?>
                        <option value="<?php echo $v->candidate_id; ?>"><?php echo $v->candidate_name.' ( '.$v->party_abbre.' ) '; ?></option>
                    <?php } ?>
                </select>
                @endif
                <div id="errMsg3" class="red errMsg">&nbsp;</div>
            </div>
            <div class="form-group col-md-4"> <label>Winning Status </label>
                <select name="show_record" id="show_record" class="form-control">
                    <option value="">Select records</option>
                    <option value="all" selected="">All</option>
                    <option value="winner">Winner</option>
                </select>
            </div>
            <div class="form-group col-md-4"> <label>Show Candidate Profile </label>
                <div><input type="checkbox" name="cand_profile" id="cand_profile" checked="" value="yes"></div>
            </div>
            <div class="form-group col-md-4 text-right">
                <img src="{{ asset('/public/img/loading-icon.gif')}}" style="margin-top: 22px;display: none;" class="loadingIcon"/>
                <button type="button" name="search" id="search_record" class="btn btn-success" style="margin-top:31px;">Search</button>
                <button type="reset" name="reset" id="reset" class="btn btn-danger" style="margin-top:31px;">Reset</button>
            </div>
        </form>   
        

    </div>
</section>


<form id="exportFrm" method="post">
    {{ csrf_field() }}											
    <input type ="hidden" name="statevalue" id='statevalue' value = "">
    <input type ="hidden" name="pcvalue" id='pcvalue' value = "">
    <input type ="hidden" name="partyvalue" id='partyvalue' value = "">
    <input type ="hidden" name="show_record_value" id='show_record_value' value = "">
    <input type ="hidden" name="cand_profile_value" id='cand_profile_value' value = "">
</form>
<div class="container-fluid">
    <!-- Start parent-wrap div -->  
    <div class="parent-wrap">
        <!-- Start child-area Div --> 
        <div class="child-area">
            <div class="page-contant">
                <div class="random-area">
                    <br>
                    <div id="datashow" class="head-title"></div>
                    
                   
                </div><!-- End Of intra-table Div -->   


            </div><!-- End Of random-area Div -->

        </div><!-- End OF page-contant Div -->
    </div>      
</div><!-- End Of parent-wrap Div -->
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
    
    $('input[type="checkbox"]').click(function(){
        if($(this).is(":checked")){
            $("#cand_profile").val('yes');
            $("#cand_profile_value").val('yes');
        }
        else if($(this).is(":not(:checked)")){
            $("#cand_profile").val('no');
            $("#cand_profile_value").val('no');
        }
    });

    //Select pc list by state start 
    $("#state").change(function(){
       $(".errMsg").html("&nbsp;");
       var state = $(this).val();
       if(state !=''){
           if(state !='all'){
               $.ajax({
                 url: '<?php echo url('/'); ?>/'+prefix+'/candidate-wise-report-getpc-state/'+encodeURI(state),
                type: "GET",
                dataType: "html",
               success: function(msg){ 
			  
                    var jsonText = $.parseJSON(msg); 
                    var pc_arrval = jsonText.pc_arr;
                    var party_arrval = jsonText.party_arr;
                  
                    var text = [];
                    var text1 = [];
                    if(msg.length > 0){
                            text.push('<option value="all" >All</option>');   
                            for (var i=0; i<pc_arrval.id.length; i++) { 
                                    text.push('<option value=' + pc_arrval.id[i] + ' >' + pc_arrval.id[i] +'-'+ pc_arrval.val[i] +'</option>');   
                            }						
                            $('#pcval').html(text);
                    } else {
                            text.push('<option selected value="No_Dis">PC Not Found</option>');
                            $('#pcval').html('');
                    }
                    if(msg.length > 0 ){
                            text1.push('<option value="all" >All</option>');   
                            for (var i=0; i<party_arrval.id.length; i++) { 
                                    text1.push('<option value=' + party_arrval.id[i] + ' >' + party_arrval.val[i]+'</option>');   
                            }                       
                            $('#party_id').html(text1);
                    } else {
                            text1.push('<option selected value="No_Dis">Candidate Not Found</option>');
                            $('#party_id').html('');
                    }
                    },
            });
           }else{
                    var text = [];
                    var text1 = [];
                    text.push('<option value="all" >All</option>');  
                    text1.push('<option value="all" >All</option>');  
                    $('#pcval').html(text);
                    $('#party_id').html(text1);
           }
            
       }
    });
    
    //Select pc list by state ends 
    
    //Select ac list by pc start 
    $("#pcval").change(function(){
       $(".errMsg").html("&nbsp;");
       var pc = $(this).val();
       var state = $("#state").val();
       if(state==''){
           $("#errMsg1").text("Please select state first.").show();
           $("#state").focus();
           return false;
       }else{
           //$("#errBox").hide();
       }
       if(pc==''){
           $("#errMsg2").text("Please select PC.").show();
           $("#pcval").focus();
           return false;
       }else{
           //$("#errBox").hide();
       }
        var state_code = $("#state").val();
        var selectedValues = [];    
            $("#pcval :selected").each(function(){
            selectedValues.push($(this).val()); 
        });	
       
       if(pc !='' && state !=''){
           if(pc!='all'){
               $.ajax({
                 url: '<?php echo url('/'); ?>/'+prefix+'/candidate-wise-report-get-party/'+encodeURI(selectedValues)+'/'+encodeURI(state),
                type: "GET",
                dataType: "html",
               success: function(msg){ 
              
                    var jsonText = $.parseJSON(msg); 
                    var text = [];
                    if(msg.length > 2){
                            text.push('<option value="all" >All</option>');   
                            for (var i=0; i<jsonText.id.length; i++) { 
                                    text.push('<option value=' + jsonText.id[i] + ' >' + jsonText.val[i]+'</option>');   
                            }                       
                            $('#party_id').html(text);
                    } else {
                            text.push('<option selected value="No_Dis">Candidate Not Found</option>');
                            $('#party_id').html('');
                    }
                    },
            });
           }else{
                    var text = [];
                    text.push('<option value="all" >All</option>');   
                    $('#party_id').html(text);
           }
            
       }
    });
    
    //Select ac list by pc ends 
    $("#party_id").change(function(){
        $("#errMsg").html("&nbsp;");
    });
    
    //Searching start here
    $("#search_record").click(function(){
        $(".errMsg").hide();
        var state_code = $("#state").val();
        var pcno = $("#pcval").val();
        var party_id = $("#party_id").val();
        if(state_code==''){
            $("#errMsg1").text("Please select state.").show();
            $("#state").focus();
            return false;
        }else{
            //$("#errBox").hide();
        }
        if(pcno==''){
            $("#errMsg2").text("Please select PC.").show();
            $("#pcval").focus();
            return false;
        }else{
            //$("#errBox").hide();
        }
        if(party_id==''){
            $("#errMsg3").text("Please select candidates.").show();
            $("#party_id").focus();
            return false;
        }else{
            //$("#errBox").hide();
        }
        
        if(state_code !='' && pcno !='' && party_id !=''){
            $.ajax({
                    type: "POST",
                    url: '<?php echo url('/'); ?>/'+prefix+'/candidate-wise-report-search', 
                    data: {
                            "_token": "{{ csrf_token() }}",
                            "stateid": state_code,
                            "pcno":pcno,
                            "party":party_id,
                            "show_record":$("#show_record").val(),
                            "cand_profile":$("#cand_profile").val()
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
                            $('#example').dataTable();
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
    
    $("#reset").click(function(){ 
			referesh_page(); 
    });
	
    
    function DownloadExcel(){
        var state_code = $("#state").val();
        var selectedValues = [];    
            $("#pcval :selected").each(function(){
            selectedValues.push($(this).val()); 
        }); 
        var selectedValues1 = [];    
            $("#party_id :selected").each(function(){
            selectedValues1.push($(this).val()); 
        }); 
        var pcno = selectedValues;
        var party_id = selectedValues1;
        
        var acurl = '<?php echo url('/'); ?>/'+prefix+'/candidate-wise-report-excel';
        
        $('#exportFrm').attr('action', acurl);
        $("#statevalue").val(state_code);
        $("#pcvalue").val(pcno);
        $("#partyvalue").val(party_id);
        $("#show_record_value").val($("#show_record").val());
        $("#cand_profile_value").val($("#cand_profile").val());
        $("#exportFrm").submit();
    }
    function DownloadPdf(){
        var state_code = $("#state").val();
        var selectedValues = [];    
            $("#pcval :selected").each(function(){
            selectedValues.push($(this).val()); 
        }); 
        var pcno = selectedValues;
        var selectedValues1 = [];    
            $("#party_id :selected").each(function(){
            selectedValues1.push($(this).val()); 
        }); 
        var pcno = selectedValues;
        var party_id = selectedValues1;
        
        var acurl = '<?php echo url('/'); ?>/'+prefix+'/candidate-wise-report-pdf';
        
        $('#exportFrm').attr('action', acurl);
        $("#statevalue").val(state_code);
        $("#pcvalue").val(pcno);
        $("#partyvalue").val(party_id);
        $("#exportFrm").submit();
    }

    function referesh_page() {
        location.reload();
    }
</script>
<script type="text/javascript" src="{{ asset('public/js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/js/jquery.stickytable.min.js') }}"></script>
@endsection
