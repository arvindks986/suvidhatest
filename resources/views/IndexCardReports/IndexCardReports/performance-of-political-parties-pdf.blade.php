<html>
  <head>
    <style>
    td {
    font-size: 13px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 6px;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
    }
    .left-al tr td{
    text-align: left;
    }
    .top
    {
    	border-top: 1px solid #000;
    }
    .table-bordered{
    border:1px solid #000;
    }
    .table-bordered td,
    .table-bordered th {
    border: 1px solid #000 !important
    }
    .table {
    width: 100%;
    border-collapse: collapse;
    font-size: .9em;
    color: #000;
    margin-bottom: 1rem;
    color: #212529;
    }
    .blc{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-spacing: 0px 8px;
    font-weight: bold;
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .padd{
    	font-weight: bold;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 14px;
    font-weight: bold !important;
    text-align: left;
    padding: 7px;
    }
   
    table{
    width: 100%;
    }
    </style>
  </head>
  <?php  $st=getstatebystatecode($st_code);   ?>
  
  <div class="bordertestreport">
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p> <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>  </p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><b>SECRETARIAT OF THE <br>ELECTION COMMISSION OF INDIA
            </b>
          <br><b>Nirvachan Sadan, Ashoka Road, New Delhi-110001</b></p>
        </td>
      </tr>
    </table>
    <table class="border">
      <tr>
        <td style="text-align: left;">
          <p style="font-size: 15px;"><b>5 - Performance of Political Parties</b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong> {{$st->ST_NAME}} </p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
  <table class="table" style="width: 100%;">
		  <thead>
			<tr>
			  <td class="bolds top blc" rowspan="2">PARTY</td>
			  <td class="bolds top blc" colspan="3" style="text-decoration: underline;">SEATS</td>
			  <td class="bolds top blc" colspan="2" style="text-decoration: underline;">SHARE IN VALID VOTES <br>POLLED IN STATE</td>
			  <td class="bolds blc top" rowspan="2" style="">VOTE % IN <br>SEATS <br>CONTESTED</td>
			</tr>
			<tr>
			  <td class="blc">CONTESTED</td>
			  <td class="blc">WON</td>
			  <td class="blc">FD</td>
			  <td class="blc">VOTES</td>
			  <td class="blc">%</td>
			</tr>
		  </thead>
		  <tbody>
			@php $i = 1; 
			$all_total_contested = $all_total_won = $all_total_fd = $all_total_fd = $all_total_party = 0;
			@endphp
			  @foreach($dataArray as $key=>$data)
				@if($key == 'N-N')
					<tr><th colspan="7"><b>NATIONAL PARTIES</b></th></tr>
				@elseif($key == 'S-U')
					<tr><th colspan="7"><b>STATE PARTIES - OTHER STATES</b></th></tr>
				@elseif($key == 'S-S')
					<tr><th colspan="7"><b>STATE PARTIES</b></th></tr>
				@elseif($key == 'U-U')
					<tr><th colspan="7"><b>REGISTERED(Unrecognised) PARTIES</b> </th></tr>
				@elseif($key == 'Z-Z')
					<tr><th colspan="7"><b>INDEPENDENTS</b></th></tr>
				@endif
				
				@php $total_contested = $total_won = $total_fd = $total_fd = $total_party = 0; 
				@endphp
				
				
				  @foreach($data as $raw)
				  
					<?php 
					if($raw['total_valid_votes'] > 0){
						$per = round((($raw['vote_secured_by_party']/$raw['total_valid_votes'])*100),2);
					}else{
						$per = 0;
					}
					
					if($raw['contests_total_votes'] > 0){
						$per_c = round((($raw['vote_secured_by_party']/$raw['contests_total_votes'])*100),2);
					}else{
						$per_c = 0;
					}
					
					$total_contested += $raw['contested'];
					$total_won += $raw['won'];
					$total_fd += $raw['fd'];
					$total_party += $raw['vote_secured_by_party'];
					
					$all_total_contested += $raw['contested'];
					$all_total_won += $raw['won'];
					$all_total_fd += $raw['fd'];
					$all_total_party += $raw['vote_secured_by_party'];
					
					$total_valid_votes = $raw['total_valid_votes'];
					
					?>				  
						<tr>
						  <td class="padd">{{$i}}.  &nbsp;{{$raw['PARTYABBRE']}}</td>
						  <td>{{$raw['contested']}}</td>
						  <td>{{$raw['won']}}</td>
						  <td>{{$raw['fd']}}</td>
						  <td>{{$raw['vote_secured_by_party']}}</td>
						  <td>{{$per}}%</td>
						  <td>{{$per_c}}</td>
						</tr>
			
			
			@php $i++; @endphp
				  @endforeach
				  <?php 
					if($total_valid_votes > 0){
						$per = round((($total_party/$total_valid_votes)*100),2);
					}else{
						$per = 0;
					}
					?>

			<tr>
			  <td class="blcs"></td>
			  <td class="blcs"><b>{{$total_contested}}</b></td>
			  <td class="blcs"><b>{{$total_won}}</b></td>
			  <td class="blcs"><b>{{$total_fd}}</b></td>
			  <td class="blcs"><b>{{$total_party}}</b></td>
			  <td class="blcs"><b>{{$per}}</b></td>
			  <td class="blcs">  </td>
			</tr>

				  
			  @endforeach
			  <?php 
					if($total_valid_votes > 0){
						$per = round((($all_total_party/$total_valid_votes)*100),2);
					}else{
						$per = 0;
					}
					?>
			  
			  
				<tr>
				  <td class="blc"><b>Grand Total:</b></td>
				  <td class="blcs"><b>{{$all_total_contested}}</b></td>
			  <td class="blcs"><b>{{$all_total_won}}</b></td>
			  <td class="blcs"><b>{{$all_total_fd}}</b></td>
			  <td class="blcs"><b>{{$all_total_party}}</b></td>
			  <td class="blcs"><b>{{$per}}</b></td>
				  <td class="blc">  </td>
				</tr>
			  </tbody>
			</table>
  <table>
    <tr style="width: 100%;">
      <td colspan="7" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>