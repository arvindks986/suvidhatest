@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
<?php
	 $st_code=!empty($st_code) ? $st_code : '0';
     $cons_no=!empty($cons_no) ? $cons_no : '0';
     $st=getstatebystatecode($st_code);
     $pcdetails=getpcbypcno($st_code,$cons_no); 
     $stateName=!empty($st) ? $st->ST_NAME : 'ALL';
     $pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
?> 
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col-md-5 mt-2 mb-2"><h5 class="mr-auto">Closed/Case Dropped : {{$count}}</h5></div> 
                <div class="col-md-7 mt-2 mb-2 text-right"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>PC:</b> <span class="badge badge-info">{{ $pcName}}</span>
                      <a href="{{url('/eci-expenditure/finalbyeciPDF2014')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/eci-expenditure/finalbyeciEXL2014')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                        <b></b><a href="{{url('/eci-expenditure/mis-officer2014')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>
                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">
                        <!--SELECT CANDIDATE-->
                        <div class="card-body" id="demo" class="collapse show">  
                            <table id="example1" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>PC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                        <th>Last Date of Submission</th>
                                        <th>Date of Scrutiny Report Submission</th>
                                        <th>Date of Lodging A/C By Candidate</th>
                                        <th>Date of Sending to the CEO</th>
                                        <th>Date of Receipt By CEO</th>
                                    </tr>
                                </thead>
                                <?php $j = 0; ?>
                                @if(!empty($getcandidateListfinalbyECI))
                                @foreach($getcandidateListfinalbyECI as $candDetails)  
                                <?php
                             //  dd($candDetails);
                                $pc = getpcbypcno($candDetails->st_code, $candDetails->pc_no);
                                $date = new DateTime($candDetails->created_at);
                                //echo $date->format('d.m.Y'); // 31.07.2012
                                $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
								$report_submitted_data = new DateTime($candDetails->report_submitted_date);
							   //echo $date->format('d.m.Y'); // 31.07.2012
							   $report_submitted_date=$report_submitted_data->format('d-m-Y'); // 31-07-2012
                                $j++;
                                ?>
                                <tr>
                                    <td>@if(!empty($pc->PC_NO))  {{ $pc->PC_NO}}-{{ $pc->PC_NAME}} @endif</td>
                                    <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                                    <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                    <td>16-06-2014</td>
<td>@if(!empty($candDetails->report_submitted_date)) {{ date('d-m-Y',strtotime($report_submitted_date))}}  @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($lodgingDate) && $lodgingDate > '04-12-2019') {{ $lodgingDate}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_sending_deo)) {{  date('d-m-Y',strtotime($date_of_sending_deo))}} @else {{ 'N/A'}} @endif</td>
<td>@if(!empty($candDetails->date_of_receipt) && ($candDetails->date_of_receipt !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->date_of_receipt))}}  @else {{ 'N/A'}} @endif</td>                         
</tr>
                                @endforeach 
                                @endif 
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--END OF SELECT CANDIDATE-->
                </div>
            </div>
        </div>
    </section>
</main>
 <script  src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
$(document).ready(function() {
    var table = $('#example1').DataTable({   
     dom: 'lBfrtip', 
     lengthMenu: [ [10, 50, 100, -1], [10, 50, 100, 'All'] ],
     pageLength: 10,
     buttons: [
            {
                extend: 'pdfHtml5',               
                pageSize: 'LEGAL',
               filename: function() {
                return 'finalbyeci-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection


