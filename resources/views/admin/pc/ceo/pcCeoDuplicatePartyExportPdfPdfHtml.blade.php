    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Duplicate Partywise Candidate</title>
        <!--HEADER STARTS HERE-->
           
        <!--HEADER ENDS HERE-->
      <style type="text/css">
          .table-strip{border-collapse: collapse;}
          .table-strip th,.table-strip td{text-align: center;}
          .table-strip tr:nth-child(odd){background-color: #f5f5f5;}
      </style>
    </head>
    <body>
      <?php  $st=app(App\commonModel::class)->getstatebystatecode($st_code);  
       // print_r($st);die;
  ?> 

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;"><img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/></th>
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
                    <table  style="width:100%">ST_NAME
                      <tbody>
                         <tr>
                           <td><strong>Duplicate Partywise Candidate</strong></td>
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
                <thead>
          <tr>
              <th>PC No.</th> 
          <th>PC Name</th> 
          <th>Candidate Name</th>
          <th>Party Name</th>

            </tr>
        </thead>
        <tbody> 
             @foreach($lists as $list) 
          <tr>
            <?php
            $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
            $partyDetails=getById('m_party','CCODE',$list->party_id);
            $pcDetails=getpcbypcno($user_data->st_code,$list->pc_no);
          // print_r($affidavit);
           ?>
            <td>{{$list->pc_no}}</td>  
            <td>{{$pcDetails->PC_NAME}}</td>
            <td>{{$candidatedetails->cand_name}}</td>
            <td>{{$partyDetails->PARTYNAME}}</td>   
            
          </tr>
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