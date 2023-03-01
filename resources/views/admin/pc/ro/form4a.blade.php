@extends('admin.layouts.pc.dashboard-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Form 3A')
@section('content')

<?php
$st=getstatebystatecode($ele_details->ST_CODE);  
$pc=getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO); 
$url = URL::to("/"); $j=1;
//dd($user_data);
?>
<style type="text/css">
  .loader {
   position: fixed;
   left: 50%;
   right: 50%;
   border: 16px solid #f3f3f3; /* Light grey */
   border-top: 16px solid #3498db; /* Blue */
   border-radius: 50%;
   width: 120px;
   height: 120px;
   animation: spin 2s linear infinite;
   z-index: 99999;
  }
    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
  </style>
<main role="main" class="inner cover mb-3">
	<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
              <div class=" card-header">
                <div class=" row">
                   <div class="col"><h2 class="mr-auto">Form-4A List </h2></div> 
                   <div class="col"><p class="mb-0 text-right">
												<b>State Name:</b> 
												<span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; 
												<b>PC:</b><span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp; 
												<b></b> <span class="badge badge-info"></span>
									  </p></div>
										</div><!-- end row-->
	              </div><!-- end card-header-->

  <div class="loader" style="display:none;"></div>
  <section class="dashboard-header section-padding">
	<div class="container-fluid">
	    <form id="generate_report_id" class="row" method="post" action="#">
      <input type="hidden" name="st_code" id="st_code" value="{{$user_data->st_code }}">
      <input type="hidden" name="pc_no" id="pc_no" value="{{$user_data->pc_no }}">
      {{ csrf_field() }} 
		<div class="form-group col-md-6">
         <label>Datewise Filter</label> &nbsp; 
         <input value="" id="date_range" name="date_range" type="text" class="ranges form-control" placeholder="Date Range" />
		</div>
         <div class="form-group col-md-6"> <label> </label> 
         <p class="mb-0 text-right"><span class="report-btn" id="export-btn">
		 <!--<a class="btn btn-primary" href="<?php echo url('/') ?>/ropc/form3apdfview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export PDF</a>-->
		 </span>
         </p> </div>
        </form> 
	</div>
</section>
                
   
<div class="card-body">  
  <div class="table-responsive" id='showrecord'>
      <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>S.No</th>
          <th>Candidate Name</th>
          <th>Father/Mother/Husband </th>
          <th>Age</th>
          <th>Address</th>
          <th>Party Affilation</th>
        </tr>
        </thead>
        <tbody id="oneTimetab">	
    @if(!empty($form3alist))
      @foreach($form3alist as $listform3a)
			<?php
        // dd($listform3a);
        $canddetailsArray=\app(App\adminmodel\CandidateModel::class)->where(['candidate_id' =>$listform3a->candidate_id])->get();
        $nominationArray=\app(App\adminmodel\CandidateNomination::class)->where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['candidate_id' =>$listform3a->candidate_id])->get();
         // dd($nominationArray);
				?>
<tr>
  <td>{{$j++}}</td>
  <td>@if(!empty($canddetailsArray[0]->cand_name)) {{$canddetailsArray[0]->cand_name}} @endif </td>
  <td>@if(!empty($canddetailsArray[0]->candidate_father_name)) {{$canddetailsArray[0]->candidate_father_name}} @endif </td>
  <td>@if(!empty($canddetailsArray[0]->cand_age)) {{$canddetailsArray[0]->cand_age}} @endif</td>
  <td>@if(!empty($canddetailsArray[0]->candidate_residence_address)) {{$canddetailsArray[0]->candidate_residence_address}} @endif</td>
  <td>@if(!empty($listform3a->party_id)) {{$listform3a->party_id}} @endif</td>
  </tr>

@endforeach 
@endif 
</tbody>
            
            </table>
           </div> <!-- end responcive-->
          </div> <!-- end card-body-->
        </div>
      </div>
     </div>
   	</div>
  </section>
	</main>
  @section('script') 
@endsection



<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function() {
    jQuery('#date_range').daterangepicker({
			startDate: moment().subtract(2, 'month'),
			endDate: moment(),
       ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          //  'Last 14 Days': [moment().subtract(13, 'days'), moment()] ,          
          //  'This Month': [moment().startOf('month'), moment().endOf('month')],
          //  'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        maxDate: new Date()
});
});  
   
jQuery(document).on('click', '.ranges, .applyBtn', function(e){
    var val=  jQuery('#date_range').val();
      //alert(val);
      getDataByTimeInterval(val);
    });
 
 function getDataByTimeInterval(val){   
   // alert(timeInterval);
    var getTimeName='';  
    // var constituency = $('#constituency').val();
    var timeInterval= val.split('-'); 
    if(timeInterval[0]!='' && timeInterval[1]!=''){
        var from_date = moment(timeInterval[0]).format('DD-MM-YYYY');
        var to_date = moment(timeInterval[1]).format('DD-MM-YYYY');
        // alert(to_date);
        if(from_date!='' && to_date!=''){  
            if(from_date==to_date) {
              var dtStr = from_date+'~'+to_date;
              getTimeName ='Report of: '+from_date;
            }else {
              var dtStr = from_date+'~'+to_date;
              getTimeName ='Report From: '+from_date+' To: '+to_date;
            }
       }
    }
     var st_code =  jQuery('input[name="st_code"]').val();
     var pc_no =   jQuery('input[name="pc_no"]').val();
     //alert(st_code);   
    // jQuery(".loader").show();
    // alert(to_date);    
    if(from_date!='' && to_date!=''){ //alert(from_date);  alert(to_date); 
        jQuery.ajax({
            type:'POST',
            url:'<?php echo url('/') ?>/ropc/datewiseform4areport', //Make sure your URL is correct
            data:{'_token':'<?php echo csrf_token() ?>', 'from_date': from_date,'to_date': to_date,'st_code': st_code,'pc_no': pc_no},
            success:function(data){         
                //alert('FinalResponse->'+data); 
                //alert(getTimeName);
                var exportpdfbtn = '<span class="report-btn" id="export-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/ropc/form4apdfview/'+btoa(dtStr)+'/'+btoa(pc_no)+'" title="Download to PDF" target="_blank">Export PDF</a></span>';
               // var exportcsvbtn = '<span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="<?php echo url('/') ?>/ropc/reportexcelview/'+btoa(dtStr)+'/'+btoa(caseType1)+'" title="Download to PDF" target="_blank">Export Excel</a></span>';
               //  console.log(data);
                jQuery('#export-btn').html(exportpdfbtn);
               // jQuery('#export-csv-btn').html(exportcsvbtn);
                jQuery('#oneTimetab').html(data);
               // jQuery('#timeRange').html(getTimeName);
               // jQuery('#caseType').html(getcaseType);
                //jQuery(".loader").hide();  
            },error:function(data){ 
               // console.log(data);  
               return 'Error';    
                //alert('Error !!');
            }
    });
    
    }else{alert('Please select time interval.')}
} 

  </script>
  
@endsection