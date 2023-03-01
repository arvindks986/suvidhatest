<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Form 21 C</title>
  
    </head>
    <body>
        <?php if($wincan){
	//if($wincan->status=='1'){
        ?>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  padding: 25px 0;" border="0" align="center" cellpadding="0">
               <thead>
			   <?php if($wincan->status=='0'){?>
                    <tr> <th style="width:100%; font-size: 30px;margin:5px 0;color: blue;" align="center" >(Preview)</th></tr>
                <?php }?>
                <tr> <th style="width:100%; font-size: 22px;margin:5px 0;" align="center" > Conduct of Elections Rules, 1961 </th></tr>
        		<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" >(Statutory Rules And Order)</td></tr>
        		<tr> <th style="width:100%; font-size:14px;margin:5px 0;" align="center" >FORM 21D</th></tr>
        		<tr><th>&nbsp;</th></tr>
				<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" >(See Rule 64) </td></tr>
				<tr><th>&nbsp;</th></tr>
		        <tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" >(For use in Election to fill a casual vacancy when seat is contested) </td></tr>
              </thead>
            </table>
			<table align="center" width="100%"> 
				<tbody> 
					<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" >Declaration of the result of Election under section 66 of the Representation of the People Act, 1951. </td></tr>
				</tbody>
			</table><br>
			<table align="center" width="100%"> 
				<tbody> 
                                    <tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" >*Election to the House of the People from the <b><u><?php if(isset($pcname)){?>{{$pcname}}<?php }?></u></b>&nbsp;parliamentary constituency in <b><u><?php if(isset($pc_state)){ echo $pc_state;}?></u></b> (State/Union territory).</td></tr>
					<tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" >In pursuance of the provisions contained in section 66 of the Representation of the People Act, 1951, read with rule 64 of the Conduct of Elections Rules, 1961, I declare that- </td></tr>
				</tbody>
			</table><br>
			<table align="center" width="100%"> 
				<tbody> 
                                        <tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="center" ><b><?php if($wincan){?>{{$wincan->lead_cand_name}}<?php }?></b> (Name)</td></tr>
                                        <tr> <td style="width:100%; font-size:18px;margin:5px 0;" align="justify" ><b><?php if($wincan){?>{{ucwords(str_replace(strtolower($dist),'',strtolower($wincan->candidate_residence_address)))}}<?php if(isset($dist)){ echo ', '.ucwords(strtolower($dist));}?><?php if(isset($state)){ echo ', '.ucwords(strtolower($state));}?><?php }?></b> (Address) [sponsored by <b><?php if($wincan){?>{{$wincan->lead_cand_party}}<?php }?></b> (name of the recognised/ registered political party)] has been duly elected to fill the seat in that House from the above constituency.</td></tr>
				</tbody>
			</table>
        <!--HEADER ENDS HERE-->
<br>
<table width="100%" align="left"> 
    <tbody>
        <tr><td style="text-align: left;">Place : &nbsp;</td><td></td></tr>   
        <tr><td style="text-align: left;">Date : {{ date('d-m-Y') }}</td><td></td></tr>    
</tbody></table>
<br><br><br><br><br><br><br>
<table width="100%">
	 <tbody>
         <tr><td style=" width: 600px;">&nbsp;</td><td>&nbsp;</td><td style=" width: 25%;font-size: 18px;" align="center"><b><?php if(isset($user_data)){?>({{$user_data->name}})<?php }?></b></td> <td></td></tr>
	 <tr><td style=" width: 600px;">&nbsp;</td><td>&nbsp;</td><td style=" width: 25%;font-size: 18px;" align="center"><b>Returning Officer</b></td> <td></td></tr>
         <tr><td style=" width: 600px;">&nbsp;</td><td>&nbsp;</td><td style=" width: 50%;font-size: 18px;" align="center"><b><?php if($pc_name1){?>{{$pc_name1}}<?php }?> Parliamentary Constituency</b></td> <td></td></tr>
	 </tbody></table>
        <?php }else{?>
<div>No record available, result not declared yet.</div>
        <?php }?>
 </body>
</html>

 
