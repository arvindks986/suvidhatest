<div class=" card-header">
      <div class=" row">
            <div class="col">
               @if(Session::has('ScheduleList'))
              <form method="post" action="{{url('/pcceo/CeoCustomReportFilter')}}" id="CeoCustomReportFilter">
                 {{ csrf_field() }}
                 
                 
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
                 
                  <input type="submit" value="Filter" class="btn btn-primary">
                  <input type="reset" value="Reset Filter" name="Cancel" class="btn">
              </form>
               @endif
            </div> 
            <div class="col"></div>
      </div>
</div>



<!-- Validation  JavaScript -->

<!--**********FORM VALIDATION STARTS**********-->
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

//*******************CEO FILTER FORM VALIDATION STARTS********************//
$("#CeoCustomReportFilter").validate({
    rules: {
                ScheduleList: { noSpace: true, number: true},
            },
  messages: { 
             
                ScheduleList: {
                      noSpace: "Scedule ID must be without space.",
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
//********************CEO FILTER FORM VALIDATION ENDS********************//

</script>

<!--**********FORM VALIDATION ENDS*************-->