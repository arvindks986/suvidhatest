@extends('admin.layouts.pc.theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'List All Accepted Candidates')
@section('content')
<?php $totrej = \app(App\adminmodel\CandidateNomination::class)->where(['st_code' => $ele_details->ST_CODE, 'pc_no' => $ele_details->CONST_NO, 'election_id' => $ele_details->ELECTION_ID])->where(['application_status' => '4'])->get()->count();
$totalwith = \app(App\adminmodel\CandidateNomination::class)->where(['st_code' => $ele_details->ST_CODE, 'pc_no' => $ele_details->CONST_NO, 'election_id' => $ele_details->ELECTION_ID])->where(['application_status' => '5'])->get()->count();

$totaccepted = \app(App\adminmodel\CandidateNomination::class)->where(['st_code' => $ele_details->ST_CODE, 'pc_no' => $ele_details->CONST_NO, 'election_id' => $ele_details->ELECTION_ID])->where(['application_status' => '6'])->where('party_id', '!=', '1180')->get()->count();
$total = \app(App\adminmodel\CandidateNomination::class)->where(['st_code' => $ele_details->ST_CODE, 'pc_no' => $ele_details->CONST_NO, 'election_id' => $ele_details->ELECTION_ID])->where('application_status', '!=', '11')->where('party_id', '!=', '1180')->get()->count();
?>

<style type="text/css">
  th,
  td {
    white-space: normal !important;
  }

  .table b {
    font-weight: 500;
  }

  .btn {
    font-size: 14px;
  }

  h5.border-bottom.m-0.p-2.mb-2 {
    padding-left: 0 !important;
  }

  .mg-width {
    width: 18%;
  }

  @media (max-width: 600px) {
    table.table td {
      display: block;
      font-size: 14px;
    }

  }
</style>
<section class="statistics color-grey pt-5 pb-5" style="border-bottom:1px solid #eee;">
  <div class="container-fluid">
    <div class="row d-flex">
      <div class="col-md-3">
        <!-- Income-->
        <div class="card income text-center">
          <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
          <div class="number yellow">{{$total}}</div>
          <p>Applications<strong class="text-primary">Applied</strong></p>

        </div>
      </div>
      <div class="col-md-3">
        <!-- Income-->
        <div class="card income text-center">
          <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
          <div class="number green">{{$totaccepted}}</div>
          <p>Applications<strong class="text-primary">Accepted</strong></p>

        </div>
      </div>
      <div class="col-md-3">
        <!-- Income-->
        <div class="card income text-center">
          <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
          <div class="number orange">{{$totrej}}</div>
          <p>Total Receipt<strong class="text-primary">Rejected</strong></p>

        </div>
      </div>
      <div class="col-md-3">
        <!-- Income-->
        <div class="card income text-center">
          <div class="icon"><img src="{{ asset('admintheme/img/icon/applied.png') }}" alt="" /></div>
          <div class="number red">{{$totalwith}}</div>
          <p>Applications<strong class="text-primary">Withdrawn</strong></p>

        </div>
      </div>


    </div>

  </div>
</section>
@if($checkval==0)
<p class="form-group alert alert-danger ">Candidate Nominations details has not been finalized</p>
@elseif($checkval==1)
<p class="form-group alert alert-success">Candidate Nominations details has been finalized</p>
@endif
<section class="data_table form">
  <div class="container">

    @if(!$lists->isEmpty())
    <?php $total = \app(App\adminmodel\CandidateNomination::class)->where(['st_code' => $ele_details->ST_CODE, 'pc_no' => $ele_details->CONST_NO])->where(['application_status' => '6'])->get()->count();
    ?>
    <div class="row">
      <div class="col">
        <form class="form-inline mb-5">

          <div class="form-group mr-8 ml-auto ">


            <!--<div class="custom-select mr-3">
          <a href="{{url('ropc/ballotpaperpdfview') }}">
            <button type="button" class="btn btn-danger btn-block" onclick="return confirm('Do you really want to download ballot paper?');">Download Ballot Paper</button>  </a> </div> 
        -->

            @if($checkval==0)
            <button type="button" class="btn btn-danger b-padding" data-toggle="modal" data-target="#finalise">Finalize PC</button>
            @elseif($checkval==1)
            <!--<h4>Your PC's are already Finalized.</h4> -->
            @endif
            @if (session('success_mes'))
            <div class="alert alert-success"> {{session('success_mes') }}</div>
            @endif
            @if (session('error_mes'))
            <div class="alert alert-danger"> {{session('error_mes') }}</div>
            @endif
            @if (session('error_mes1'))
            <div class="alert alert-danger"> {{session('error_mes1') }}</div>
            @endif
            @if(!empty($errors->first()))
            <div class="alert alert-danger"> <span>{{ $errors->first() }}</span> </div>
            @endif
          </div>


        </form>
      </div>
    </div>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col">
              <h3>List All Contesting Candidates</h3>
              <br />
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="input-group ">
                <input type="text" class="form-control input-lg" name="search" placeholder="Search By Candidate Name" id="myInput" />
                <span class="input-group-btn">
                  <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-search"></i></button>
                </span>
                &nbsp; <a class="float-right" href="{{ route('pdfview',['download'=>'adminpdf']) }}">
                  <button type="button" class="btn btn-danger" style="padding:12px;">Download & Verify List of Contesting Candidates </button></a>
              </div>


            </div>
          </div>

        </div>
      </div>
      <form class="form-horizontal" id="form7" method="POST" action="{{url('ropc/change-sequence') }}">
        {{ csrf_field() }}
        <div class="row" id="myTable">
          <div class="col">

            <ul id="sortable1" class="connectedSortable list-group">
              <?php $i = 1;
              $url = URL::to("/");
              $val = 0; ?>

              @foreach ($lists as $key=>$list)
              <?php
              $affidavit = getById('candidate_affidavit_detail', 'nom_id', $list->nom_id);
              $party = getpartybyid($list->party_id);
              $symb = getsymbolbyid($list->symbol_id);
              $s = getnameBystatusid($list->application_status);

              if ($list->cand_party_type == "N") $p = "National";
              if ($list->cand_party_type == "S") $p = "State";
              if ($list->cand_party_type == "U" || $list->cand_party_type == "0") $p = "Unrecognized";
              if ($list->cand_party_type == "Z") $p = "Independent";
              ?>
              <li class="ui-state-default ">
                <div class="card">

                  <table class="table">
                    <tr>
                      <td class="profileimg" style="width: 150px;">@if($list->cand_image!='')
                        <img src="{{$url.'/'.$list->cand_image}}" class="prfl-pic img-thumbnail" alt="">
                        @else
                        <img src="{{ asset('admintheme/img/male_avatar.png') }}" class="prfl-pic img-thumbnail" alt="">
                        @endif
                        <span class="btn btn-danger btn-number">{{$list->new_srno}}</span>
                      </td>
                      <td>
                        <table class="table" style="width:100%">
                          <tr>
                            <td colspan="4">
                              <h5 style="margin-top: 12px;">@if(isset($party)){{ucwords($party->PARTYNAME)}}@endif</h5>
                              <hr />
                            </td>
                          </tr>
                          <tr>
                            <td class="mg-width">Name in English </td>
                            <td><b>{{$list->cand_name}} </b> </td>
                            <td class="mg-width"> Name in Hindi </td>
                            <td><b>@if(!empty($list->cand_hname)) {{$list->cand_hname}} @endif </td>
                          </tr>
                          <tr>
                            <td class="mg-width">Name in Vernacular </td>
                            <td><b>@if(!empty($list->cand_vname)){{$list->cand_vname}} @endif </b></td>
                            <td class="mg-width">Party Type </td>
                            <td><b>@if(isset($p)){{ucwords($p)}} @endif</td>
                          </tr>
                          <tr>
                            <td class="mg-width">Gender </td>
                            <td><b>{{ucwords($list->cand_gender)}}</b></td>
                            <td class="mg-width">Symbol </td>
                            <td><b>@if(isset($symb)) {{$symb->SYMBOL_DES}}@endif</b></td>
                          </tr>
                          <tr>
                            <td>Current Status </td>
                            <td><b class="text-success">@if(isset($s)){{ucwords($s)}} @endif</b></td>
                            <td colspan="2">
                              <div class="form-inline float-right">
                                <label for=""> @if($checkval==0) Enter New Sr.No
                                  <input style="height:38px!important" type="text" placeholder="Enter Sr No." class="form-control input-small ml-2" min="0" max="30" value="{{old('newsrno'.$i)}}" name="newsrno{{$i}}" id="newsrno{{$i}}" />
                                  <input type="hidden" name="nom_id{{$i}}" value="{{$list->nom_id}}" /> @endif
                                </label>
                              </div>
                              <span id="errmsg{{$i}}" class="text-danger"></span>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>




                </div>
              </li>
              <?php $i++; ?>
              @endforeach
            </ul>
            @if($checkval==0)
            <div class="form-group row float-right">
              <div class="col">
                <button type="submit" id="candnomination" class="btn btn-primary">Update</button>
              </div>
            </div>
            @endif
            <input type="hidden" name="totalvalue" value="{{$total}}" />
            <input type="hidden" value="{{$i}}" name="noval" id="noval" />
          </div>
        </div>

      </form>
    </div>
    @else
    <div class="norecords"><i class="fa fa-ban"></i>
      <h4>No Records Found</h4>
    </div>
    @endif
  </div>
</section>
<!-- Modal Content Starts here -->
<!-- Modal -->
<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="changestatus" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header mb-3">
        <h4 class="modal-title" id="exampleModalLabel">Candidate Finalize your Constituency</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="election_form" method="POST" action="{{url('ropc/statusvalidation') }}">
          {{ csrf_field() }}

          <input type="hidden" name="pc_no" id="pc_no" value="" readonly="readonly">
          <input type="hidden" name="st_code" id="st_code" value="" readonly="readonly">

          <div class="mb-3">
            <p>Verify OTP Number :-<sup>*</sup></p>
            <label class="sr-only" for="validationTextarea">Verify OTP Number :-<sup>*</sup></label>
            <input type='text' name="verifyotp" id="verifyotp" class="nomination-field-2" value="{{old('verifyotp') }}" />
            <div class="invalid-feedback">
              Please enter a message in the textarea.
            </div>



          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->

<div class="modal" id="finalise">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header modal-custom-header">
          <h4 class="modal-title">Finalise Contesting Candidate</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          Kindly ensure that Form 7A is being finalized in accordance with Rule 10 of Conduct of Election Rules, 1961 and Commission's instructions thereupon. <br>  Also ensure that you have verified complete details. <br>  Once finalized, all details will become non-editable. <br> <br>
          Do you want to finalise ?
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <a href="{{url('ropc/finalize-ac') }}"><button type="button" class="btn btn-primary">Ok</button></a>
          <button type="button" class="btn btn-secodary" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
  </div>
<!-- Modal Content Ends Here -->
@endsection
@section('script')
<script type="text/javascript">
  jQuery(document).ready(function() {
    var v = $("#noval").val();
    //By Searh Text
    jQuery("#myInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      jQuery("#myTable div").filter(function() {
        jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    for (i = 1; i <= v; i++) {
      jQuery("#newsrno" + i).keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
          //display error message
          jQuery("#errmsg" + i).html("Digits Only").show().fadeOut("slow");
          return false;
        }
      });
    } // end for
  });

  /* $( function() {
         $( "#sortable1, #sortable2" ).sortable({
           connectWith: ".connectedSortable"
         }).disableSelection();
     }); */
</script>
@endsection