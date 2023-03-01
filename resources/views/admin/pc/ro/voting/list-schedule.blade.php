@extends('admin.layouts.pc.theme')
@section('title', 'Suvidha')
@section('bradcome', 'Poll Day Schedule')
@section('content')
<?php $st = getstatebystatecode($ele_details->ST_CODE);
$pc = getpcbypcno($ele_details->ST_CODE, $ele_details->CONST_NO);
$url = URL::to("/");
$j = 0;
?>

<main role="main" class="inner cover mb-3">

  <div class="container-fluid mt-3">
    <div class="row text-center mb-3">
      <div class="col">
        <span class="">
          <span class="badge badge-success" style="    font-size: 90px;  padding: 25px 50px;">{{$totalturnout_per}}%</span>
          <br />
          <span type="text" style="color: #28a745;  text-transform: uppercase;  letter-spacing: 3px;" class=" ">Voter Turn Out</span></span>
      </div>
    </div>
    <div class="row text-center">
      <div class="col">


        <span type="text" class="btn btn-outline-dark outlinDark">Female
          <span class="badge badge-light">{{$femaleturnout_per}}%</span>
        </span>

        <span type="text" class="btn btn-outline-dark outlinDark">Male
          <span class="badge badge-light">{{$maleturnout_per}}%</span>
        </span> <span type="text" class="btn btn-outline-dark outlinDark">Others
          <span class="badge badge-light">{{$othersturnout_per}}%</span>
        </span>


      </div>
    </div>
    <div class="row">





      <div class="card text-left mt-5" style="width:100%;">
        <div class=" card-header">
          <div class=" row">
            <div class="col">
              <h4>End of Poll Turnout Details </h4>
            </div>
            <div class="col">
              <p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp; <b>PC Name:</b>
                <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;
              </p>
            </div>

          </div>
        </div>
        <div class="row">

          @if($errors->any())
          <div class="alert alert-info">{{$errors->first()}}</div>
          @endif

          @if (session('error_mes'))
          <div class="alert alert-info">{{ session('error_mes') }}</div>
          @endif
          @if (session('success_mes'))
          <div class="alert alert-success"> {{ session('success_mes') }}</div>
          @endif



        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th rowspan="2">Sl. No.</th>
                <th rowspan="2"> AC Name</th>
                <th colspan="4" align="center">Electors</th>
                <th colspan="4" align="center">Voters</th>
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
              </tr>
            </thead> 
            <?php 
              $net_total_voter = 0;
              $net_other_voter = 0;
              $net_female_voter = 0;
              $net_male_voter = 0;

              $net_male_electors = 0;
              $net_female_electors = 0;
              $net_other_electors = 0;
              $net_total_electors = 0; 
            ?>
            <tbody>
              @if(isset($lists))
              @foreach($lists as $list)
              <?php $j++;
                $ac = getacbyacno($ele_details->ST_CODE, $list->ac_no);
                $ele = getcdacelectorsdetails($ele_details->ST_CODE, $list->ac_no);
                //electors total

                if (!empty($ele->electors_total)) {
                  $et = $ele->electors_total;
                } else {
                  $et = 0;
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
              ?>
              <tr>
                <td>{{$j}}</td>
                <td>{{$ac->AC_NO}}- {{$ac->AC_NAME}}</td>
                <td>@if(isset($ele)) {{$em}} @endif</td>
                <td>@if(isset($ele)){{$ef}} @endif</td>
                <td>@if(isset($ele)){{$eo}} @endif</td>
                <td>@if(isset($ele)){{$et}} @endif </td>
                <td>{{$list->total_male}}</td>
                <td>{{$list->total_female}}</td>
                <td>{{$list->total_other}}</td>
                <td>{{$list->total}}</td>
                <!-- <td>@if($list->end_of_poll_finalize==0) <button type="button" id="{{$list->id}}" class="btn btn-primary  btn-sm getdata" data-toggle="modal" data-target="#changestatus" data-id="{{$list->id}}" data-acno="{{$list->ac_no}}" data-pcno="{{$list->pc_no}}" data-acname="{{$ac->AC_NAME}}" data-male="{{$list->end_voter_male}}" data-female="{{$list->end_voter_female}}" data-others="{{$list->end_voter_other}}" data-total="{{$list->end_voter_total}}">Edit</button>@else Finalize by CEO @endif</td> -->
              </tr>
              @php($net_total_voter +=$list->total)
              @php($net_other_voter +=$list->total_other)
              @php($net_female_voter +=$list->total_female)
              @php($net_male_voter +=$list->total_male)

              @php($net_male_electors +=$em)
              @php($net_female_electors +=$ef)
              @php($net_other_electors +=$eo)
              @php($net_total_electors +=$et)


              @endforeach
              <tr>
                <td> </td>
                <td> SUM </td>
                <td>{{$net_male_electors}}</td>
                <td>{{$net_female_electors}}</td>
                <td>{{$net_other_electors}}</td>
                <td>{{$net_total_electors}}</td>
                <td>{{$net_male_voter}}</td>
                <td>{{$net_female_voter}}</td>
                <td>{{ $net_other_voter}}</td>
                <td>{{$net_total_voter}}</td>
              </tr>
              @endif
            </tbody>

          </table>
        </div>

      </div>
    </div>


  </div>
  </div>
  </section>
</main>
<!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">End of Poll Turnout Details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="election_form" method="POST" action="{{url('ropc/voting/end-of-poll-change') }}">
        {{ csrf_field() }}
        <div class="modal-body mt-0">
          <p class="mb-4 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp;
            <b>PC Name:</b> <span class="badge badge-info">{{$pc->PC_NAME}}</span>&nbsp;&nbsp;
            <b>AC Name:</b> <span class="badge badge-info" name="acname" id="acname"> </span>
          </p>


          <input type="hidden" name="id" id="id" value="" readonly="readonly">
          <input type="hidden" name="acno" id="acno" value="" readonly="readonly">
          <input type="hidden" name="pcno" id="pcno" value="" readonly="readonly">

          <hr class="row mb-4" />
          <div class="mb-3 form-row">
            <label for="PercenTage" class="mt-2 col-md-4"><b>Voters Male</b></label>
            <input type="text" name="voters_male" id="voters_male" class="PoLLInput form-control col-md-8" placeholder="Voters Male" value="" />
            <span id="errmsg" class="error text-right"></span>
            @if ($errors->has('voters_male'))
            <span style="color:red;">{{ $errors->first('voters_male') }}</span>
            @endif

          </div>
          <div class="mb-3 form-row">
            <label for="PercenTage" class="mt-2 col-md-4"><b>Voters Female</b></label>
            <input type="text" name="voters_female" id="voters_female" class="PoLLInput form-control col-md-8" placeholder="Voters Female" value="" />
            <span id="errmsg1" class="error text-right"></span>
            @if ($errors->has('voters_female'))
            <span style="color:red;">{{ $errors->first('voters_female') }}</span>
            @endif

          </div>
          <div class="mb-3 form-row">
            <label for="PercenTage" class="mt-2 col-md-4"><b>Voters Others</b></label>
            <input type="text" name="voters_others" id="voters_others" class="PoLLInput form-control col-md-8" placeholder="Voters Others" value="" />
            <span id="errmsg2" class="error text-right"></span>
            @if ($errors->has('voters_others'))
            <span style="color:red;">{{ $errors->first('voters_others') }}</span>
            @endif

          </div>
          <div class="mb-3 form-row">
            <label for="PercenTage" class="mt-2 col-md-4"><b>Voters Total</b></label>
            <input type="text" name="voters_total" id="voters_total" class="PoLLInput form-control col-md-8" placeholder="Voters Total" value="" />
            <span id="errmsg3" class="error text-right"></span>
            @if ($errors->has('voters_total'))
            <span style="color:red;">{{ $errors->first('voters_total') }}</span>
            @endif

          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saverec">Save changes</button>
        </div>
      </form>


    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->

@endsection
@section('script')

<script type="text/javascript">
  $(document).ready(function() {
    //called when key is pressed in textbox

    $("#election_form").submit(function() {

      if ($("#voters_male").val() <= 0) {
        $("#errmsg").text("");
        $("#errmsg").text("Please enter voters male");
        $("#voters_male").focus();
        return false;
      }
      if ($("#voters_female").val() <= 0) {
        $("#errmsg").text("");
        $("#errmsg1").text("Please enter voters female");
        $("#voters_female").focus();
        return false;
      }
      if (parseFloat($("#voters_others").val()) < 0 || $("#voters_others").val().trim() == '') {
        $("#errmsg").text("");
        $("#errmsg2").text("Please enter voters others");
        $("#voters_others").focus();
        return false;
      }
      if (parseFloat($("#voters_total").val()) <= 0) {
        $("#errmsg").text("");
        $("#errmsg3").text("Please enter voters total");
        $("#voters_total").focus();
        return false;
      }



      var total_add = parseFloat($("#voters_male").val()) + parseFloat($("#voters_female").val()) + parseFloat($("#voters_others").val());
      var total = parseFloat($("#voters_total").val());

      if (total_add != total) {
        $("#errmsg").text("");
        $("#errmsg3").text("Total value is not equal to male+female+other");
        $("#voters_total").focus();
        return false;
      }



    });
  });

  $(document).on("click", ".getdata", function() {
    id = $(this).attr('data-id');
    var acno = $(this).attr('data-acno');
    var acname = $(this).attr('data-acname');
    var pcno = $(this).attr('data-pcno');
    var data_male = $(this).attr('data-male');
    var data_female = $(this).attr('data-female');
    var data_others = $(this).attr('data-others');
    var data_total = $(this).attr('data-total');

    $("#id").val(id);
    $("#acno").val(acno);
    $("#acname").text(acname);
    $("#pcno").val(pcno);
    $("#voters_male").val(data_male);
    $("#voters_female").val(data_female);
    $("#voters_others").val(data_others);
    $("#voters_total").val(data_total);

  });
</script>
@endsection