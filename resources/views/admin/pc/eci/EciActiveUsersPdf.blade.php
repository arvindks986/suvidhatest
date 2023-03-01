    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Active Users</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="left" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style=""><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="font-weight:normal;">
                        SECRETARIAT OF THE<br>
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: left;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%;" border="0" align="left">  
                <tr>
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>List Of Active Users</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <!--<tr>  
                           <td><strong>Phase:</strong>   aa</td>
                         </tr>
                          <tr>  
                           <td><strong>Assembly:</strong> SNAME</td>
                         </tr>  --> 
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
                       <!-- <tr>  
                           <td align="right"><strong>Phase Starts:</strong> </td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Phase Ends:</strong>  </td>
                         </tr>  -->
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center" cellpadding="5">
            <thead>
                <tr>
              <th>Serial No</th>
          <th>State</th> 
          <th>Total User</th> 
          <th>Active Users</th> 
          <th>Percentage</th> 
                </tr>
            </thead>
            <tbody>
     @php  
        $count = 1; 
        $TotalUsers = 0;
        $ActiveUsers = 0;

        @endphp
         @forelse ($EciActiveUsersPdf as $key=>$listdata)

         @php

         $TotalUsers +=$listdata->total_user;
         $ActiveUsers +=$listdata->active_users;

         @endphp
          <tr>
             <td>{{ $count }}</td>
            <td>{{ $listdata->ST_NAME }}</td>
            <td> @if($listdata->total_user =='' )     0  @else  {{ $listdata->total_user }} @endif</td>
            <td> @if($listdata->active_users =='' )   0  @else {{ $listdata->active_users }} @endif</td>
            <td> @if($listdata->percentage =='' )     0  @else <b>{{ $listdata->percentage }}</b> @endif</td>
            
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Active Users</td>                 
              </tr>
          @endforelse
           <tr><td><b>Total</b></td><td></td><td><b>{{$TotalUsers}}</b></td><td><b>{{$ActiveUsers}}</b></td><td></td></tr>
            </tbody>
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="0" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>