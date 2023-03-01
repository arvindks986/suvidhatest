    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Finalized PCs All Phases</title>
       
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
                           <td><strong>List Of Finalized PCs All Phases</strong></td>
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
                        <tr>  
                           <td align="right"><strong>Report From:</strong> 18-Mar-2019</td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Report To:</strong>  {{ date('d-M-Y') }}</td>
                         </tr>
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <tr>
                <!--  <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>Phase Number:</strong>&nbsp;&nbsp;</td> -->
               </tr>
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>
                <tr>
                   <th>Serial No</th>
                    <th>Phase No</th> 
                    <th>No of Total PCs</th> 
                    <th>Finalized PCs</th> 
                </tr>
            </thead>
            <tbody>
@php  $count = 1; $TotalPc = 0; $TotalFinalized = 0; @endphp
         @forelse ($EciNominationFinalizedPdf as $key=>$listdata)

         @php 

         $TotalPc += $listdata->total_pc;

         $TotalFinalized += $listdata->finalized_pc;

        @endphp

          <tr>
          <td>{{ $count }}</td> 
            <td>Phase {{$listdata->sid }}</td>
            <td>{{$listdata->total_pc }}</td>
            <td>{{$listdata->finalized_pc }}</td>
          </tr>
         
       
         @php  $count++; @endphp

          @empty

                <tr>
                  <td colspan="4">No Data Found For Finalized PCs</td>                 
              </tr>
          @endforelse
          <tr class="totalClass"><td><b>Total</b></td><td></td><td><b>{{ $TotalPc }}</b></td><td><b>{{ $TotalFinalized }}</b></td></tr>
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