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
                <!-- <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-success text-center"><b>Police Station</b> &nbsp; <div class="btn-group float-right">
<!--					<button type="button" class="btn btn-sm btn-outline-primary">View</button>-->
                  <a type="button" href="{{url('/pcdeo/viewps')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcdeo/addps')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div></div>
              </div>
            </div>
<!--          <div class="col-lg-3 ">
               Income
              <div class="card income text-center">
                 <div class="icon"><i class="icon-line-chart"></i></div> 
                <div class="text-info"><b>Permission</b> &nbsp; <div class="btn-group float-right mt-2">
					<button type="button" class="btn btn-sm btn-outline-primary">View</button>
                  <a type="button" href="{{url('/pcdeo/viewpermsn')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcdeo/addpermission')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div>
              </div>
            </div> -->
			<div class="col">
              <!-- Income-->
              <div class="card income ">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
			   <div class="card-body">
                <div class="text-info"><b>Authority</b> &nbsp; <div class="btn-group float-right">
		  <a type="button" href="{{url('/pcdeo/viewauthority')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcdeo/addauthority')}}" class="btn btn-sm btn-primary">Add</a>
				  </div>				</div></div>
              </div>
            </div>
          <div class="col pr-0">
              <!-- Income-->
              <div class="card income">
			  <div class="card-body">
               <!--  <div class="icon"><i class="icon-line-chart"></i></div> -->
                <div class="text-warning"><b>Location</b> &nbsp; <div class="btn-group float-right">
                 <a type="button" href="{{url('/pcdeo/viewaddlocation')}}" class="btn btn-sm btn-outline-primary">View</a>
                  <a type="button" href="{{url('/pcdeo/addlocation')}}" class="btn btn-sm btn-primary">Add</a>
				  </div></div></div>   
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
                  <h2>ADD Authority</h2>
                </div>
                   @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                   @endif
             <div class="card-body getpermission">
			
			 
			 
                      <form class="form-horizontal" method="POST" action="{{url('/pcdeo/addauthoritydata')}}">
                          {{csrf_field()}}
                        <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Select Approving Authority<sup>*</sup> <br/><span class="text-danger">(Authority type will be added by CEO)</span> </label>
                          
                          <div class="col-sm-8">
                          <select class="form-control" name="authid">
                          <option value="0">Select Approving Authority</option>
                          @if(!empty($authority))
                            @foreach($authority as $data)
                            <option  value="{{$data->id}}" >
                                @if (!empty($data->name)) 
                                 {{$data->name}}
                                @endif
                            </option>
                            @endforeach
                            @endif
                         </select>
                         <span class="text-danger">{{ $errors->error->first('authid') }}</span>
                          </div>
                        </div> 
                          <div class="form-group row">
                      <label class="col-sm-4 form-control-label">Select PC<sup>*</sup></label>
                      <div class="col-sm-8">
                          <select name="pc" class="form-control" id="pc">
                            <option value="0">Select PC</option>
                            @if(!empty($getAllPC))
                            @foreach($getAllPC as $pc)  
                            <option value="{{$pc->PC_NO}}" {{ (collect(old('pc'))->contains($pc->PC_NO)) ? 'selected':'' }}> {{$pc->PC_NAME_EN}}</option>
                            @endforeach 
                            @endif
                        </select>
                        <span class="text-danger">{{ $errors->error->first('pc') }}</span>
                        <span class="text-danger">{{ $errors->error->first('acno') }}</span>
                      </div>
                    </div>
                    <div class="form-group row" id="allac">
                          <span class="text-danger">{{ $errors->error->first('acno') }}</span>
                      </div>
                         <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Department <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="text" class="form-control" placeholder="Enter Department" name="dept" value="{{ old('dept') }}">
                           <span class="text-danger">{{ $errors->error->first('dept') }}</span>
                          </div>
                        </div>
                          <div class="form-group row">
                          <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                          <div class="col-sm-8">
                          <textarea name="addr" class="form-control" placeholder="Add Address Here" id="" cols="3" rows="4">{{old('addr')}}</textarea>
                          <span class="text-danger">{{ $errors->error->first('addr') }}</span>
                          </div>
                        </div>
						
			<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Name <sup>*</sup></label>
                          <div class="col-sm-8">
                           <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{ old('name') }}">
                           <span class="text-danger">{{ $errors->error->first('name') }}</span>
                          </div>
                        </div>	
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Designation <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" placeholder="Enter Designation" name="desig" value="{{old('desig')}}">
                           <span class="text-danger">{{ $errors->error->first('desig') }}</span>
                          </div>
                        </div>
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Mobile No <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" placeholder="Enter Mobile Number" name="mb" value="{{old('mb')}}">
                           <span class="text-danger">{{ $errors->error->first('mb') }}</span>
                          </div>
                        </div>						
						
						
						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Incharge Email Id <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="email" class="form-control" placeholder="Enter Email ID" name="email" value="{{old('email')}}">
                           <span class="text-danger">{{ $errors->error->first('email') }}</span>
                          </div>
                        </div>
						
						
						
<!--						<div class="form-group row">
                          <label class="col-sm-4 form-control-label">Epic No <sup>*</sup></label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" placeholder="Enter Epic Number" name="eno" value="{{old('eno')}}">
                           <span class="text-danger">{{ $errors->error->first('eno') }}</span>
                          </div>
                        </div>-->
						
					
							
							
							
					  
                      
                    </div>
					<div class="card-footer">
						     <div class="form-group row">
                         
                          <div class="col">
                           <button class="btn btn-success float-right" name="submit" value="ADD">ADD</button>
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

@endsection
@section('script')
<script type="text/javascript">
    $(function () {
        var base_url = $("#base_url").val();
         var token = $('meta[name="csrf-token"]').attr('content');
          $("#allac").empty();
        $('select#pc').change(function () {
            var pc_id = $(this).val();
//            var stcode = $("#state :selected").val();
//            $("select[name='police_station']").empty();
//            var achtml = '';
//            achtml = achtml + "<option value='0'>Select Police Station</option>";
            
            $.ajax({
                url: base_url + '/pcdeo/getAllAC',
                type: 'POST',
                data: {_token: token, pc_id:pc_id},
                success: function (response)
                {
//                    alert(response);exit;
                    var achtml = '';
                    achtml = achtml+ "<label class='col-sm-4 form-control-label'>Select AC<sup>*</sup></label><div class='col-sm-8'><select name='acno' class='form-control' id='ac'><option value='0'>Select AC</option>";
                    var cnt = response.length;
                    if(cnt != 0 )
                    {
                    for (var i = 0; i < cnt; i++) {
                        var ac_no=response[i]['AC_NO'];
                        var ac_name=response[i]['AC_NAME'];
                       achtml = achtml + "<option value="+ac_no+">"+ac_name+"</option>";
                   }
                   achtml = achtml+"</div></select";
                   if(achtml != '')
                   {
                    $("#allac").empty();
                    $("#allac").css('display','');
                    $("#allac").append(achtml);
                   }
               }
                   else
                   {
                       $("#allac").empty();
                       $("#allac").css('display','none');
                   }
                }
        });
    });
    });
</script>
@endsection