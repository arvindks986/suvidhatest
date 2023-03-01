<!DOCTYPE html>
<html>
<head>
<style type="text/css">
    table{}
    tr{}
    td{}
span.text {
    float: left;
}

span.line {
    border-bottom: 2px solid #0b0b0b;
    display: inline-block;
    width: 100%;    height:40px;
}
table td{font-size:14px;}
</style>
</head>
<body style="  width: 800px;  text-align: center;  margin: 0 auto;">

	<table width="100%" align="center">
    <tbody>
        <tr><td colspan="2" align="center"><h1 style=" margin: 10px 0 0 0;">FORM-21E</h1></td></tr>
        <tr><td colspan="2" align="center" style="font-size: 20px; font-weight: 600;">Return of Election</td></tr>
        <tr><td colspan="2" align="center" style="font-style: italic;">[See rule 64 of the Conduct of Elections Rules, 1961]</td></tr>
		</tbody>
	</table>
<br />
<table cellspacing="8">
	  <tbody>
        <tr>
		<td style="width: 120px; font-size: 16px;">Election to the</td>
		<td style="font-size: 16px; border-bottom: 1px solid #000;  width: 250px;   text-align: left;    padding: 0 10px;"><?php echo $state;?></td>
		<td style="font-size: 16px;">From the</td>
		<td style="font-size: 16px; border-bottom: 1px solid #000;   width: 250px;   text-align: left;   padding: 0 10px;
"><?php echo $pcname;?></td></tr>

		<tr><td colspan="2" style="font-size: 16px; border-bottom: 1px solid #000;"></td><td style="font-size: 16px;">Constituency</td> <td></td></tr>
		
    </tbody>
</table>
<br />
<table align="center">
	 <tr><td align="center"><h2>RETURN OF ELECTION</h2></td></tr>
</table>

<table border="1" width="100%" cellspacing="0" cellpadding="4">
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
 <br />
 <br />
<table width="80%" align="right">
	<tbody><tr>
		<td align="left">
			<table style="  width: 100%;  margin-bottom: 10px;">
				<tbody>
				<tr>
					<td style="font-size: 14px;" width="32%">Total numbers of electors</td>
					<td style="font-size: 14px; border-bottom: 1px solid #000; padding: 0 10px;"><?php echo number_format($tot_electrol);?></td>
				</tr>
			</tbody></table>
			
			<table style="
    width: 100%;
    margin-bottom: 10px;
">
				<tbody>
				<tr>
					<td style="font-size: 14px;" width="45%">Total numbers of valid votes polled</td>
					<td style="font-size: 14px; border-bottom: 1px solid #000; padding: 0 10px;"><?php echo number_format($total_validpol);?></td>
				</tr>
			</tbody>
			</table>
			
			<table style="width: 100%; margin-bottom: 10px;">
				<tbody>
					<tr>
						<td style="font-size: 14px;" width="40%">Total numbers of rejected votes</td>
						<td style="font-size: 14px; border-bottom: 1px solid #000; padding: 0 10px;"><?php echo $array[0]['rejectedvote'];?></td>
					</tr>
				</tbody>
			</table>
			
			<table style="margin-bottom: 10px;  width: 100%;">
				<tbody>
					<tr>
						<td style="font-size: 14px;" width="41%">Total numbers of tendered votes</td>
						<td style="font-size: 14px; border-bottom: 1px solid #000; padding: 0 10px;"><?php echo $array[0]['rejectedvote'];?></td>
					</tr>
				</tbody>
			</table>
			
			<table style="margin-bottom: 10px;  width: 100%;">
				<tbody>
					<tr>
						<td style="font-size: 14px;" width="41%">I declare that :-</td>
						
					</tr>
				</tbody>
			</table>
		</td>	
		</tr>
</tbody>
</table>


<table width="100%" align="left">
	<tr>
		<td style="font-size: 14px; border-bottom: 1px solid #000; padding: 0 10px 0 0;">{{$win_can->cand_name}}</td>
		<td style="font-size: 14px;" width="80px" align="right"> (Name)</td>
	</tr>
</table>
<table width="100%" align="left" >
	<tr>
		<td style="font-size: 14px;" width="50px"align="left">of</td>
		<td style="font-size: 14px; border-bottom: 1px solid #000; padding: 0 10px;">{{$win_can->candidate_residence_address}}</td>
		<td style="font-size: 14px;" width="80px" align="right"> (address)</td>
	</tr>
</table>
<table width="100%" align="left" >
	<tr align="left">
		<td>has been duly elected to fill the seat.</td>
		
	</tr>
</table>
<br />
 <?php  date_default_timezone_set("Asia/Calcutta");  ?>
<table style=" width: 100%;">
<tr><td style="text-align: left;">Place:- <span>{{$user_data->placename}}</span></td><td></td></tr>   
</table>
<table width="100%">
	 <tr><td>Date:- </td><td>{{ date('d-m-Y H:i:s') }}</td> <td>Returning Officer</td> <td></td></tr>
</table>
</body>
</html>