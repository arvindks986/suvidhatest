<!DOCTYPE html>
<html>
<head>
<title>Election Comission Of India:: Round Wise Report</title>

</head>

<!-- New Here -->
@php
	$maxRound=0
@endphp
@php
	$maxRound = app(App\Http\Controllers\Admin\PcWiseRoundReport\PcWiseRoundReportController::class)->getMaxRound($st_code, $pc)
@endphp
<!-- End New Here -->

<?php 
ini_set("pcre.backtrack_limit", "5000000000000000");
if($maxRound >= 6){  ?>
<style>

@page { sheet-size: A3-L;font-size:20px; }
@page bigger { sheet-size: 520mm 470mm; }
@page toc { sheet-size: A4; }
h1.bigsection {
	page-break-before: always;
	page: bigger;
}

</style>
<?php } ?>
<body>
<table style="width:100%;  border: 1px solid #d7d7d7;border-collapse: collapse;" border="0" align="center" cellpadding="5">
   <thead>
   	<tr>
   		<th align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ asset('img/logo/eci-logo.png')}}" alt=""  width="85" border="0"/></th>
   		<th align="center" style="font-size: 16px; border-bottom: 1px dotted #d7d7d7;">Election Commission Of India</th>
   		<th align="right" style="border-bottom: 1px dotted #d7d7d7;"><img src="{{ asset('admintheme/img/logo/eci-logo.png')}}" width="125" alt="" border="0"/></th>
   	</tr>
   </thead>
</table>
  <table class="table" style="width:100%; background-color: #ffffff; " border="0" align="center" cellpadding="5">
	<tbody>
   	<tr style="font-size: 16px; text-transform: uppercase; background-color: #f7f7f7;">
	 <td colspan="2" align="center" style="padding: 15px;"><strong>Round Wise Report</strong></td>
	</tr>
	</tbody>	 
</table>
<table class="table" style="width:100%; background-color: #ffffff;" border="0" align="center">
	<tbody>
	  <tr style="font-size: 10px; text-transform: uppercase; background-color: #f7f7f7;">
		<td >State:&nbsp;&nbsp;{{$state_name}}</td>
	  </tr>
	</tbody>	
</table>
<br/>
<table id="acViewBody" border="1" class="" width="100%" cellpadding="5" cellspacing="0" style="width:100%;">
<thead style="text-align: center;18px;">
       <tr style="">
        <th style="width:25px;text-align:center;"> S. No. </th>
		<th style="width:25px;text-align:center;">PC No.</th>
		<th style="width:125px;">PC Name</th>
		<th style="text-align:left;width:225px;">Party</th>
        <th style="text-align:left;width:225px;">Candidate</th>
		
		<!-- New -->
			<?php $b=0; if($maxRound == 0 ){ ?>
			<th>Round Wise EVM Votes </th>
			<?php } ?>
			
			<?php  for($m=1; $m<=$maxRound; $m++){   ?>
			<th> <span> <?php  echo 'R'.$m; ?> </span></th>
			<?php   } ?>
		<!-- New -->
		<th style="">Total EVM Votes</th>
       </tr>
	</thead>
    <tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		
		<?php
			$mData = (array)$data; 
		?>
		
		@php
		$getpc = app(App\Http\Controllers\Admin\PcWiseRoundReport\PcWiseRoundReportController::class)->getPc($st_code, $mData['pc_no'])
		@endphp
        <tr>
        <td style="width:25px;text-align:center;">  <span>{{$i}}</span>  </td> 
		<td style="width: 25px;text-align:center;">{{$mData['pc_no']}}</td>
		<td>@if(isset($getpc)){{$getpc}}@else{{'NA'}}@endif</td>
		<td style="width: 125px;">  <span>{{$mData['party_name']}}</span>  </a>  </td> 
		<td style="width: 125px;">  <span>{{$mData['candidate_name']}}  </span>  </a>  </td> 
		
			<?php 	
			$total_votes=0; $p=0; $remain=0; $isRound=0;
			for($k=1; $k<=$maxRound; $k++){   
			?>
					<?php   $dataok = 'R'.$k;
						 $p++; $isRound++;
						?>
							<td align="center"> <span> 
								<?php  
									echo $mData[$dataok];	
									$total_votes=$total_votes + $mData[$dataok];
								?> 
							</span></td>
							
			<?php  } 
			
			if(($isRound==0)){ ?>
			<td align="center" style=""> 
					<span> 	NA </span>
			</td>
			<?php  } ?>
			
			<td style="border-bottom:1px solid black!important;text-align:right;width:25px!important;max-width: 125px;">  <span>{{$total_votes}}</span>  </td> 
		</tr>
		
		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="7">No Record available</td> 
		</tr>
		@endif
       </tbody></table>
	<h5>&nbsp;Downloaded by: {{ $LoginData['user_data']['name'] }}<span></span></h5>
	<h5>&nbsp;Date: {{ date('d-m-Y H:i:s') }}<span></span></h5>
</body>
</html>
