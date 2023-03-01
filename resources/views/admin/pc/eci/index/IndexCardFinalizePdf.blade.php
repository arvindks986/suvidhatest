    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>List Of Counting Status</title>
       
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
                           <td><strong>{!! $heading_title !!}</strong></td>
                         </tr>
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
                         <tr>  
                           <td align="right">&nbsp;</td>
                         </tr> 
                      </tbody>
                    </table>
                 </td>
               </tr>
              
            </table>
        <table class="table-strip" style="width: 100%;" border="1" align="center">
            <thead>
              <tr>
          <th>Serial No</th>
          <th>State Name</th> 
          <th>PC Num - PC Name</th> 
          <th>Finalized By RO</th> 
          <th>Finalized By CEO</th> 
          <th>Nomination Finalized</th> 
          <th>Counting Finalized</th>
        </tr>
            </thead>
            <tbody>
    @php  

        $count = 1;
         @endphp

        @forelse($results as $result)

          <tr>
             <td>{{ $count }}</td>
            <td>{{ $result->st_name }}</td>
            <td>{{ $result->pcno }} - {{ $result->pc_name }} </td>

            @php if($result->FinalizeRo  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->FinalizeRo  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->FinalizeRo }}</td>
            @php } @endphp


            @php if($result->FinalizeCeo  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->FinalizeCeo  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->FinalizeCeo }}</td>
            @php } @endphp

			@php if($result->NominationFinalize  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->NominationFinalize  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->NominationFinalize }}</td>
            @php } @endphp
			
			@php if($result->CountingFinalize  == 'Yes'){  @endphp
            <td style="color:#008000;">{{$result->CountingFinalize  }}</td>
             @php }else{ @endphp
            <td style="color:#FF0000;">{{$result->CountingFinalize }}</td>
            @php } @endphp
         
          
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Index Card Finalize Statusss</td>                 
              </tr>
          @endforelse
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