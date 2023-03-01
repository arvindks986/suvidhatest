<style type="text/css">
  th, td{padding:5px; font-family:arial; vertical-align:middle;}
 </style>
<div class="container">
<table  class="table" cellpadding="0" cellspacing="0" width="100%" style="font-family:'Times New Roman', Times, serif">
<tr> <td colspan="2" align="center">
  
  <h3 align="center">ELECTION COMMISSION OF INDIA</h3>
Nirvachan Sadan, Ashoka Road, New Delhi-110 001</td></tr>
<tr style="border-bottom:2px solid #000;"><td colspan="2"><hr></td></tr>


<tr>
  <td align="left"><b>State:</b> {{ $state }} <br> <b>Constituency:</b> {{ $pcname }}</td>
  <td align="right"><b>Date:</b> {{ date("d.m.y") }} </td>
</tr>
<tr> <td colspan="2" align="center"><h4 align="center"></h4></td></tr>


</table>
    <div align="center"> <h2 align="center">LIST OF CONTESTING CANDIDATES</h2>
    </div>  
  <table class="table" border="1" cellpadding="0" cellspacing="0" width="100%" align="center" style="font-size:14px; ">
    <thead><tr>
      <th>Serial No.</th>
      <th>Name Of Candidate </th>
      <th>Address of Candidate </th>
      <th>Party Affiliation </th>
      <th>Symbol Allotted </th>
    </tr></thead>
    <tr>
      <td align="center"> 1 </td>
      <td align="center"> 2 </td>
      <td align="center"> 3 </td>
      <td align="center"> 4 </td>
      <td align="center"> 5 </td>
    </tr>
    
       
    @foreach ($candlist as $cand)
        <tr>
		      <td>{{ $cand["cand_sn"] }}</td>
          <td>{{ $cand["cand_name"] }}</td>
          <td>{{ $cand["candidate_residence_address"] }}</td>
          <td>{{ $cand["party_name"] }}</td>
          <td>{{ $cand["symbol_name"] }}</td>
        </tr>

    @endforeach
   
    <tbody>
  </table>
  <br> <br>
  <small style="text-align:center; display:block;"> <span style="font-size:14px;">*</span> Disclaimer: This is Computer generated report.</small>
        
</div>