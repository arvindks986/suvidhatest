@extends('admin.layouts.pc.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
    <!--FILTER STARTS FROM HERE-->
 <div class="card-header pt-3">
   
           
               @if(Session::has('ScheduleList'))
              <form method="post" action="{{url('/eci/EciCustomReportFilter')}}" id="EciCustomReportFilter">
		     <div class=" row">
                 {{ csrf_field() }}
                 <div class="col-sm-4">
                  <!--STATE LIST DROPDOWN STARTS-->
				  <label for="state">Select States</label>
                  <select name="state" id="state" class="form-control">
                      <option value="all">All States</option>
                     @php $statelist = getallstate(); @endphp

                      @foreach ($statelist as $state_List ))

                        @if (old('state') == $state_List->ST_CODE)
                              <option value="{{ $state_List->ST_CODE }}" selected>{{$state_List->ST_NAME}}</option>
                        @else
                              <option value="{{ $state_List->ST_CODE }}">{{$state_List->ST_NAME}}</option>
                        @endif
                      
                      @endforeach

                      @if ($errors->has('state'))
                      <span class="help-block">
                          <strong class="user">{{ $errors->first('state') }}</strong>
                      </span>
                      @endif
                  
                  </select></div>
                 <!--STATE LIST DROPDOWN ENDS-->

                 <!--PHASE LIST DROPDOWN STARTS--> <div class="col-sm-4">
				 <label for="ScheduleList">Select Phase</label>
                  <select name="ScheduleList" id="ScheduleList" class="form-control">
                  <option value="">Select Phase</option>
                  @php  $i = 1; @endphp
                  @foreach (Session::get('ScheduleList') as $Schedule_List ))

                  @if (old('ScheduleList') == $Schedule_List->SCHEDULEID)
                        <option value="{{ $Schedule_List->SCHEDULEID }}" selected>Phase {{$i}}</option>
                  @else
                        <option value="{{ $Schedule_List->SCHEDULEID }}">Phase {{$i}}</option>
                  @endif
                  
                   @php  $i++;  @endphp
                  @endforeach                 

                  @if ($errors->has('ScheduleList'))
                  <span class="help-block">
                      <strong class="user">{{ $errors->first('ScheduleList') }}</strong>
                  </span>
                  @endif
                  
                  </select></div>
                   <!--PHASE LIST DROPDOWN ENDS-->

                 
<div class="col-md-4">
<label for="">&nbsp;</label>
<div>
                  <input type="submit" value="Filter" class="btn btn-primary">
                  <input type="reset" value="Reset Filter" name="Cancel" class="btn"></div></div>
             
         
           
      </div>
	   </form>
               @endif
</div>

 <!--FILTER ENDS HERE-->

<section class="mt-5">
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4> List Of Election Schedule {{$user_data->placename}}</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; <a href="{{url('/eci/EciElectionSchedulePdf')}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci/EciElectionScheduleExcel')}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <!-- <th>Serial No</th> -->
          <th>Phase No</th> 
          <th>State</th>
          <th>PC Name</th> 
          <th>PC No</th> 
          <th>Issue of Notification</th> 
          <th>LD For Nominations</th> 
          <th>Scrutiny Date</th> 
          <th>LD For Withdrawl</th> 
          <th>Date Of Poll</th> 
        </tr>
        </thead>
        <tbody>
        @php  $count = 1; @endphp
         @forelse ($ScheduleSelectData as $key=>$listdata)
          <tr>
           <!--  <td>{{ $count }}</td> -->
            <td>Phase {{$listdata->sid }}</td>
            <td>{{$listdata->state_name }}</td>
            <td>{{$listdata->npc }}</td>
            <td>{{$listdata->cno }}</td>
            <td>{{GetReadableDateFormat($listdata->start_nomi_date) }}</td>
            <td>{{GetReadableDateFormat($listdata->last_nomi_date) }}</td>
            <td>{{GetReadableDateFormat($listdata->dt_nomi_scr) }}</td>
            <td>{{GetReadableDateFormat($listdata->last_wid_date) }}</td>
            <td>{{GetReadableDateFormat($listdata->poll_date) }}</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Election Schedule</td>                 
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


<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">
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
$("#EciCustomReportFilter").validate({
    rules: {
                state:        { required: true,noSpace: true},
                ScheduleList: { number: true},
            },
  messages: { 
                state: {
                      required: "Select state name.",
                      noSpace: "State name must be without space.",
                  },
                ScheduleList: {
                      number: "Scedule ID should be numbers only.",
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

</script>

<!--**********FORM VALIDATION ENDS*************-->
@endsection



