@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Form 21 C Details')
@section('content')
<style type="text/css">
.Pdf-container {width:800px; background: #fff; margin: 0 auto;}
section.pdfDoc table { font-family: serif;}
section.pdfDoc table.table td{padding:5px;border: 1px solid #000;border-top: 0;}
section.pdfDoc table td {padding: 4px 0;font-size: 18px;}
section.pdfDoc table h1{font-size: 36px;  font-weight: 700;}
section.pdfDoc table h2 {font-size: 24px;  font-weight: 800;   color: #000;}
section.pdfDoc table th {font-size: 20px;  font-weight: 800;}
.showname {font-size: 18px; margin-left: 10px; margin-right:10px; font-weight:bold;    text-decoration-style: solid; border-bottom: 1px solid #000;}
</style>
<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/FileSaver.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.wordexport.js') }}"></script>

<div class="loader" style="display:none;"></div>
<?php if($wincan){
	if($wincan->status=='1'){
?>
<section class="statistics color-grey pt-4 pb-2">
<div class="container-fluid">
 <div class="row">
  
     <div class="col-md-12  pull-right text-right report_section">
         <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('/ropc/form-21c-report-pdf')}}" title="Download PDF" >Export PDF</a></span>
		 <span class="report-btn" id="export-doc-btn"><a class="btn btn-success word-export" href="javascript:void(0)"  title="Download DOC" >Export DOC</a></span>
<!--		 <span class="report-btn" id="upload-doc-btn"><a class="btn btn-default" href="{{url('/ropc/form-21-report-upload')}}">Upload FORM 21</a></span>-->
     </div>

 </div>
</div>
</section>
<?php }}?>
<?php if($wincan){
    if($wincan->status=='1'){
?>
<section class="pdfDoc" id="export-content">
	<div class="container-fluid">
		<div class="row">
		<div class="Pdf-container card pt-4 pb-4">
		<div class="card-body">
			<table width="100%" align="center" class="">
				<tbody>
					<tr><td colspan="2" align="center"><h4 style="font-weight:bold;">Conduct of Elections Rules, 1961 </h4></td></tr>
					<tr><td colspan="2" align="center">(Statutory Rules And Order)</td></tr>
					<tr><td colspan="2" align="center"><h5 style="font-weight:bold;">FORM 21C</h5></td></tr>
					<tr><td colspan="2" align="center">(See Rule 64) </td></tr>
					<tr><td colspan="2" align="center">(For use in General Election when seat is contested) </td></tr>
				</tbody>
			</table>
			<br>
			<table align="center" width="100%"> 
				<tbody>
					<tr>
                        <td>Declaration of the result of Election under section 66 of the Representation of the People Act, 1951. </td>
                    </tr>
				</tbody>
			</table>
			<br>
			<table width="100%" align="center"> 
				<tbody>
					<tr>
                        
                                            <td>*Election to the House of the People from the <b><u><?php if(isset($pcname)){?>{{$pcname}}<?php }?></u></b>&nbsp;parliamentary constituency in <b><u><?php if(isset($pc_state)){ echo $pc_state;}?></u></b> (State/Union territory).</td>
                    </tr>
					<tr>
					<td>In pursuance of the provisions contained in section 66 of the Representation of the People Act, 1951, read with rule 64 of the Conduct of Elections Rules, 1961, I declare that- </td>
					</tr>
				</tbody>
			</table>
			<br>
			<table width="100%"> 
				<tbody>
					<tr align="center">
                        <td><b><?php if($wincan){?>{{$wincan->lead_cand_name}}<?php }?></b> (Name)</td>
                    </tr>
					<tr align="justify">
                                        <td><b><?php if($wincan){?>{{ucwords(str_replace(strtolower($dist),'',strtolower($wincan->candidate_residence_address)))}}<?php if(isset($dist)){ echo ', '.ucwords(strtolower($dist));}?><?php if(isset($state)){ echo ' ,'.ucwords(strtolower($state));}?><?php }?></b> (Address) [sponsored by <b>{{$wincan->lead_cand_party}}</b> (name of the recognised/ registered political party)] has been duly elected to fill the seat in that House from the above constituency.</td>
					</tr>
				</tbody>
			</table>


<br>
<?php  date_default_timezone_set("Asia/Calcutta");  ?>
<table width="100%" align="left"> 
<tbody>
    <tr><td style="text-align: left;">Place : &nbsp;</td><td></td></tr>   
    <tr><td style="text-align: left;">Date : {{ date('d-m-Y') }}</td><td></td></tr>   
</tbody></table>
<br><br><br><br><br><br><br>
<table width="100%">
	 <tbody>
         <tr><td style=" width: 100px;">&nbsp;</td><td>&nbsp;</td><td style=" width: 30%;" align="center"><b><?php if(isset($user_data)){?>({{$user_data->name}})<?php }?></b></td> <td></td></tr>
	 <tr><td style=" width: 100px;">&nbsp;</td><td>&nbsp;</td><td style=" width: 25%;" align="center"><b>Returning Officer</b></td> <td></td></tr>
         <tr><td style=" width: 100px;">&nbsp;</td><td>&nbsp;</td><td style=" width: 40%;" align="center"><b><?php if(isset($pc_name1)){?>{{$pc_name1}}<?php }?> Parliamentary Constituency</b></td> <td></td></tr>
	 </tbody></table>
		</div>	
		</div>	
		</div>	
	</div>	
</section>
<?php }else{?>
<div class="clearfix">&nbsp;</div>
    <section class="pdfDoc" id="export-content">
        <div class="container-fluid">
            <div class="row">
                <div class="Pdf-container card pt-4 pb-4">
                    <div class="card-body">
                        No record available, result not declared yet.
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php }}else{?>
<div class="clearfix">&nbsp;</div>
    <section class="pdfDoc" id="export-content">
        <div class="container-fluid">
            <div class="row">
                <div class="Pdf-container card pt-4 pb-4">
                    <div class="card-body">
                        No record available, result not declared yet.
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php }?>


</div> 
<script>
$(document).ready(function($) {
  $("a.word-export").click(function(event) {
    $("#export-content").wordExport();
  });
});

</script>
@endsection
