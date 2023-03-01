<!DOCTYPE html>
<html>
<head>
      <style>
      td {
      font-size: 12px !important;
      font-weight: 500 !important;
      color: #4a4646 !important;
      font-family: "Times New Roman", Times, serif;
      }

      h3{
        font-size: 18px !important;
        font-weight: 600;
      }


    .border{
      border-bottom: 1px solid #666;
    }
      th {
      background: #959798;
      color: #fff !important;
      text-align: center;
      font-size: 13px;
      text-transform: capitalize !important;
      font-weight: 500 !important;
      }
      tr:nth-child(even) {
      background: #8e99ab29;
      }
      table{
      width: 100%;
      }

      html { margin: 50px; }
      </style>
  </head>
   <body>

            <div class="bordertestreport">
            

               <table class="border">
                   <tr>
                         <td>
                             <p> <img src="img/Cyber-Security-Logo.png" class="img-responsive" style="width:100px;" alt="">  </p>
                         </td>
                       <td style="text-align: right;">
                         <p style="float: right;width: 100%;">ELECTIONs COMMISSION OF INDIA, <br>Nirvachan Sadan, Ashoka Road, New Delhi-110001
                          <br> General Elections, 2019 </p>
                   </td>
               </tr>
           </table>

           <table>
               <tr>
                   <td>
                      <h4>Election Commission of India, General Elections, 2019 (17th LOK SABHA )</h4>


                         <h2>.24StateWise Overseas Electors Voters</h2>

                   </td>
                  <!--  <td style="text-align: right;">
                       <p style="float: right;width: 100%;"><strong>State :</strong> { </p>
                   </td> -->
               </tr>
           </table>


                @foreach($statewisedata as  $key => $value)
               <div class="">
                  <div class="table-responsive">
                     <table class="table table-bordered table-striped" style="width: 100%;">
                        <thead>
                           <tr>
                              <th style="font-size: 17px;">State : <span style="color: #fff; font-style: normal;font-weight: bold; text-decoration: underline;"> {{$key}}</span> </th>
                           </tr>
                           <tr class="table-primary">
                              <th scope="col">PC Type</th>
                              <th colspan="4">Electors</th>
                              <th colspan="4">Voters</th>
                           </tr>
                        </thead>
                        <tbody>
                          <tr>
                              <td></td>
                              <td>Male</td>
                              <td>Female</td>
                              <td>Other</td>
                              <td>Total Electors</td>
                              <td>Male</td>
                              <td>Female</td>
                              <td>Other</td>
                              <td>Total Voters</td>
                           </tr>

                               @foreach($value as $k)
                           <tr>
                              <?php if($k['GENSEATS'] != 0)
                                            $seats = $k['GENSEATS'];
                                        else if($k['SCSEATS'] != 0)
                                            $seats = $k['SCSEATS'];
                                        else $seats = $k['STSEATS'];
                              ?>
                              <td>{{$k['pc_type']}}</td>
                              <td>{{$k['maletotalnrielector']}}</td>
                              <td>{{$k['femaletotalnrielector']}}</td>
                              <td>{{$k['othertotalnrielector']}}</td>
                              <td>{{$k['totalnrielector']}}</td>
                              <td>{{$k['votermalenritotal']}}</td>
                              <td>{{$k['voterfemalenritotal']}}</td>
                              <td>{{$k['voterothernritotal']}}</td>
                              <td>{{$k['voterallnritotal']}}</td>
                           </tr>

                        </tbody>
                        @endforeach
                     </table>
                     @endforeach
                  </div>
               </div>
            </div>

   </body>
</html>
