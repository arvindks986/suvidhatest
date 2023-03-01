@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content')
@php
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $pcdetails=getpcbypcno($st_code, $cons_no); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
// echo $st_code.'cons_no=>'.$cons_no;

@endphp

 
 
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
      <div class="container-fluid">
        <div class=" row">
          <div class="col-md-5 mt-2 mb-2"><h5 class="mr-auto">Candidate List Pending At ECI : {{$count}}</h2></div> 
            <div class="col-md-7 mt-2 mb-2 text-right"><p class="mb-0"><b>State Name:</b> 
	            <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
	            <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
	            <b>PC:</b> <span class="badge badge-info">{{ $pcName}}</span>
	            <a href="{{url('/eci-expenditure/pendingateciPDF')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
	            <a href="{{url('/eci-expenditure/pendingateciEXL')}}/{{base64_encode($st_code)}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
	            <b></b><a href="{{url('/eci-expenditure/mis-officer/')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a></p></div>
            </div> <!-- end row -->
        </div>
  </section>

<section class="mt-5">
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <div class="card text-left" style="width:100%;">
                <div class="card-body" id="demo" class="collapse show">
				 <div class="clearfix"></div>
                    <table id="example1" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
							    <th>State</th>
                                <th>PC No & Name</th>
                                <th>Candidate Name</th>
                                <th>Party Name</th>
                                <th>Last Date of Submission</th>
								<th>Date of Received by ECI</th>
								<!--<th>Date of Lodging A/C By Candidate</th>-->
								<!--<th>Date of Sending to the CEO</th>-->
								<!-- <th>Date of Receipt By CEO</th> -->
								<th class="width-150">Action</th>
                            </tr>
                        </thead>
                        <?php $j = 0; ?>
                        @if(!empty($pendingateciCandlist))
                        @foreach($pendingateciCandlist as $candDetails)  
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
                            <td>@if(!empty($pc->PC_NO))  {{ $pc->PC_NO }}-{{ $pc->PC_NAME}} @endif</td>
                            <td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
                            <td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
                           <td>@if(!empty($candDetails->last_date_prescribed_acct_lodge)) {{ date('d-m-Y',strtotime($candDetails->last_date_prescribed_acct_lodge))}}  @else {{ '22-06-2019'}} @endif</td>
							<td>@if(!empty($candDetails->date_of_receipt_eci)) {{ date('d-m-Y',strtotime($candDetails->date_of_receipt_eci))}}  @else {{ 'N/A'}} @endif</td>
							<!--<td>@if(!empty($candDetails->date_orginal_acct)) {{ date('d-m-Y',strtotime($candDetails->date_orginal_acct))}} @else {{ 'N/A'}} @endif</td>-->
							<!--<td>@if(!empty($candDetails->date_of_sending_deo)) {{  date('d-m-Y',strtotime($candDetails->date_of_sending_deo))}} @else {{ 'N/A'}} @endif</td>-->
							<!-- <td>@if(!empty($candDetails->date_of_receipt) && ($candDetails->date_of_receipt !='0000-00-00')) {{ date('d-m-Y',strtotime($candDetails->date_of_receipt))}}  @else {{ 'N/A'}} @endif</td> -->
                            <td>  @if(($candDetails->final_by_ro=='1'))
							<a href="{{url('/')}}/eci-expenditure/printScrutinyReport/{{base64_encode($candDetails->candidate_id)}}" class="btn btn-primary btn-sm width-75" target="_blank">Report</a>  @endif
							 <a href="javascript:void(0)" class="btn btn-info btn-sm width-75"
							 onclick="showTracking({{($candDetails->candidate_id)}})" >Tracking</a> 
						  </td>
                        </tr>
                        @endforeach 
                        @endif 
						<tbody></tbody>
                    </table>
				</div>
            </div>
            <!--END OF SELECT CANDIDATE-->
        </div>
<!--Start Of Tracking Div-->	
<?php 
/*
 global $CandidatStatus;
if(isset($_GET['candidate_id']) ){  echo 'byee';
   echo $candidate_id = $_GET['candidate_id'];
	$CandidatStatus = DB::table('expenditure_reports')
	->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
	->leftjoin('candidate_nomination_detail', 'candidate_nomination_detail.candidate_id', '=', 'expenditure_reports.candidate_id') 
	->join('m_party', 'candidate_nomination_detail.party_id', '=', 'm_party.CCODE')  
	->where('candidate_nomination_detail.application_status','=','6')
	->where('candidate_nomination_detail.finalaccepted','=','1')
	->Where('candidate_personal_detail.cand_name', '<>', 'NOTA')
	->where('expenditure_reports.candidate_id',$candidate_id)
	->groupBy('expenditure_reports.candidate_id')
	->get();
	
   }
*/
?> 
<div class="col-lg-4 col-md-4 col-sm-4 menu1" style="">
	<div class="card" id="showTracking" style="display: none;">
		<!--<div class="scroll-tracks">
		<div class="bs-vertical-wizard">
		<p class="text-left h6 pb-3 pt-4 Orange_text" style="margin-left: -50px;"><strong>Tracking Module For Expenditure (PC)</strong></p>
		<div class="clearfix"></div>
			<ul>
				<li class="complete">
					<a href="#">
					<i class="ico ico-green">RO</i> 									
					<span>
						<div class="contentBox">
							<div class="date h6 text-success"><strong>Finalize: @if(!empty($CandidatStatus->finalized_date)) {{ date('d-m-Y',strtotime($CandidatStatus->finalized_date))}}  @else {{ 'N/A'}} @endif</strong></div>
							<p class="graySquire"> Account Loged By Candidate : @if(!empty($CandidatStatus->date_original_acct)) {{ date('d-m-Y',strtotime($CandidatStatus->date_original_acct))}}  @else {{ 'N/A'}} @endif </p>
							<p class="greenSquire">Scrutiny Submit Date : </p>
							<p class="yellowSquire">Received Reports CEO</p>	
						</div>							
					</span>
					</a>
					<p class="dateleft">0 - 38&nbspDays</p>									
					<div class="clearfix"></div>	
				</li>

				<li class="complete prev-step">
					<a href="#"> 
					<i class="ico ico-green">CEO</i>
						<span class="desc">	
						<div class="contentBox">
							<div class="date h6 text-success"><strong>Date: 23/05/2019</strong></div>
							<p class="graySquire"> Yet not Officer to CEO</p>
							<p class="greenSquire">Sent 80% Report CEO</p>
							<p class="yellowSquire">Received Reports CEO</p>	
						</div>
						</span>
					</a>
					<p class="dateleft">0 - 38&nbspDays</p>
				</li>								
				<li class="current">
					<a href="#">
					<i class="ico ico-green">ECI</i> 
						<span class="desc">										
							<div class="contentBox">
							<div class="date h6 text-warning"><strong>Date 23/05/2019</strong></div>
							<p class="graySquire"> Yet not Officer to CEO</p>
							<p class="greenSquire">Sent 80% Report CEO</p>
							<p class="yellowSquire">Received Reports CEO</p>	
						</div>								
						</span>										
					</a>
					<p class="dateleft">0 - 38&nbspDays</p>		
				</li>
				<li class="pending">
					<a href="#">
					<i class="ico ico-green">Action</i>
						<span class="desc">										
							<div class="contentBox">
							<div class="date h6 text-secondary"><strong>Date 23/05/2019</strong></div>
							<p class="graySquire"> Yet not Officer to CEO</p>
							<p class="greenSquire">Sent 80% Report CEO</p>
							<p class="yellowSquire">Received Reports CEO</p>	
						</div>	
						</span>
					</a>
					<p class="dateleft">0 - 38&nbspDays </p>		
				</li>
				<li class="pending">
					<a href="#">
					<i class="ico ico-green ptop">Close</i>
						<span class="desc">										
							<div class="contentBox">
							<div class="date h6 text-secondary"><strong>Date 23/05/2019</strong></div>
							<p class="graySquire"> Yet not Officer to CEO</p>
							<p class="greenSquire">Sent 80% Report CEO</p>
							<p class="yellowSquire">Received Reports CEO</p>	
						</div>	
						</span>
					</a>
				</li>                     
			   <!--<li class="locked">
					<a href="#">Locked <i class="ico fa fa-lock ico-muted"></i>
						<span class="desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, cumque.</span>
					</a>
				</li>
				<li class="locked">
					<a href="#">Images <i class="ico fa fa-lock ico-muted"></i>
						<span class="desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, cumque.</span>
					</a>
				</li>-->
			</ul>
		</div>
		</div>
		</div>
    </div>
</div></div>
<!--End Of Tracking Div-->
</section>
</main>

<script type="text/javascript">
	function showTracking(candidate_id){
		 $('#showTracking').css('display','block');
		var candidate_id = candidate_id;
		//alert(candidate_id);
		 $.ajax({
			url: '<?php echo url('/') ?>/eci-expenditure/getCandTracking/'+candidate_id,
            type: 'GET',
           // data: { _token: '{{csrf_token()}}' },
		    success: function(response){
			// Code
			var html = '';
			//console.log(response);
			$('#showTracking').html(response);
		}
		});
	}
</script>
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
                return 'pendingateci-report';    
              },
             title: function() {
                  return '<?php echo 'State Name:'.$stateName.'   PC:'.$pcName.''; ?>'
              },
            }],
           
         
      
    });
  })
  </script>
@endsection
