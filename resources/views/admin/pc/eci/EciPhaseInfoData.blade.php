@extends('admin.layouts.pc.report-theme')
@section('content')
<main role="main" class="inner cover mb-3">
    <!--FILTER STARTS FROM HERE-->
 <div class=" card-header">
  
               @if(Session::has('ScheduleList'))
              <form method="post" action="{{url('/eci/EciPhaseInfoDataCandWiseForm')}}" id="EciPhaseInfoDataCandWiseForm">
                 {{ csrf_field() }}
				     <div class=" row">
          
		<div class="col-md-8">
                 <!--PHASE LIST DROPDOWN STARTS-->
				 <label for="phaseid">Select Phase</label>
                  <select name="phaseid" id="phaseid" class="form-control" >
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
</div>
<div class="col-md-4">
<label for="">&nbsp;</label>
<div>
                  <input type="submit" value="Filter" class="btn btn-primary">
                  <input type="reset" value="Reset Filter" name="Cancel" class="btn"></div>
				  </div>
				  </div>
              </form>
               @endif

            </div> 


            
 


 <!--FILTER ENDS HERE-->
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left mt-3" style="width:100%; margin:0 auto;">
      <div class=" card-header">
      <div class=" row d-flex align-items-center">
            <div class="col"><h4>List Of All Election Nomination {{$user_data->placename}}</h4></div> 
              <div class="col"><p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b> 
              <span class="badge badge-info"></span>&nbsp;&nbsp;  <a href="{{url('/eci/EciPhaseInfoDataPdf')}}/" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
              <a href="{{url('/eci/EciPhaseInfoDataExcel')}}/" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;

              <button type="button" id="Cancel" class="btn btn-primary" onclick="window.history.back();">Back</button>
              </p>
              </div>
            </div>
      </div>
   
 <div class="card-body">  
    <table class="table table-striped table-bordered table-hover" style="width:100%">
         <thead>
         <tr>
          <th>Serial No</th>
          <th>State/Uts</th> 
          <th>Total Nominations Filed</th> 
          <th>National Parties</th> 
          <th>State Parties</th> 
          <th>Other Parties</th> 
          <th>Independent</th> 
          <th>Male</th> 
          <th>Female</th> 
          <th>Others</th>
          <th>Total Valid Nominations</th> 
        </tr>
        </thead>
        <tbody>
        @php  
        $count = 1; 

        $TotalNomination = 0; 
        $TotalNational = 0;
        $TotalState = 0;
        $TotalOther= 0;
        $TotalIndependent = 0;
        $TotalMale = 0;
        $TotalFemale = 0;
        $TotalOthers = 0;
        $TotalValidNomination=0;


        @endphp

         @forelse ($EciPhaseInfoData as $key=>$listdata)

         @php 

         $TotalNomination             +=   $listdata->TOTAL_NOMINATION;
         $TotalNational               +=   $listdata->NATIONAL;
         $TotalState                  +=   $listdata->STATE;
         $TotalOther                  +=   $listdata->OTHER;
         $TotalIndependent            +=   $listdata->INDEPENDENT;
         $TotalMale                   +=   $listdata->male;
         $TotalFemale                 +=   $listdata->female;
         $TotalOthers                 +=   $listdata->others;
         $TotalValidNomination        +=   $listdata->total;

        @endphp


          <tr>
             <td>{{ $count }}</td>
            <!-- <td><a href="{{url('/eci/EciNominationStateWiseReport')}}/{{base64_encode($listdata->ST_CODE)}}">{{$listdata->ST_NAME }}</a></td> -->
            <td>{{$listdata->ST_NAME }}</td>
            <td>{{$listdata->TOTAL_NOMINATION }}</td>
            <td>{{$listdata->NATIONAL }}</td>
            <td>{{$listdata->STATE }}</td>
            <td>{{$listdata->OTHER }}</td>
            <td>{{$listdata->INDEPENDENT }}</td>
            <td>{{$listdata->male }}</td>
            <td>{{$listdata->female }}</td>
            <td>{{$listdata->others }}</td>
            <td><b>{{$listdata->total }}</b></td>
          </tr>
       
       @php  $count++;  @endphp
           @empty
                <tr>
                  <td colspan="4">No Data Found For Election Nomination Data</td>                 
              </tr>
          @endforelse
           <tr class="totalClass">
            <td><b>Total</b></td>
            <td></td>
            <td><b>{{$TotalNomination}}</b></td>
            <td><b>{{$TotalNational}}</b></td>
            <td><b>{{$TotalState}}</b></td>
            <td><b>{{$TotalOther}}</b></td>
            <td><b>{{$TotalIndependent}}</b></td>
            <td><b>{{$TotalMale}}</b></td>
            <td><b>{{$TotalFemale}}</b></td>
            <td><b>{{$TotalOthers}}</b></td>
            <td><b>{{$TotalValidNomination}}</b></td>
            
          </tr>
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
$("#EciPhaseInfoDataCandWiseForm").validate({
    rules: {
              phaseid: { required: true,number:true},
            },
  messages: { 
                phaseid: {
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



