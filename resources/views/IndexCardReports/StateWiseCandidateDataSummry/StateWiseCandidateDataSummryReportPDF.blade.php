<!DOCTYPE html>
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

<body>

    <div class="bordertestreport">
      <table class="border">
          <tr>
                <td style="text-align: left;"> 
                    <p> <img src="img/Cyber-Security-Logo.png" class="img-responsive" style="width:100px;" alt="">  </p>
                </td>
              <td style="text-align: right;">
                <p style="float: right;width: 100%;">ELECTION COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001
                 <br> General Elections, 2019 </p>
          </td>
      </tr>
  </table>

  <table>
      <tr>
          <td>
             <h3>6.State Wise Candidate Data Summary</h3>

          </td>
          <!-- <td style="text-align: right;">
              <p style="float: right;width: 100%;"><strong>State :</strong> </p>
          </td> -->
      </tr>
  </table>


    
                @foreach($statewisedata as  $key => $value)
                 
                <?php //echo '<pre>'; print_r($statewisedata); die; ?>
                <div class="">
                    <div class="table-responsive" style="padding: 0px 0px 4px 0px;">
                        <table class="table table-bordered table-striped" style="width: 100%;table-layout: fixed;">
                            <thead>

                                <tr>

                                    <th colspan="6" style="font-size: 13px;padding: 5px;">State : <span style="color: #000; font-style: normal; text-decoration: underline;">{{$key}}</span> </th>
                                </tr>
                                <tr class="table-primary">
                                    <th width="15%" scope="">Constituency Type</th>
                                    <th scope="">No Of Seats</th>
                                    <th colspan="4">Nominations Filled</th>
                                    <th colspan="4">Nominations Rejected</th>
                                    <th colspan="4">Nominations Withdrawn</th>
                                    <th colspan="4">Contesting Candidates</th>
                                    <th colspan="4">Deposits Forfeited</th>

                                </tr>

                            </thead>

                            <tbody>
                                <tr>

                                    <td></td>
                                    <td></td>
                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                    <td>Men</td>
                                    <td>Women</td>
                                    <td>Other</td>
                                    <td>Total</td>

                                </tr>
                                <?php $cnomfdtotal = $cnomfdother = $cnomfdfemale = $cnomfdmale = $cnomcototal = $cnomcother = $cnomcofemale = $cnomcomale = $cnomwtotal = $cnomwother = $cnomwfemale = $cnomwmale = $cnomrall = $cnomrother = 
                                $cnomrfemale = $cnomrmale = $CandNomall = $CandNomOther = $CandNomFemale = $candNomMale = $totSeat = 0; ?>
                                @foreach($value as $k)
                                <tr>
                                    <?php if($k['GENSEATS'] != 0)
                                            $seats = $k['GENSEATS'];
                                        else if($k['SCSEATS'] != 0)
                                            $seats = $k['SCSEATS'];
                                        else $seats = $k['STSEATS'];
                                    ?>
                                    <td>{{$k['pc_type']}}</td>
                                    <td>{{$seats}}</td>
                                    <td>{{$k['CandNommale']}}</td>
                                    <td>{{$k['CandNomFemale']}}</td>
                                    <td>{{$k['CandNomOther']}}</td>
                                    <td>{{$k['CandNomall']}}</td>
                                    <td>{{$k['cnomrmale']}}</td>
                                    <td>{{$k['cnomrfemale']}}</td>
                                    <td>{{$k['cnomrother']}}</td>
                                    <td>{{$k['cnomrall']}}</td>
                                    <td>{{$k['cnomwmale']}}</td>
                                    <td>{{$k['cnomwfemale']}}</td>
                                    <td>{{$k['cnomwother']}}</td>
                                    <td>{{$k['cnomwtotal']}}</td>
                                    <td>{{$k['cnomcomale']}}</td>
                                    <td>{{$k['cnomcofemale']}}</td>
                                    <td>{{$k['cnomcother']}}</td>
                                    <td>{{$k['cnomcototal']}}</td>
                                    <td>{{$k['cnomfdmale']}}</td>
                                    <td>{{$k['cnomfdfemale']}}</td>
                                    <td>{{$k['cnomfdother']}}</td>
                                    <td>{{$k['cnomfdtotal']}}</td>
                                </tr>

                                <?php $totSeat += $seats;
                                    $candNomMale += $k['CandNommale'];
                                    $CandNomFemale += $k['CandNomFemale'];
                                    $CandNomOther  += $k['CandNomOther'];
                                    $CandNomall   += $k['CandNomall'];
                                    $cnomrmale += $k['cnomrmale'];
                                    $cnomrfemale += $k['cnomrfemale'];
                                    $cnomrother  += $k['cnomrother'];
                                    $cnomrall   += $k['cnomrall'];
                                    $cnomwmale  += $k['cnomwmale'];
                                    $cnomwfemale += $k['cnomwfemale'];
                                    $cnomwother += $k['cnomwother'];
                                    $cnomwtotal += $k['cnomwtotal'];
                                    $cnomcomale += $k['cnomcomale'];
                                    $cnomcofemale += $k['cnomcofemale'];
                                    $cnomcother += $k['cnomcother'];
                                    $cnomcototal += $k['cnomcototal'];
                                    $cnomfdmale += $k['cnomfdmale'];
                                    $cnomfdfemale += $k['cnomfdfemale'];
                                    $cnomfdother += $k['cnomfdother'];
                                    $cnomfdtotal += $k['cnomfdtotal'];
                                    ?>
                                @endforeach

                                <tr style="font-weight:bold;">

                                    <td>Total</td>
                                    <td>{{$totSeat}}</td>
                                    <td>{{$candNomMale}}</td>
                                    <td>{{$CandNomFemale}}</td>
                                    <td>{{$CandNomOther}}</td>
                                    <td>{{$CandNomall}}</td>
                                    <td>{{$cnomrmale}}</td>
                                    <td>{{$cnomrfemale}}</td>
                                    <td>{{$cnomrother}}</td>
                                    <td>{{$cnomrall}}</td>
                                    <td>{{$cnomwmale}}</td>
                                    <td>{{$cnomwfemale}}</td>
                                    <td>{{$cnomwother}}</td>
                                    <td>{{$cnomwtotal}}</td>
                                    <td>{{$cnomcomale}}</td>
                                    <td>{{$cnomcofemale}}</td>
                                    <td>{{$cnomcother}}</td>
                                    <td>{{$cnomcototal}}</td>
                                    <td>{{$cnomfdmale}}</td>
                                    <td>{{$cnomfdfemale}}</td>
                                    <td>{{$cnomfdother}}</td>
                                    <td>{{$cnomfdtotal}}</td>
                                </tr>
                               
                                    </tbody>
                        </table>
                      <!--  <?php //break; ?> -->
                         @endforeach
                       

                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>