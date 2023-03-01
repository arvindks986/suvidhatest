 <style type="text/css">
  th, td{padding:5px; font-family:arial;}
 </style>
<div class="container">
    <div align="center"> <h2 align="center">LIST OF CONTESTING CANDIDATES</h2>
     <p align="center">Election of the {{strtoupper($state->ST_NAME) }}  for  {{strtoupper($pc->PC_NAME) }}  Parliament constituency.</p>
    </div>  
  <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px; ">
    <thead><tr>
          <th>Serial No.</th>
      <th>Name Of Candidate </th>
      <th>Address of Candidate </th>
      <th> Party Affiliation </th>
      <th>Symbol Allotted </th>
    </tr></thead>
    <tr>
      <td align="center"> 1 </td>
      <td align="center"> 2 </td>
      <td align="center"> 3 </td>
      <td align="center"> 4 </td>
      <td align="center"> 5 </td>
    </tr>
    
       
    @foreach ($candn as $key => $item)
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
          <td>{{ $item->cand_name }}</td>
          <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
          <td>{{ $item->SYMBOL_DES }}</td>
        </tr>

    @endforeach
    
    <tbody>
  </table>
    
        
</div>