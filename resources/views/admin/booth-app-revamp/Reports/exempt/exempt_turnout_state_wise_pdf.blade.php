<html>
  <head>
    <style>
    td {
    font-size: 12px !important;
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
    font-size: 12px;
    font-weight: bold !important;
    padding: 5px;
    text-align: left;
    }
 
    table{
    width: 100%;
    border-collapse: collapse;
    }
    </style>
  </head>
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
          <p style="font-size: 15px;"><b>{!! $heading_title !!}</b></p>
        </td>
      </tr>
      @if(isset($filter_buttons) && count($filter_buttons)>0)
        <section class="statistics pt-4 pb-2">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                @foreach($filter_buttons as $button)
                    <?php $but = explode(':',$button); ?>
                    <span class="pull-right" style="margin-right: 10px;">
                    <span><b>{!! $but[0] !!}:</b></span>
                    <span class="badge badge-info">{!! $but[1] !!}</span>

                    </span>
                    
                @endforeach
              </div>
            </div>
          </div>
        </section>
        @endif
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: {{$user_data->placename}}</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
  <table align="center" class="table table-bordered  poll-table">
					<thead class="sticky">
				<tr class="turnoutbg">
					<td colspan="5" style="">{!! $heading_title !!}</td>
					<td colspan="5" style="" align="right"><span id="poll_turnout_percentage">{{$poll_turnout_percentage}}</span>%</td>
				</tr>

					<tr>
            <th style="background:#6ccac6;">State/UT Name</th>
            <th style="background:#6ccac6;">Total PS</th>
            <th style="background:#6ccac6;">Total Exempted PS</th>
            <th style="background:#6ccac6;">Round1 (Poll Start to 9:00 AM)</th>
            <th  style="background:#6ccac6;">Round2  (Poll Start to 11:00 AM)</th>
            <th style="background:#6ccac6;">Round3  (Poll Start to 1:00 PM)</th>
            <th  style="background:#6ccac6;">Round4  (Poll Start to 3:00 PM)</th>
            <th  style="background:#6ccac6;">Round5  (Poll Start to 5:00 PM)</th>
            <th  style="background:#6ccac6;">Latest Updated Poll</th>
          </tr>
					</thead>
				

					<tbody id="voter_turnouts">
					@if(count($results)>0)

                @foreach($results as $result) 
                <tr>
                  <td width="40%"><span>{{$result['label']}}</span></td>
                  <td><span>{{$result['total_ps']}}</span></td>
                  <td><span>{{$result['total_exempted_ps']}}</span></td>
            <td><span>{{$result['round_1_total']}}</span></td>
            <td><span>{{$result['round_2_total']}}</span></td>
            <td><span>{{$result['round_3_total']}}</span></td>
            <td><span>{{$result['round_4_total']}}</span></td>
            <td><span>{{$result['round_5_total']}}</span></td>
            <td align="center"><span>{{$result['rounds_total']}}</span></td>
                </tr>
                @endforeach
                @else
                <tr align="center"><td colspan="11">No Record</td></tr>
                @endif
					</tbody>
				</table>
  
  <table>
    <tr style="width: 100%;">
      <td colspan="7" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>