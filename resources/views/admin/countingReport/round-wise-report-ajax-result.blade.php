 <style type="text/css">
	.dataTables_wrapper .row:nth-child(2) .col-sm-12 {
    overflow: unset;
}
 </style>
<!-- New Here -->
@php
	$maxRound=0
@endphp
@php
	$maxRound = app(App\Http\Controllers\Admin\RoundWiseReport\RoundWiseReportController::class)->getMaxRound($st_code, $pc, $ac)
@endphp
<!-- End New Here -->
<table id="list-table"  class="table table-striped table-bordered datatable">
<thead>
		<tr class="sticky-header">
        <th> S.No </th>
		@if($user!='ARO')
		<th>AC Name (No.)</th>
        <th>AC No.</th>
		@endif
		<th style="text-align: left;" class="sticky-cell cand_name">Candidate(Party)</th>

			
			<?php if($maxRound == 0 ){ ?>
			<th>Round Wise EVM Votes
			<?php } ?>
			
			
			<?php  for($m=1; $m<=$maxRound; $m++){   ?>
			 <th class="text-center"><span> <?php  echo 'R'.$m; ?> </span></th>
			<?php   }   ?>
			<?php if($maxRound == 0 ){ ?>
			</th>	
			<?php } ?>
			
			
			
	
		<!--<th style="text-align: center;">Postal Votes</th>-->
        <th style="text-align: center;" class="sticky-cell-opposite">Total EVM Votes</th>
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
		$getAc = app(App\Http\Controllers\Admin\RoundWiseReport\RoundWiseReportController::class)->getAc($st_code, $mData['ac_no'])
		@endphp
        <tr>
        <td>  <span>{{$i}}</span>  </td> 
		@if($user!='ARO')
		<td>{{$getAc}}({{$mData['ac_no']}})</td>
		<td>{{$mData['ac_no']}}</td>
		@endif
		 <td style="text-align: left;" class="sticky-cell cand_name">  <span>{{$mData['candidate_name']}} ({{$mData['party_abbre']}}) </span>  </a>  </td> 
		 
							
								<?php 	
								$total_votes=0; $p=0; $remain=0; $isRound=0;
								for($k=1; $k<=$maxRound; $k++){  
											$dataok = 'round'.$k;
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