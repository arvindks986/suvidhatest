    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
       
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
                           <td><strong>User:</strong> {{$user_data->placename}}</td>
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
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>

              
              
            </table>
<table class="table-strip" style="width: 100%;" border="1" align="center">
                  <tbody>
                  <tr>
                  <td align="right" >
<h1 class="text-danger text-right" style="font-size: 10px;margin-bottom: 30px;">
          *State percentage shown as sub-total is 'Average Weighted Percentage'.
        </h1>
      </td>
    </tr>
  </tbody>
</table>

          <table class="table-strip" style="width: 100%;" border="1" align="center">
                  <tbody>
                  <tr>
                  <td align="center" class="text-center" style="font-size: 16px;"><strong>{!! $heading_title !!}</strong></td>
                  </tr>
                    <tr>
                  <td align="center" class="text-center" style="font-size: 16px;"><strong> Estimated Turn Out 2019 - {{$number_of_voting}}%</strong></td>
                  </tr>
                </tbody>
                </table>
                
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>
       <tr>
          <th rowspan="2" class="text-center">State</th>
          <th rowspan="2" class="text-center"> PC No & Name </th>
          <th rowspan="2" class="text-center">2014 Turnout(in %)</th>
          <th colspan="2" class="text-center"> 2019 Elections</th>
          <th rowspan="2" class="text-center">Change from 2014</th>
        </tr>
        <tr>
        <!--   <th colspan="1">9:00 AM %</th>
         <th colspan="1">11:00 PM %</th>
         <th colspan="1">1:00 PM %</th>
         <th colspan="1">3:00 PM %</th>
         <th colspan="1">5:00 PM %</th> -->
          <th colspan="2" class="text-center"> Estimated Turnout (in %)</th>
       </tr>


    </thead>
        <tbody style="width: 100%;">
      <?php $index = 0; ?>
      @foreach($results as $pcs_array)

        @foreach($pcs_array as $result)

    

          <tr class="<?php if($result['is_state']==1){ ?> state_row <?php } ?>">
          <td><span>{!! $result['label'] !!}</span></td> 

          <td>{{$result['pc_no'] }} - {{$result['pc_name'] }}</td>

          <td><span>{!! $result['total_previous'] !!}</span></td> 

        <!--   <td>{{$result['est_total_round1'] }}</td>
          
          <td>{{$result['est_total_round2'] }}</td>
          
          <td>{{$result['est_total_round3'] }}</td>
          
          <td>{{$result['est_total_round4'] }}</td>

          <td>{{$result['est_total_round5'] }}</td> -->

    
          <td colspan="2">{{$result['total_percentage'] }}</td>


          <td><span>{!! $result['difference'] !!}</span> </td> 
   
           </tr>

    
          @endforeach

        @endforeach
      <?php if(isset($totals)){ ?>
        <!--<tfoot>
        <tr>
        <td><span>{!! $totals['label'] !!}</span></td> 
        <td></td>
        <td>{!! $totals['total_previous'] !!}</td>
        <td>{!! $totals['est_total_round1'] !!} </td>         
        <td>{!! $totals['est_total_round2'] !!} </td>       
        <td>{!! $totals['est_total_round3'] !!} </td> 
        <td>{!! $totals['est_total_round4'] !!} </td> 
        <td>{!! $totals['est_total_round5'] !!} </td>  

        <td>{!! $totals['close_of_poll'] !!} </td>  
        <td>{!! $totals['total_percentage'] !!} </td>  

        <td>{!! $totals['difference'] !!}</td>
         </tr>
        </tfoot> -->

        <?php } ?>
       </tbody></table>
      <table style="width:100%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>