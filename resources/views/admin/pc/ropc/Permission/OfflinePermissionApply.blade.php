@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Permission Details')
@section('bradcome', 'Apply Offline Permission')
@section('content') 
<style type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css"></style>
<main role="main" class="inner cover mb-3 mb-auto">
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
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    <section class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>Apply Offline Permission</h4>
                        </div>
                        <div class="card-body tabular-pane">
                            <form class="form-horizontal" id="permission" method="post" action="{{url('/ropc/permission/UserDetails')}}" enctype="multipart/form-data" autocomplete="off">
                                {{csrf_field()}}
                                <div class="row">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Mobile No</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="user_mb" class="form-control" placeholder="Enter Mobile Number" id="mobileno" value="{{old('user_mb')}}">
                                                        <span class="text-danger">{{ $errors->error->first('user_mb') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Applicant Type</label>
                                                    <div class="col-sm-8">
                                                        <select name="user_type" class="form-control" id="user_type">
                                                            <option value="0">Select Applicant Type</option>
                                                            @if(!empty($getAllUserType))
                                                            @foreach($getAllUserType as $u_type)  
                                                            <option value="{{ $u_type->role_id}}" {{ (collect(old('user_type'))->contains($u_type->role_id)) ? 'selected':'' }}> {{$u_type->role_name }}</option>
                                                            @endforeach 
                                                            @endif
                                                        </select>
                                                        <span class="text-danger">{{ $errors->error->first('user_type') }}</span>
                                                    </div></div>
                                            </div> 


                                        </div> 
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Select Political Party/Independent</label>
                                                    <div class="col-sm-8">
                                                <select name="political_party" class="form-control" id="party">
                                                    <option value="0">Select Political Party/Independent</option>
                                                    @if(!empty($allParty))
                                                    @foreach($allParty as $party)  
                                                    <option value="{{ $party->CCODE}}" {{ (collect(old('political_party'))->contains($party->CCODE)) ? 'selected':'' }}> {{$party->PARTYNAME }}</option>
                                                    @endforeach 
                                                    @endif
                                                </select>
                                                <span class="text-danger">{{ $errors->error->first('political_party') }}</span>
                                                    </div></div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Name <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="user_name" class="form-control" placeholder="Enter Name" id="name" value="{{old('user_name')}}">
                                                        <span class="text-danger">{{ $errors->error->first('user_name') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Email ID <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="user_email" class="form-control" placeholder="Enter Email ID" id="email" value="{{old('user_email')}}">
                                                        <span class="text-danger">{{ $errors->error->first('user_email') }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Father's Name/ Mother's Name/ Husband's Name <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <input type="text"  name="fathers_name" value="{{old('fathers_name')}}" class="form-control" placeholder="Enter Father's Name/ Mother's Name/ Husband's Name" id="fathers_name">
                                                        <span class="text-danger">{{ $errors->error->first('fathers_name') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Gender <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <div class="radio-area">
                                                            
                                                            <div class="custom-radio-btn">
                                                                <input type="radio" name="gender" value="male" {{ (old('gender') == 'male') ? 'checked' : '' }} id="radio2">  
                                                                <label for="radio2">Male</label>
                                                            </div>
                                                            <div class="custom-radio-btn">
                                                                <input type="radio" name="gender" value="female" {{ (old('gender') == 'female') ? 'checked' : '' }} id="radio1">  
                                                                <label for="radio1">Female</label>
                                                            </div>
                                                            <div class="custom-radio-btn">
                                                                <input type="radio" name="gender" value="third" {{ (old('gender') == 'third') ? 'checked' : '' }} id="radio3">  
                                                                <label for="radio3">Other</label>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger">{{ $errors->error->first('gender') }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Date of Birth<sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <input name="dob" type="text" class="form-control" placeholder="Date &amp; time" id='dob' value="{{old('dob')}}">
                                                        <span class="text-danger">{{ $errors->error->first('dob') }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                           
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Permission Type <sup>*</sup> <br/><span class="text-danger">(Permission type will be added by CEO)</span></label>
                                                    <div class="col-sm-8">
                                                        <select name="permission_type" class="form-control" id="selectprmsn">
                                                            <option value="0">Select Permission Type</option>
                                                            @if(count($permission_type)>0)
                                                            @foreach ($permission_type as $key=>$rosuper_list)
                                                            <option value="{{$rosuper_list->permsn_id}}{{'#'}}{{$rosuper_list->permission_type_id}}" {{ (collect(old('permission_type'))->contains($rosuper_list->permsn_id)) ? 'selected':'' }}>{{$rosuper_list->pname}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                        <span class="text-danger">{{ $errors->error->first('permission_type') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col"></div>
                                        </div>
                                         <div class="row">
                                            <div class="col-md-12" id="permsn_doc">
                                                
                                            </div></div>
                                        <div class="row">
                                             <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">State<sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <select name="state" id="state" class="form-control">
                                                            @if(!empty($user_data->st_code))
                                                            <option value="{{$user_data->st_code}}" selected> {{$getrodetails->ST_NAME }}</option>
                                                             @endif
                                                        </select>
                                                       
                                                        <span class="text-danger">{{ $errors->error->first('state') }}</span>
                                                    </div>
                                                </div></div>
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">District<sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        
                                                        <select name="district"  id="district" class="form-control">
														<option value="0">Select District</option>
                                                            <!--@if(!empty($user_data->dist_no))
                                                            <option value="{{$user_data->dist_no}}" selected> {{$getrodetails->DIST_NAME}}</option>
                                                            @endif -->
															@if(!empty($getallDist))
																@foreach($getallDist as $dist)
															<option value="{{$dist->DIST_NO}}" > {{$dist->DIST_NAME}}</option>
																@endforeach
															@endif
                                                        </select>
                                                        
                                                        <span class="text-danger">{{ $errors->error->first('district') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>

                                        <div class="row">
                                            <div class="col" id="pcno">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">PC <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <select name="pc"  id="pc" class="form-control">
                                                        @if(!empty($getrodetails->PC_NAME))
                                                            <option value="{{$user_data->pc_no}}" selected> {{ $getrodetails->PC_NAME }}</option>
                                                         @endif
                                                         </select>
                                                        <span class="text-danger">{{ $errors->error->first('pc') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col" id="acno">
                                                 <div class="form-group row" >
                                                    <label class="col-sm-4 form-control-label">AC <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <select name="ac_no" class="form-control" id="ac">
                                                            <option value="0">Select AC</option>
                                                            @if(!empty($getallAC))
                                                            @foreach($getallAC as $ac)
                                                            <option value="{{$ac->AC_NO}}" {{ (collect(old('ac_no'))->contains($ac->AC_NO)) ? 'selected':'' }}>{{$ac->AC_NAME}}</option>
                                                            @endforeach
                                                            @else
                                                            <option value=""></option>
                                                            @endif
                                                        </select> 
                                                        <span class="text-danger">{{ $errors->error->first('police_station') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                       

                                        <div class="row">
                                           <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Address <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <textarea cols="40" rows="5" name="address" id="user_address">{{old('address')}}</textarea>
                                                        <span class="text-danger">{{ $errors->error->first('address') }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col" >
                                                <div class="form-group row" id="psno">
                                                    <label class="col-sm-4 form-control-label">Police Station <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <select name="police_station" class="form-control" id="ps">
                                                            <option value="0">Select Police Station</option>
                                                        </select> 
                                                        <span class="text-danger">{{ $errors->error->first('police_station') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="row">
                                             <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Submission Date &amp; Time <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <input name="subdate" type="text" id="subdate" class="form-control" placeholder="Date &amp; time" value="{{old('stdate')}}">
                                                        
                                                        <span class="text-danger">{{ $errors->error->first('stdate') }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col"></div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Event Start Date &amp; Time <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <input name="stdate" type="text" id="startdate" class="form-control" id="datetimepicker" placeholder="Date &amp; time" value="{{old('stdate')}}">
                                                        
                                                        <span class="text-danger">{{ $errors->error->first('stdate') }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Event End Date &amp; Time <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <input name="enddate" type="text" id="enddate" class="form-control" id="datetimepicker1" placeholder="Date &amp; time" value="{{old('enddate')}}">
                                                        <span class="text-danger">{{ $errors->error->first('enddate') }}</span>
                                                    </div>

                                                </div>
                                            </div>
                                            </div>
                                        <div class="row" id="locid">
                                            <div class="col">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 form-control-label">Event Place <sup>*</sup></label>
                                                    <div class="col-sm-8">
                                                        <select name="location" id="location" class="form-control">
                                                            <option value="0"> Select Location</option>
                                                            @if(!empty($getAllLocation))
                                                            @foreach($getAllLocation as $loc)
                                                            <option value="{{$loc->id}}" {{ (collect(old('location'))->contains($loc->id)) ? 'selected':'' }}>{{$loc->location_name}} , {{$loc->location_details}}</option>
                                                            @endforeach
                                                            @endif
                                                            <option value="other">Add More Locations</option>
                                                        </select>
                                                        <span class="text-danger">{{ $errors->error->first('location') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group row"  >
                                                    <label class="col-sm-4 form-control-label" style="display:none" id="other">Add More Locations <sup>*</sup></label>
                                                    <div class="col-sm-8" style="display:none" id="otherInput">
                                                        <input  name="other" type="text" class="form-control" placeholder="Enter Location Here" value="{{old('other')}}">
                                                        <span class="text-danger">{{ $errors->error->first('other') }}</span>
                                                    </div>

                                                </div>
                                                
                                            </div>
                                        </div>

                                    </div>
                                </div>  
<!--                                <div class="row">
                                    <div class="col">
                                        <div id="dvMap"></div>
                                    </div>
                                </div>-->
                                <div class="row">
                                    <div class="col">
                                        
                                    </div>
                                         

                                    <div class="col text-right">
                                        <input type="hidden" name="userId" id="userId"/>
                                        <input type="hidden" name="userdata" id="userdata"/>
                                        <input type="hidden" name="preqid" id="preqid"/>
                                        <input type="hidden" name="userroleid" id="userroleid"/>
                                         <input type="hidden" name="userparty" id="userparty"/>
                                        <div class="form-group row">
                                            <label class="col-sm-4 form-control-label"></label>
                                            <div class="col-sm-8">                               
<!--                                                <button type="submit" class="btn btn-primary" name="print" value="Save">Print</button>-->
<button type="submit" class="btn btn-primary text-center" name="AddPS" value="Save" id="disabled_button">Save</button>
                                            </div>
                                        </div>
                                        
                                    </div>	
                                </div>
                                </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        </div>

    </section>
    <input type="hidden" id="base_url" value="<?php echo url('/'); ?>">
</main>
<style>
    /* Always set the map height explicitly to define the size of the div
    * element that contains the map. */
    #dvMap {
        height: 300px;
        width: 100%;
    }
</style>
@endsection

@section('script')
<script type="text/javascript">
    $(function () {
         var base_url = $("#base_url").val();
         var token = $('meta[name="csrf-token"]').attr('content');
         $('#disabled_button').click(function(e){
			$(this).prop('disabled', true);
			$(this).val('Submiting...');
			$('#permission').submit();
		});

        $('#dob').datetimepicker({
	 format: 'DD-MM-YYYY',
         useCurrent: false,
         maxDate: new Date(),
	});
var startDate;
var dateToday = new Date(); 
         $("#startdate").datetimepicker({
              format: 'DD-MM-YYYY HH:mm:ss',
//               minDate: dateToday,
                minDate: new Date(2019,02,10),
         });
         $("#subdate").datetimepicker({
              format: 'DD-MM-YYYY HH:mm:ss',
              maxDate: new Date(),
              useCurrent: false,
         });
        $("#enddate").datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss',
            useCurrent: false,
        });
        $("#startdate").on("dp.change", function (e) {
            $('#enddate').data("DateTimePicker").minDate(e.date);
        });
        $("#enddate").on("dp.change", function (e) {
            $('#startdate').data("DateTimePicker").maxDate(e.date);
        });
        var token = $('meta[name="csrf-token"]').attr('content');
        var base_url = $("#base_url").val();
$('#mobileno').on('blur', function () {
//        $("#mobileno").blur(function () {
            var no = $(this).val();
            var length = no.length;
            var chck = $.isNumeric(no);
            if (length == 10 && chck == true)
            {
                $.ajax({
                    url: base_url + '/ropc/permission/getUserDetails',
                    type: 'POST',
                    data: {_token: token, mb_no: no},
                    success: function (data)
                    {
//                        alert(data);exit;
//                        alert(data[0]['party_id']);exit;
                        //alert(data[0]['name']);exit;
                        var str = '';
//                       var chckdata = $.isNumeric(data);
                        var userid = data[0]['login_id'];
                        var name = data[0]['user_name'];
                        var email = data[0]['email'];
                        var fathers_name = data[0]['fathers_name'];
                        var gender = data[0]['gender'];
                        var dob = data[0]['dob'];
                        var address = data[0]['address'];
                        var state_id = data[0]['state_id'];
                        var district_id = data[0]['district_id'];
                        var ac_id = data[0]['ac_id'];
                        var role_id = data[0]['role_id'];
                        var role_name = data[0]['role_name'];
                        var address = data[0]['address'];
                        var partyid=data[0]['party_id'];
                        var preqid=data[0]['permission_request_status'];
                        var pratyname=data[0]['PARTYNAME'];
                        var rolename=data[0]['role_name'];
                        if(fathers_name != null)
                        {
                            $('#userdata').val('1');
                        }
                        else
                        {
                            $('#userdata').val('0');
                        }
                        if (userid != null && userid != 0)
                        {
                            $('#userId').val(userid);
                            $('#email').val(email);
                            $('#name').val(name);
                            $('#fathers_name').val(fathers_name);
                            $('#dob').val(dob);
                            $('#address').val(address);
                           
                            if(partyid != null && partyid != 0)
                            {
                              $('#party').val(partyid);
                            }
                            else
                            {
                                $('#userparty').val('0');
                            }
                            if(role_id != null && role_id  != 0)
                            {
                                $('#user_type').val(role_id);
                            }
                            else
                            {
                                $('#userroleid').val('0')
                            }
//                            var partyoption=$('<option></option>').attr("value", partyid).text(pratyname);
//                                $("#party").empty().append(partyoption);
                                
                            $('#user_address').text(address)
                            $('#preqid').val(preqid)
//                           $('#state').val(state_id).change();
//                           $('#district').val(district_id).change();
//                           $('#ac').val(ac_id).change();

                            if (gender == 'female')
                            {
                                $('input:radio[name=gender][id=radio1]').click();
                            } else if (gender == 'male')
                            {
                                $('input:radio[name=gender][id=radio2]').click();
                            } else if (gender == 'third')
                            {
                                $('input:radio[name=gender][id=radio3]').click();
                            } else
                            {
//                               $('input:radio[name=gender][id=radio3]').click();
                            }
                            if(preqid == 1)
                            {
                                $('#email').attr('readonly','readonly');
                                $('#name').attr('readonly','readonly');
                                $('#fathers_name').attr('readonly','readonly');
                                $('#dob').attr('readonly','readonly');
                                $('#address').attr('readonly','readonly');
                                $('#user_type').attr('readonly','readonly');
                                $('#party').attr('readonly','readonly');
                                $('#user_address').attr('readonly','readonly');
                            }
                            else if(preqid == 0)
                            {
                                $('#email').removeAttr('readonly');
                                $('#name').removeAttr('readonly');
                                $('#fathers_name').removeAttr('readonly');
                                $('#dob').removeAttr('readonly');
                                $('#address').removeAttr('readonly');
                                $('#user_type').removeAttr('readonly');
                                $('#party').removeAttr('readonly');
                                $('#user_address').removeAttr('readonly');
                            }
                        } 
                        else
                        {
//                            alert('ok');
                            $('#userId').val('0');
                            $('#email').val('');
                            $('#name').val('');
                            $('#fathers_name').val('');
                            $('#dob').val('');
                            $('#address').val('');
                            $('#user_type').val('0');
                             $('#party').val('0');
                            $('#user_address').text('')
//                            $('#user_type').html(); 
//                            $('#user_type').val(null).trigger('change');
//                            $("#user_type").empty();
                            
                            $('#email').removeAttr('readonly');
                                $('#name').removeAttr('readonly');
                                $('#fathers_name').removeAttr('readonly');
                                $('#dob').removeAttr('readonly');
                                $('#address').removeAttr('readonly');
                                $('#user_type').removeAttr('readonly');
                                $('#party').removeAttr('readonly');
                                $('#user_address').removeAttr('readonly');
//                           $('#state').val(state_id).change();
//                           $('#district').val(district_id).change();
//                           $('#ac').val(ac_id).change();

//                            if (gender == 'female')
//                            {
//                                $('input:radio[name=gender][id=radio1]').click();
//                            } else if (gender == 'male')
//                            {
//                                $('input:radio[name=gender][id=radio2]').click();
//                            } else if (gender == 'third')
//                            {
//                                $('input:radio[name=gender][id=radio3]').click();
//                            } else
//                            {
//                               $('input:radio[name=gender][id=radio3]').click();
//                            }
//                            alert('Please Enter Valid Mobile No.');
                        }
                    }
                });

            } else
            {
                alert('Please Enter Valid Mobile No.')
            }
        });
        $('select#ac').change(function () {
            var ac_id = $(this).val();
            var stcode = $("#state :selected").val();
            $("select[name='police_station']").empty();
            var achtml = '';
            achtml = achtml + "<option value='0'>Select Police Station</option>";
            
            $.ajax({
                url: base_url + '/ropc/permission/getPS',
                type: 'POST',
                data: {_token: token, ac_id:ac_id,st_code:stcode},
                success: function (response)
                {
                    var cnt = response.length;
                    for (var i = 0; i < cnt; i++) {
                        var ps_no=response[i]['id'];
                        var ps_name=response[i]['police_st_name'];
                       achtml = achtml + "<option value="+ps_no+">"+ps_name+"</option>";
                   }
                   if(achtml != '')
                   {
                    $("select[name='police_station']").append(achtml);
                   }
                   else
                   {
                        $("select[name='police_station']").append(achtml);
                   }
                }
        });
    });
     $('select#ac').change(function () {
            var stcode = $("#state :selected").val();
            var district = $("#district :selected").val();
            var ac_id = $(this).val();
            var pc = $("#pc :selected").val();
            $("select[name='location']").empty();
            var achtml = '';
            achtml = achtml + "<option value='0'> Select Location</option><option value='other'>Add More Locations</option>";
            
            $.ajax({
                url: base_url + '/ropc/permission/getlocation',
                type: 'POST',
                data: {_token: token, ac_id:ac_id,st_code:stcode,dist:district,pc_id:pc},
                success: function (response)
                {
//                    alert(response);exit;
                    var cnt = response.length;
                    var lochtml = '';
                     lochtml = lochtml + "<option value='0'> Select Location</option>";
                    for (var i = 0; i < cnt; i++) {
                        var loc_no=response[i]['id'];
                        var loc_name=response[i]['location_name'];
                       lochtml = lochtml + "<option value="+loc_no+">"+loc_name+"</option>";
                   }
                   if(lochtml != '')
                   {
                    $("select[name='location']").empty();
                   lochtml = lochtml + "<option value='other'>Add More Locations</option>";
                    $("select[name='location']").append(lochtml);
                   }
                   else
                   {
                        $("select[name='location']").append(achtml);
                   }
                }
        });
    });

        $('select#selectprmsn').change(function () {
//            var permsn_id = $(this).val();
            var ptype = $(this).val();
            var permsnval= ptype.split('#');
//            var permsn_id = $(this).val();
            var permsn_id= permsnval[0];
            var ptype_id= permsnval[1];
            if(ptype_id == 3 || ptype_id == 6)
            {
                $('#locid').css('display','none');
                $('#acno').css('display','');
                $('#pcno').css('display','');
                $('#psno').css('display','');
//                var dist;
//                var dist_no= '<?php // echo $user_data->dist_no; ?>' ;
//                var dist_name= '<?php // echo $getrodetails->DIST_NAME; ?>' ;
//                dist = dist + "<option value="+dist_no+">"+dist_name+"</option>";
//                $("select[name='district']").empty();
//               $("select[name='district']").append(dist);
                var stcode = jQuery("#state :selected").val();
                var pc = jQuery("#pc :selected").val();
                $.ajax({
                url: base_url + '/ropc/permission/getpcalldistrict',
                type: 'POST',
                data: {_token: token, stcode: stcode, pc:pc},
                success: function (response)
                {
                    var cnt = response.length;
                    var disthtml = '';
                     disthtml = disthtml + "<option value='0'> Select District</option>";
                    for (var i = 0; i < cnt; i++) {
                        var dist_no=response[i]['DIST_NO'];
                        var dist_name=response[i]['DIST_NAME_EN'];
                       disthtml = disthtml + "<option value="+dist_no+">"+dist_name+"</option>";
                   }
                   if(cnt != '')
                   {
                    $("select[name='district']").empty();
                    $("select[name='district']").append(disthtml);
                   }
//                   
                }
                });
            }
            else if(ptype_id == 8)
            {
                $('#locid').css('display','none');
                $('#acno').css('display','none');
                $('#pcno').css('display','none');
                $('#psno').css('display','none');
                var stcode = jQuery("#state :selected").val();
                $.ajax({
                url: base_url + '/ropc/permission/getalldistrict',
                type: 'POST',
                data: {_token: token, stcode: stcode},
                success: function (response)
                {
                    var cnt = response.length;
                    var disthtml = '';
                     disthtml = disthtml + "<option value='0'> Select District</option>";
                    for (var i = 0; i < cnt; i++) {
                        var dist_no=response[i]['DIST_NO'];
                        var dist_name=response[i]['DIST_NAME'];
                       disthtml = disthtml + "<option value="+dist_no+">"+dist_name+"</option>";
                   }
                   if(cnt != '')
                   {
                    $("select[name='district']").empty();
                    $("select[name='district']").append(disthtml);
                   }
//                   else
//                   {
//                       var dist_no= <?php echo $user_data->dist_no; ?> ;
//                        var dist_name= <?php echo $getrodetails->DIST_NAME; ?> ;
//                        var dist = disthtml + "<option value="+dist_no+">"+dist_name+"</option>";
//                        $("select[name='district']").empty();
//                       $("select[name='district']").append(dist);
//                   }
                }
                });
            }
            else
            {
                $('#locid').css('display','');
                $('#acno').css('display','');
                $('#pcno').css('display','');
                $('#psno').css('display','');
                var stcode = jQuery("#state :selected").val();
                var pc = jQuery("#pc :selected").val();
                $.ajax({
                url: base_url + '/ropc/permission/getpcalldistrict',
                type: 'POST',
                data: {_token: token, stcode: stcode, pc:pc},
                success: function (response)
                {
                    var cnt = response.length;
                    var disthtml = '';
                     disthtml = disthtml + "<option value='0'> Select District</option>";
                    for (var i = 0; i < cnt; i++) {
                        var dist_no=response[i]['DIST_NO'];
                        var dist_name=response[i]['DIST_NAME_EN'];
                       disthtml = disthtml + "<option value="+dist_no+">"+dist_name+"</option>";
                   }
                   if(cnt != '')
                   {
                    $("select[name='district']").empty();
                    $("select[name='district']").append(disthtml);
                   }
                }
                });
            }
            //alert(permsn_id);
            $.ajax({
                url: base_url + '/ropc/permission/offlinePermission',
                type: 'POST',
                data: {_token: token, permsn_id: permsn_id, view: '0'},
                success: function (response)
                {
//                    alert(response);exit;
                    var cnt = response.length;
                    var str = '';
                    var required_status = '';
                    //alert(cnt);
                   var j=1;
                    $('#permsn_doc').css('display', '');
                    if (response != 0)
                    {
                         str +="<table class='table table-bordered'><tr><th>S.no.</th><th>Document Details</th><th>Upload Document</th><th>Required Status</th></tr>";
                    for (var i = 0; i < cnt; i++) {
//                        var j=1;
                        var doc_name = response[i]['doc_name']
                        var doc_size = response[i]['doc_size']
                        var status = response[i]['required_status']
                        var stcode=response[i]['st_code']
                        if (status == 1)
                        {
                            required_status = 'Mandatory';
                        } else
                        {
                            required_status = 'Not Mandatory';
                        }
                        var file_name = response[i]['file_name']
//                         str += "<ul class='list-inline'><li>" + doc_name + "</li><li>" +doc_size+"</li><li>"+required_status+"</li><li><a href='{{asset('public/uploads/permission-document')}}/"+file_name+" ' download>"+file_name+"</a></li><li><input type='file' name='permsndoc["+i+"][p_doc]'></li></ul>";
//                            str += "<div class='row'><div class='col-md-12'><p>" + doc_name + " <small class='text-danger float-right'>" + required_status + "</small></p><br /><div class='custom-file browsebtn  mb-3'><input type='file' class='custom-file-input' id='customFile' name='permsndoc[" + i + "][p_doc]'><label class='custom-file-label' for='customFile'>Choose file</label></div></div></div>";
//                            str += "<p>" + doc_name + " <small class='text-danger float-right'>" + required_status + "</small></p><br /><div class='custom-file browsebtn  mb-3'><input type='file' class='custom-file-input' id='customFile' name='permsndoc[" + i + "][p_doc]'><label class='custom-file-label' for='customFile'>Choose file</label></div>";
                 if(status == 1)  
                 {
                        str +="<tr><td>"+j+"</td><td><p>"+ doc_name +" <span class='text-alert'> <a href='{{asset('uploads/permission-document')}}/"+stcode+"/"+file_name+" ' download>Download Format</a></span></p></td><td><input type='file' id='file' name='permsndoc["+i+"][p_doc]' required></td><td><span class='text-alert'>"+ required_status +"</span></td</tr>"
                    }
                    else
                    {
                        str +="<tr><td>"+j+"</td><td><p>"+ doc_name +" <span class='text-alert'> <a href='{{asset('uploads/permission-document')}}/"+stcode+"/"+file_name+" ' download>Download Format</a></span></p></td><td><input type='file' id='file' name='permsndoc["+i+"][p_doc]'></td><td><span class='text-alert'>"+ required_status +"</span></td</tr>"
                    }
                    j++;
                        }
                    }
                    else
                       {
                           str += "<p style='color:red'>No Document Required.</p>";

                       }
                    str +="</table>";
                    $('#permsn_doc').html(str);

                }
            });
        });
        
        //Map Jquery
        var lat;
        var lng;
        var stcodeval;
        var stid;
        var stname;
        var latitude;
        var longitude;
        var latac;
        var lngac;
        var mapname;
        var placenames;
        var stcodes = new Array();
        var lat = new Array();
        var lng = new Array();
        var title = new Array();
//	jQuery("#ac").change(function()
//	{
        var stcode = jQuery("#state :selected").val();
        var district = jQuery("#district :selected").val();
        var ac = jQuery("#ac :selected").val();
//                alert(ac);
        /*if(ac != "")
         {
         alert('1');
         $('#openmap').css('display', ($(this).val() != '') ? 'block' : 'none');
         }
         else
         {
         $('#openmap').css('display', ($(this).val() != '') ? 'display' : 'none');
         } */
        jQuery.ajax({
            url: "{{url('/ropc/permission/getlocat')}}",
            type: 'GET',
            dataType: 'json',
            data: {stcode: stcode, ac: ac},
            success: function (result) {
//		alert(result);
                var jsonObj = JSON.stringify(result);
                //alert(jsonObj);
                //alert(result[0]['location_name']);
                var acselect = jQuery('select[name="location"]');
//                acselect.empty();

                var map = new google.maps.Map(document.getElementById('dvMap'), {
                    zoom: 10,
                    center: new google.maps.LatLng(result[0]['latitude'], result[0]['logitude']),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                var infowindow = new google.maps.InfoWindow();
                var marker, i;

                for (i = 0; i < result.length; i++)
                {
                    marker = new google.maps.Marker
                            ({
                                position: new google.maps.LatLng(result[i]['latitude'], result[i]['longitude']),
                                map: map
                            });

                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            infowindow.setContent(result[i]['location_name']);
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }
                var src = 'https://cvigil.eci.nic.in/GIS/' + stcode + '.kmz';
                var kmlLayer = new google.maps.KmlLayer(src, {
                    suppressInfoWindows: true,
                    preserveViewport: false,
                    map: map
                });
                var achtml = '';
                var otherhtml = '';
                achtml = achtml + '<option value=""> Select Location</option>';
                var achtmlother = '<option value=other>Add More Locations</option>';
                jQuery.each(result, function (key, value)
                {
                    achtml = achtml + '<option value="' + value.id + '">' + value.location_name + '</option>';
                });
                achtml = achtml + achtmlother;
                jQuery("select[name='location']").html(achtml);
                //alert(achtml);

                var achtmlend = '';
                jQuery("select[name='location']").append(achtmlend)
            }
        });

        /*police station ajax*/
//		$.ajax({
//		url:base_url+'/offlinePermission',
//		type: 'POST',
//		data: { _token:token,stcode:stcode,district:district,ac_no:ac,view:'0'},
//		success: function(result){
//			//alert(result);exit;
//			//$("select[name='police_station']").html(policehtml);
//                     $("select[name='police_station'").html('');
//                    $.each(result,function(key, value) {
//						alert(value);
//						alert(value.id);
//                    $("select[name='police_station'").append('<option value="'+value.id+'">'+value.police_st_name+'</option>');
//
//                    });
//		}
//		});

//    });	


        jQuery("#location").change(function ()
        {
            var stcode = jQuery("select[name='state']").val();
            var district = jQuery("select[name='district']").val();
            var ac = jQuery("select[name='ac']").val();
            var locationid = jQuery(this).val();
            if (locationid == "other")
            {
                $('#other').css('display', ($(this).val() == 'other') ? 'block' : 'none');
                $('#otherInput').css('display', ($(this).val() == 'other') ? 'block' : 'none');
            } else
            {
                $('#other').css('display', ($(this).val() == 'other') ? 'display' : 'none');
                $('#otherInput').css('display', ($(this).val() == 'other') ? 'block' : 'none');
            }
            jQuery.ajax({
                url: "{{url('/ropc/permission/getlatl')}}",
                type: 'GET',
                // dataType:'json',
                data: {locationid: locationid},
                success: function (arr)
                {
                    var jsonObj = JSON.parse(arr);
                    placenames = jsonObj[0]['location_name'];
                    stcodes = jsonObj[0]['ST_CODE'];
                    //alert(stcodes);
                    $("#latitude").val(jsonObj[0]['latitude']);
                    $("#longitude").val(jsonObj[0]['longitude']);
                    $("#placename").val(jsonObj[0]['name']);
                    LoadMap(jsonObj[0]['latitude'], jsonObj[0]['longitude']);
                }
            });

        });

        function LoadMap(lat, lng)
        {
            var src = 'https://cvigil.eci.nic.in/GIS/' + stcodes + '.kmz';
            var markers = [{
                    "lat": lat,
                    "lng": lng,
                    "description": '<div class="popupmap">' + placenames + '</div>'
                },
            ];

            var mapOptions = {
                center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            var myContent = 'test';
            var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
            //Create and open InfoWindow.
            //var infowindow = new google.maps.InfoWindow();
            var infowindow = new google.maps.InfoWindow({
                //content: '<div id="myInfoWinDiv">'+ myContent +'</div>'
            });

            google.maps.event.addListener(infowindow, 'domready', function ()
            {
            });

            var marker, i;


            marker = new google.maps.Marker
                    ({
                        position: new google.maps.LatLng(markers[0].lat, markers[0].lng),
                        map: map,
                        icon: 'https://www.google.com/mapfiles/marker_black.png'
                    });

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(markers[0].description);
                    infowindow.open(map, marker);


                }
            })(marker, i));
            var src = 'https://cvigil.eci.nic.in/GIS/' + stcodes + '.kmz';
            var kmlLayer = new google.maps.KmlLayer(src, {
                suppressInfoWindows: true,
                preserveViewport: false,
                map: map
            });



        }
	
    });

</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4mlo-kY0vyBDIdeXffR2igqE5igx3piE&callback=LoadMap">
		
</script>


@endsection

