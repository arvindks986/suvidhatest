@extends('admin.layouts.pc.theme')
@section('content')
<main role="main" class="inner cover mb-3">
	<section>
		<div class="container-fluid mt-3">
			<div class="row">
				<div class="card text-left" style="width:100%; margin:0 auto;">
					<div class=" card-header">
						<div class=" row">
							<div class="col">
								@if ( $currentUserRole == 4 )
									<h4> List of {{ ( ( $typ == 'ac' ) ? "Assembly Constituencies (AC's)" : "Parliament Constituencies (PC's)" ) }} in {{ $states['ST_NAME'] }}</h4>
								@else 
									<h4> State-wise {{ ( ( $typ == 'ac' ) ? "Assembly Constituencies (AC's)" : "Parliament Constituencies (PC's)" ) }}</h4>
								@endif
							</div> 
							<div class="col">
								<p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
									<span class="badge badge-info"></span>&nbsp;&nbsp;
									<a href="javascript://" onClick="getReport();" title="Click here to Download the below displaying data in CSV format" class="btn btn-info" role="button">Export Data in CSV</a> &nbsp;&nbsp;
									<button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
								</p>
							</div>
						</div>
					</div>
					<div class="card-body">  
						<div class="row {{ ( ( $currentUserRole == 4 ) ? '' : 'acpc-cls-1' ) }}" style="padding-left: 250px;">
							<!--@if ( $currentUserRole == 4 )
								<div class="col-md-12" id="acpc_msg"><h6>Please select Type to proceed.</h6></div>
							@else 
								@if ( $typ == 'ac' )
									<div class="col-md-12" id="acpc_msg"><h6>Please select State to get all the AC's. </h6></div>
								@else
									<div class="col-md-12" id="acpc_msg"><h6>Please select State to get all the PC's. </h6></div>
								@endif
							@endif -->
							<div class="col-md-12" id="mappingButton" style="display:none;"></div>
							<div class="col-md-6">
								@if ( $currentUserRole == 4 )
									<input type="hidden" class="form-control" name="s_t" id="s_t" value="{{ $states['ST_CODE'] }}" />
								@else 
									<select name="s_t" id="s_t" class="form-control" onChange="removeBorders();">
										<option value="">Select State</option>
										<option value="all">All States</option>
										@if ( isset($states) && count($states) > 0 )
											@foreach( $states as $k => $v )
												<option value="{{ $v['ST_CODE'] }}">{{ $v['ST_NAME'] }}</option>
											@endforeach
										@endif
									</select>
								@endif
								@if ( $typ == 'ac' )
									<input type="hidden" class="form-control" name="t_p" id="t_p" value="ac" />
								@else
									<input type="hidden" class="form-control" name="t_p" id="t_p" value="pc" />
								@endif
							</div>
							@php $styl = "display:none"; @endphp
							<div class="col-md-6" id="fnl-btn-1" style='{{ ( ( $currentUserRole == 4 ) ? $styl : "" ) }}'>
								<a href="javascript://" title="Search" class="smt-btn btn btn-sm" id="sbmt-lst-btn" onClick="getData();"><i class="fa fa-search" aria-hidden="true"></i></a>
								<a href="javascript://" title="Reset your Search" class="reset-btn btn btn-sm" id="reset-btn" onClick="resetData('{{$currentUserRole}}');"><i class="fa fa-refresh" aria-hidden="true"></i></a>
							</div>
							<div class="col-md-12" id="errMsg" style="display:none;"><small>Please select State to proceed.</small></div>
						</div>
						<div class="col-md-12" id="loader-div"></div>
						<div class="col-md-12" id="count-div"></div>
						<table id="ac_pc_table" class="table table-striped table-bordered table-hover" style="width:100%"></table>
					</div>
				</div>
			</div>
			<div id="acListModal" class="modal fade" tabindex="-1" data-backdrop="static">
				<div class="modal-dialog">
					<div class="modal-content" id="acContent">
					</div>
				</div>
			</div>
		</div>
	</section>
</main>
<link rel="stylesheet" href="{{ asset('assets/css/ac_pc.css') }}" />
<script type="text/javascript" src="{{ asset('assets/js/ac_pc.js') }}"></script>
@if ( $currentUserRole == 4 )
	<script>
		$(document).ready(function(){
			$('#sbmt-lst-btn').trigger('click');
		});
	</script>
@endif
@endsection