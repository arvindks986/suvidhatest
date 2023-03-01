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
<main role="main" class="inner cover mb-3">
  
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-3" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4>Polling Station Details {{$user_data->placename}}</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;  
              </p>
              </div>
            </div>
      </div>

  <div class=" card-header">
      <div class=" row d-flex align-items-center">
            
              <div class="col"><p class="text-center">Polling Station wise voter turnout has to be entered only after completion of Poll 
              </p>
              </div>
            </div>
      </div>

      
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
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

        @endphp

         @forelse ($PsWiseDetails as $key=>$listdata)


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
            <td><button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-etotal="{{$listdata->electors_total }}" data-vmale="{{$listdata->voter_male }}" data-vfemale="{{$listdata->voter_female }}" data-vother="{{$listdata->voter_other }}" data-vtotal="{{$listdata->voter_total }}" data-psname="{{$listdata->PS_NAME_EN }}" data-psno="{{$listdata->PS_NO }}">Edit</button></td>
         
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="5">No Data Found For Election Nomination Data</td>                 
              </tr>
          @endforelse         
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>

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
       <form class="form-horizontal" method="POST" action="{{url('aro/voting/PsWiseDetailsUpdate')}}" id="PsWiseDetailsUpdate">

       	 {{ csrf_field() }}
                         
         <input type="hidden" name="psnoinput" id="psnoinput" value="">

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


  <!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">

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

//*******************POLLIN STATION FORM VALIDATION STARTS********************//
$("#PsWiseDetailsUpdate").validate({
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
//**********FORM VALIDATION ENDS*************

function filter(){
    var url = "<?php echo $action ?>";
    var query = '';
    if(jQuery("#phase").val() != '' && jQuery("#phase").val() != 'undefined'){
      query += '&phase='+jQuery("#phase").val();
    }
    
    if(jQuery("#pc_no").val() != '' && jQuery("#pc_no").val() != 'undefined'){
      query += '&pc_no='+jQuery("#pc_no").val();
    }
    window.location.href = url+'?'+query.substring(1);
}

setTimeout(function(e){
    referesh_page();
},300000);

function referesh_page(){
    location.reload();
}

</script>



@endsection 

