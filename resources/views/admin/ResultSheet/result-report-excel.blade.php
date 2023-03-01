<table>
<tbody>
<tr>
			<td colspan="10" style="text-align:center;font-weight:bold;font-size:22px;">  GENERAL ELECTION TO VIDHAN SABHA RESULT 2022 </td> 
		</tr>

       </tbody></table>
<table>
<thead>	
		<tr>
        <th><b>S.No </b></th>
		<th><b>State Name</b></th>
		<th><b>PC Name</b></th>
        <th><b>PC No.</b></th>
		<th><b>Leading  Party</b></th>
		<th><b>Leading Candidate</b></th>
		<th><b>Trailing Party</b></th>
		<th><b>Trailing Candidate</b></th>
		<th><b>Margin</b></th>
		<th><b>Counting status (Rounds Completed / Total)</b></th>
		</tr>
 </thead>
		
		<tbody>
		@if(count($result) > 0 )
		@php $i=1 @endphp
		@foreach($result as  $data)
		<?php
		$status='';
		
		$scheduled=$data->scheduled_round;
		$completedRound=completeRound($data->st_code,$data->pc_no);
				
		if($scheduled==0){
			$status='Rounds Not Scheduled';	
		}else if($data->status==1){
			$status='Result declared';	
		}else if($scheduled == $completedRound){
			$status='Completed';			
		}else{
			$status = ''.$completedRound.' / '.$scheduled.'';			
		}
	
		?>
        <tr>
        <td>{{$i}}</td> 
		<td>@if(isset($data->st_name)&& (!empty($data->st_name))){{$data->st_name}}@else{{'NA'}}@endif</td>
		<td>@if(isset($data->pc_name) && (!empty($data->pc_name))){{$data->pc_name}}@else{{'NA'}}@endif</td>
		<td>@if(isset($data->pc_no) && (!empty($data->pc_no)) ){{$data->pc_no}}@else{{'NA'}}@endif</td>
		<td>
		@if((isset($data->lead_cand_party)) && (!empty($data->lead_cand_party))){{$data->lead_cand_party}}@else{{'NA'}}@endif
		</td>
		<td>
		@if(isset($data->lead_cand_name) && (!empty($data->lead_cand_name))){{$data->lead_cand_name}}
			@if($data->status=='1' && $data->margin!='0')<span style="color:green;">({{'WINNER'}})</span>@endif
		@else{{'NA'}}@endif</td>
		
		<td>@if(isset($data->trail_cand_party) && (!empty($data->trail_cand_party))){{$data->trail_cand_party}}@else{{'NA'}}@endif</td>
		<td>@if(isset($data->trail_cand_name) && (!empty($data->trail_cand_name))){{$data->trail_cand_name}}@else{{'NA'}}@endif</td>
		<td>@if(isset($data->margin) && (!empty($data->margin))){{$data->margin}}@else{{'0'}}@endif</td>
		<td>@if(isset($status) && (!empty($status))){{$status}}@else{{'NA'}}@endif</td>
		</tr>

		@php $i++ @endphp
		@endforeach
		@else 
		<tr>
			<td colspan="10">  No record available </td> 
		</tr>
		@endif
       </tbody></table>





