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
    @if (session('message'))
    <div class="alert alert-success" id="msg">
        {{ session('message') }}
    </div>
    @endif
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    @if(count($errors->error))
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.
        <br/>
        <ul>
            @foreach($errors->error->all() as $erro)
            <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
    @endif
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
    @if (session('chckmessage'))
    <div class="alert alert-danger">
        {{ session('chckmessage') }}
    </div>
    @endif
    <section class="mt-5" id="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="sidebar__inner">
                        <div class="card"><!--  style="max-width:700px; margin:0 auto;" -->
                            <div class="card-header d-flex align-items-center">
                                <h2>Update Permission</h2>
                            </div>
                            <div class="card-body getpermission">


                                @if(!empty($getpermsndetails))
                                @foreach($getpermsndetails as $data)
                                <form class="form-horizontal" method="POST" action="{{url('/pcceo/editpermsn')}}" enctype="multipart/form-data">
                                    {{csrf_field()}}
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Permission Name <sup>*</sup></label>
                                        <div class="col-sm-8">
                                            <input type="hidden" name="p_id" value="{{$data->p_id}}" id="p_id"/>
                                            <input type="text" class="form-control" name="pname" value="{{$data->pname}}" readonly>
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Assigned to Level<sup>*</sup></label>
                                        <div class="col-sm-8">
<!--                                            <input type="text" class="form-control" name="pname" value="{{old('pname')}}">
                                            <span class="text-danger">{{ $errors->error->first('pname') }}</span>-->
                                            <select name="ofcrlevel" class="form-control" >
                                                <option value="0">Select Assigned to Level</option>
                                                @if(!empty($getpermsndetails[0]->role_id) && ($getpermsndetails[0]->role_id) != '0')
                                                <option value="{{$getpermsndetails[0]->role_id}}" selected>{{$getpermsndetails[0]->role_name}}</option>
                                                @endif
                                                @if(!empty($getrole))
                                                @foreach($getrole as $pdata)
                                                @if($pdata->role_id != $getpermsndetails[0]->role_id)
                                                <option value="{{$pdata->role_id}}" {{ (collect(old('ofcrlevel'))->contains($pdata->role_id)) ? 'selected':'' }}>{{$pdata->role_name}}</option>
                                                @endif
                                                @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->error->first('ofcrlevel') }}</span>
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4 form-control-label">Approval Required from Authority <sup>*</sup></label>
                                        @if(!empty($data->authority_type_id))
                                        @php 
                                        $auth_name=explode(',',$data->authority_type_id);
                                        @endphp
                                        <div class="col-sm-8">
                                            @if(!empty($getAuthType))
                                            @foreach($getAuthType as $authdata)
                                            @if(in_array($authdata->id,$auth_name)== true)
                                            <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox1" type="checkbox" name="auth_name[]" value="{{$authdata->id}}" checked="checked"> {{$authdata->name}}
                                                <span class="text-danger">{{ $errors->error->first('auth_name') }}</span>
                                            </label>
                                            @else
                                            <label class="checkbox-inline mr-3">
                                                <input id="inlineCheckbox1" type="checkbox" name="auth_name[]" value="{{$authdata->id}}" > {{$authdata->name}}
                                                <span class="text-danger">{{ $errors->error->first('auth_name') }}</span>
                                            </label>

                                            @endif
                                            @endforeach
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    @php $d=0; @endphp
                                    <div id="dynamic_field">
                                        @if(!empty($getpermsndocdetails))
                                        @foreach($getpermsndocdetails as $docdata)
                                        @if($docdata->doc_name != 'NULL' && $docdata->file_name != 'NULL')
                                        <div class="row d-flex align-items-center form-inline" id="existrow{{$d}}">
                                            <div class="col">
                                                <label class="sr-only" for="inlineFormInputName2">Document Name</label> 
                                                <input style="width:100%;" class="form-control" type="hidden" name="doc[{{$d}}][doc_id]" value="{{$docdata->doc_id}}" id='doc_id{{$d}}'>
                                                <input style="width:100%;" type="text" name="doc[{{$d}}][Dname]" value="{{$docdata->doc_name}}" class="form-control" id="inlineFormInputName2" placeholder="Document Name">
                                                <span class="text-danger">{{ $errors->error->first('doc.0.Dname') }}</span></div>


                                            <div class="form-check">
                                                @if($docdata->required_status == 1)
                                                <input class="form-check-input" type="checkbox" name="doc[{{$d}}][chck]" value="1" id="inlineFormCheck" checked>
                                                @else
                                                <input class="form-check-input" type="checkbox" name="doc[{{$d}}][chck]" value="1" id="inlineFormCheck" >
                                                @endif
                                                <label class="form-check-label" for="inlineFormCheck">
                                                    Mandatory
                                                </label>
                                            </div>

                                            @if(!empty($docdata->file_name) && $docdata->file_name != 'NULL')
                                            <div class="col">
                                                <div class="mr-sm-3">
                                                    <div class="custom-file browsebtn">
                                                        <label class="custom-file-label" for="customFile">{{$docdata->file_name}}</label>
                                                        <input type="file" name="doc[{{$d}}][format]" class="custom-file-input" id="customFile" >
                                                        <span class="text-danger">{{ $errors->error->first('doc.0.format') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-sm-4 col-12">
                                                <div class="mr-sm-3">
                                                    <div class="custom-file browsebtn  mb-3">
                                                        <input type="file" name="doc[{{$d}}][format]" class="custom-file-input" id="customFile" >
                                                        <label class="custom-file-label" for="customFile">Choose file</label>
        <!--                                                <span class="text-danger">{{ $errors->error->first('doc.0.format') }}</span>-->
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                        @php $d++; @endphp
                                        @endforeach
                                        <button type="button" class="btn btn-primary btn-submit mb-2 text-right" id="add">Add New</button>
                                        @endif

                                    </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group row">

                                    <div class="col">
                                        <button class="btn btn-success float-right" name="UpdatePermission" value="update">UPDATE</button>
                                    </div>
                                </div>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>





            </div>
        </div>

    </section>
    <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
</main>
@endsection
@section('script')
<script type="text/javascript">
    $(function(){
    var i =<?php echo $d; ?>;
    $('#add').click(function(){
    $('#dynamic_field').append('<div class="row d-flex align-items-center form-inline dynamic-added mt-2" style="clear:both;" id="row' + i + '"><div class="col">\n\
   <label class="sr-only" for="inlineFormInputName2">Document Name</label>\n\
  <input style="width:100%;" type="text" name="doc[' + i + '][Dname]" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="Document Name" required></div>\n\
<div class="form-check mb-2 mr-sm-2"> <input class="form-check-input" type="checkbox" name="doc[' + i + '][chck]" value="1" id="inlineFormCheck"><label class="form-check-label" for="inlineFormCheck">Mandatory</label></div>\n\
<div class="col"><div class="file-box" id="active_div' + i + '"><div class="file-select"><div class="file-select-name noFile' + i + '" id="">No file chosen...</div><input type="file" onchange="getfile(' + i + ')" name="doc[' + i + '][format]" class="custom-file-input" id="customFile' + i + '" required><div class="file-select-button customchoose" id="fileName">Choose File</div></div></div></div>\n\
<button type="button" name="remove" class="btn btn-warning  btn_remove" id="' + i + '">Remove</button></div>\n\
   ');
    i++;
    });
    $(document).on('click', '.btn_remove', function(){
    var button_id = $(this).attr("id");
    $('#row' + button_id + '').remove();
    });
    $(document).on('click', '.existbtn_remove', function(){
    var base_url = $("#base_url").val();
    var token = $('meta[name="csrf-token"]').attr('content');
    var button_id = $(this).attr("id");
    var doc_id = $('#doc_id' + button_id).val();
    var p_id = $('#p_id').val();
    var conf = confirm('Are you sure want to remove this document.');
    if (conf == true)
    {
    $.ajax({
    url: base_url + '/pcceo/removepermsn',
            type: 'POST',
            data: {_token: token, permsn_id: p_id, doc_id:doc_id},
            success: function (response)
            {
            if (response == 1)
            {
            $('#existrow' + button_id + '').remove();
            $('#msg').html('Successfully Removed');
            }
            else
            {
            alert('Some error occured');
            }
            }
    });
    }
    else
    {
    return false;
    }


    });
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    });</script>
<script type="text/javascript">
    //$('#customFile'+id).bind('change', function () {
    function getfile(id){
    var filename = $("#customFile" + id).val();
    if (/^\s*$/.test(filename)) {
    $("#active_div" + id).removeClass('file-upload active');
    $(".noFile" + id).text("No file chosen...");
    }
    else {
    $("#active_div" + id).addClass('file-upload active');
    $(".noFile" + id).text(filename.replace("C:\\fakepath\\", ""));
    }
    }
</script>

@endsection