@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Polling Station Details')
@section('content')

@if ($errors->any())
<div class="alert  alert-warning alert-dismissible fade show" role="alert">
  @foreach ($errors->all() as $error)
  <span>
    <p>{{ $error }}</p>
  </span>
  @endforeach
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if (session('success'))
<div class="alert  alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif


@if (session('error') && !is_array(session('error')))
<div class="alert alert-danger">{{ session('error') }}</div>
@elseif(session('error') && is_array(session('error')))
@foreach(session('error') as $error)
<div class="alert alert-danger"><strong>Error:</strong> Unable to Import Excel because In row {{ $error->row()}} {{ $error->errors()[0] }}</div>
@endforeach
@elseif(session('error'))
<div class="alert  alert-danger alert-dismissible fade show" role="alert">
  {{ session('error') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<style>
  .disabled{
    cursor: not-allowed;
    color: #000;
  }
</style>
<section class="dashboard-header section-padding">
	<div class="container-fluid">
		<form class="row" method="get" action="{{url('pcceo/PcCeoPSElectoralDefinalzied')}}">
			<?php if (isset($phases) && count($phases) > 0) { ?>
				<div class="form-group col-md-3"> <label>Election Phase</label>
					<select name="phase" id="phase" class="form-control" onchange="clearpcs()">
						@foreach($phases as $result)
							@if($phase==$result->PHASE_NO)
								<option value="{{$result->PHASE_NO}}" selected="selected">{{$result->PHASE_NO}}-Phase</option>
							@else
								<option value="{{$result->PHASE_NO}}">{{$result->PHASE_NO}}-Phase</option>
							@endif
						@endforeach
					</select>
				</div>
        <div class="form-group col-md-3"> <label>PC</label>
					<select name="pc" id="pc" class="form-control">
            <option value="" >All PC</option>
						@foreach($pcs as $p)
							@if($pc==$p->PC_NO)
								<option value="{{$p->PC_NO}}" selected="selected">{{$p->PC_NO}}-{{$p->PC_NAME}}</option>
                @else
								<option value="{{$p->PC_NO}}">{{$p->PC_NO}}-{{$p->PC_NAME}}</option>
							@endif
						@endforeach
					</select>
				</div>
			<?php } else { ?>
				<input type="hidden" id="phase" name="phase" value="{!! $phase !!}">
			<?php } ?>
      <div class="form-group col-md-2"> 
        <label>&nbsp;</label>
        <button type="submit" class="btn btn-success"  style="width:100%">Submit</button>
      </div>
    </form>
  </div>
</section>
<section class="statistics color-grey pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8 pull-left">
        <h4>{!! $heading_title !!}</h4>
      </div>
      @if(count($results) > 0)
      <div class="col-md-4 pull-right text-right">
        <span class="report-btn"><a class="btn btn-warning" href="{{url('pcceo/PcCeoPSElectoralDefinalzied?excel=download')}}{{($phase) ? '&phase='.$phase : ''}}{{($pc) ? '&pc='.$pc : ''}}" title="Export in Excel">Export in Excel</a></span>
      </div>
      @endif
      </div>
      <div class="col-md-12">
        <div class="table-responsive">

          <table id="data_table_table" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th> S.no</th>
                <th> PC No</th>
                <th> PC Name</th>
                <th> AC No</th>
                <th> AC Name </th>
                <th> Action </th>
              </tr>


            </thead>
            <tbody>
              @foreach($results as $key => $result)
              <tr>
                <td>{{$key + 1 }}</td>
                <td>{{$result['PC_NO'] }}</td>
                <td>{{$result['pc']['PC_NAME'] }}</td>
                <td>{{$result['AC_NO'] }}</td>
                <td>{{$result['AC_NAME'] }}</td>
                <td>
                  @if($result['ps_finalized'] == 1 && $showDefinalizeBtn)
                  <button type="button" class="btn btn-success definalize" data-acname="{{$result['AC_NAME']}}" data-acno="{{$result['AC_NO']}}" data-pcname="{{$result['pc']['PC_NAME']}}" data-pcno="{{$result['PC_NO']}}">Definalize</button>
                  @elseif($result['ps_finalized'] == 1 && !$showDefinalizeBtn)
                    <button class="btn btn-default disabled" disabled="disabled">Definalize Option is Disabled</button>
                  @else
                  Not Yet Finalize
                  @endif
                </td>
              </tr>
              @endforeach

            </tbody>
          </table>

        </div><!-- End Of  table responsive -->
      </div>
    </div>
  </div>
</section>
<div class="modal modal-big fade" id="definalizeModal" tabindex="-1" role="dialog" aria-labelledby="definalizeModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header mb-3">
				<h5 class="modal-title" id="exampleModalLabel">Confirmation For PS wise electoral details Definalize!</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="{{url('pcceo/PcCeoPSElectoralDefinalziedUpdate')}}">
        {{ csrf_field() }}
        <input type="hidden" name="ac_no" value="">
        <input type="hidden" name="pc_no" value="">
				<div class="modal-body">
					<div class="mb-3">
						<div style="font-size:16px;">Are you sure you want to definalize <b id="acname"></b> of <b id="pcname"></b> electoral details for modifications?</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						<button type="submit" id="submit_final_form" class="btn btn-success submit-button">Update</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
  $(document).ready(function(){
    $(document).on('click', '.definalize', function(){
      $('input[name=ac_no]').val($(this).attr('data-acno'));
      $('input[name=pc_no]').val($(this).attr('data-pcno'));
      $('#acname').text($(this).attr('data-acno')+'-'+$(this).attr('data-acname'));
      $('#pcname').text($(this).attr('data-pcno')+'-'+$(this).attr('data-pcname'));
      $('#definalizeModal').modal('show');
    })
  })

  function clearpcs() {
    $('#pc').html('');
    $('#pc').append('<option value="">All PC</option>');
  }
</script>
@endsection