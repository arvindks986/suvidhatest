    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Counting Status</title>
        <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          
          @page { sheet-size: A3-L; }
          @page bigger { sheet-size: 420mm 370mm; }
          @page toc { sheet-size: A4; }
@page {
            header: page-header;
            footer: page-footer;
        }		  
      </style>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
  <div class="bordertestreport">
      <table class="table-strip" align="center">
           <tr>
              <td style="text-align: center;"><p style="font-size: 12px;font-weight: bold;">Election Commission of India, Elections,2019 ( 17 LOK SABHA )</p></td>
            </tr>
            
           
  </table>

      <table class="table-strip" align="center">
      <tr><td style="text-align: center;">
                        <p style="font-size: 18px;text-transform: uppercase;padding: 6px;"><b>7 - Constituency Wise Summary</b></p>
                  </td>
              </tr>
  </table>
        <!--HEADER ENDS HERE-->
     <br>
        <table style="border: 1px solid #000;" border="0" align="center">  
                <?php  if (verifyreport(7) == 0){ ?>
           <tr>
        <td style="text-align: left;"><b style="font-size: 15px; ">User</b>: ECI</td>
        <td style=""><p style="width: 100%;font-size: 15px;"><b>Date of Print</b> : <?php echo date("d-m-Y h:i:s A") . "\n"; ?>
    </p></td>
    <td><p style="font-size: 15px;font-weight: bold;">Draft</p></td>
      </tr>
    <?php } else { ?>


    <?php } ?>
              
          </table>
        <table class="table-strip" border="1" align="center">
           <thead>
         <tr>
         
          <th>PC No</th>
          <th>PC Name</th> 
          <th>No Of AC Segments</th> 
          <th>No Of Polling Station</th> 
          <th>Electors</th> 
          <th>Avg. No. of Electors Per PS</th> 
          <th>Nominations</th> 
          <th>Contestants</th> 
          <th>Forefeited Deposits</th> 
          <th>Voters</th> 
          <th>Voters Turn Out (%)</th>          
        </tr>
        </thead>
          <tbody>
        @php  

        $count = 1;

        $TotalAc          = 0;
        $TotalPs          = 0;
        $TotalElector     = 0;
        $TotalAvgElector  = 0;
        $TotalNominated   = 0;
        $TotalContested   = 0;
        $TotalForefeited  = 0;
        $TotalVoter       = 0;
       

         @endphp

        @forelse($results as $result)

         @php
         if($result['is_state']==1){

         $TotalAc          +=$result['total_const'];
         $TotalPs          +=$result['total_ps'];
         $TotalElector     +=$result['total_electors'];
         $TotalAvgElector  +=$result['avg_elector_in_ps'];
         $TotalNominated   +=$result['nominated'];
         $TotalContested   +=$result['contested'];
         $TotalForefeited  +=$result['forefeited'];
         $TotalVoter       +=$result['total_voter'];

        }

         @endphp

          @if($result['is_state'] == 0 && empty($result['constno']))
          <tr class="">
          
            <td colspan="11" align="left" style="border:none;"><b>{{ $result['st_name'] }}</b></td> 
          </tr>
          @else
          <tr class="<?php if($result['is_state']==1){ ?> state_row <?php } ?>">
             

           @if($result['is_state'] == 1)
            <td style="border-right:none;border-left:none;"><b>{{ $result['constno'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['const_name'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_const'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_ps'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_electors'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['avg_elector_in_ps'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['nominated'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['contested']  }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['forefeited'] }}</b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['total_voter'] }}  </b></td>
            <td style="border-right:none;border-left:none;"><b>{{ $result['voterturnout'] }}</b></td>
           @else
           <td>{{ $result['constno'] }}</td>
            <td>{{ $result['const_name'] }}  </td>
            <td>{{ $result['total_const'] }}  </td>
            <td>{{ $result['total_ps'] }}  </td>
            <td>{{ $result['total_electors'] }}  </td>
            <td>{{ $result['avg_elector_in_ps'] }}</td>
            <td>{{ $result['nominated'] }}</td>
            <td>{{ $result['contested']  }}</td>
            <td>{{ $result['forefeited'] }}</td>
            <td>{{ $result['total_voter'] }}  </td>
            <td>{{ $result['voterturnout'] }}</td>
           @endif
           
          
          </tr>
          @endif
     
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Constituency (PC) Wise Summary</td>                 
              </tr>
          @endforelse
          @php if($user_data->role_id == '7' || $user_data->role_id =='27'){  @endphp
          <tr><td><b>Grand Total</b></td><td></td><td><b>{{$TotalAc}}</b></td><td><b>{{$TotalPs}}</b></td><td><b>{{$TotalElector}}</b></td><td><b>{{round($TotalElector/$TotalPs,0)}}</b></td><td><b>{{$TotalNominated}}</b></td><td><b>{{$TotalContested}}</b></td><td><b>{{$TotalContested}}</b></td><td><b>{{$TotalVoter}}</b></td><td><b>{{ ROUND($TotalVoter/$TotalElector*100,2)}}</b></td></tr>

           @php } @endphp
        </tbody>
        </table>
      


 <h4 style="padding-top: 8px;">Disclaimer</h4>
 <p style="position: relative;top: -11px;font-size: 13px;">This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.</p>



<htmlpagefooter name='page-footer'>
      <table class="table-strip" align="left">
 <tr>
 <?php if (verifyreport(7) == 1){ ?>
 <td align="left"><span style="float:left;color: #d3d3d3;">{{getreportsequence(777)}}</span></td>
    
    <?php } ?>
</tr>
</table>


      <table class="table-strip" align="right">
<tr>
 <td align="right"><span style="float:right;">Page {PAGENO}</span></td>
</tr>
</table>

 </htmlpagefooter>


</body>
</html>