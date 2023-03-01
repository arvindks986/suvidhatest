    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Nomination Data Report State and Phase Wise</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
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
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
        <table style="width:100%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:50%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>Nomination Data State Wise </strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{$state}}</td>
                         </tr>
                          <tr>  
                           @if($phaseid != '') <td><strong>Phase No:</strong> &nbsp; {{$phaseid}}</h4> </td> @else  @endif
                         </tr>  
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:50%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d-M-Y h:i a') }}</td>
                         </tr>
                         @if($PhaseInfo !="")
                        <tr>  
                           <td align="right"><strong>Notification Date:</strong> {{GetReadableDateFormat($PhaseInfo->DT_ISS_NOM)}}</td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Last Date of Withdrawl:</strong> {{GetReadableDateFormat($PhaseInfo->LDT_WD_CAN)}}</td>
                         </tr> 
                        @endif
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <!-- <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>Phase Number:</strong>&nbsp;&nbsp;{{$phaseid}}</td>
               </tr> -->
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>
                <tr>
                   <th>PC No</th> 
                   <th>PC Name</th> 
                   <th>Total Nomination</th> 
                   <th>Accepted Status</th>
                </tr>
            </thead>
            <tbody>
        
         @php  $TotalNomination = 0; $TotalAccepted=0; @endphp
      
         @forelse ($EciNominationStateWisePdf as $key=>$listdata)

         @php  
         $TotalNomination    +=   $listdata->totalnomination;
         $TotalAccepted      +=   $listdata->accepted_status;
          @endphp
          <tr>
           
            <td>{{ $listdata->PC_NO }}</td>
            <td>{{ $listdata->PC_NAME }}</td>
            <td> @if($listdata->totalnomination =='' )     0  @else  {{ $listdata->totalnomination }} @endif</td>
            <td> @if($listdata->accepted_status =='' )     0  @else  {{ $listdata->accepted_status }} @endif</td>
           
          </tr>
      
           @empty
                <tr>
                  <td colspan="4">No Data Found For Nominations</td>                 
              </tr>
          @endforelse
          <tr class="totalClass">
            <td>Total</td>
            <td></td>
            <td><b>{{$TotalNomination}}</b></td>
            <td><b>{{$TotalAccepted}}</b></td>
          </tr>
            </tbody>
        </table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>