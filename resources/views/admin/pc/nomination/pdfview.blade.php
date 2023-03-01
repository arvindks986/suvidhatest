 <style type="text/css">
  th, td{padding:5px; font-family:arial;}
 </style>
<div class="container">
    <div align="center"> <h2 align="center">FROM 7 A</h2>
         <p align="center">[See rule 10 (1)]</p>
      <h2 align="center">LIST OF CONTESTING CANDIDATES</h2>
     <p align="center">Election to the House of the People {{strtoupper($state->ST_NAME) }}  for  {{strtoupper($pc->PC_NAME) }}  Parliament constituency.</p>
    </div>  
  <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px; ">
    <thead><tr>
      <th>Serial No.</th>
      <th>Name Of Candidate </th>
      <th>Photo Of Candidate </th>
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
      <td align="center"> 6 </td>
    </tr>
    
       
    @foreach ($candn as $key => $item)
         <?php 
               $st=getstatebystatecode($item->candidate_residence_stcode);   
               $dist=getdistrictbydistrictno($item->candidate_residence_stcode,$item->candidate_residence_districtno); 
                
               $ac=getacname($item->candidate_residence_stcode,$item->candidate_residence_acno);
               //dd($ac);
               if(isset($ac))  $ac_name=$ac->AC_NAME;  
               if(isset($st))   $st_name=$st->ST_NAME; 
               if(isset($dist))   $dist_name=$dist->DIST_NAME;  
                
         ?>
        <tr>
          <td>{{ $item->new_srno }}</td>
          <td>{{ $item->cand_name }}</td>
          <td><img src="{{ public_path($item->cand_image) }}" style="width:100px" class="prfl-pic img-thumbnail" alt=""></td>
          <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
          <td>{{ $item->SYMBOL_DES }}</td>
        </tr>
    
    @endforeach
    
      
    <tbody>
  </table>
      <ol type="i" style="text-align:left ">
        <li>  Candidates of recognised National and State Political Parties. </li>
        <li>  Candidates of registered political parties (other than recognised National and State Political Parties).</li>
        <li>  Other Candidates. </li>
        </ol> 


   <p align="left">Place .................. </p>  
   <p align="left">Date .................. </p>
   <p align="right">Returning Officer </p>
   <hr>

    <p align="left">* Applicable in the case of candidates mentioned under categories (i) and (ii) above.</p> 
    <p align="left">Note:- Under Col. 1 above, the serial number of candidates of all the three categories shall be given consecutively and not separately for each category.</p> 
</div>
