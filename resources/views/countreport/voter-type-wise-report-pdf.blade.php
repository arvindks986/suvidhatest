<!DOCTYPE html>
<html>
<head>
<title>Election Comission Of India:: New Unit Order Report</title>
<style type="text/css"> 
  html,body{font-family:Segoe, 'Segoe UI', 'DejaVu Sans', 'Trebuchet MS', Verdana, 'sans-serif'; font-size: 14px; line-height: 24px;}
  .tble-ordr thead tr{background-color: #e8e8e8;}
  .tble-ordr tbody tr:nth-child(odd){background-color: #fafafa;}
  img{max-width: 100%;}
</style>
</head>
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
<table class="table" style="width:100%; background-color: #ffffff; border: 1px solid #d7d7d7; border-top: 1px solid transparent;" border="0" align="center" cellpadding="5">
  <tbody>
   	<tr style="font-size: 16px; text-transform: uppercase; background-color: #f7f7f7;">
	 <td colspan="2" align="center" style="padding: 15px;"><strong>Voter Type Wise Report</strong></td>
	</tr>
   </tbody>	 
 </table>
<table class="tble-ordr" style="width:98%; border-collapse: collapse; border: 1px solid #d7d7d7;">
					    <thead>
						 <tr>
						  	<th>SL No</th>
							<th>State Name</th>
							<th>PC Name</th>
							<th>Party Name</th>
							<th>Candidate Name</th> 
							<th>EVM Vote</th> 
							<th>Postal Vote</th> 
							<th>Total Vote</th> 
							</tr>
						  </thead>
						  <tbody>;
				    @php $i=0;
					@endphp
					@if(count($array)>0)
					@foreach($array as $data)
					@php $i++;
					@endphp
						<tr>
					    <td>{{$i}}</td>
                                            <td>{{$data['state_name'] }}</td>
                                            <td>{{$data['pc_name'] }}</td>
                                            <td>{{$data['party_name']}}</td>
                                            <td>{{$data['candidate_name']}}</td>
                                            <td>{{$data['evm_vote']}}</td>
                                            <td>{{$data['postal_vote']}}</td>
                                            <td>{{$data['total_vote']}}</td>
					</tr>
					@endforeach
					@else
					<tr><td colspan="6" style="text-align:center">-- No Record Available --</td></tr>
					@endif
				  </table> 
    <?php  date_default_timezone_set("Asia/Calcutta");  ?>
<h5>&nbsp;Downloaded by: {{ $user_data->name }}<span></span></h5>
<h5>&nbsp;Date: {{ date('d-m-Y H:i:s') }}<span></span></h5>
</body>
</html>
