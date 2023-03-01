@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'List Of Political Parties Participated')
@section('content')
<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}<br>(3 - List Of Political Parties Participated)</h4></div> 
              <div class="col">
			  <p class="mb-0 text-right"><b class="bolt">State Name:</b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
               </p>
			   <p class="mb-0 text-right">
					 <a href="listofpoliticalpartiesparticipated_pdf" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
        <a href="listofpoliticalpartiesparticipated_xls" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative;top: -3px; width: 61px !important;display: table-row;"></a>
			   </p>
              </div>
			  
			
            </div>
      </div>
  
 <div class="card-body">
 
	<div class="table-responsive">
                <table class="table table-bordered" style="width: 100%;table-layout: fixed;">
                    <thead>
                        <tr>
                            <th>PARTY TYPE</th>
                            <th>ABBREVIATION</th>
                            <th>PARTY SYMBOL</th>
                            <th>PARTY</th>
                        </tr>

                       
                    </thead>


                    <tbody>

						<?php $i = 1; ?>
						@foreach ($dataArray as $key => $row)
												
						<tr>
							@if($key=='N')
                            <th colspan="4">NATIONAL PARTIES</th>
							@elseif($key=='S')
							<th colspan="4">STATE PARTIES</th>
							@elseif($key=='U')
							 <th colspan="4">REGISTERED(UNRECOGNISED) PARTIES</th>
							@elseif($key=='Z')
							<th colspan="4">INDEPENDENT</th>
							@endif
                        </tr>
											
						@foreach ($row as $keys => $rowData)
					
                        <tr>
                            <td>{{$i}} </td>
                            <td>{{$rowData['PARTYABBRE']}}</td>
                            <td>{{$rowData['SYMBOL_DES']}}</td>
                            <td>{{$rowData['PARTYNAME']}}</td>
                        </tr>
                        
						<?php $i++; ?>

						@endforeach
						@endforeach
						
						<tr>
						<th colspan="4">NOTA</th>
                        </tr>
						<tr>
                            <td>{{$i}} </td>
                            <td>NOTA</td>
                            <td>NOTA</td>
                            <td>None of the Above</td>
                        </tr>

                    </tbody>
                </table>
                </div>
 </div>
 </div>
 </div>
 </div>
 </section>

@endsection