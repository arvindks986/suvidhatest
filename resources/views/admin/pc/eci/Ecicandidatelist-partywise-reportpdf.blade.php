<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Party Wise Nomination Reports</title>
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
      date_default_timezone_set('Asia/Kolkata');   
       // print_r($st);die;
  ?> 

   <table style="width:98%;  border: 1px solid #000;" border="0" align="center" cellpadding="5">
               <thead>
                <tr>
                    <th  style="width:49%" align="left" style="border-bottom: 1px dotted #d7d7d7;">
                    <img src="<?php echo url('/'); ?>/admintheme/img/logo/eci-logo.png" alt=""  width="100" border="0"/>
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
                           <td><strong>Party Wise Candidate List Reports</strong></td>
                         </tr>
                         <tr>  
                           <td><strong>Name:</strong> Eci</td>
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
          <th>Serial No</th> 
          <th>State</th> 
          <th>PC Number&Name</th> 
          <th>Candidate Name</th> 
          <th>Party Name</th> 
          <th>Symbol</th> 
        </tr>
        </thead>
        <tbody> 
        <?php 
            $count = 1; 
           
          if(count($AllcandListbyParty)>0){
            ?>
            @foreach($AllcandListbyParty as  $candListbyPCData) 

          <?php
        $candidatedetails=getById('candidate_personal_detail','candidate_id',$candListbyPCData->candidate_id);
        $partyDetails=getById('m_party','CCODE',$candListbyPCData->party_id);
        $pcDetails=getpcbypcno($candListbyPCData->st_code,$candListbyPCData->pc_no);
        $symbolDetails=getsymbolbyid($candListbyPCData->symbol_id);
        $statedetails=getstatebystatecode($candListbyPCData->st_code);   

        ?>
         <tr>
          <td >{{$count++}}</td>
          <td> @if(isset($statedetails)){{$statedetails->ST_NAME}} @endif</a></td>
          <td >@if(isset( $candListbyPCData)){{ $candListbyPCData->pc_no.' - '.$pcDetails->PC_NAME}}@endif</td>
          <td > @if(isset( $candidatedetails->cand_name)){{ $candidatedetails->cand_name}} @endif</td>
          <td >@if(isset($partyDetails)){{ $partyDetails->PARTYNAME }}  @endif</td>
          <td >@if(isset($symbolDetails)) {{$symbolDetails->SYMBOL_DES}} @endif</td>
          </tr>
            @endforeach 
            <?php } else {?>
            <tr>
            <td class="col-md-6" colspan='6'> <p>No Records  Founds </p></td>
             </tr>  
             <?php } ?>
      
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