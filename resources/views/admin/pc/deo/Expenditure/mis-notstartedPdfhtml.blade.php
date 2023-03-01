@php 
$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
 //echo $st_code.'cons_no'.$cons_no; die;
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Not Filed Candidate List</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
       <?php  $st=getstatebystatecode($stateName);  
     ?> 

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;">
                    <img src="<?php echo url('/'); ?>/admintheme/images/logo/eci-logo.png" alt=""  width="100" border="0"/>
                  </th>
                    <th  style="width:49%" align="right" style="border-bottom: 1px dotted #d7d7d7;">
                        SECRETARIAT OF THE<br> 
                        ELECTION COMMISSION OF INDIA<br>
                        Nirvachan Sadan, Ashoka Road, New Delhi-110001<br>  
                    </th>
                </tr>
              </thead>
            </table>
        <table style="width:98%; border: 1px solid #000;" border="0" align="center">  
                <tr>
                 <td  style="width:49%;">
                    <table  style="width:100%">
                      <tbody>
                         <tr>
                           <td><strong>Not Filed Candidate List</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name: PC General</strong> </td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{$stateName}}</td>
                         </tr>
                  
                         
                      </tbody>
                    </table>  
                 </td>
                 <td  style="width:49%">
                  <table style="width:100%">
                      <tbody>
                         <tr>
                           <td align="right"><strong>Date of Print:</strong> {{ date('d.m.Y h:i a') }}</td>
                         </tr>
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
               <
            </table>
      
        <table class="table-strip" style="width: 98%;" border="1" align="center">
          <thead>
        <tr>
        <th>PC No & Name</th>
        <th>Candidate Name</th>
        <th>Party Name</th>
        <th>Last Date Of Lodging</th>
        </tr>
        </thead>
        <tbody>
            <?php 
             $count = 1; 
             $TotalUsers = 0;
            ?>
              @if(!empty($notstarted))
             @foreach($notstarted as $candDetails)  
              <?php
               $count++; 
               $date = new DateTime($candDetails->created_at);
                //echo $date->format('d.m.Y'); // 31.07.2012
                $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
                $pc=getpcbypcno($candDetails->st_code,$candDetails->pc_no); 
				$stDetails=getstatebystatecode($candDetails->st_code);
                 ?>
            
            <tr>
            <td>@if(!empty($candDetails->pc_no)) {{ $pc->PC_NO}}-{{ $pc->PC_NAME}} @endif</td>
            <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
            <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
           <td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '22-06-2019'}} @endif</td>
       
            </tr>
            @endforeach 
            @endif 
             </tbody>
        </table>
      <table style="width:98%; border-collapse: collapse;" align="center" border="1" cellpadding="5">
          <tbody>
            <tr>
              <td colspan="2" align="center"><strong>Nirvachan Sadan, Ashoka Road, New Delhi- 110001</strong></td>  
            </tr>
          </tbody>
      </table>
    </body>
</html>