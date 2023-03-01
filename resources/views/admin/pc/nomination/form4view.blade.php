@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Form 4 Display')
@section('content')
<?php $url = URL::to("/");    $i=0;   ?>
<section class="statistics color-grey pt-3 pb-2 border-bottom">
	<div class="container-fluid">
			<div class="row">
			         <div class="col-md-9"> <h5> Form 4 Display</h5> </div>
               <div class="col-md-3 float-right">   <button type="button" class="btn btn-danger" onclick="location.href ='{{$url}}/ropc/download-form-4-report';">Download & Verify </button> </div> 
			</div>
	</div>
</section>
 <section class="data_table">
    <div class="container">
    <div align="center"> <h3 align="center">FROM 4</h3>
         <p align="center">[See rule 8]</p>
      <h2 align="center">LIST OF VALIDLY NOMINATED CANDIDATES</h2>
     <p align="center">Election to the House of the People <b>{{$state_name}}</b>  for <b>{{$const_name}} </b> Parliament constituency.</p>
    </div>  
  <table class="table-bordered" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px; ">
    <thead><tr>
      <th>Serial No.</th>
      <th>Name Of Candidate </th> <th>Name Of Father / Mother / Husband</th>
      <th>Address of Candidate </th>
      <th> Party Affiliation </th>
      
    </tr></thead>
     <tbody>
    @if(isset($candn)) 
    @foreach ($candn as $key => $item)
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
          <td>{{ ucwords($item->cand_name) }}</td><td>{{ ucwords($item->candidate_father_name) }}</td>
          <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
           
        </tr> 
     @endforeach
    @endif
    @if(isset($cands)) 
    @foreach ($cands as $key => $item)
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
          <td>{{ ucwords($item->cand_name) }}</td><td>{{ ucwords($item->candidate_father_name) }}</td>
          <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
           
        </tr>
     @endforeach
    @endif
    @if(isset($candu)) 
    @foreach ($candu as $key => $item)
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
          <td>{{ ucwords($item->cand_name) }}</td><td>{{ ucwords($item->candidate_father_name) }}</td>
          <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
           
        </tr>
     @endforeach
    @endif  


    @if(isset($candz)) 
    @foreach ($candz as $key => $item)
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
          <td>{{ ucwords($item->cand_name) }}</td><td>{{ ucwords($item->candidate_father_name) }}</td>
          <td>{{ $item->candidate_residence_address }} {{$ac_name}}  {{ $dist_name}} {{ $st_name }} </td>
          <td>{{ $item->PARTYNAME }}</td>
           
        </tr>
     @endforeach
    @endif    
   </tbody>
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
    <p align="left">* Appropriate particulars of the Election to be inserted here.</p> 
    <p align="left">** Strike off the inappropriate alternative.</p> 
    <p align="left">@ Applicable in the case of candidates mentioned under categories (i) and (ii) above.</p> 
    <p align="left">Note:- Under Col. 1 above, the serial number of candidates of all the three categories shall be given consecutively and not separately for each category.</p> 
</div>
   
</section>
@endsection
 