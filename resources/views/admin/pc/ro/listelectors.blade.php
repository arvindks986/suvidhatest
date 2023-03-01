@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Electors Details')
@section('content')
<?php $st = getstatebystatecode($ele_details->ST_CODE);
$pc = getpcbypcno($ele_details->ST_CODE, $ele_details->CONST_NO);

?>

@if($errors->any())
<div class="alert alert-info">{{$errors->first()}}</div>
@endif
@if (session('success_mes'))
    <div class="alert alert-success"> {{session('success_mes') }}</div>
@endif
@if (session('errors_mes'))
    <div class="alert alert-danger"> {{session('errors_mes') }}</div>
@endif
<main role="main" class="inner cover mb-3">

  <section>
    <div class="container-fluid">
      <div class="row">
        <div class="card text-left mt-3" style="width:100%; margin:0 auto;">
          <div class=" card-header">
            <div class=" row">

              <div class="col form-inline">
                <h6 class="mr-auto">AC Wise Electors Details</h6>
                <p class="mb-0 text-right"><b class="bolt">State Name:</b>
                  <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b class="bolt">PC Name:</b>
                  <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;
                </p>
              </div>
            </div>
          </div>
          <?php $netmale = 0;
          $netfemale = 0;
          $netothers = 0;
          $nettotal = 0;
          $gtotal = 0;
          $gnettotal = 0;
          $netservot = 0; ?>
          <div class="card-body">
            <table class="table table-striped table-bordered table-hover" style="width:100%">
              <thead>
                <tr>
                  <th>Serial No</th>
                  <th>AC No / Name</th>
                  <th>Male</th>
                  <th>Female</th>
                  <th>Other</th>
                  <th>Total</th>
                  <th>Service Vote</th>
                  <th>G. total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @php
                $count = 1;

                @endphp

                <?php //echo "<pre>"; print_r($listac); die;
                ?>

                @forelse ($listac as $key=>$list)
                <?php $ele = getcdacelectorsdetails($ele_details->ST_CODE, $list->AC_NO);
                //electors total

                if (!empty($ele->electors_total)) {
                  $et = $ele->electors_total;
                } else {
                  $et = 0;
                }

                //electors service

                if (!empty($ele->electors_service)) {
                  $es = $ele->electors_service;
                } else {
                  $es = 0;
                }

                //electors male

                if (!empty($ele->electors_male)) {
                  $em = $ele->electors_male;
                } else {
                  $em = 0;
                }

                //electors female

                if (!empty($ele->electors_female)) {
                  $ef = $ele->electors_female;
                } else {
                  $ef = 0;
                }

                //electors other

                if (!empty($ele->electors_other)) {
                  $eo = $ele->electors_other;
                } else {
                  $eo = 0;
                }


                $gtotal = $et + $es;  ?>

                <tr>
                  <td>{{ $count }}</td>
                  <td>{{$list->AC_NO}}-{{ $list->AC_NAME }}</td>
                  <td>{{$em }}</td>
                  <td>{{$ef }}</td>
                  <td>{{$eo }}</td>
                  <td>{{$et }}</td>
                  <td>{{$es }}</td>
                  <td>{{$gtotal }}</td>

                  <td>

                    <button type="button" class="btn btn-primary electrolpopup" data-toggle="modal" data-target="#myModal" data-ac_no="{{$list->AC_NO}}" data-male="{{$em }}" data-female="{{$ef }}" data-other="{{$eo }}" data-total="{{$et }}" data-ser="{{$es }}">Edit</button>

                  </td>

                </tr>
                <?php $netmale = $netmale + $em;
                $netfemale = $netfemale + $ef;
                $netothers = $netothers + $eo;
                $nettotal = $nettotal + $et;
                $gnettotal = $gnettotal + $gtotal;
                $netservot = $netservot + $es; ?>
                @php $count++; @endphp
                @empty
                <tr>
                  <td colspan="5">No Data Found For Election Nomination Data</td>
                </tr>
                @endforelse
                <tr bgcolor="CCCCCC">
                  <td colspan="2">Total</td>
                  <td>{{$netmale }}</td>
                  <td>{{$netfemale }}</td>
                  <td>{{$netothers }}</td>
                  <td>{{$nettotal }}</td>
                  <td>{{$netservot }}</td>
                  <td>{{$gnettotal}}</td>
                  <td>&nbsp;</td>
                </tr>
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
        <h4 class="modal-title">Update Electors Data</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="{{url('ropc/verifyac-wise-electors-details')}}" id="ElectorsDetailsUpdate">

          {{ csrf_field() }}

          <input type="hidden" id="ac_no" class="form-control" name="ac_no" value="">
          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Male <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_male" maxsize="6" minsize="1" class="form-control" name="electors_male" value="">
              <span class="text-danger"></span>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Female <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_female" maxsize="6" minsize="1" class="form-control" name="electors_female" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Other <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_other" maxsize="6" minsize="1" class="form-control" name="electors_other" value="">
              <span class="text-danger"></span>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Total <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="electors_total" maxsize="6" minsize="1" class="form-control" name="electors_total" value="">
              <span class="text-danger"></span>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 form-control-label">Service Voters <sup>*</sup></label>
            <div class="col-sm-8">
              <input type="text" id="service_total" maxsize="6" minsize="1" class="form-control" name="service_total" value="">
              <span class="text-danger"></span>
            </div>
          </div>
          <input type="submit" name="Update" value="Update">

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
  $(document).on("click", ".electrolpopup", function() {


    male = $(this).attr('data-male');
    female = $(this).attr('data-female');
    other = $(this).attr('data-other');
    total = $(this).attr('data-total');
    ac_no = $(this).attr('data-ac_no');
    service_total = $(this).attr('data-ser');

    $('#electors_male').val(male);
    $('#electors_female').val(female);
    $('#electors_other').val(other);
    $('#electors_total').val(total);
    $('#ac_no').val(ac_no);
    $('#service_total').val(service_total);
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
  $("#ElectorsDetailsUpdate").validate({
    rules: {
      electors_male: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      electors_female: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      electors_other: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      electors_total: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
    },
    messages: {
      electors_male: {
        required: "Male Numbers required.",
        number: "Male should be numbers only.",
        noSpace: "Enter Male without space.",
        minlength: "Minlength length of Male should be 1 characters.",
        maxlength: "Maximum length of Male should be 7 characters.",
      },
      electors_female: {
        required: "Female Numbers required.",
        number: "Female should be numbers only.",
        noSpace: "Enter Female without space.",
        minlength: "Minlength length of Female should be 1 characters.",
        maxlength: "Maximum length of Female should be 7 characters.",
      },
      electors_other: {
        required: "Other Numbers required.",
        number: "Other should be numbers only.",
        noSpace: "Enter Other without space.",
        minlength: "Minlength length of Other should be 1 characters.",
        maxlength: "Maximum length of Other should be 7 characters.",
      },
      electors_total: {
        required: "Total Numbers required.",
        number: "Total should be numbers only.",
        noSpace: "Enter Total without space.",
        minlength: "Minlength length of Total should be 1 characters.",
        maxlength: "Maximum length of Total should be 7 characters.",
      },
      service_total: {
        required: "Service Voter required.",
        number: "Service Voter should be numbers only.",
        noSpace: "Enter Service Voter without space.",
        minlength: "Minlength length of Service Voter should be 1 characters.",
        maxlength: "Maximum length of Service Voter should be 7 characters.",
      },

    },
    errorElement: 'div',
    errorPlacement: function(error, element) {
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