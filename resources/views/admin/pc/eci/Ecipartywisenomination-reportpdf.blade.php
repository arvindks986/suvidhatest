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
                           <td><strong>Party Wise Nomination Reports</strong></td>
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
          <th>S.No</th>
           <th>Party Abbreviation</th>
           <th>Party name</th> 
           <th>Party type</th> 
           <th>Total Nominations applied</th> 
           <th>Total Validly Nominated Candidates</th> 
        </tr>
        </thead>
        <tbody> 
        <?php 
            $j=0;  
            $total_applied=0;
            $total_accepted=0;
            $total_rejected=0;
            $total_withdrowl=0;
            $total_validnom=0;
          if(count($EciPartywiseNominationReport)>0){
            ?>
            @foreach($EciPartywiseNominationReport as $partywiseDetailList) 

          <?php
         $pcDetails=getpcbypcno($partywiseDetailList->st_code,$partywiseDetailList->pc_no); 
         //$acDetails =getacbyacno($partywiseDetailList->st_code,$officerDetailsList->ac_no);
         $st=getstatebystatecode($partywiseDetailList->st_code);
        // $partyDetails=getById('m_party','CCODE',$partywiseDetailList->party_id);
         $j++; 
    //dd($partywiseDetailList);  
    $totapplied=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'1'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count();
    $totrej=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'4'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count();
    $totalwith= \app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'5'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count() ;
    $totaccepted=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'6'])->where('party_id', '=' ,$partywiseDetailList->party_id)->get()->count();
    $total=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where('party_id', '!=' ,'1180')->where('application_status','!=','11')->get()->count();
    $totalvelidcand=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$partywiseDetailList->st_code])->where(['application_status' =>'6'])->where('party_id', '=' ,$partywiseDetailList->party_id)->where(['finalaccepted'=>1])->get()->count();

    if($partywiseDetailList->PARTYTYPE=='N') { $parttype='National Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='S'){ $parttype='State Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='Z'){ $parttype='Independent Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='U'){ $parttype='Unrecognized Party';} 
      elseif($partywiseDetailList->PARTYTYPE=='0'){ $parttype='Unrecognized Party';}  

        ?>
         <tr>
          <td >{{ $j }}</td>
            <td >@if(isset($partywiseDetailList->PARTYABBRE)) {{$partywiseDetailList->PARTYABBRE}}@endif</td>
            <td>@if(isset($partywiseDetailList->PARTYNAME)) {{$partywiseDetailList->PARTYNAME}} @endif</td>
            <td>@if(isset($partywiseDetailList->PARTYTYPE)) {{ $parttype }}@endif</td>
            <td >@if(isset($partywiseDetailList->totalnomination)) {{$partywiseDetailList->totalnomination}}@endif</td>
            <td >@if(isset($totalvelidcand)) {{$totalvelidcand}}@endif</td> 
            <!-- <td >@if(isset($totaccepted)) {{$totaccepted}}@endif</td>
            <td >@if(isset($totrej)) {{$totrej}}@endif</td>
            <td >@if(isset($totalwith)) {{$totalwith}}@endif</td>-->
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