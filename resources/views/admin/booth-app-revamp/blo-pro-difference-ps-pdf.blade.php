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
        <p style="font-size: 15px;"><b>Blo/Pro Turnout Report</b></p>
      </td>
    </tr>
    <tr>
      <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
      <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
    </tr>
  </table>
  <table><tr><td><p></p></td></tr>
  </table>
  <table class="table table-bordered " id="my-list-table">
   <thead>
    <tr>
<th>PS No. & Name</th>
      <th>State Name</th>
      <th>AC No. & Name</th>
      
      <!--<th>BLO Name & Mobile</th>
      <th>PO Name & Mobile</th>
      <th>PRO Name & Mobile</th>-->
      <th>Total BLO Scan</th>
      <th>Total PO Scan</th>
      <th>Total Difference</th>
    </tr>
  </thead>
  <tbody>  
   @php
   $bloscan = '0';
   $poscan = '0';
   $difference = '0';
   @endphp

   @if(count($voter_turnouts)>0)
   @foreach($voter_turnouts as $result)
   <tr>
   <td>{{$result['ps_no']}}-{{$result['ps_name']}}</td>
     <td>{{$result['st_name']}}</td>
     <td>{{$result['ac_no']}}-{{$result['ac_name']}}</td>
     
     <!--<td>{{$result['blo_name']}} @if($result['blo_mobile']) - {{$result['blo_mobile']}} @endif</td>
     <td>{{$result['po_name']}} @if($result['po_mobile']) - {{$result['po_mobile']}} @endif</td>
     <td>{{$result['pro_name']}} @if($result['pro_mobile']) - {{$result['pro_mobile']}} @endif</td>-->
     <td>{{$result['blo_turnout']}}</td>
     <td>{{$result['pro_turnout']}}</td>
     <td>{{$result['difference']}}</td>
   </tr>

   @php
   $bloscan +=   $result['blo_turnout'];
   $poscan +=   $result['pro_turnout'];
   $difference  +=   $result['difference'];

   @endphp
   @endforeach
   @else
   <tr>
    <td colspan="6">
      No Record Found.
    </td>
  </tr>
  @endif
</tbody>
<tr>
  <td style="text-align: center;"><b>Grand Total</b></td>
  <td><b></b></td>
          <td><b></b></td>
          <!--<td><b></b></td>
          <td><b></b></td>
          <td><b></b></td>-->
  <td><b>{{$bloscan}}</b></td>
  <td><b>{{$poscan}}</b></td>
  <td><b>{{$difference}}</b></td>

</tr>
</table>

<table>
  <tr style="width: 100%;">
    <td colspan="7" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
  </tr>
</table>
</div>
</html>