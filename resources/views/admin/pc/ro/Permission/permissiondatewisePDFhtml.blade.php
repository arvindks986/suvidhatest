<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
          <!--TITLE STARTS HERE-->
        <title>Date Wise Permission Reports</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
        <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
       </style>
    </head>
    <body>
      <?php  $st=app(App\commonModel::class)->getstatebystatecode($d->st_code); 
             $ac=getacbyacno($d->st_code,$d->ac_no);
       // print_r($st);die;
      ?> 

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;">
                    <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
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
                           <td><strong>Date Wise Permission Reports</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>State:</strong> {{$st->ST_NAME}}</td>
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
              <th>AC Name</th>
              <th>Total Request</th>
              <th>Accepted</th>
              <th>Rejected</th>
              <th>Inprogress</th>
              <th>Pending</th>
              <th>Cancel</th>
            </tr>
        </thead>
       
        <tbody> 
        @foreach($pdfrecordData as $record_data) 
         
        <?php
       
          $arr = array();
         // foreach($records as $record_data)
         // {  
          if($record_data->pending == '')
          {
          $record_data->pending = '0';
          }
          if($record_data->total_request == '')
          {
          $record_data->total_request = '0';
          }
          if($record_data->approved == '')
          {
          $record_data->approved = '0';
          }
          if($record_data->inprogress == '')
          {
          $record_data->inprogress = '0';
          }
          if($record_data->rejected == '')
          {
          $record_data->rejected = '0';
          }
          if($record_data->Cancel == '')
          {
          $record_data->Cancel = '0';
          }
     
           ?>
            
          <tr>
            <td>{{$ac->AC_NAME}}</td>
            <td>@if(isset($record_data->total_request)) {{$record_data->total_request}}@endif</td>
            <td>@if(isset($record_data->approved)) {{$record_data->approved}} @endif</td>
            <td>@if(isset($record_data->rejected)) {{$record_data->rejected}} @endif</td>
            <td>@if(isset($record_data->inprogress)) {{$record_data->inprogress}} @endif</td>   
            <td>@if(isset($record_data->pending)) {{$record_data->pending}} @endif</td>
            <td>@if(isset($record_data->Cancel)) {{$record_data->Cancel}} @endif</td>   
          </tr>
        <?php //} ?>
            @endforeach
      
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