@extends('admin.layouts.pc.dashboard-theme') 
@section('content')
<style type="text/css">
    .loader {
        position: fixed;
        left: 50%;
        right: 50%;
        border: 16px solid #f3f3f3;
        /* Light grey */
        border-top: 16px solid #3498db;
        /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        z-index: 99999;
    }
    
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    
    #acViewBody a {
        text-decoration: none !important;
        color: #000 !important;
        cursor: default !important;
    }
    
    #acViewBody a:hover {
        text-decoration: none !important;
        color: #000 !important;
        cursor: default !important;
    }
</style>
<div class="loader" style="display:none;"></div>
<section class="statistics color-grey pt-4 pb-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9 pull-left">
                <h4>{!! $heading_title !!}</h4>
            </div> 	
            <div class="col-md-3  pull-right text-right">
                <span class="report-btn" id="export-csv-btn"><a class="btn btn-primary" href="{{url('pcceo/schedule-report-excel')}}/{{$urlexcel}}" title="Download Excel" target="_blank">Export Excel</a></span>
                <span class="report-btn" id="export-pdf-btn"><a class="btn btn-primary" href="{{url('pcceo/schedule-report-pdf')}}/{{$urlpdf}}" title="Download PDF" target="_blank">Export PDF</a></span>
            </div>

        </div>
    </div>
</section>
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
	        <span class="pull-right" style="margin-right: 10px;">
            <span><b>AC:</b></span>
            <span class="badge badge-info">@if($ac_no!=0) {{getacbyacno($state,$ac_no)->AC_NAME}} @else All Selected @endif</span>
            </span>
	        <span class="pull-right" style="margin-right: 10px;">
            <span><b>PC:</b></span>
            <span class="badge badge-info">@if($pc_no!=0) {{getpcbypcno($state,$pc_no)->PC_NAME}} @else All Selected @endif</span>
            </span>
      </div>
    </div>
  </div>
</section>

<section class="dashboard-header section-padding">
    <div class="container-fluid">
        <form  action="{{url('pcceo/schedule-report')}}" class="row" method="POST" >
		{{csrf_field()}}
			<input type="hidden" name="state_code" id="state_code" value="{{$state}}"> 
            <div class="form-group col-md-3">
                <label>Select PC <span style="color:red">*</span></label>
                <select name="pc_no" id="pc_no" class="form-control" required onchange="getACList(this.value);">
                    <option value="">Select PC</option>
					<option value="0" @if(isset($pc_no) && $pc_no!="") selected @endif>Select All</option>
					@foreach($data['m_pc'] as $data)
                    <?php $d=getpcbypcno($state,$data->CONST_NO); ?>
                    <option value="{{$d->PC_NO}}" @if(isset($pc_no) && $pc_no==$d->PC_NO) selected @endif>{{$d->PC_NO}} -{{$d->PC_NAME}} </option>
					@endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Select AC <span style="color:red">*</span></label>
                <select name="ac_no" id="ac_no" class="form-control">
                     @if(isset($pc_no) && $pc_no!="")
					   <?php echo getAcListDropdown($state,$pc_no,$ac_no) ?>
				     @else
						<option value="">Select AC</option> 
				     @endif
                </select>
				<div id="show_ac_list" style="display:inline"></div>
				</div>
			 <div class="form-group col-md-3">
                <button type="submit" name="search" id="submit-report" class="btn btn-success" style="margin-top:31px;">Search</button>
            </div>
        </form>
    </div>
</section>
<div class="container-fluid">
    <!-- Start parent-wrap div -->
    <div class="parent-wrap">
        <!-- Start child-area Div -->
        <div class="child-area">
            <div class="page-contant">
                <div class="random-area">
                    <br>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th> S.No </th>
                                    <th> PC No & Name</th>
                                    <th> AC No & Name</th>
                                    <th> Total Scheduled Rounds</th>
                                    <th> Total Completed Rounds</th>
                                    <th> Total Pending Rounds</th>
                                </tr>
                            </thead>
                            <tbody>
							
							@if(count($result)>0)
							 @php $i=1; @endphp
							 @foreach($result as $data)
							<?php 
 							 $totalScheduled=$data->S_ROUND; 
							 $completedRound=completeRound($data->STATE,$data->PC_NO,$data->AC_NO)
							 ?>
								<tr>
                                    <td><span>{{$i}}</span></td>
                                    <td><span>{{$data->PC_NO}} -{{getpcbypcno($data->STATE,$data->PC_NO)->PC_NAME}}</span></td>
                                    <td><span>{{$data->AC_NO}} -{{getacbyacno($data->STATE,$data->AC_NO)->AC_NAME}}</span></td>
									<td><span>{{$totalScheduled}}</span></td>
									<td><span>{{$completedRound}}</span></td>
                                    <td><span><?php echo $totalScheduled-$completedRound; ?></span></td>
                                </tr>
								@php $i++ @endphp
								@endforeach
								@else
								<tr >
                                    <td colspan="8" style="text-align:center">--No Record Found--</td>
                                </tr>
								@endif
                            </tbody>
                        </table>
                    </div>
                    <!-- End Of  table responsive -->
                </div>
            </div>
            <!-- End Of random-area Div -->

        </div>
        <!-- End OF page-contant Div -->
    </div>
</div>
<!-- End Of parent-wrap Div -->
</div>
<script>
  function getACList(pc_no){
       var state= $('#state_code').val();
        jQuery.ajax({
          type: "GET",
          url: "<?php echo url('/'); ?>/pcceo/pc-by-ac/"+encodeURI(state)+'/'+encodeURI(pc_no),
          dataType: "html",
          success: function (response) { 		 
          jQuery("#show_ac_list").show();   
          jQuery("#ac_no").hide();   
          jQuery('#show_ac_list').html(response);	
          },
          error: function (xhr, ajaxOptions, thrownError) {
          alert(thrownError);
          }
      });
  }

 
	</script>
	
	
	
@endsection