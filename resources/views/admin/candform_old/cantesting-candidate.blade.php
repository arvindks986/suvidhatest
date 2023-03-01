<!DOCTYPE html>
<html lang="en">
<?php   $url = URL::to("/");  ?>
    <head>
        <meta charset="utf-8">
        <title>{!! $heading_title !!}</title>
      <style type="text/css">
        @page {
        header: page-header;
        footer: page-footer;
      }
      table .th{
            -ms-transform: rotate(-90deg); /* IE 9 */
            -webkit-transform: rotate(-90deg); /* Safari 3-8 */
            transform: rotate(-90deg);
            margin: 5px;
           }
      </style>
      
    </head>
    <body>
@if(isset($record))
<htmlpageheader name="page-header">
  <div class="header_section">
                                  
         <table style="width:100%;padding:10px 0;" border="0" align="center" cellpadding="5">
            <thead> 
               <tr> <th style="width:100%; font-size:18px;margin:10px 0;" align="center" >@if(isset($message)){{@$message}}@endif {{$record->title1}}</th></tr>
               <tr> <th style="width:100%; font-size:14px;margin:5px 0;" align="center" >{{$record->title2}}</th></tr>
              <tr> <th style="width:100%; font-size:14px;margin:5px 0;" align="center" > 
                <p class="mb-0">{{$record->title3}}</p>
              <p class="mb-0">{{$record->title4}}</p> </th></tr>    
              </thead>
          </table>   
   </div>
</htmlpageheader>
                   
       
        <table style="width:100%; text-align:center;" border="1" align="center" cellpadding="5" cellspacing="0" >
            <thead><tr>
                  <td>{{$record->header1}}</td>
                  <td>{{$record->header2}}</td>
                  <td>{{$record->header3}} </td>
                  <td> {{$record->header4}}</td>
                  <td>{{$record->header5}}</td>
                  <td>{{$record->header6}}</td>
                </tr>
                <tr>
                  <td>{{$record->subheader1}}</td>
                  <td>{{$record->subheader2}}</td>
                  <td>{{$record->subheader3}} </td>
                  <td> {{$record->subheader4}}</td>
                  <td>{{$record->subheader5}}</td>
                  <td>{{$record->subheader6}}</td>
                </tr></thead>
          <tbody> 
            <tr> <td colspan="6"><h3>{{$record->middle_title1}}</h3></td> </tr>
            @foreach ($cands as $key => $item)
                <?php 
                   $st=getstatebystatecode($item->candidate_residence_stcode);   
                   $dist=getdistrictbydistrictno($item->candidate_residence_stcode,$item->candidate_residence_districtno); 
                   $ac=getacname($item->candidate_residence_stcode,$item->candidate_residence_acno);
                   if(isset($ac))  $ac_name=$ac->AC_NAME;  
                   if(isset($st))   $st_name=$st->ST_NAME; 
                   if(isset($dist))   $dist_name=$dist->DIST_NAME;  
                ?>
                  <tr>
                    <td>{{ $item->new_srno }}</td>
                    <td>{{ $item->cand_name }}  </td>
                    <td>
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td>{{ $item->candidate_residence_address }}</td>
                    <td>{{ $item->PARTYNAME }}</td>
                    <td>{{ $item->SYMBOL_DES }}</td>
                  </tr>
             @endforeach
              <tr> <td colspan="6"><h3>{{$record->middle_title2}}</h3></td> </tr>

             @foreach ($candu as $key => $item)
            <?php 
                   $st=getstatebystatecode($item->candidate_residence_stcode);   
                   $dist=getdistrictbydistrictno($item->candidate_residence_stcode,$item->candidate_residence_districtno); 
                   $ac=getacname($item->candidate_residence_stcode,$item->candidate_residence_acno);
                   if(isset($ac))  $ac_name=$ac->AC_NAME;  
                   if(isset($st))   $st_name=$st->ST_NAME; 
                   if(isset($dist))   $dist_name=$dist->DIST_NAME;  
               ?>
                  <tr>
                    <td>{{ $item->new_srno }}</td>
                    <td>{{ $item->cand_name }}  </td>
                    <td>
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td>{{ $item->candidate_residence_address }}</td>
                    <td>{{ $item->PARTYNAME }}</td>
                    <td>{{ $item->SYMBOL_DES }}</td>
                  </tr>
             @endforeach 
             <tr> <td colspan="6"><h3>{{$record->middle_title3}}</h3></td> </tr>
             @foreach ($candz as $key => $item)
            <?php 
                   $st=getstatebystatecode($item->candidate_residence_stcode);   
                   $dist=getdistrictbydistrictno($item->candidate_residence_stcode,$item->candidate_residence_districtno); 
                   $ac=getacname($item->candidate_residence_stcode,$item->candidate_residence_acno);
                   if(isset($ac))  $ac_name=$ac->AC_NAME;  
                   if(isset($st))   $st_name=$st->ST_NAME; 
                   if(isset($dist))   $dist_name=$dist->DIST_NAME;  
               ?>
                   <tr>
                    <td>{{ $item->new_srno }}</td>
                    <td>{{ $item->cand_name }}  </td>
                    <td>
                      @if($item->cand_image!='')
                       <img src="{{public_path($item->cand_image)}}" style="width:100px" class="prfl-pic img-thumbnail" alt="">
                      @endif </td>
                    <td>{{ $item->candidate_residence_address }}</td>
                    <td>{{ $item->PARTYNAME }}</td>
                    <td>{{ $item->SYMBOL_DES }}</td>
                  </tr>
             @endforeach  

          </tbody>  
          </table> 
            <div >

            </div>
           
   
           

           <htmlpagefooter name="page-footer">
              <table style="width:100%; text-align:center;" border="0" align="center" cellpadding="2" cellspacing="2" >
                 <thead>
                  <tr> <td> &nbsp;&nbsp;</td>  <td> &nbsp;&nbsp;</td><td> &nbsp;&nbsp;</td> </tr>
                  <tr> <td align="left">{{$record->footer1}}</td> <td> &nbsp;&nbsp;</td>
                    <td align="right">{{$record->footer3}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                  <tr> <td align="left">{{$record->footer2}}</td><td> &nbsp;&nbsp;</td> <td align="right">{{$record->footer4}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
                  <tr> <td> &nbsp;&nbsp;</td><td> &nbsp;&nbsp;</td> <td align="right">{{$record->footer5}}</td></tr>
                </thead>
             </table>
           </htmlpagefooter>
      @else 
          <h2> Form 7 A heading not enter by RO.</h2>  
    @endif      
        

    </body>
</html>
