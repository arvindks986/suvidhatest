    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Election Nomination In Phase</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="left" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
                    <th  style="width:50%" align="right">
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
                           <td><strong>List Of Election Nomination In Phase </strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <!--<tr>  
                           <td><strong>Phase:</strong>   {{$phaseid}}</td>
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
                        <tr>  
                           <td align="right"><strong>Notification Date:</strong> {{GetReadableDateFormat($PhaseInfo->DT_ISS_NOM)}}</td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Last Date of Withdrawl:</strong> {{GetReadableDateFormat($PhaseInfo->LDT_WD_CAN)}}</td>
                         </tr> 
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <tr>
                 <td colspan="2" align="left" style=""><strong>Phase Number:</strong>&nbsp;&nbsp;{{$phaseid}}</td>
               </tr>
            </table>
        <table class="table-strip" style="width: 100%; border:1px solid #d5d5d5;" border="1" align="left" cellpadding="5">
            <thead>
                <tr>
                   <th>Serial No</th>
                    <th>State/Uts</th> 
                    <th>Total Nominations Filed</th> 
                    <th>National Parties</th> 
                    <th>State Parties</th> 
                    <th>Other Parties</th> 
                    <th>Independent</th> 
                    <th>Male</th> 
                    <th>Female</th> 
                    <th>Others</th> 
                    <th>Total Valid Nominations</th> 
                </tr>
            </thead>
            <tbody>
@php  
        $count = 1; 

        $TotalNomination = 0; 
        $TotalNational = 0;
        $TotalState = 0;
        $TotalOther= 0;
        $TotalIndependent = 0;
        $TotalMale = 0;
        $TotalFemale = 0;
        $TotalOthers = 0;
        $TotalValidNomination=0;


        @endphp
         @forelse ($EciPhaseInfoDataCandWisePdf as $key=>$listdata)

          @php 

         $TotalNomination             +=   $listdata->TOTAL_NOMINATION;
         $TotalNational               +=   $listdata->NATIONAL;
         $TotalState                  +=   $listdata->STATE;
         $TotalOther                  +=   $listdata->OTHER;
         $TotalIndependent            +=   $listdata->INDEPENDENT;
         $TotalMale                   +=   $listdata->male;
         $TotalFemale                 +=   $listdata->female;
         $TotalOthers                 +=   $listdata->others;
         $TotalValidNomination        +=   $listdata->total;

        @endphp

          <tr>
             <td>{{ $count }}</td>
            <td>{{$listdata->ST_NAME }}</td>
            <td>{{$listdata->TOTAL_NOMINATION }}</td>
            <td>{{$listdata->NATIONAL }}</td>
            <td>{{$listdata->STATE }}</td>
            <td>{{$listdata->OTHER }}</td>
            <td>{{$listdata->INDEPENDENT }}</td>
            <td>{{$listdata->male }}</td>
            <td>{{$listdata->female }}</td>
             <td>{{$listdata->others }}</td>
            <td><b>{{$listdata->total }}</b></td>
          </tr>
       
         @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found In This Phase Election Nomination Data</td>                 
              </tr>
          @endforelse

          <tr class="totalClass">
            <td><b>Total</b></td>
            <td></td>
            <td><b>{{$TotalNomination}}</b></td>
            <td><b>{{$TotalNational}}</b></td>
            <td><b>{{$TotalState}}</b></td>
            <td><b>{{$TotalOther}}</b></td>
            <td><b>{{$TotalIndependent}}</b></td>
            <td><b>{{$TotalMale}}</b></td>
            <td><b>{{$TotalFemale}}</b></td>
             <td><b>{{$TotalOthers}}</b></td>
            <td><b>{{$TotalValidNomination}}</b></td>
            
          </tr>
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