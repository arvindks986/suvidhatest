<html>
  <head>
    <style>
    td {
    font-size: 14px !important;
    font-weight: 500 !important;
    text-align: center;
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
    .table td{
    text-align: left;
    width: 50%;
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
          <p style="font-size: 15px;"><b>1 - OTHER ABBREVIATIONS AND DESCRIPTIONS</b></p>
        </td>
        <td style="text-align: right;">
          <p style="float: right;width: 100%;font-size: 15px;"><strong>State :</strong>{{$st->ST_NAME}} </p>
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
        <th class="blcs">ABBREVIATIONS </th>
        <th class="blcs">DESCRIPTIONS</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>FD</td>
        <td>Forfeited Deposits</td>
      </tr>
      <tr>
        <td>GEN</td>
        <td>General Constituency
        </td>
      </tr>
      <tr>
        <td>SC</td>
        <td>Reserved for Scheduled Castes
        </td>
      </tr>
      <tr>
        <td>ST</td>
        <td>Reserved for Scheduled Tribes
        </td>
      </tr>
      <tr>
        <td>M</td>
        <td>Male</td>
      </tr>
      <tr>
        <td>F</td>
        <td>Female</td>
      </tr>
      <tr>
        <td>O</td>
        <td>Third Gender</td>
      </tr>
      <tr>
        <td>T</td>
        <td>Total</td>
      </tr>
      <tr>
        <td>N</td>
        <td>National Party</td>
      </tr>
      <tr>
        <td>S</td>
        <td>State Party</td>
      </tr>
      <tr>
        <td>U</td>
        <td>Registered (Unrecognised) Party</td>
      </tr>
      <tr>
        <td class="blc">Z</td>
        <td class="blc">Independent</td>
      </tr>
      <tr><td colspan="2"><p style="border-top: 1px solid #000;"></p></td></tr>
    </tbody>
  </table>
  <table>
    <tr style="width: 100%;">
      <td colspan="7" style="text-align: center;"><p><b style="font-size: 15px;">Nirvachan Sadan, Ashoka Road, New Delhi- 110001</b></p></td>
    </tr>
  </table>
</div>
</html>