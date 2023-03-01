    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Finalized PCs Phase Wise ECI</title>
       
    </head>
    <body>
         <!--HEADER STARTS HERE-->
            <table style="width:100%;" border="0" align="left" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:50%" align="left" style="">
					<img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
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
                           <td><strong>State and PC Wise Data </strong></td>
                         </tr>
                         <tr>  
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
                         </tr>
                         <!--<tr>  
                           <td><strong>State:</strong>   aa</td>
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
                           <td align="right"><strong>Report From:</strong> fromdate</td>
                         </tr>
                         <tr>  
                           <td align="right"><strong>Report To:</strong>  todate</td>
                         </tr> -->
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              <tr>
                 <td colspan="2" align="center" style="border-top: 1px solid #000;"><strong>State:</strong>&nbsp;&nbsp; {{$state}}<strong>&nbsp;&nbsp;PC Number:</strong>&nbsp;&nbsp; {{$pcno}}</td>
               </tr> 
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="left" cellpadding="5">
            <thead>
                <tr>
                <th>Serial No</th>
          <th>Candidate Name</th> 
          <th>PC Name</th> 
          <th>Party Name</th> 
          <th>Symbol</th> 
                </tr>
            </thead>
            <tbody>
        @php  $count = 1; @endphp
         @forelse ($EciNominationPcWisePdf as $key=>$listdata)
          <tr>
            <td>{{ $count }}</td>
            <td>{{ $listdata->cand_name }}</td>
            <td>{{ $listdata->PC_NAME }}</td>
            <td>{{ $listdata->PARTYNAME }}</td>
            <td>{{ $listdata->SYMBOL_DES }}</td>           
          </tr>
        @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">No Data Found For Nominations</td>                 
              </tr>
          @endforelse
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