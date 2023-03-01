<html>
  <head>
    <style>

    td {
    font-size: 12px !important;
    font-weight: 500 !important;
    text-align: left;
    padding: 2px;
	width:10.4%;
    font-family: "Times New Roman", Times, serif;
    }
    h3{
    font-size: 18px !important;
    font-weight: 600;
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
    .bolds{
      font-weight: bold;
    }

    .bold{
      font-weight: bold;
	  padding:12px 0px 0px 14px;
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
          <p style="font-size: 15px;"><b>7 - INDIVIDUAL PERFORMANCE OF WOMEN CANDIDATES</b></p>
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

    <table><tr><td><p></p></td></tr></table>
    <table><tr><td><p></p></td></tr></table>
  

  <table class="table" style="width: 100%;white-space: nowrap;">
			  <thead>
				<tr>
				  <td colspan="9" class="bolds" style="">Name of Constituency </td>
				</tr>
				<tr>
				  <td class="bolds;" style="text-align:center;"><b>Sl No.</b></td>
				  <td class="bolds" style="width:14% !important;">Name of candidate </td>
				  <td class="bolds">Party</td>
				  <td class="bolds">Party <br>Type</td>
				  <td class="bolds">Votes <br>Secured</td>
				  <td colspan="2" class="bolds" style="text-decoration: underline;text-align: center;">% of votes secured</td>
				  <td class="bolds">Status</td>
				  <td class="bolds">Total <br>Valid <br>votes</td>
				</tr>
				<tr>
				  <td colspan="5" class="bolds blc"></td>
				  <td class="bolds blc" style="font-size:11px;">over total <br>electors</td>
				  <td class="bolds blc" style="font-size:11px;">over total <br>votes polled</td>
				  <td colspan="2" class="bolds blc"></td>
				</tr>

        <tr><td><p></p></td></tr>
        <tr><td><p></p></td></tr>
			  </thead>
			  <tbody>
				<tr>
				  <td colspan="9" class="bolds">State/UT : {{$st->ST_NAME}}</td>
				</tr>
				
				@foreach($dataArray as $key => $data)
				
				<tr>
				  <td colspan="9" class="bold">{{$key}}</td>
				</tr>
				@foreach($data as $key1 => $raw)
				
				<tr>
				  <td colspan="2" style="text-align: left;padding:12px 0px 0px 22px;">{{$raw['srno']}} {{$raw['candidate_name']}}</td>
				  <td> {{$raw['party_abbre']}} </td>
				  <td> {{$raw['PARTYTYPE']}} </td>
				  <td> {{$raw['candidate_votes']}} </td>
				  <td>@if($raw['total_electors'] > 0)
						{{number_format((float)($raw['candidate_votes']*100)/$raw['total_electors'], 2, '.', '')}}
						@else
							0
						@endif
					</td>
					<td>@if($raw['total_votes'])
					{{number_format((float)($raw['candidate_votes']*100)/$raw['total_votes'], 2, '.', '')}}
						@else
							0
						@endif</td>
				  <td> {{$raw['status']}} </td>
				  <td> {{$raw['total_votes']}} </td>
				</tr>
				@endforeach
				@endforeach

				<tr><td colspan="9"><p style="border-top: 1px solid #000;"></p></td></tr>
			  </tbody>
			</table>
  <table>
    <tr style="width: 100%;">
      <td colspan="7" style="text-align: center;border-top: 1px solid #000;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>