@extends('admin.layouts.pc.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">
   
    <!--FILTER STARTS FROM HERE-->
 <div class=" card-header">
      <div class=" row">
            <div class="col">
               @if(Session::has('ScheduleList'))
              <form method="post" action="{{url('/eci/EciNominationStatePhase')}}" id="EciNominationStatePhase">
                 {{ csrf_field() }}

                 <!--PHASE LIST DROPDOWN STARTS-->
                  <select name="ScheduleList" id="ScheduleList">
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
                  
                  </select>
                   <!--PHASE LIST DROPDOWN ENDS-->

                  <input type="submit" value="Filter" class="btn btn-primary">
                  <input type="reset" value="Reset Filter" name="Cancel" class="btn">
              </form>
               @endif

            </div> 
            <div class="col"> @if($ScheduleList != '') <h4> States In Election Phase {{$ScheduleList}}</h4>  @else  @endif</div>
      </div>
</div>

 <!--FILTER ENDS HERE-->

<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row">
            <div class="col">
              <h4> List Of Election Schedule {{$user_data->placename}} 
             </h4>

            </div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp; 
              <a href="{{url('/eci/EciNominationStatePhaseExcel')}}/{{base64_encode($ScheduleList)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;

              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th> 
         <!--  <th>Phase No</th>  -->
          <th>State Name</th>
          <th>State Code</th>  
          <th>Issue of Notification</th> 
          <th>LD For Nominations</th> 
          <th>Scrutiny Date</th> 
          <th>LD For Withdrawl</th> 
          <th>Date Of Poll</th> 
        </tr>
        </thead>
        <tbody>
        @php  $count = 1; @endphp
         @forelse ($EciNominationStatePhase as $key=>$listdata)
          <tr>
            <td>{{ $count }}</td>
           <!--  <td>{{$listdata->sid }}</td> -->
            <td><a href="{{url('/eci/EciNominationStateWiseReport')}}/{{base64_encode($listdata->state)}}/{{base64_encode($ScheduleList)}}">{{$listdata->state_name }}</a></td>
            <td><a href="{{url('/eci/EciNominationStateWiseReport')}}/{{base64_encode($listdata->state)}}/{{base64_encode($ScheduleList)}}">{{$listdata->state }}</a></td>
            <td>{{GetReadableDateFormat($listdata->start_nomi_date) }}</td>
            <td>{{GetReadableDateFormat($listdata->last_nomi_date) }}</td>
            <td>{{GetReadableDateFormat($listdata->dt_nomi_scr) }}</td>
            <td>{{GetReadableDateFormat($listdata->last_wid_date) }}</td>
            <td>{{GetReadableDateFormat($listdata->poll_date) }}</td>
          </tr>
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Election Schedule </td>                 
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
$("#EciNominationStatePhase").validate({
    rules: {
              ScheduleList: { required: true,number: true},
            },
  messages: { 
                ScheduleList: {
                      required: "Phase required.",
                      number: "Phase should be numbers only.",
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



