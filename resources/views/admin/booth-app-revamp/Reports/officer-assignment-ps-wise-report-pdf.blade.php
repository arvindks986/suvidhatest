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
          <p style="font-size: 15px;"><b>Booth App - Officer Assignment Report</b></p>
        </td>
      </tr>
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
  <table class="table table-bordered">
      <thead>
        <tr> 
              <th>S. No.</th>
              <th>State/UT Name</th>
              <th>Const No-Name</th>
              <th>PS NO & PS Name</th>
              <th>BLO Assigned</th>              
              <th>PO Assigned</th>
              <th>PRO Assigned</th>
              <th>SM Assigned</th>
            </tr>
          </thead>
          <tbody>
			@php
        $total_ps = $total_blo = $total_pro = $total_po = $total_sm = 0;
        $count = 1;
       @endphp
			 

		  
             @if(count($results)>0)
       
            @foreach($results as $result)  
            <tr>
              <td>{{$count}}</td>
              <td>{!! $result['label'] !!}</td>
              <td>{!! $result['const_no'] !!}-{!! $result['const_name'] !!}</td>
              <td>{!! $result['ps_no'] !!}-{!! $result['ps_name'] !!}</td>
              <td>{!! $result['total_blo'] !!}</td>
              <td>{!! $result['total_po'] !!}</td>
              <td>{!! $result['total_pro'] !!}</td>
              <td>{!! $result['total_sm'] !!}</td>
            </tr>
      @php
        $total_ps += $result['total_ps'];
        $total_blo += $result['total_blo'];
        $total_pro += $result['total_pro'];
        $total_po += $result['total_po'];
        $total_sm += $result['total_sm'];
        $count++;
       @endphp
      
            @endforeach
      
      <tr>
              <td style="text-align: center;"><b>Grand Total</b></td>
              <td><b></b></td>
              <td><b></b></td>
              <td><b>{{$total_ps}}</b></td>
              <td><b>{{$total_blo}}</b></td>
              <td><b>{{$total_po}}</b></td>
              <td><b>{{$total_pro}}</b></td>
              <td><b>{{$total_sm}}</b></td>
             
            </tr>
            @else 
            <tr>
              <td colspan="8">
                No Record Found.
              </td>
            </tr>
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