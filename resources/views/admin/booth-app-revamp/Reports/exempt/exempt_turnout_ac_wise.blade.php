@extends('admin.layouts.ac.theme')
@section('content')
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

<div class="loader" style="display:none;"></div>

@include('admin/common/form-filter')

<section class="statistics color-grey pt-4 pb-2">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-9 pull-left">
				<h4>{!! $heading_title !!}</h4>
			</div>

			<div class="col-md-3 pull-right text-right">

				@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach

			</div>
		</div>
	</div>  
</section>
@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        @foreach($filter_buttons as $button)
            <?php $but = explode(':',$button); ?>
            <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>

            </span>
            
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif



@if(Session::has('flash-message'))
@if(Session::has('status'))
<?php
$status = Session::get('status');
if($status==1){
	$class = 'alert-success';
}
else{
	$class = 'alert-danger';
}
?>
@endif
<div class="alert <?php echo $class; ?> fade in">
	<a href="#" class="close" data-dismiss="alert">&times;</a>
	{{ Session::get('flash-message') }}
</div>
@endif


<div class="container-fluid">
	<!-- Start parent-wrap div -->  
	<div class="parent-wrap">
		<!-- Start child-area Div --> 
		<div class="child-area">
			<div class="page-contant">
				<div class="random-area">


					<div class="table-responsive">
						<table align="center" class="table table-bordered  poll-table" data-page-length='50' id="list-table">
							<thead class="sticky">
								<tr class="turnoutbg">
									<td colspan="5" style="border-color: #49a8a4;">{!! $heading_title !!}</td>
									<td colspan="5" style="border-color: #49a8a4;" align="right"><span id="poll_turnout_percentage">{{$poll_turnout_percentage}}</span>%</td>
								</tr>

								<tr>
									
									<th style="background:#6ccac6;">AC No & Name</th>
									<th style="background:#6ccac6;">Total PS</th>
            <th style="background:#6ccac6;">Total Exempted PS</th>
									<th style="background:#6ccac6;">Round1 (Poll Start to 9:00 AM)</th>
									<th  style="background:#6ccac6;">Round2  (Poll Start to 11:00 AM)</th>
									<th style="background:#6ccac6;">Round3  (Poll Start to 1:00 PM)</th>
									<th  style="background:#6ccac6;">Round4  (Poll Start to 3:00 PM)</th>
									<th  style="background:#6ccac6;">Round5  (Poll Start to 5:00 PM)</th>
									<th  style="background:#6ccac6;">Latest Updated Poll</th>

								</tr>
							</thead>



							<tbody id="voter_turnouts">
								@if(count($results)>0)

								@foreach($results as $result) 
								<tr>
									<td width="40%"><a href="<?php echo $result['href'] ?>"><span>{{$result['label']}}</span></a></td>
									<td><a href="<?php echo $result['href'] ?>"><span>{{$result['total_ps']}}</span></a></td>
									<td><a href="<?php echo $result['href'] ?>"><span>{{$result['total_exempted_ps']}}</span></a></td>

						<td><a href="<?php echo $result['href'] ?>"><span>{{$result['round_1_total']}}</span></a></td>
						<td><a href="<?php echo $result['href'] ?>"><span>{{$result['round_2_total']}}</span></a></td>
						<td><a href="<?php echo $result['href'] ?>"><span>{{$result['round_3_total']}}</span></a></td>
						<td><a href="<?php echo $result['href'] ?>"><span>{{$result['round_4_total']}}</span></a></td>
						<td><a href="<?php echo $result['href'] ?>"><span>{{$result['round_5_total']}}</span></a></td>
						<td align="center"><a href="<?php echo $result['href'] ?>"><span>{{$result['rounds_total']}}</span></a></td>
								</tr>
								@endforeach
								@else
								<tr align="center"><td colspan="11">No Record</td></tr>
								@endif
							</tbody>
						</table>
					</div><!-- End Of  table responsive -->  
				</div><!-- End Of intra-table Div -->   


			</div><!-- End Of random-area Div -->

		</div><!-- End OF page-contant Div -->
	</div>      
</div><!-- End Of parent-wrap Div -->
</div> 


@endsection

@section('script')

@endsection