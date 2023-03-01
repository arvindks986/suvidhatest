<!DOCTYPE html>
<html>
<head>
	<title>Candidate Nomination Details</title>
	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
	
	<style type="text/css">
	body{font-family: 'Lato', sans-serif;}
	.hindi{font-family: 'Noto Sans', sans-serif;}
	span.hindi{font-size: 18px;}
	p > b {
    font-size: 24px;
}

small {
      font-size: 14px;
    color: rgba(0,0,0,0.4);
    margin-bottom: 4px;
    display: block;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
}

p {margin-top: 0;
    line-height: 23px;
    font-size: 16px;
    line-height: 26px;}
p > span:after {
    content: " / ";
    font-size: 26px;
    position: relative;
    top: 5px;
}

p > span:last-child:after {
    content: " ";
}

p > span {
        font-size: 16px;
}
	small{}
	p{}
	span{}
	p > span{}
	
	</style>
</head>
<body style="background:#e9ebee">

		<table width="800px" cellpadding="5" cellspacing="5" style="margin:0 auto; border:1px solid #dddfe2; background:#fff;">
		<tbody>
		 <tr>
	        <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
	        <th  style="width:50%font-size: 16px;font-family:freeserif;" align="right" style="border-bottom: 1px dotted #d7d7d7;">
	            SECRETARIAT OF THE<br>
	            ELECTION COMMISSION OF INDIA<br>
	            Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
	        </th>
        </tr>
		<tr>
			<td style="width:50%;"><strong style="font-size: 16px;font-family:freeserif;">Candidate Nomination Details</strong></td>
			<td style="width:50%;" align="right"><strong style="font-size: 16px;font-family:freeserif;">Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
		</tr>
		</tbody>
		</table>
		@forelse ($EciViewNominationPdf as $key=>$listdata)
		<table width="800px" cellpadding="5" cellspacing="5" style="margin:0 auto;border:1px solid #dddfe2;background:#fff;margin-top: 10px;border-bottom: none;font-family:freeserif;">
			<tbody><tr>
			<td width="200px">
				@if($listdata->cand_image != '' )
				<img width="200px" src="{{url($listdata->cand_image)}}" alt="">
				@else
				
				<div id="imagePreview"></div>
				
				@endif
			</td>
			<td width="500px">
				<table>
					<tbody>
					<tr>
						<td style="font-size: 16px;font-family:freeserif;">
							<strong style="font-size: 16px;font-family:freeserif;">Candidate Name</strong>
							<p style="font-size: 16px;font-family:freeserif;"><span>{{$listdata->cand_name}}</span>/<span class="hindi" style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_hname}}</span>/<span style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_vname}}</span></p>
						</td>	
					</tr>
					<tr>
						<td>
							<strong style="font-size: 16px;font-family:freeserif;">Party Name</strong>
							<p style="font-size: 16px;font-family:freeserif;">{{ $listdata->PARTYNAME }}</p>
						</td>	
					</tr>
					
					
					<tr style="">
						<td style="">
							
							<p><strong style="font-family:freeserif;font-size: 16px;margin-right: auto;margin-top: 14px;float: left;">Symbol</strong><br><span style="font-family:freeserif;font-size: 16px;background: #f0587e;padding: 10px;float: right;display: inline-block;color: #fff;font-size: large;font-weight: 500;">{{$listdata->SYMBOL_DES}}</span></p>
						</td>	
					</tr>
				
						
				</tbody></table>
			
			
			
			</td>
			</tr>
		</tbody>
		</table>
		
		<table width="800px" cellpadding="5" cellspacing="15" style="font-family:freeserif;margin:0 auto;border-left: 1px solid #dddfe2;background:#fff;border-right: 1px solid #dddfe2;">
		<tbody>
		<tr>
			<td width="800px" colspan="3">
				<h3 style="text-align: center;font-size: x-large;color: #bb4292;border-bottom: 1px solid #bb4292;padding-bottom: 10px;border-top: 1px solid #bb4292;padding-top: 10px;font-family:freeserif;">Candidate Personal Details</h3>
			</td>
		</tr>
		
		<!-- <tr style="/* background: #f2f2f2; */" cellpadding="0">
			<td width="800px" colspan="3" style="
">
				<small>Father’s Name</small>
				<p><span>Vyricherla Kishore Chandra Suryanarayana Deo</span> <span>वैरीचेरला किशोर चन्द्र सूर्यानारायण देव</span></p>
			</td>
		</tr> -->

		<tr>
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Father's / Husband's Name </strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->candidate_father_name}} </p>
			</td>
			<td colspan="2" style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Father's / Husband's Name  Hindi</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_fhname}}</p>
			</td>
			
		</tr>
		
		<tr>
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Email</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_email}}</p>
			</td>
			<td colspan="2" style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Mobile</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_mobile}} </p>
			</td>
			
		</tr>	
		
		<tr>
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">PAN Number</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_panno}}</p>
			</td>
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Gender</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_gender}}</p>
			</td>
			
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Age</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_age}}</p>
			</td>
			
		</tr>
		<!-- <tr>
			<td width="800px" colspan="3">
				<small>Address</small>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. </p>
				<p>करती सीमित यन्त्रालय स्वतंत्रता प्रौध्योगिकी जानते बनाए संस्था सोफ़तवेर असरकारक विकासक्षमता अन्तरराष्ट्रीयकरन कीसे जानते उदेश वातावरण बिन्दुओमे कार्य सुविधा आवश्यकत अविरोधता संदेश संभव दर्शाता</p>
			</td>
		</tr> -->
		<tr>
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Address</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->candidate_residence_address}}</p>
			</td>
			<td colspan="2" style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Address In Hindi</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->candidate_residence_addressh}}</p>
			</td>
			
		</tr>
		<tr>
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">State Name</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->ST_NAME}}</p>
			</td>
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">PC Name</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->PC_NAME}}</p>
			</td>
			
			<td style="font-size: 16px;font-family:freeserif;">
				<strong style="font-size: 16px;font-family:freeserif;">Category</strong>
				<p style="font-size: 16px;font-family:freeserif;">{{$listdata->cand_category}}</p>
			</td>
			
		</tr>
		@empty
		  <tr>
		          <td colspan="5">No Data Found For Candidate</td>                 
		      </tr>
		   @endforelse
		</tbody>
		</table>






</body>
</html>