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
      <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: {{$user_data->placename}}</td>
        <td style="text-align: right;"><p style="float: right;width: 100%;font-size: 15px;"><b>Date of Print</b> :<?php echo date("d-m-Y h:i A") . "\n"; ?></p></td>
      </tr>
    </table>
    <table><tr><td><p></p></td></tr>
  </table>
  <table class="table table-bordered">
      <thead>
        <tr> 
              <th>State/UT Name</th>
              <!--<th>PS Location</th> -->
              <th>Total PS</th>
              <!--<th>BLO Assigned</th>  
              <th>BLO Not Activated</th> -->            
              <th>PO Assigned</th>
              <th>PO Not Activated</th>
              <!--<th>PRO Assigned</th>
              <th>PRO Not Activated</th>
              <th>SM Assigned</th>
              <th>SM Not Activated</th> -->
              <th>Total Officers</th>
              <th>Total Not Activated</th>
              <th>Non Activated(%)</th>
            </tr>
          </thead>
          <tbody>
		  
             @foreach($results as $result)   
            <tr>
             <td>
              {!! $result['label'] !!}
            </td> 
            <!--<td>
              {!! $result['total_location'] !!} 
            </td> -->
            <td>
              {!! $result['total_ps'] !!} 
            </td> 
            <!--<td>
              {!! $result['total_blo'] !!}
            </td> 
            <td>
              {!! $result['blo_not_activated'] !!} 
            </td>  -->
            <td>
              {!! $result['total_po'] !!}
            </td> 
            <td>
              {!! $result['po_not_activated'] !!} 
            </td> 
           <!-- <td>
            {!! $result['total_pro'] !!}
            </td> 
            <td>
              {!! $result['pro_not_activated'] !!} 
            </td> 
            <td>
            {!! $result['total_sm'] !!}
            </td>
            <td>
              {!! $result['sm_not_activated'] !!} 
            </td> -->
            <td>
              {!! $result['total_activated'] !!} 
            </td>
            <td>
              {!! $result['total_not_activated'] !!} 
            </td>
            <td>
              {!! $result['percentage'] !!} %
            </td>
           
            </tr>
			     @endforeach
      </tbody>

    </table>
  
  <table>
    <tr style="width: 100%;">
      <td colspan="7" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>