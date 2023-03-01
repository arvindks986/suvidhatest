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
        <div class="col-sm-12">

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
        <div class="card text-left mt-3" style="width:100%; margin:0 auto;">
          <div class="card-header">
            <div class="row d-flex align-items-center">
              <div class="col">
                <h4>Polling Station Details {{$user_data->placename}}</h4>
              </div>
              <div class="col">
                <p class="mb-0 text-right"><b>Name:</b> <span class="badge badge-info">{{$user_data->placename}}</span> &nbsp;&nbsp; <b></b>
                  <span class="badge badge-info"></span>&nbsp;&nbsp;
                </p>
              </div>
              @if($showFinalizeBtn)
              <span class="report-btn psfinalize btn btn-warning btn-lg" onclick="psfinalize(this)" data-ac="{{$user_data->ac_no}}" data-statecode="{{$user_data->st_code}}">Finalize PS Data</span>
              @endif
            </div>
          </div>
          <section class="mt-3">
            <div class="container">
              <div class="row">
                <div class="card text-left" style="width:100%; margin:0 auto;">
                  <table class="table table-striped table-bordered" style="width:100%">
                    <thead>
                      <tr>
                        <th colspan="4" align="center">Electors</th>
                        <th colspan="4" align="center">End of Poll Turnout</th>
                        <th colspan="4" align="center">Turnout % </th>
                      </tr>
                      <tr>
                        <th>Male</th>
                        <th>female</th>
                        <th>Other</th>
                        <th>total</th>
                        <th>Male</th>
                        <th>female</th>
                        <th>Other</th>
                        <th>total</th>
                        <th>Male</th>
                        <th>female</th>
                        <th>Other</th>
                        <th>total</th>
                      </tr>
                    </thead>

                    <?php

                    $TotalElectorMaleNew = 0;
                    $TotalElectorFeMaleNew = 0;
                    $TotalElectorOtherNew = 0;
                    $TotalElectorNew = 0;
                    $TotalVoterMaleNew = 0;
                    $TotalVoterFeMaleNew = 0;
                    $TotalVoterOtherNew = 0;
                    $TotalVoterNew = 0;

                    $maleTurnoutPercentage = 0;
                    $femaleTurnoutPercentage = 0;
                    $otherTurnoutPercentage = 0;
                    $totalTurnoutPercentage = 0;

                    foreach ($PsWiseDetails as $key => $listdataNew) {

                      $TotalElectorMaleNew   += $listdataNew->electors_male;
                      $TotalElectorFeMaleNew += $listdataNew->electors_female;
                      $TotalElectorOtherNew  += $listdataNew->electors_other;
                      $TotalElectorNew       += $listdataNew->electors_total;
                      $TotalVoterMaleNew     += $listdataNew->voter_male;
                      $TotalVoterFeMaleNew     += $listdataNew->voter_female;
                      $TotalVoterOtherNew     += $listdataNew->voter_other;
                      $TotalVoterNew          += $listdataNew->voter_total;
                    }

                    ?>

                    <?php
                    if ($TotalVoterMaleNew > 0 && $TotalElectorMaleNew > 0) {
                      $maleTurnoutPercentage = round((($TotalVoterMaleNew / $TotalElectorMaleNew) * 100), 2);
                    }

                    if ($TotalVoterFeMaleNew > 0 && $TotalElectorFeMaleNew > 0) {
                      $femaleTurnoutPercentage = round((($TotalVoterFeMaleNew / $TotalElectorFeMaleNew) * 100), 2);
                    }

                    if ($TotalVoterOtherNew > 0 && $TotalElectorOtherNew > 0) {
                      $otherTurnoutPercentage = round((($TotalVoterOtherNew / $TotalElectorOtherNew) * 100), 2);
                    }

                    if ($TotalVoterNew > 0 && $TotalElectorNew > 0) {
                      $totalTurnoutPercentage = round((($TotalVoterNew / $TotalElectorNew) * 100), 2);
                    }

                    ?>

                    <tr>
                      <td>{{$TotalElectorMaleNew}}</td>
                      <td>{{$TotalElectorFeMaleNew}}</td>
                      <td> {{$TotalElectorOtherNew}}</td>
                      <td>{{$TotalElectorNew}}</td>
                      <td>{{$TotalVoterMaleNew}}</td>
                      <td>{{$TotalVoterFeMaleNew}}</td>
                      <td>{{$TotalVoterOtherNew}}</td>
                      <td>{{$TotalVoterNew}}</td>
                      <td>{{$maleTurnoutPercentage}}%</td>
                      <td>{{$femaleTurnoutPercentage}}%</td>
                      <td>{{$otherTurnoutPercentage}}%</td>
                      <td>{{$totalTurnoutPercentage}}%</td>
                    </tr>
                  </table>
                </div>




              </div>
            </div>
          </section>


          <div class="card-header">
            <div class=" row d-flex align-items-center">
              <!--<div class="col"><p class="text-center">Polling Station wise voter turnout has to be entered only after completion of Poll -->
              </p>
            </div>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>.
                <th>Serial No</th>
                <th>PS No</th>
                <th>PS Name</th>
                <th>Location Type</th>
                <th>PS Type</th>
                <th>Electors Male</th>
                <th>Electors Female</th>
                <th>Electors Other</th>
                <th>Electors Total</th>
                @if($showTableColumns)
                <th>Voter Male</th>
                <th>Voter Female</th>
                <th>Voter Other</th>
                <th>Voter Total</th>
                <th>Action</th>
                @endif
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

              @forelse ($PsWiseDetails as $key=>$listdata)


              @php

              $TotalElectorMale +=$listdata->electors_male;
              $TotalElectorFeMale +=$listdata->electors_female;
              $TotalElectorOther +=$listdata->electors_other;
              $TotalElector +=$listdata->electors_total;
              $TotalVoterMale +=$listdata->voter_male;
              $TotalVoterFeMale +=$listdata->voter_female;
              $TotalVoterOther +=$listdata->voter_other;
              $TotalVoter +=$listdata->voter_total;


              @endphp


              <tr>
                <td>{{ $count }}</td>
                <td>{{$listdata->PS_NO }}</td>
                <td>{{$listdata->PS_NAME_EN }}</td>
                <td>{{$listdata->LOCN_TYPE }}</td>
                <td>{{$listdata->PS_TYPE }}</td>
                <td>{{$listdata->electors_male }}</td>
                <td>{{$listdata->electors_female }}</td>
                <td>{{$listdata->electors_other }}</td>
                <td>{{$listdata->electors_total }}</td>
                @if($showTableColumns)
                <td>{{$listdata->voter_male }}</td>
                <td>{{$listdata->voter_female }}</td>
                <td>{{$listdata->voter_other }}</td>
                <td>{{$listdata->voter_total }}</td>
                <td>
                  @if($listdata->ro_ps_finalize == 0)
                  <button type="button" class="btn btn-primary PsWiseDetailspopup" data-toggle="modal" data-target="#myModal" data-ccode="{{$listdata->CCODE }}" data-target="#myModal" data-emale="{{$listdata->electors_male }}" data-efemale="{{$listdata->electors_female }}" data-efemale="{{$listdata->electors_female }}" data-eother="{{$listdata->electors_other }}" data-etotal="{{$listdata->electors_total }}" data-vmale="{{$listdata->voter_male }}" data-vfemale="{{$listdata->voter_female }}" data-vother="{{$listdata->voter_other }}" data-vtotal="{{$listdata->voter_total }}" data-psname="{{$listdata->PS_NAME_EN }}" data-psno="{{$listdata->PS_NO }}">Edit</button>
                  @else
                    Finalized
                  @endif
                </td>
                @endif
              </tr>

              @php $count++; @endphp
              @empty
              <tr>
                <td colspan="5">No Data Found For Election Nomination Data</td>
              </tr>
              @endforelse

              <tr>
                <td><b>Total</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>{{$TotalElectorMale}}</b></td>
                <td><b>{{$TotalElectorFeMale}}</b></td>
                <td><b>{{$TotalElectorOther}}</b></td>
                <td><b>{{$TotalElector }}</b></td>
                @if($showTableColumns)
                <td><b>{{$TotalVoterMale}}</b></td>
                <td><b>{{$TotalVoterFeMale}}</b></td>
                <td><b>{{$TotalVoterOther}}</b></td>
                <td><b>{{$TotalVoter}}</b></td>
                @endif
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
@if($showTableColumns)
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Polling Station <span id="psnameid"></span>-<span id="psnoid"></span></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form class="form-horizontal" method="POST" action="{{url('aro/voting/PsWiseDetailsUpdate')}}" id="PsWiseDetailsUpdate">
      <!-- Modal body -->
      <div class="modal-body">

          {{ csrf_field() }}

          <input type="hidden" name="psnoinput" id="psnoinput" value="">
          <input type="hidden" name="psccode" id="psccode" value="">
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
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-success" name="Update">
        </div>
      </form>

    </div>
  </div>
</div>
@endif
<!--EDIT POP UP ENDS-->

<!--**********FORM VALIDATION STARTS**********-->
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-validation/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('jquery-validation/additional-methods.min.js') }}"></script>

<!--**********FORM VALIDATIONS SCRIPT**********-->
<script type="text/javascript">
  function psfinalize(clicked_object) {
    var ac_no = clicked_object.getAttribute('data-ac');
    var statecode = clicked_object.getAttribute('data-statecode');

    $.ajax({
      url: "<?php echo url('/aro/voting/PsWiseFinalize') ?>",
      type: "POST",
      cache: false,
      data: '_token=<?php echo csrf_token() ?>',
      success: function() {
        $(".showMsg").show();
        setTimeout(function() {
          location.reload();
        }, 3000);
        $(".updated").css("display", "block");
      }
    });
  }
  $(document).on("click", ".PsWiseDetailspopup", function() {
    vmale = $(this).attr('data-vmale');
    vfemale = $(this).attr('data-vfemale');
    vother = $(this).attr('data-vother');
    vtotal = $(this).attr('data-vtotal');
    psname = $(this).attr('data-psname');
    psno = $(this).attr('data-psno');
    ccode = $(this).attr('data-ccode');

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

  //*******************POLLIN STATION FORM VALIDATION STARTS********************//
  $("#PsWiseDetailsUpdate").validate({
    rules: {
      voter_male: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      voter_female: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      voter_other: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
      voter_total: {
        required: true,
        number: true,
        noSpace: true,
        minlength: 1,
        maxlength: 7,
      },
    },
    messages: {
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
    errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    }
  });
</script>
@endsection