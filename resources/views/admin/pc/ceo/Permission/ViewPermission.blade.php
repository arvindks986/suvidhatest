@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content')
<style>
 .statistics div[class*=col-] .card {
    padding: 20px 7px;
 
    }
    .card.income b {
    font-size: 16px;
}
</style>
<main role="main" class="inner cover mb-3 mb-auto">
    <section class="statistics">
        <div class="container-fluid mt-5 mb-5">
          <div class="row d-flex">
	<div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority Type</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/pcceo/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
          <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/pcceo/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> 
              <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/pcceo/viewnodals')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcceo/addnodals')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div>
              </div>
            </div>
              
               <div class="col-lg-3">
              <!-- Income-->
              <div class="card income text-center">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-info"><b>Permission Date Restriction</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/pcceo/EditRestriction')}}" class="btn btn-sm btn-outline-primary">View</a>
<!--                  <a type="button" href="{{url('/pcceo/addpermission')}}" class="btn btn-sm btn-primary">Update</a>-->
				  </div></div>
              </div>
            </div> 
		
          </div>
        </div>
      </section>

    <section>
        @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h2>Permission List</h2>
                        </div>
                        <div class="card-body tabular-pane">
                            <div class="form-group row">
                                <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                                <div class="col-sm-8">
        <!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                    <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                                    <select name="pname" class="form-control" id="selectprmsn">
                                        <option value="0">Select Permission Type</option>
                                        @if(!empty($getAllPermsData))
                                        @foreach($getAllPermsData as $pdata)
                                        <option value="{{$pdata->p_id}}" {{ (collect(old('pname'))->contains($pdata->p_id)) ? 'selected':'' }}>{{$pdata->pname}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-md-12" id="permsn_doc">

                                </div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <input type="hidden" value="<?php echo url('/'); ?>" id='base_url'/>
</main>


@endsection
@section('script')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    var table = $('#example').DataTable();

    $('select#selectprmsn').change(function () {
        var permsn_id = $(this).val();
        var base_url = $("#base_url").val();
        var token = $('meta[name="csrf-token"]').attr('content');
        //alert(permsn_id);
        $.ajax({
            url: base_url + '/pcceo/getdocdetails',
            type: 'POST',
            data: {_token: token, p_id: permsn_id},
            success: function (response)
            {
//                    alert(response);exit;
                var cnt = response[0].length;
                var str = '';
                var required_status = '';
                var prmsnid = response[0][0]['permission_id'];

                var authname = response[1][0]['auth_name'];
//                    alert(cnt);exit;
                var j = 1;
                $('#permsn_doc').css('display', '');
                if (response != 0)
                {
                    str += "<table class='table table-bordered'><tr><th>S.no.</th><th>Document Details</th><th>Authority Type</th><th>Required Status</th></tr>";
                    for (var i = 0; i < cnt; i++) {
//                        var j=1;
                        var doc_name = response[0][i]['doc_name'];
                        var doc_size = response[0][i]['doc_size'];
                        var status = response[0][i]['required_status'];
                        var stcode = response[0][i]['st_code'];
                        var ptypeid = response[0][i]['permission_type_id'];

                        if (status == 1)
                        {
                            required_status = 'Mandatory';
                        } else
                        {
                            required_status = 'Not Mandatory';
                        }
                        var file_name = response[0][i]['file_name'];
//                         str += "<ul class='list-inline'><li>" + doc_name + "</li><li>" +doc_size+"</li><li>"+required_status+"</li><li><a href='{{asset('public/uploads/permission-document')}}/"+file_name+" ' download>"+file_name+"</a></li><li><input type='file' name='permsndoc["+i+"][p_doc]'></li></ul>";
//                            str += "<div class='row'><div class='col-md-12'><p>" + doc_name + " <small class='text-danger float-right'>" + required_status + "</small></p><br /><div class='custom-file browsebtn  mb-3'><input type='file' class='custom-file-input' id='customFile' name='permsndoc[" + i + "][p_doc]'><label class='custom-file-label' for='customFile'>Choose file</label></div></div></div>";
//                            str += "<p>" + doc_name + " <small class='text-danger float-right'>" + required_status + "</small></p><br /><div class='custom-file browsebtn  mb-3'><input type='file' class='custom-file-input' id='customFile' name='permsndoc[" + i + "][p_doc]'><label class='custom-file-label' for='customFile'>Choose file</label></div>";
                        if (doc_name != null && file_name != null)
                        {
                            if (status == 1)
                            {
                                str += "<tr><td>" + j + "</td><td><p>" + doc_name + " <span class='text-alert'> <a href='{{asset('uploads/permission-document')}}/" + stcode + "/" + file_name + " ' download>Download Format</a></span></p></td><td>" + authname + "</td><td><span class='text-alert'>" + required_status + "</span></td</tr>"
                            } else
                            {
                                str += "<tr><td>" + j + "</td><td><p>" + doc_name + " <span class='text-alert'> <a href='{{asset('uploads/permission-document')}}/" + stcode + "/" + file_name + " ' download>Download Format</a></span></p></td><td>" + authname + "</td><td><span class='text-alert'>" + required_status + "</span></td</tr>"
                            }
                        }
                        j++;

                    }
                    str += "<tr rowspan='3'><td><a href='{{url('/pcceo/editpermsn')}}/" + prmsnid + "'><span class='btn btn-success'>Edit</span></a></td></tr>"
                } else
                {
                    str += "<p style='color:red'>No Document Required.</p>";

                }
                str += "</table>";
                $('#permsn_doc').html(str);

            }
        });
    });
});
</script>
@endsection