
<!-- New Here -->
@php
	$maxRound=0
@endphp
@php
	$maxRound = app(App\Http\Controllers\Admin\PcWiseRoundReport\PcWiseRoundReportController::class)->getMaxRound($st_code, $pc)
@endphp
<!-- End New Here -->
<table id="list-table"  class="table table-striped table-bordered datatable  ">
<thead>
		<tr class="sticky-header">
        <th> S.No </th>
		@if($user!='ARO')
		<th>PC Name</th>
        <th>PC No.</th>
		@endif
		<th  class="sticky-cell cand_name">Party</th>
		<th class="sticky-cell cand_name">Candidate</th>

			
			<?php $b=0; if($maxRound == 0 ){ ?>
			<th>Round Wise EVM Votes </th>
			<?php } ?>
			
			
			<?php  for($m=1; $m<=$maxRound; $m++){   ?>
			 <th class="text-center"><span> <?php  echo 'R'.$m; ?> </span></th>
			<?php   }   ?>
			
		 <th style="text-align: center;" class="sticky-cell-opposite">Total EVM Votes</th>
       </tr>
 </thead>
 
        <tbody style="text-align: center;">
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$mData = (array)$data; 
		$getpc='';
		//echo $st_code .'-'. $mData['pc_no']; die;
		?>
		@php
		$getpc = app(App\Http\Controllers\Admin\PcWiseRoundReport\PcWiseRoundReportController::class)->getPc($st_code, $mData['pc_no'])
		@endphp
		
		
        <tr>
        <td>  <span>{{$i}}</span>  </td> 
		@if($user!='ARO')
		<td style="text-align:left;">@if(isset($getpc)){{$getpc}}@else{{'NA'}}@endif</td>
		<td style="text-align:left;"> {{$mData['pc_no']}}</td>
		@endif
		 <td style="text-align:left;" >{{$mData['party_name']}}</td>
		 <td style="text-align:left;"  class="sticky-cell cand_name"> {{$mData['candidate_name']}} </td> 
		 
							
								<?php 	
								$total_votes=0; $p=0; $remain=0; $isRound=0;
								for($k=1; $k<=$maxRound; $k++){  
											$dataok = 'R'.$k;
											 $p++; $isRound++; ?>
												<td> 
												<span>
													<?php  
														echo $mData[$dataok];	
													    $total_votes=$total_votes + $mData[$dataok];
													?> 
												</span>
												</td>
								
								<?php  }  
								if(($isRound==0)){ ?>
								<td> <span>	NA </span></td>								
								<?php  }  ?>
					
			
			<td class="sticky-cell-opposite">  <span>{{$total_votes}}</span>  </td> 
		</tr>
		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="7">  No record available </td> 
		</tr>
		@endif
       </tbody></table>
   




<script type="text/javascript">

$(document).ready( function () {
    $('.datatable').DataTable();
} );
</script>
  
<script type="text/javascript">
if($(window).width() < 767)
{
   $(document).ready(function(){
 $('.table').wrap('<div class="table-responsive"></div>');
});
} else {
   $(document).ready(function(){
 $('.table').wrap('<div class="sticky-table sticky-ltr-cells "></div>');
});
}
</script>