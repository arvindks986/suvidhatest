@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
<?php
	 $st_code=!empty($st_code) ? $st_code : '';
     $cons_no=!empty($cons_no) ? $cons_no : '';
     $st=getstatebystatecode($st_code);
     $pcdetails=getpcbypcno($st_code,$cons_no); 
     $stateName=!empty($st) ? $st->ST_NAME : 'ALL';
     $pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
?> 
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col"><h2 class="mr-auto">Pending At CEO : {{$count}}</h2></div> 
                <div class="col"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>PC:</b> <span class="badge badge-info">{{ $pcName}}</span>
                        <b></b> <!--<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>-->
						<b></b><a href="{{url('/eci-expenditure/statusExpdashboard')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>


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
									    <th>State</th>
                                        <th>PC No & Name</th>
                                        <th>Candidate Name</th>
                                        <th>Party Name</th>
                                        <th>Last Date Of Lodging</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <?php $j = 0; ?>
                                @if(!empty($finalbyceoCandList))
                                @foreach($finalbyceoCandList as $candDetails)  
                                <?php
                                $pc = getpcbypcno($candDetails->ST_CODE, $candDetails->constituency_no);
                                $date = new DateTime($candDetails->created_at);
                                //echo $date->format('d.m.Y'); // 31.07.2012
                                $lodgingDate = $date->format('d-m-Y'); // 31-07-2012
								 $stDetails=getstatebystatecode($candDetails->ST_CODE);
                                $j++;
                                ?>
                                <tr>
								    <td>@if(!empty($stDetails->ST_NAME)) {{ $stDetails->ST_NAME}} @endif</td>
                                    <td>@if(!empty($pc->PC_NO))  {{ $pc->PC_NO}}-{{ $pc->PC_NAME}} @endif</td>
                                    <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                                    <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                                   <td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '22-06-2019'}} @endif</td>

                                    <td>  @if($candDetails->final_by_ro==1)
                <a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a> 
                @endif</td>
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
                <!-- <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">

                        <div class="card-body"  class="collapse show">
                            @if($count>0)
                            <div id="barchart"></div>
                            @else
                            No data for graph. 
                            @endif
                        </div>
                    </div>
                </div> -->
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
                return 'finalbyceo-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>' 
              },
            }],
           
         
      
    });
  })
  </script>
@endsection


