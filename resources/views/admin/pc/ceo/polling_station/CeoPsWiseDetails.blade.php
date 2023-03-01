@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Polling Station Details')
@section('content')

 
@if($errors->any())
        <div class="alert alert-info">{{$errors->first()}}</div>
@endif

@if (session('error'))
           <div class="alert alert-info">{{ session('error') }}</div>
@endif

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


<section class="statistics color-grey pt-4 pb-2">

<div class="container-fluid">
  <div class="row">
  <div class="col-md-7 pull-left">
   <h4>{!! $heading_title !!}</h4>
  </div>

   <div class="col-md-5  pull-right text-right">

@foreach($buttons as $button)
<span class="report-btn"><a class="btn btn-primary" href="{{ $button['href'] }}" title="Download Excel" <?php if($button['target']){?> target='_blank' <?php } ?> >{{ $button['name'] }}</a></span>
@endforeach
@if($lists)
	@if($ac_id)
		@if($lists->end_of_poll_finalize==0)
			@if($is_finalize_deo=='1')
			<span class="report-btn psdefinalize btn btn-warning btn-lg">Definalize PS Data</span>
				@if($is_finalize=='0')
				<span class="report-btn psfinalize btn btn-success btn-lg">Finalize PS Data</span>
				@endif
			@else
			<span class="report-btn btn btn-warning btn-lg">Waiting For ROPC Approval</span>	
			@endif
		@endif
	@endif
	@if($is_finalize)

	@endif 
@endif	
    </div> 

  </div>
</div>  
</section>

@if(isset($filter_buttons) && count($filter_buttons)>0)
<section class="statistics pt-4 pb-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
	  <a href="{{url('pcceo/turnout-publish-status-list')}}"><button class="btn btn-primary">AC Wise Turnout Publish Status</button></a>
	  @if(isset($filter_buttons) && count($filter_buttons)>0)
        @foreach($filter_buttons as $button)
            <?php $but = explode(':',$button); ?>
            <span class="pull-right" style="margin-right: 10px;">
            <span><b>{!! $but[0] !!}:</b></span>
            <span class="badge badge-info">{!! $but[1] !!}</span>

            </span>
            
        @endforeach
		@endif
      </div>
    </div>
  </div>
</section>
@endif

<section class="dashboard-header section-padding">
  <div class="container-fluid">
  
        
    <form class="row"  method="get" action="{{$action}}" id="pswisedataform">
      
          <input type="hidden" name="state" id="state" value="{{$state}}">
           <!---PC FILTER-->
          <div class="form-group col-md-3"> <label>PC Constituency </label> 
          
            <select name="pc_id" id="pc_id" class="form-control">
              <option value="">Select PC</option>
            @if(isset($consituencies))
            @foreach($consituencies as $a)
              @if($pc_id==$a['pc_no'])
                <option value="{{$a['pc_no']}}" selected="{{$pc_id}}" >{{$a['pc_name']}}</option> 
              @else 
                <option value="{{$a['pc_no']}}" >{{$a['pc_name']}}</option> 
              @endif   
            @endforeach
            @endif  
            </select>
          </div>

           <!---AC FILTER-->
          <div class="form-group col-md-3"> <label>AC Constituency </label> 
          
            <select id="ac_id" name="ac_id" class="form-control">
              <option value="">--- Select Assembly ---</option> 
            </select>
          </div>

          
         

        <div class="form-group col-md-3">
        <label class="col" for="">&nbsp;</label>
         <input type="submit" value="Submit" class="btn btn-primary">
       </div>


        </form>   
  
    
  </div>
</section>


<section class="mt-3">
  <div class="container">
<div class="row">
    <div class="col">
        
        <div class="alert alert-success showMsg" style="display:none;">Polling Station Data Finalized Successfully !</div>
         @if (session('error_mes'))
          <div class="alert alert-danger"> {{session('error_mes') }}</div>
        @endif
         @if (\Session::has('success'))
			<div class="alert alert-success">
				<ul>
					<li>{!! \Session::get('success') !!}</li>
				</ul>
			</div>
		@endif
         
    </div>
	@if($lists)
	@if($lists->end_of_poll_finalize == '1' && $ac_id <>'')
	<div class="col-md-12 text-center">
		<button type="button" class="btn btn-success btn-lg">AC Finalized & Turnout Successfully Published</button>
	</div>
	<div>&nbsp;</div>
	@endif
	@endif
  <div class="card text-left" style="width:100%; margin:0 auto;">
                
    <table   class="table table-striped table-bordered" style="width:100%">
        <thead> <tr>  <th colspan="4" align="center">Electors</th> <th colspan="4" align="center">End of Poll Turnout</th>
        <th colspan="4" align="center">Turnout % </th>  </tr>
          <tr>  <th>Male</th> <th>female</th><th>Other</th><th>total</th><th>Male</th> <th>female</th><th>Other</th><th>total</th>  
            <th>Male</th> <th>female</th><th>Other</th><th>total</th>
             </tr>
        </thead>
		
		
		@php $maleturnout_per = $femaleturnout_per = $othersturnout_per = $totalturnout_per =0; @endphp
		@if(isset($ac_data))
			@if( $ac_data->electors_male >0)
				@php $maleturnout_per = round((($ac_data->voter_male/$ac_data->electors_male)*100),2); @endphp
			@endif
			 
			@if($ac_data->electors_female)
				@php $femaleturnout_per = round((($ac_data->voter_female/$ac_data->electors_female)*100),2); @endphp
			@endif
			 
			@if($ac_data->electors_other)
				@php  $othersturnout_per = round((($ac_data->voter_other/$ac_data->electors_other)*100),2);  @endphp
			@endif
			
			@if($ac_data->electors_total)
				@php  $totalturnout_per = round((($ac_data->voter_total/$ac_data->electors_total)*100),2);  @endphp
			@endif
		
        @endif

        <tr><td>@if(isset($ac_data)) {{$ac_data->electors_male}} @endif</td><td>@if(isset($ac_data)) {{$ac_data->electors_female}} @endif</td> <td>@if(isset($ac_data)) {{$ac_data->electors_other}} @endif</td><td>@if(isset($ac_data)) {{$ac_data->electors_total}} @endif</td>
        <td>@if(isset($ac_data)) {{$ac_data->voter_male}} @endif</td><td>@if(isset($ac_data)) {{$ac_data->voter_female}} @endif</td> <td>@if(isset($ac_data)) {{$ac_data->voter_other}} @endif</td><td>@if(isset($ac_data)) {{$ac_data->voter_total}} @endif</td> 
        <td>{{$maleturnout_per}}%</td><td>{{$femaleturnout_per}}%</td> <td>{{$othersturnout_per}}%</td><td>{{$totalturnout_per}}%</td>
            </tr>
    </table>

     @if($is_finalize == '1')
		 
	 @if($lists->end_of_poll_finalize==0) 
       <div class="row"><div class="col-md-2 p-0 m-0" style="width: 100px;"></div>
      <div class="col-md-12 " style="margin-left:20px;">
         <label for="candidate_id" class="col-form-label">Editing of Voter Details will not be availaible after clicking on Publish Turnout Button</label> 
         
               <button type="button"  class="btn btn-primary custombtn"  onclick="return finalize();">Publish Turnout</button>
      </div>  
      </div>
	  
		@endif
	@endif
	  
    </div>
  
  
  </div>
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
      
            <table id="data_table_table" class="table table-striped table-bordered" style="width:100%"><thead>

      <tr><th colspan="14" class="text-center">{!! $heading_title !!}</th></tr>


       <tr>
        <th>Serial No</th>
          <th>PS No</th>
          <th>PS Name</th> 
          <th>PS Type</th> 
          <th>Electors Male</th> 
          <th>Electors Female</th> 
          <th>Electors Other</th> 
          <th>Electors Total</th> 
          <th>Voter Male</th> 
          <th>Voter Female</th> 
          <th>Voter Other</th> 
          <th>Voter Total</th> 
        <th>Action</th>
         
       </tr>


    </thead>
        <tbody>
           @php  
        $count = 1;
         
          $TotalElectorMale = 0;
          $TotalElectorFeMale = 0;
          $TotalElectorOther = 0;
          $TotalElector = 0;
          $TotalVoterMale = 0;
          $TotalVoterFeMale = 0;
          $TotalVoterOther = 0;
           $TotalVoter = 0;



        @endphp

         @forelse ($results as $key=>$listdata)

         @php

         $TotalElectorMale   +=$listdata->electors_male;
         $TotalElectorFeMale +=$listdata->electors_female;
         $TotalElectorOther  +=$listdata->electors_other;
         $TotalElector       +=$listdata->electors_total;
         $TotalVoterMale     +=$listdata->voter_male;
         $TotalVoterFeMale     +=$listdata->voter_female;
         $TotalVoterOther     +=$listdata->voter_other;
         $TotalVoter          +=$listdata->voter_total;
        

         @endphp


          <tr>
             <td>{{ $count }}</td>
            <td>{{$listdata->PS_NO }}</td>
            <td>{{$listdata->PS_NAME_EN }}</td>
            <td>{{$listdata->PS_TYPE }}</td>
            <td>{{$listdata->electors_male }}</td>
            <td>{{$listdata->electors_female }}</td>
            <td>{{$listdata->electors_other }}</td>
            <td>{{$listdata->electors_total }}</td>
            <td>{{$listdata->voter_male }}</td>
            <td>{{$listdata->voter_female }}</td>
            <td>{{$listdata->voter_other }}</td>
            <td>{{$listdata->voter_total }}</td>
          <td>
		  
		  <?php if($lists->end_of_poll_finalize==0) { ?>
						
		  <?php if($listdata->ro_ps_finalize == 0) { ?>
						 
		  <button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-etotal="{{$listdata->electors_total }}" data-vmale="{{$listdata->voter_male }}" data-vfemale="{{$listdata->voter_female }}" data-vother="{{$listdata->voter_other }}" data-vtotal="{{$listdata->voter_total }}" data-psname="{{$listdata->PS_NAME_EN }}" data-psno="{{$listdata->PS_NO }}" data-ccode="{{$listdata->CCODE }}">Edit</button>
		  
		   <?php } else { echo "Finalized"; } ?>
						
		   <?php } else {  echo "Finalized"; } ?>
						
		  </td>
         
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td class="text-center" colspan="14">No Data Found For Polling Station</td>                 
              </tr>
          @endforelse   

          <tr>
            <td><b>Total</b></td>
            <td></td>
            <td></td>
             <td></td>
            <td><b>{{$TotalElectorMale}}</b></td>
            <td><b>{{$TotalElectorFeMale}}</b></td>
            <td><b>{{$TotalElectorOther}}</b></td>
            <td><b>{{$TotalElector }}</b></td>
            <td><b>{{$TotalVoterMale}}</b></td>
            <td><b>{{$TotalVoterFeMale}}</b></td>
            <td><b>{{$TotalVoterOther}}</b></td>
            <td><b>{{$TotalVoter}}</b></td>
          </tr>  
        
       </tbody></table>

         </div><!-- End Of  table responsive -->  
      </div><!-- End Of intra-table Div -->   
        
         
      </div><!-- End Of random-area Div -->
      
    </div><!-- End OF page-contant Div -->
    </div>      
  </div><!-- End Of parent-wrap Div -->
  </div> 

  <!--EDIT POP UP STARTS-->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Polling Station <span id="psnameid"></span>-<span id="psnoid"></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <form class="form-horizontal" method="POST" action="{{url('pcceo/CeoPsWiseDetailsUpdate')}}" id="CeoPsWiseDetailsUpdate">

         {{ csrf_field() }}
                         
         <input type="hidden" name="psnoinput" id="psnoinput" value="">
         <input type="hidden" name="psccode" id="psccode" value="">
         <input type="hidden" name="pc_no" id="pc_no" value="{{$pc_id}}">
         <input type="hidden" name="ac_no" id="ac_no" value="{{$ac_id}}">

         <div class="form-group row">
          <label class="col-sm-4 form-control-label">Electors Male <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="electors_male"  maxsize="6" minsize="1" class="form-control" name="electors_male" value="">
           <span class="text-danger"></span>
          </div>
        </div>

          <div class="form-group row">
          <label class="col-sm-4 form-control-label">Electors Female <sup>*</sup></label>
          <div class="col-sm-8">
          <input type="text" id="electors_female"  maxsize="6" minsize="1" class="form-control" name="electors_female" value="">
          <span class="text-danger"></span>
          </div>
        </div>
        

    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Electors Other <sup>*</sup></label>
          <div class="col-sm-8">
           <input type="text" id="electors_other"  maxsize="6" minsize="1" class="form-control" name="electors_other" value="">
           <span class="text-danger"></span>
          </div>
        </div>  
        

    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Electors Total <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="electors_total" maxsize="6" minsize="1" class="form-control" name="electors_total" value="">
           <span class="text-danger"></span>
          </div>
    </div>

    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Voter Male <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="voter_male" maxsize="6" minsize="1" class="form-control" name="voter_male" value="">
           <span class="text-danger"></span>
          </div>
    </div>


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Voter Female <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="voter_female" maxsize="6" minsize="1" class="form-control" name="voter_female" value="">
           <span class="text-danger"></span>
          </div>
    </div>


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Voter Other <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="voter_other" maxsize="6" minsize="1" class="form-control" name="voter_other" value="">
           <span class="text-danger"></span>
          </div>
    </div>


    <div class="form-group row">
          <label class="col-sm-4 form-control-label">Voter Total <sup>*</sup></label>
          <div class="col-sm-8">
              <input type="text" id="voter_total" maxsize="6" minsize="1" class="form-control" name="voter_total" value="">
           <span class="text-danger"></span>
          </div>
    </div>

        <input type="submit" name="Update">
              
    </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
  <!--EDIT POP UP ENDS-->
<div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">               					
				<h4 class="modal-title w-100">Are you sure you want to finalize End of Poll Voter turnout data?</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p><span style="color:red">After Publish, the Voter turnout percentage will be updated for public through Voter turnout app.<span></p>
            </div>
			<form method="post" action="<?php echo url('pcceo/publish-turnout'); ?>" id="publishTurnoutFrm">
				{{ csrf_field() }}
				<input type="hidden" name="ac_no" value="{{$ac_id}}">
				<input type="hidden" name="dist_no" value="{{$dist_no}}">
			</form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok confirm_button" onclick="submitForm();">Confirm</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!--**********FORM VALIDATION STARTS**********-->
<!--<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>
-->

<script type="text/javascript">
function finalize(){
	 $('#confirm').modal('show');
}
	
function submitForm(){
	//window.location.href = "<?php echo url('roac/turnout/publish-turnout'); ?>";
	document.getElementById("publishTurnoutFrm").submit();
}

$(document).on("click", ".psdefinalize", function () {        
        var ac_no = $('#ac_id').val();

        $.ajax({
          headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
          url: "<?php echo url('/pcceo/CeoPsDefinalizeUpdate') ?>",
          type: "POST",
          //data:'ac_no='+ac_no+'_token=<?php echo csrf_token() ?>',
          data:{ac_no:ac_no},
          cache: false,
          success: function () {
                  $(".showMsg").show().text('Polling Station Data Definalized Successfully !');;
                    setTimeout(function(){location.reload();}, 3000);
                   $(".updated").css("display", "block");
              } 
        });

    });

    $(document).on("click", ".psfinalize", function () {        
        var ac_no = $('#ac_id').val();

        $.ajax({
          headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
          url: "<?php echo url('/pcceo/CeoPsFinalizeUpdate') ?>",
          type: "POST",
          //data:'ac_no='+ac_no+'_token=<?php echo csrf_token() ?>',
          data:{ac_no:ac_no},
          cache: false,
          success: function () {
                   $(".showMsg").show().text('Polling Station Data Finalized Successfully !');;
                    setTimeout(function(){location.reload();}, 3000);
                   $(".updated").css("display", "block");
              } 
        });

    });

$(document).on("click", ".PsWiseDetailspopup", function () {


       emale = $(this).attr('data-emale');
       efemale = $(this).attr('data-efemale');
       eother = $(this).attr('data-eother');
       etotal = $(this).attr('data-etotal');
       vmale = $(this).attr('data-vmale');
       vfemale = $(this).attr('data-vfemale');
       vother = $(this).attr('data-vother');
       vtotal = $(this).attr('data-vtotal');
       psname = $(this).attr('data-psname');
       psno = $(this).attr('data-psno');
       ccode = $(this).attr('data-ccode');
     
       $('#electors_male').val(emale);
       $('#electors_female').val(efemale);
       $('#electors_other').val(eother);
       $('#electors_total').val(etotal);
       $('#voter_male').val(vmale);
       $('#voter_female').val(vfemale);
       $('#voter_other').val(vother);
       $('#voter_total').val(vtotal);
       $('#psnameid').text(psname);
       $('#psnoid').text(psno);
       $('#psnoinput').val(psno);
       $('#psccode').val(ccode);

       
      

   });
  //*******************EXTRA VALIDATION METHODS STARTS********************//
  //maxsize
  $.validator.addMethod('maxSize', function(value, element, param) {
    return this.optional(element) || (element.files[0].size <= param) 
  });
  //minsize
  $.validator.addMethod('minSize', function(value, element, param) { 
      return this.optional(element) || (element.files[0].size >= param) 
  });
  //alphanumeric
  $.validator.addMethod("alphnumericregex", function(value, element) {
      return this.optional(element) || /^[a-z0-9\._\s]+$/i.test(value);
    });
  //alphaonly
  $.validator.addMethod("onlyalphregex", function(value, element) {
  return this.optional(element) || /^[a-z\.\s]+$/i.test(value);
  });
  //without space
  $.validator.addMethod("noSpace", function(value, element) { 
    return value.indexOf(" ") < 0 && value != ""; 
  }, "No space please and don't leave it empty");
//*******************EXTRA VALIDATION METHODS ENDS********************//

//*******************ECI FILTER FORM VALIDATION STARTS********************//
$("#pswisedataform").validate({
    rules: {
            
              pc_id: { required: true,number:true},
              ac_id: { required: true,number:true},
            },
  messages: { 
                  pc_id: {
                      required: "PC required.",
                      number: "PC should be numbers only.",
                  },
                  ac_id: {
                      required: "AC required.",
                      number: "AC should be numbers only.",
                  },
                 
            },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = $(element).data('error');
              if (placement) {
                  $(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//********************ECI FILTER FORM VALIDATION ENDS********************//  

//ASSEMBY ONCHAGE FUNCTION STARTS
    function get_ac_list_by_st_pc() {
        var default_ac = "<?php echo $ac_id; ?>";
        var state = jQuery('#state').val();
        var pc_id = jQuery('#pc_id').val();
        var ac_id = jQuery('#pc_id').val();
        var election_id = "<?php echo $user_data['election_id']; ?>";
       
        jQuery.ajax({
             url: APP_URL+"/get_ac_list_by_st_pc/"+state+'/'+pc_id,
             type: 'GET',
             //data:  "state="+state+"pc_id="+pc_id,
             data: {'state' : state,'pc_id' : pc_id,'election_id' : election_id, '_token': '{{csrf_token()}}'},
             success: function(response) {
             if(response.status == 200 && response.error==false && response.acdata!=''){ 

                       var output = [];
                       $.each(response.acdata, function(key, value)
                       {
                         if(default_ac == value['AC_NO']){
                         output.push('<option value="'+ value['AC_NO'] +'" selected="selected">'+ $.trim(value['AC_NAME']) +'</option>');
                        }else{
                          output.push('<option value="'+ value['AC_NO'] +'">'+ $.trim(value['AC_NAME']) +'</option>');
                        }
                       });
                       $('#ac_id').html(output.join(''));
                   } else {
                       /*alert('Enternal Server Error!');*/
                   }
           }
        });
        
    }

  jQuery(document).ready(function(e){

    jQuery('#pc_id').change(function(e){
      get_ac_list_by_st_pc();
    });

  });


<?php if(isset($pc_id) && $pc_id){ ?>
      get_ac_list_by_st_pc();
    <?php } ?>
//ASSEMBY ONCHANGE FUNCTION ENDS 


//*******************POLLIN STATION FORM VALIDATION STARTS********************//
$("#CeoPsWiseDetailsUpdate").validate({
    rules: {
              electors_male: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_female: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_other: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              electors_total: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_male: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_female: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_other: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
              voter_total: { required: true,number:true,noSpace: true,minlength:1, maxlength: 7,},
            },
  messages: { 
                electors_male: {
                      required: "Electors Male Numbers required.",
                      number: "Electors Male should be numbers only.",
                      noSpace: "Enter Electors Male without space.",
                      minlength: "Minlength length of Electors Male should be 1 characters.",
                      maxlength: "Maximum length of Electors Male should be 7 characters.",
                  },
                  electors_female: {
                      required: "Electors Female Numbers required.",
                      number: "Electors Female should be numbers only.",
                      noSpace: "Enter Electors Female without space.",
                      minlength: "Minlength length of Electors Female should be 1 characters.",
                      maxlength: "Maximum length of Electors Female should be 7 characters.",
                  },
                  electors_other: {
                      required: "Electors Other Numbers required.",
                      number: "Electors Other should be numbers only.",
                      noSpace: "Enter Electors Other without space.",
                      minlength: "Minlength length of Electors Other should be 1 characters.",
                      maxlength: "Maximum length of Electors Other should be 7 characters.",
                  },
                  electors_total: {
                      required: "Electors Total Numbers required.",
                      number: "Electors Total should be numbers only.",
                      noSpace: "Enter Electors Total without space.",
                      minlength: "Minlength length of Electors Total should be 1 characters.",
                      maxlength: "Maximum length of Electors Total should be 7 characters.",
                  },
                  voter_male: {
                      required: "Voter Male Numbers required.",
                      number: "Voter Male should be numbers only.",
                      noSpace: "Voter Enter Male without space.",
                      minlength: "Minlength length of Voter Male should be 1 characters.",
                      maxlength: "Maximum length of Voter Male should be 7 characters.",
                  },
                  voter_female: {
                      required: "Voter Female Numbers required.",
                      number: "Voter Female should be numbers only.",
                      noSpace: "Enter Female without space.",
                      minlength: "Minlength length of Voter Female should be 1 characters.",
                      maxlength: "Maximum length of Voter Female should be 7 characters.",
                  },
                  voter_other: {
                      required: "Voter Other Numbers required.",
                      number: "Voter Other should be numbers only.",
                      noSpace: "Enter Other without space.",
                      minlength: "Minlength length of Voter Other should be 1 characters.",
                      maxlength: "Maximum length of Voter Other should be 7 characters.",
                  },
                  voter_total: {
                      required: "Voter Total Numbers required.",
                      number: "Voter Total should be numbers only.",
                      noSpace: "Enter Voter Total without space.",
                      minlength: "Minlength length of Voter Total should be 1 characters.",
                      maxlength: "Maximum length of Voter Total should be 7 characters.",
                  },
            },
        errorElement: 'div',
          errorPlacement: function (error, element) {
              var placement = $(element).data('error');
              if (placement) {
                  $(placement).append(error)
              } else {
                  error.insertAfter(element);
              }
          }
});
//********************POLLIN STATION FORM VALIDATION ENDS********************//


</script>
@endsection
