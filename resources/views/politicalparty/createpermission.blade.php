@extends('layouts.theme')
@section('title', 'Permission')
@section('content')
<?PHP // print_r($user_details_location);die;?>
<style type="text/css">
h4{
	color:#ffffff !important;
	padding: 0.5em !important;
}
.dataTables_filter {
    display: none;
}
.odd{display: none;}

</style>

<style>
/* Always set the map height explicitly to define the size of the div
* element that contains the map. */
#dvMap {
height: 300px;
width: 100%;
}
/* Optional: Makes the sample page fill the window. */
html, body {
height: 100%;
margin: 0;
padding: 0;
}
</style>
<section class="mt-5">
<div class="container">
<div class="row">
<div class="col-lg-12 p-0">
    <div class="card">
    	<div class="card-header d-flex align-items-center">
            <h3>Apply Permission</h3>
        </div>
      	@if(session::has('msg'))
		<div class="alert alert-danger">
		{{session()->get('msg')}}
		</div>
		@endif
		@if($errors->any())
		<div class="alert alert-danger">{{$errors->first()}}<!-- <br>{{ $errors->first('district') }}<br>{{ $errors->first('ac') }}<br>{{ $errors->first('location') }} --></div>
        @endif
		<div class="card-body tabular-pane">
				<div class="row">
			        <div class="col">
						<form class="form-horizontal" method="post" action="{{url('/Applypermission')}}" enctype="multipart/form-data" autocomplete="off" id="permission" onsubmit="return checkForm(this);">
						{{ csrf_field() }}
			       		@if(count($user_details)>0)
	                	@foreach ($user_details as $key=>$rosuper_list)
	                	<!-- ist Row -->
	                	<div class="row">
	                		<div class="col">
								<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Applicant Type</label>
                          		<div class="col-sm-8">
                          			<input type="text" class="form-control" id="n" value="{{$users=Session::get('Applicant_type')}}" readonly>
                           			<!-- <input type="text" class="form-control" placeholder="Enter Name"> -->
                          		</div>
                        		</div>
                   			</div>
                    		<div class="col">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Political Party / Independent</label>
                          		<div class="col-sm-8">
                          			<select name="party_master" class="form-control">
										<option value="{{$rosuper_list->CCODE}}">{{$rosuper_list->PARTYNAME}}</option>
									</select>
                          		</div>
                        		</div>
                    		</div>
                   		</div>
                    	<!-- 2nd row -->
	               		<div class="row">
	                		<div class="col">
								<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Name</label>
                          		<div class="col-sm-8">
                          			<input type="hidden" class="form-control" value="{{$rosuper_list->user_login_id}}" name="userid" readonly>
                          			<input type="text" class="form-control" id="n" value="{{$rosuper_list->name}}" name="name" readonly>
                         		</div>
                        		</div>
                    		</div>
	                        <div class="col">
	                        	<div class="form-group row">
	                          		<label class="col-sm-4 form-control-label">Email ID</label>
	                          	<div class="col-sm-8">
	                           		<input type="text" class="form-control" placeholder="Enter Email ID" value="{{$rosuper_list->email}}" name="email" readonly>
	                          	</div>
	                        	</div>
	                    	</div>
                    	</div>
                    	<!-- 3rd row -->
                    	<div class="row">
                    		<div class="col">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">State</label>
                          		<div class="col-sm-8">
                          			<select name="state" id="state" class="form-control">
									<option value="{{$rosuper_list->ST_CODE}}">{{$rosuper_list->ST_NAME}}</option>
						  			</select>
                          		</div>
                        		</div>
                    		</div>
							<div class="col">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Mobile No</label>
                          		<div class="col-sm-8">
                           			<input type="text" class="form-control" value="{{$rosuper_list->mobileno}}" name="mobile" id="m" readonly>
                          		</div>
                        		</div>
                    		</div>
                    	</div>
                  
						<hr />
						<div class="row">
							<div class="col">
								<h5>Details of Applied for</h5>
							</div>
						</div>
						<hr />
						<!-- 4th row -->
					  	<div class="row">
                    		<div class="col">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Permission Type<sup style="color:red">*</sup></label>
                          		<div class="col-sm-8">
                          			<select name="permission_type" id="selectprmsn" class="form-control" >
			                        <option value="">Select Permission Type</option>
					                @if(count($permission_type)>0)
					                @foreach ($permission_type as $key=>$rosuper_list)
					                <option value="{{$rosuper_list->permsn_id}}{{'#'}}{{$rosuper_list->permission_type_id}}" >{{$rosuper_list->permission_name}}</option>
					                @endforeach
					                @else
					                <!-- <option >Permission Type Is Not Available</option> -->
					                
					                @endif
			                        </select>                       
	                       			<span class="text-danger">{{ $errors->first('permission_type') }}</span>
                          		</div>
                       			</div>
                    		</div>
							<div class="col" id="districtmsg">
                        		<div class="form-group row" >
                          			<label class="col-sm-4 form-control-label">District<sup style="color:red">*</sup></label>
                          		<div class="col-sm-8">
		                          	<select  name="district" id="district" class="form-control">
		                          		<option value=""> Select District</option>
								  	</select>
						  			<span class="text-danger">{{ $errors->first('district') }}</span>
                          		</div>
                       			</div>
                    		</div>
                    	</div>

                    	<!-- 5th row-->
                    	<div class="row">
                        	<div class="col"  style="display:block;">
                        	<div class="form-group row">
                          		<label class="col-sm-4 form-control-label">PC<sup style="color:red">*</sup></label>
                          	<div class="col-sm-8">
		                        <select name="PC" id="pc" class="form-control" >
					            <option value="">Select PC</option>
				                </select>
                          	</div>
                        	</div>
                    		</div>

                    		<div class="col" id="assembly">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">AC<sup style="color:red">*</sup></label>
                         		<div class="col-sm-8">
		                          <select name="ac" id="ac" class="form-control">
		                          	<option value=""> Select AC</option>
								  </select>
								  <span class="text-danger">{{ $errors->first('ac') }}</span>
                          		</div>
                       			</div>
                    		</div> 
                        	
                    	</div>
                    	<!-- pc -->
                    	<div class="row">
                    		<div class="col" id="policestation">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Police Station<sup style="color:red">*</sup></label>
                          		<div class="col-sm-8">
		                          	<select name="police_station" id="ps" class="form-control" >
					                	<option value="">Select Police Station</option>
				                    </select>
		                    		<span class="text-danger">{{ $errors->first('police_station') }}</span>
                          		</div>
                        		</div>
                    		</div>

                    		<!-- style="display:none" -->
                    		<!-- <center><p style="color:red">Note :  Select PC, If you want vechicle permission for across the whole PC !</p></center> -->
                    		

                    		<div class="col" style="display:block">
                        		<div class="form-group row">
                        			<label class="col-sm-4 form-control-label">Poll Date<sup style="color:red">*</sup></label>
                          		<div class="col-sm-8" id="poll">
                          		</div> 
                        		</div>
                    		</div> 
                    	</div>
                    	<!-- pcend -->

                    	

                    	<!-- 6th row -->
                    	<div class="row">
                            <div class="col-md-12" id="permsn_doc">
							</div>
						</div>
						<!-- 7th row -->
                        <div class="row">
                        	<div class="col">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Event Start Date & Time<sup style="color:red">*</sup></label>
                          		<div class="col-sm-8">
                           			<input type="text" class="form-control" id="datetimepicker" placeholder="MM/DD/YYYY HH:MM AM/PM" name="start" >
                           			<span class="text-danger">{{ $errors->first('start') }}</span>
                           			<p style="color:red" id="date-comment">Permission to be applied 48 hour before !</p>
                          		</div>
                        		</div>
                    		</div>
                    		<div class="col">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">End Date & Time <sup style="color:red">*</sup></label>
                         		<div class="col-sm-8">
                           			<input type="text" class="form-control" id="datetimepicker1" placeholder="MM/DD/YYYY HH:MM AM/PM" name="end">
                           			<span class="text-danger">{{ $errors->first('end') }}</span>
                          		</div>
                          		</div>
		                	</div>
		                </div>
		                <!-- 8th row -->
		                <div class="row">
		                	<div class="col" id="event" style="display:block">
                        		<div class="form-group row">
                          			<label class="col-sm-4 form-control-label">Event Place<sup style="color:red">*</sup></label>
                          		<div class="col-sm-8">
		                       		<select name="location1" id="location1" class="form-control">
		                       		<option value="">Select Location</option>
			                        @if(!empty($user_details_location))
			                        @foreach($user_details_location AS $data)
			                           <option value="{{$data->id}}">{{$data->location_name}}</option>
			                        @endforeach
		                                <option value="other">Add More Location</option>
		                                @else
					                		<option>Location Is Not Available</option>
			                        @endif
		                                
			                        </select>
			                        <span class="text-danger">{{ $errors->first('location') }}</span>
                                </div>
                          		</div>
                    		</div>
                    	
                    		<div class="col" >
                        		<div class="form-group row" id="other"  style="display:none;">
                          			<label class="col-sm-4 form-control-label">Add Location's<sup style="color:red">*</sup></label>
                          		<div class="col-sm-8">
	                           		<input type="text" class="form-control" name="other" placeholder="Enter event place">
	                        	</div>
                        		</div>
                    		</div>
                    	</div>
						@endforeach
						@endif
						<!-- <div class="row">
		                	<div class="col">
                        		<div class="form-group row">
                          		<div class="col-sm-12" id="poll">
                          		</div> 
                        		</div>
                    		</div>                    	
                    	</div> -->
						<div class="row">
							<div class="col">
								<div id="dvMap"></div>	
							</div>
						</div>
						<div class="form-group float-right">   
<!--	                    	<input type="submit" class="btn btn-primary btn-lg" id="disabled_button" value="Submit">    -->
                              <button type="submit" class="btn btn-primary text-center" name="AddPS" value="Save" id="disabled_button">Save</button>                       
	                    </div>
					 	</form>
					</div>
				</div>
			</div>
		</div>
</div>
</div>
</div>
</section>
@endsection

@section('script')

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4mlo-kY0vyBDIdeXffR2igqE5igx3piE&callback=LoadMap"></script>
<script>
	$(document).ready(function() {
            //new code
            var rstatus;
            var get_pollday;
            var max = "";
            var newd = "";
            var date = new Date();
            max = new Date();
            max.setDate(max.getDate()+5);
            date.setDate(date.getDate()+2);
            $('#datetimepicker1').on('click',function(){
                if(rstatus == 0)
                {
		 			jQuery('#datetimepicker1').datetimepicker({
		 				format:'DD-MM-YYYY HH:mm:ss',
		 				minDate: new Date()
		 			}).focus();

                        jQuery('#date-comment').css("display", "none");
                } else {
                
                var idperm = $('#selectprmsn').val();
		var ptypid= idperm.split('#');
                var get_ptypid = ptypid[1];
                 if(get_ptypid == 3 || get_ptypid == 8 || get_ptypid==13 || get_ptypid==14 || get_ptypid==15 || get_ptypid==16 || get_ptypid==17 || get_ptypid==18 || get_ptypid==20 || get_ptypid==25)
                {
                    var get_pollday1 = get_pollday.split('-');
                    var subget_pollday = get_pollday1[0];
                    var pppddd = get_pollday.split("-").reverse().join("-");
                    newd = new Date(pppddd);
//                    console.log(newd + 'sds');
//                   newd.setDate(newd - 2);
                   newd.setDate(newd.getDate()-2);
                   
                   if(date <= newd)
                   {
                       newd = newd;
                   }
                   else
                   {
                       newd = max;
                   }
                   var pd = newd.getDate();
                   var pm = newd.getMonth()+1;
                   var py =  newd.getFullYear();
                   
                   var newpdd = new Date(py+"-"+pm+"-"+pd+" 23:59:59");
                        jQuery('#datetimepicker1').datetimepicker({
                                format:'DD-MM-YYYY HH:mm:ss',
                                minDate: date,
                                maxDate: new Date(newpdd)
                        }).focus();

                        jQuery('#date-comment').css("display", "block");
                }
                else
                {
//                    console.log(max +'eee');
                   
		 			jQuery('#datetimepicker1').datetimepicker({
		 				format:'DD-MM-YYYY HH:mm:ss',
		 				minDate: date,
		 				maxDate: max
		 			}).focus();

                        jQuery('#date-comment').css("display", "block");
                }
            }
                });
                 $('#datetimepicker').on('click',function(){
                     
                if(rstatus == 0)
		 		{
		 			//console.log(status);
		 			jQuery('#datetimepicker').datetimepicker({
		 				format:'DD-MM-YYYY HH:mm:ss',
		 				minDate: new Date()
		 			}).focus();
//		 			
					jQuery('#date-comment').css("display", "none");
		 		}else
		 		{
		 			// alert(status+'hi');
//		 			console.log(status);
		 			jQuery('#datetimepicker').datetimepicker({
		 				format:'DD-MM-YYYY HH:mm:ss',
		 				minDate: date,
		 				maxDate: max
		 			}).focus();
//		 			
					jQuery('#date-comment').css("display", "block");
		 		}
                });
		// end of new code
//		$('#disabled_button').click(function(e){
//			$(this).prop('disabled', true);
//			$(this).val('Submiting...');
//			$('#permission').submit();
//		});
		
		var st = $('#state').val();
		 	jQuery.ajax({
		 	url:"{{url('/permissiondistrict')}}/"+st,
		 	type: "GET",
		 	dataType: "Json",
		 	success : function(dist){
		 		if(dist)
		 		{
		 			$("#district").empty();
  	                $("#district").append('<option value="">Select District</option>');
		 			$.each(dist,function(key, value)
	                {
	                    $('#district').append('<option value=' +value.DIST_NO+ '>' +value.DIST_NAME+ '</option>');
	                });
		 		}else{
		 			$("#district").empty();
		 		}
		 		
		 	}
		});

		$('#district').on('change',function(){
			$('#PC').empty();
			var districtID = $(this).val();
			var stateID = $('#state').val();
			$.ajax({
			   type:"GET",
			   url:"{{url('/getpcname')}}/"+stateID+"/"+districtID,
			   success:function(pcdetail){ 
			  	if(pcdetail)
			   	{
			   		// console.log(pcdetail);
					$("#pc").empty();
	  	            $("#pc").append('<option>Select PC</option>');
			        $.each(pcdetail,function(key, pcdetail)
			        {
			            $('#pc').append('<option value="'+pcdetail.PC_NO+'">'+pcdetail.PC_NAME_EN+'</option>');
			            });
			        }else{
			            $("#PC").empty();
			        }                 
			    }
			});
                        var idperm = $('#selectprmsn').val();
                        var ptypid= idperm.split('#');
                        var get_ptypid = ptypid[1];
                        var acIDD = 0;
                        if(get_ptypid == 8)
                        {
                         $.ajax({
		           type:"GET",
		           url:"{{url('/getpc')}}/"+stateID+"/"+acIDD+"/"+districtID,
		           success:function(poll_day){ 
                               get_pollday = poll_day;
		           	if(poll_day)
		           	{
		         		console.log(poll_day); 
		         		$("#poll").empty();	           		
		                $('#poll').append('<input type="text" class="form-control" value="'+poll_day+'" name="electiondate" id="electiondate" readonly style="display:block;">');
		           	}              
		           }
		        });
                        }
		});

		$('#pc').on('change',function(){
			var pc 			= $(this).val();
			var districtID 	= $('#district').val();
			var stateID 	= $('#state').val();
			var permission  = $("#selectprmsn").val();
			var permissionId = permission.split('#');
			// console.log(permissionId[1]);

		
		$.ajax({
		   type:"GET",
		   url:"{{url('/permissionAC')}}/"+stateID+"/"+districtID+"/"+pc,
		   success:function(acdata){ 
		   	// console.log(acdata);
			  	if(acdata)
			   	{
			   		// console.log(acdata);
					$("#ac").empty();
	  	            $("#ac").append('<option value="">Select AC</option>');
			        $.each(acdata,function(key, acdata)
			       		{
			            	$('#ac').append('<option value="'+acdata.AC_NO+'">'+acdata.AC_NAME+'</option>');
			        	});
			    }else{
			        $("#ac").empty();
			    }                 
			  	}
			});
		});

		$('#ac').on('change',function(){
		    var acID = $(this).val();
		    var stateID = $('#state').val();
		    $('#ps').empty();
		    
		        $.ajax({
		           type:"GET",
		           url:"{{url('/policeAC')}}/"+stateID+"/"+acID,
		           success:function(police){ 
		           	if(police)
		           	{
		           		$("#ps").empty();
  	                	$("#ps").append('<option>Select Police Station</option>');
		           		$.each(police,function(key, pol)
		                {
		                    $('#ps').append('<option value=' +pol.id+ '>' +pol.police_st_name+ '</option>');
		                });
		           	}else{
		           		$("#ps").empty();
		           	}
		                        
		           }
		        });
		         // ajax for find election date
                         var districtID = 0;
		        if(acID){
		        	$.ajax({
					           type:"GET",
					           url:"{{url('/getpc')}}/"+stateID+"/"+acID+"/"+districtID,
					           success:function(poll_day){ 
                                                        get_pollday = poll_day;
					           	if(poll_day)
					           	{   
					           		$("#poll").empty();      
					           		$("#poll").css("display", "block");		
					                    $('#poll').append('<input type="text" class="form-control" value="'+poll_day+'" name="electiondate" id="electiondate" readonly>');
					           	}else{
					           		$("#poll").empty();
					           	}
					                        
					           }
					        });
		        }
		});
                $('#ac').on('change',function(){
//                     Initialize
                    $('#datetimepicker1').datetimepicker({
                     format: "DD-MM-YYYY HH:mm:ss",
                    });
                    $('#datetimepicker1').datetimepicker('destroy');
                   });
		$('select').on('change',function(){
			var permissionid 	= $('#selectprmsn').val();
			var pID 			= permissionid.split('#');		
			// alert(pID[1]);
			if(pID[1] =='8'){
				$('#policestation,#assembly').css("display", "none");
			}else{
				$('#policestation,#assembly').css("display", "block");
			}	
			// console.log("XXX");
		});

		// 
		 $('#selectprmsn').on('change',function(){
		    var permissionid 	= $('#selectprmsn').val();
		    var pID 			= permissionid.split('#');
		    // alert(permissionid);
		    if(pID[1] > 0)
		    {
		    	$('#assembly,#policestation,#district,#poll').val('');
		    }

		  });

		 //

		$('#selectprmsn').on('change',function(){
		    var id=$(this).val();
		    var ptypid= id.split('#');
                    $('#datetimepicker1').datetimepicker({
                     format: "DD-MM-YYYY HH:mm:ss",
                    });
                    $('#datetimepicker1').datetimepicker('destroy');
		    
		    // alert(id)
		    // $("#policestation").css("display", "block");
		    if(ptypid[1]==3||ptypid[1]==6||ptypid[1]==8)
		    {
		    	if(ptypid[1]==8)
		    	{
		    		// district hide after selection of PC
		    		$('#pc').on('change',function(){
		    			var pc_id=$('#pc').val();
		    			// alert(pc_id);
		    			if(pc_id)
		    			{
		    				// $('#policestation').css("display", "none");
		    				// $("#assembly").css("display", "none");
		    				// ajax for find election date
		
		    				var SID = $('#state').val();
		    				
		    					$.ajax({
							           type:"GET",
							           url:"{{url('/getpollday')}}/"+SID+"/"+pc_id,
							           success:function(poll_day){ 
                                                                        get_pollday = poll_day;
							           	if(poll_day)
							           	{
							           		$("#poll").empty();	         		
							                    $('#poll').append('<input type="text" class="form-control" value="'+poll_day+'" name="electiondate" id="electiondate" readonly>');
							               
							           	}else{
							           		// $("#ps").empty();
							           		confirm(hello);
							           	}
							                        
							           }
							        });
		    				
		    			}
		    		});
		    		$("#event").css("display", "none");
		    	}else{
		    		$("#event").css("display", "none");

		    	}
		    	// if()
		    	$("#event").css("display", "none");
		    }else{
		    	$("#event").css("display", "block");

		    }
		    });
		
		// 
		$('#ac').on('change',function(){
		var stcode = jQuery("#state :selected").val();
		var district = jQuery("#district :selected").val();
		var ac = jQuery("#ac :selected").val();
	   
			jQuery.ajax({
			url: "{{url('/politicalparty/getlocations')}}",
			type: 'GET',
			dataType: 'json',
			data: {stcode:stcode,ac:ac},
			success: function(result){
				
			var jsonObj = JSON.stringify(result);
			var acselect = jQuery('select[name="location1"]');
			//acselect.empty();
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
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
			return function() {
			infowindow.setContent(result[i]['location_name']);
			infowindow.open(map, marker);
			}
			})(marker, i)); 
			}
			var src = 'https://cvigil.eci.nic.in/GIS/'+stcode+'.kmz';
			var kmlLayer = new google.maps.KmlLayer(src, {
			suppressInfoWindows: true,
			preserveViewport: false,
			map: map
			}); 
			var achtml = '';
			var otherhtml = '';
			achtml = achtml + '<option value=""> Select Location</option>';
		    var achtmlother = '<option value=other>Other</option>';
			jQuery.each(result, function(key, value) 
			{ 
			achtml = achtml + '<option value="'+value.id+'">'+value.location_name+', '+value.location_details+'</option>';
			});
			achtml = achtml + achtmlother;
			jQuery("select[name='location1']").html(achtml);	
			var achtmlend = '';
			jQuery("select[name='location1']").append(achtmlend)
				}
			});
		});

	
		jQuery("select[name='location1']").change(function()
		{
			

			var stcode = jQuery("select[name='state']").val();
			var district = jQuery("select[name='district']").val();
			var ac = jQuery("select[name='ac']").val();
			var locationid = jQuery(this).val();
			if(locationid == "other")
			{
			 $('#other').css('display', ($(this).val() == 'other') ? 'block' : 'none');
			}
			else
			{
			$('#other').css('display', ($(this).val() == 'other') ? 'display' : 'none');	
			}
			jQuery.ajax({
			url: "{{url('/politicalparty/getlatlong')}}",
			type: 'GET',
			data: {locationid:locationid},
			success: function(arr)
			{
			var jsonObj = JSON.parse(arr);
			placenameslocation = jsonObj[0]['location_name'];
			stcodes =  jsonObj[0]['ST_CODE'];
			$("#latitude").val(jsonObj[0]['latitude']);
			$("#longitude").val(jsonObj[0]['longitude']);
			$("#placenameslocation").val(jsonObj[0]['name']);
			LoadMap(jsonObj[0]['latitude'], jsonObj[0]['longitude']);			 
			}
			});
		});
		function LoadMap( lat, lng) 
		{
			var src = 'https://cvigil.eci.nic.in/GIS/'+stcodes+'.kmz';
			var markers = [{
			"lat": lat,
			"lng": lng,
			},	
			];
			var mapOptions = {
			center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
			zoom: 10,
			mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var myContent = 'test';
			var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
			var infowindow = new google.maps.InfoWindow({
			});
			var marker, i;	  
			marker = new google.maps.Marker
			({
			position: new google.maps.LatLng(markers[0].lat, markers[0].lng),
			map: map,
			icon:  'https://www.google.com/mapfiles/marker_black.png'
			});

		    google.maps.event.addListener(marker, 'click', (function(marker, i) {
			return function() {
			infowindow.setContent(placenameslocation);
			infowindow.open(map, marker);
			}
			})(marker, i)); 
			var src = 'https://cvigil.eci.nic.in/GIS/'+stcodes+'.kmz';
			var kmlLayer = new google.maps.KmlLayer(src, {
			suppressInfoWindows: true,
			preserveViewport: false,
			map: map
			});
		}
		// select permission type
		$('select#selectprmsn').change(function(){
            var permsn_id=$(this).val();
            var base_url = $("#base_url").val();
            var token = $('meta[name="csrf-token"]').attr('content');
            // alert(permsn_id);
             $.ajax({
                url:base_url+'/getSelectDetails',
                type:'POST',
                data: {_token:token,permsn_id:permsn_id},
                success:function(response)
                {
//                    alert(response);exit;
                    var cnt = response.length;
                    var str='';
                    var required_status='';
                    //alert(cnt);
                     $('#permsn_doc').css('display','');
                     str +="<table class='table table-bordered'><tr><th>S.no.</th><th>Document Details</th><th>Upload Document</th></tr>";
                     var j = 1;
                     for (var i=0; i<cnt; i++) {
                        var doc_name=response[i]['doc_name']
                        var doc_size=response[i]['doc_size']
                        var status=response[i]['required_status']
                        var stcode=response[i]['st_code']
                        if(status == 1)
                        {
                            required_status='Mandatory';
                        }
                        else
                        {
                            required_status='';
                        }
                        var file_name=response[i]['file_name']
                        if(response!=0)
                        {
                        	if(status == 1)
                        	{
                        		str +="<tr><td>"+j+"</td><td><p>"+ doc_name +" <span class='text-alert'> <a href='{{asset('uploads/permission-document')}}/"+stcode+"/"+file_name+" ' download>Download Format</a><sup style='color:red'>* Mandatory</sup></span></p></td><td><input type='file' id='file' name='permsndoc["+i+"][p_doc]' required></td></tr>"
                        		// /public/uploads/permission-document/
                        	}else{
                        		str +="<tr><td>"+j+"</td><td><p>"+ doc_name +" <span class='text-alert'> <a href='{{asset('uploads/permission-document')}}/"+stcode+"/"+file_name+" ' download>Download Format</a>Not Mandatory</span></p></td><td><input type='file' id='file' name='permsndoc["+i+"][p_doc]'></td></tr>"
                        	}
                         
                        }else
                        {
                             str += "<p style='color:red'>No Document Required.</p>";
                            
                        }
                       j++; 
                       }
                        str +="</table>";
                   $('#permsn_doc').html(str);
                     
                }
            });
        });
		//end neera
		// restrication masteer
		var StateId = $('#state').val();
		var date = new Date();
		var max = new Date();
		max.setDate(max.getDate()+5);
		date.setDate(date.getDate()+2);

		jQuery.ajax({
		 	url:"{{url('/datevalidation')}}/"+StateId,
		 	type: "GET",
		 	dataType: "Json",
		 	success : function(data){
		 		var status = data[0].restriction_status;
                                rstatus = status;
//		 		if(status == 0)
//		 		{
//		 			console.log(status);
//		 			$('#datetimepicker').datetimepicker({
//		 				format:'DD-MM-YYYY HH:mm:ss',
//		 				minDate: new Date()
//		 			});
//		 			$('#datetimepicker1').datetimepicker({
//		 				format:'DD-MM-YYYY HH:mm:ss',
//		 				minDate: new Date()
//		 			});
//
//		 			$('#date-comment').css("display", "none");
//					
//		 		}else
//		 		{
//		 			console.log(status);
//		 			$('#datetimepicker').datetimepicker({
//		 				format:'DD-MM-YYYY HH:mm:ss',
//		 				minDate: date,
//		 				maxDate: max
//		 			});
//		 			$('#datetimepicker1').datetimepicker({
//		 				format:'DD-MM-YYYY HH:mm:ss',
//		 				minDate: date,
//		 				maxDate: max
//		 			});
//					
//					$('#date-comment').css("display", "block");
//		 		}
		 	}
		});

		// $(function () { 		date-comment
		//   var date = new Date();
		//   var max = new Date();
		//   max.setDate(max.getDate()+5);
		//   date.setDate(date.getDate()+2);
		//   $('#datetimepicker').datetimepicker({ 
		//   	format:'DD-MM-YYYY HH:mm:ss',
		//    	minDate: date,
		// 	maxDate: max
		//   });
		// });
		// $(function () { 
		//   var date = new Date();
		//   var datemax = new Date();
		//   date.setDate(date.getDate()+2);
		//   datemax.setDate(datemax.getDate()+5);
		//   $('#datetimepicker1').datetimepicker({ 
		//   	format:'DD-MM-YYYY HH:mm:ss',
		//    	minDate: date,
		// 	maxDate:datemax
		//   });
		// });


		// $('#datetimepicker').datetimepicker({ 
		// 			  format:'DD-MM-YYYY HH:mm:ss',
		// 			  minDate: new Date()
		// 			});
		// 			$('#datetimepicker1').datetimepicker({ 
		// 			  format:'DD-MM-YYYY HH:mm:ss',
		// 			  minDate: new Date()
		// 			});

		// $('#datetimepicker').datetimepicker({ 
		// 			  format:'DD-MM-YYYY HH:mm:ss',
		// 			  minDate: date,
		// 			  maxDate: max
		// 			});
		// 			$('#datetimepicker1').datetimepicker({ 
		// 			  format:'DD-MM-YYYY HH:mm:ss',
		// 			  minDate: date,
		// 			  maxDate: max
		// 			});
	});
    function checkForm(form) // Submit button clicked
    {
        form.AddPS.disabled = true;
        form.AddPS.value = "Please wait...";
        return true;
    }

</script>
@endsection