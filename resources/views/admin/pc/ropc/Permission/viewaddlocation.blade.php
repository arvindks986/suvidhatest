@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content') 

<section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
            <div class="col pl-0">
              <!-- Income-->
              <div class="card income">
			  <div class="card-body">        
                <div class="text-success"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
                  <a type="button" href="{{url('/ropc/permission/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addps')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div></div>
              </div>
            </div>
			<div class="col">
              <!-- Income-->
              <div class="card income "> 
			   <div class="card-body">
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/ropc/permission/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addauthority')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div>				</div></div>
              </div>
            </div>
          <div class="col pr-0">
              <!-- Income-->
              <div class="card income">
			  <div class="card-body">              
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right">
                 <a type="button" href="{{url('/ropc/permission/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/ropc/permission/addlocation')}}" class="btn btn-sm btn-primary">Add</a>-->
				  </div></div></div>   
              </div>
            </div>
          </div>
        </div>
      </section>
    <section>
        @if (Session::has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h2>Location List</h2>
                        </div>
                        <div class="card-body tabular-pane">
                            <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-sm-2 form-control-label"><b>SELECT AC</b> </label>
                                <div class="col pr-0">
        <!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                    <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                                    <select name="pname" class="form-control" id="selectac">
                                        <option value="0">Select AC</option>
                                        @if(!empty($getallac))
                                        @foreach($getallac as $pdata)
                                        <option value="{{$pdata->AC_NO}}" {{ (collect(old('pname'))->contains($pdata->AC_NO)) ? 'selected':'' }}>{{$pdata->AC_NAME}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                </div>

                            </div>
                        </div>
                        <hr/>
                        
                        <div id="viewps">
                        
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</main>

@endsection
@section('script')
<script type="text/javascript">
    $(function () {
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');
        $('select#selectac').on('change', function () {
            var acid = $(this).val();
            $.ajax({
                url: base_url + '/ropc/permission/getallacloc',
                type: 'POST',
                data: {_token: token, acid: acid},
                success: function (data)
                {
//                    alert(data);
                   var str = '';
                  var cnt = data.length;
                  if(data != '')
                  {
                       str +="<table id='list-table' class='table table-striped table-bordered table-hover' style='width:100%'><thead><th>S.no.</th><th>Location Name</th><th>Location Address</th></thead><tbody>";
                       for (var i = 0; i < cnt; i++) {
                           var id=data[i]['id'];
                           var lname=data[i]['location_name'];
                           var ladd=data[i]['location_details'];
                           str +="<tr><td>"+id+"</td><td>"+lname+"</td><td>"+ladd+"</td></tr>"
                       }
                       str +="</tbody></table>";
                       $('#viewps').html(str);
                       $('#list-table').DataTable();
                  }
                  else
                  {
                    $('#viewps').html('No data avilable');
                  }
                }
            });
        });
    });

</script>
@endsection