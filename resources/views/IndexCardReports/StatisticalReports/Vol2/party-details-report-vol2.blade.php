@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'Party Details Report')
@section('content')

<?php  $st=getstatebystatecode($user_data->st_code);   ?> 
<section class="">
	<div class="container">
		<div class="row">
			<div class="card text-left mt-5" style="width:100%; margin:0 auto;">
				<div class=" card-header">
					<div class=" row">
						<div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(Party Details Report)</h4></div> 
						<div class="col">
							<p class="mb-0 text-right"><b class="bolt"></b> <span class="badge badge-info">All India</span> &nbsp;&nbsp; <b></b> 
							</p>
							<p class="mb-0 text-right">
							<a href="PartyDetailsReportPDFVol2" target="_blank" class="btn show pdfbut"><img src="/assets/images/pdf.png" style="width: 53px !important;"></a>
							<a href="PartyDetailsReportXlsVol2" target="_blank" class="btn  show pdfbut"><img src="/assets/images/excel.jpg" style="position: relative; top: -3px; width: 61px !important;display: table-row;"></a>
							</p>
						</div>
					</div>
				</div>
				
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Party Type</th>
                            <th>Abbreviation</th>
                            <th>Party Symbol</th>
                            <th>Party</th>
                        </tr>

                       
                    </thead>


                    <tbody>

                        <?php $i = 1;  //print_r($partyDetailData);?>
                        @foreach ($partyDetailData as $key => $row)
                                                
                        <tr>
                            @if($key=='N')
                            <th colspan="4">National Parties</th>
                            @elseif($key=='S')
                            <th colspan="4">State Parties</th>
                            @elseif($key=='U')
                             <th colspan="4">Registered(unrecognised) Parties</th>
                            @elseif($key=='Z1')
                            <th colspan="4">Nota</th>
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

                    </tbody>
               </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
