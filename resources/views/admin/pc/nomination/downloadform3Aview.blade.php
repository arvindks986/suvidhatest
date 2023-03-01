 <style type="text/css">
  th, td{padding:5px; font-family:arial;}
 </style>
 <?php     $i=0;   ?>
<div class="container">
    <div align="center"> <h3 align="center">FROM 3A</h3>
         <p align="center">[See rule 7]</p>
      <h2 align="center">NOTICE OF NOMINATION</h2>
     <p align="center">Election to the House of the People <b>{{$state_name}}</b>  form the <b>{{$const_name}} </b> Parliament constituency.</p>
      <p align="center">Notices is hereby given that the following nominations in respect of the above election have been received up to 3 P.M. Date: -{{date("d-m-Y",strtotime($cur_date))}} </p>
    </div>  
  <table class="table-bordered" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px; ">
    <thead><tr>
      <th>Serial No. of nomination paper</th>
      <th>Name Of Candidate </th> <th>Name Of Father / Mother / Husband</th><th>Age Of Candidate </th>
      <th>Address of Candidate </th>
      <th> Party Affiliation </th><th> Particulars Castes or tribes for candidates belonging to scheduled castes or scheduled tribes </th>
      <th> Electoral roll number of candidate </th><th> Name of proposers </th><th> Electoral roll number of proposers </th>
      
    </tr></thead>
     
   
    @foreach ($result as $key => $item)
         <?php 
               $st=getstatebystatecode($item->candidate_residence_stcode);   
               $dist=getdistrictbydistrictno($item->candidate_residence_stcode,$item->candidate_residence_districtno); 
               $ac=getacname($item->candidate_residence_stcode,$item->candidate_residence_acno);
               if(isset($ac))  $ac_name=$ac->AC_NAME;  
               if(isset($st))   $st_name=$st->ST_NAME; 
               if(isset($dist))   $dist_name=$dist->DIST_NAME;  
          ?>  
        <?Php $i++; ?>      
        <tr>
          <td>{{ ($item->new_srno) }}</td>
          <td>{{ ucwords($item->cand_name) }}</td><td>{{ ucwords($item->candidate_father_name) }}</td><td>{{ $item->cand_age}}</td>
          <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
          <td>{{ $item->cand_category }}</td>
          <td>{{ $item->cand_epic_no }}</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr> 
     @endforeach
    
         
    <tbody>
  </table>
       

   <p align="left">Place .................. </p>  
   <p align="left">Date .................. </p>
   <p align="right">Returning Officer </p>
   <hr>
     
    <p align="left">* Strike off the inappropriate alternative.</p> 
</div>