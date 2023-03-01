<html>
  <head>
       <style>
    td {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #4a4646 !important;
    text-align: center;
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

    .bordertestreport{
      border:1px solid #000;
    }
    .border{
    border-bottom: 1px solid #000;
    }
    th {
    background: #eff2f4;
    color: #000 !important;
    text-align: center;
    font-size: 11px;
    font-weight: bold !important;
    }
    
    table{
    width: 100%;
    }
    
    </style>
  </head>
  <div class="bordertestreport">
      <table class="border">
          <tr>
                <td style="text-align: left;">
                    <p> <img src="img/Cyber-Security-Logo.png" class="img-responsive" style="width:100px;" alt="">  </p>
                </td>
              <td style="text-align: right;">
                <p style="float: right;width: 100%;">ELECTION COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001
                 <br> General Elections, {{$year}} </p>
          </td>
      </tr>
  </table>

  <table>
      <tr>
          <td style="text-align: left;">
             <h3>12.State Wise Voter Turnout</h3>

          </td>
          <td style="text-align: right;">
              <p style="float: right;width: 100%;"><strong>State :</strong> All India </p>
          </td>
      </tr>
  </table>		
                <table class="table table-striped table-bordered table-hover">
  <thead class="thead-light">
    <tr>
      <th scope="col">SL.No</th>
      <th scope="col">State/UT</th>
      <th scope="col" colspan="2">Electors</th>
      <th scope="col" colspan="2">Voters</th>
      <th scope="col">Voters Turnout(%)</th>
    </tr>
<tr>
      <th scope="col"></th>
      <th scope="col"></th>
      <th scope="col">General</th>
      <th scope="col">Service</th>
      <th scope="col">EVM</th>
      <th scope="col">Postal</th>
      <th scope="col"></th>
    </tr>

  </thead>
  <tbody>
    <tr>
    </tr>
 @php
	$i=1
	@endphp  
@foreach ($statewisevoterturnouts as $statewisevoterturnout) 
<?php $voteper = 0;  ?>
     <tr>
      <td>{{$i}}</td>
      <td>{{ $statewisevoterturnout->ST_NAME }}</td>
      <td>{{ $statewisevoterturnout->e_gen_t }}</td>
      <td>{{ $statewisevoterturnout->e_ser_t }}</td>
      <td>{{ $statewisevoterturnout->vt_all_t }}</td>
      <td>{{ $statewisevoterturnout->postal_valid_votes }}</td>
      <td>@if(($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t) > 0)	  {{round(((($statewisevoterturnout->vt_all_t+$statewisevoterturnout->postal_valid_votes)/($statewisevoterturnout->e_gen_t+$statewisevoterturnout->e_ser_t))*100),2) }}
	  @else
		  0
	  @endif
	  </td>
    </tr>
	@php
	$i++
	@endphp
@endforeach

              </tbody>
            </table>