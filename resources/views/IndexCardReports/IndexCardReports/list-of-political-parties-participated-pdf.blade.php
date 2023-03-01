<html>
  <head>
    <style>
    td {
    font-size: 14px !important;
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
    }
    .blcs{
    border-collapse: collapse;
    border-bottom: 1px solid #000;
    border-top: 1px solid #000;
    }
    .border{
    border: 1px solid #000;
    }
    .borders{
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    }
    th {
    font-size: 16px;
    font-weight: bold !important;
    text-align: left;
    padding: 7px;
    }

    table{
    width: 100%;
    border-collapse: collapse;
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
          <p style="font-size: 15px;"><b>3 - LIST OF PARTICIPATING POLITICAL PARTIES</b></p>
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
  <table class="" style="width: 100%;">
			<thead>
				<tr>
				  <th class="blcs">PARTY TYPE</th>
				  <th class="blcs">ABBREVIATION</th>
				  <th class="blcs">PARTY</th>
				</tr>
			  </thead>
			  <tbody>
				@php $i = 1; @endphp
			  @foreach($dataArray as $key=>$data)
				@if($key == 'N-N')
					<tr><td><b>NATIONAL PARTIES</b></td></tr>
				@elseif($key == 'S-U')
					<tr><td colspan="3"><b>STATE PARTIES - OTHER STATES</b></td></tr>
				@elseif($key == 'S-S')
					<tr><td><b>STATE PARTIES</b></td></tr>
				@elseif($key == 'U-U')
					<tr><td colspan="3"><b>REGISTERED(Unrecognised) PARTIES</b> </td></tr>
				@elseif($key == 'Z-Z')
					<tr><td><b>INDEPENDENTS</b>  </td></tr>
				@endif
				
				  @foreach($data as $raw)
						<tr>
						  <td>{{$i}}.</td>
						  <td>{{$raw['PARTYABBRE']}}</td>
						  <td>{{$raw['PARTYNAME']}}</td>
						</tr>	
					@php $i++; @endphp
				  @endforeach				  
			  @endforeach				
			  </tbody>
          </table>
  <table>
    <tr style="width: 100%;">
      <td colspan="3" style="text-align: center;border-top: 1px solid #000;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>