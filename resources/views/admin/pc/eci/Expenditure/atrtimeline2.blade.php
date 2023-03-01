@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Breach Details Report')
@section('description', '')
@section('content') 

@php 

$st_code=!empty($st_code) ? $st_code : '0';
$cons_no=!empty($cons_no) ? $cons_no : '0';
$party = !empty($_GET['party'])?$_GET['party']:"";
$pc = !empty($_GET['pc']) ? $_GET['pc'] : $cons_no; 

$st=getstatebystatecode($st_code);
$pcdetails=getpcbypcno($st_code,$cons_no); 
$stateName=!empty($st) ? $st->ST_NAME : 'ALL';
$pcName=!empty($pcdetails) ? $pcdetails->PC_NAME : 'ALL';
$all_pc=getpcbystate($st_code);
 
$graphText='';
if(!empty($st->ST_NAME)){
$graphText.=$st->ST_NAME;
}
if(!empty($pcdetails->PC_NAME)){
$graphText.=' '.$pcdetails->PC_NAME.'(PC)';
}
 if(empty($graphText)){
  $graphText='All States';
}
 $noData='';

@endphp


<style type="text/css">
</style>

<main role="main" class="inner cover mb-3">
    <section class="statistics mt-2" style="border-bottom:1px solid #eee;">
        <div class="container-fluid">
          <div class="row">
              <div class="card">
                <div class="card-body">
                <p class="text-center"><strong class="h6 text-primary"><u>Candidate Details</u></strong></p>
                <div class="row text-center">
                  <div class="col-lg-4 col-md-4 col-sm-12">
                  <ul class="check-lists list-unstyled mt-2">
                    <li class="d-flex align-items-center"> 
                      <p><strong>Candidate Name : </strong> Rakesh Kumar</p>
                    </li>
                    <li class="d-flex align-items-center"> 
                      <p><strong>AC / PC Name : </strong> Laxmi Nagar</p>
                    </li>
                     <li class="d-flex align-items-center"> 
                      <p><strong>Party Name : </strong> Congress</p>
                    </li>
                  </ul>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-12">
                  <ul class="check-lists list-unstyled mt-2">
                    <li class="d-flex align-items-center"> 
                      <p><strong>Election Type : </strong> General Election</p>
                    </li>
                    <li class="d-flex align-items-center"> 
                      <p><strong>Candidate Type : </strong> Returned</p>
                    </li>
                    <li class="d-flex align-items-center"> 
                      <p><strong>Result Declaration Date : </strong> 23-05-2019</p>
                    </li>
                     
                  </ul>
                </div>
                  <div class="col-lg-4 col-md-4 col-sm-12">
                  <ul class="check-lists list-unstyled mt-2">
                    <li class="d-flex align-items-center"> 
                      <p><strong>Account Lodged : </strong> Yes</p>
                    </li>
                    <li class="d-flex align-items-center"> 
                      <p><strong>Account Lodged Date : </strong> 10-06-2019</p>
                    </li>
                    <li class="d-flex align-items-center"> 
                      <p><strong>Date of Submission of Scrutiny Report to DEO : </strong> 30-06-2019</p>
                    </li>
                  </ul>
                  </div>

                </div>
              </div>

              </div>
            </div>

          </div>
        </div>
      </section>

    <section class="">
      <div class="container-fluid">
        <div class="row">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h6 class="mr-auto"><strong>Tracking Status : Mohendra Chandra Das</strong></h6>          
<!--<small class="text-data text-success"><i class="fa fa-check"></i>Active</small>-->
              <button class="btn-animated success">Current Status : Pending At ECI</button>
            </div>
          <div class="card-body bg-purple pb-4">
          <section id="cd-timeline" class="cd-container">

          <div class="cd-timeline-block">
          <div class="cd-timeline-img cd-picture">
            <!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/148866/cd-icon-picture.svg" alt="Picture">  -->
            <h5>DEO</h5>
          </div> <!-- cd-timeline-img -->

          <div class="cd-timeline-content">
            <h4>SR Submission Date to CEO : 10-06-2019</h4>
            <h4>SR Submission Date to CEO : 10-06-2019</h4>
            <!--<a href="#0" class="cd-read-more">View More...</a> -->
            <span class="cd-date">SR sending to CEO</span>
          </div> <!-- cd-timeline-content -->
        </div> <!-- cd-timeline-block -->

        <div class="cd-timeline-block">
          <div class="cd-timeline-img cd-movie">
            <h5>CEO</h5>
          </div> <!-- cd-timeline-img -->

          <div class="cd-timeline-content">
            <h4>SR Received Date : 10-06-2019</h4>
            <h4>SR Sending Date to CEO : 10-06-2019</h4>
            <span class="cd-date">SR sending to ECI by CEO</span>
          </div> <!-- cd-timeline-content -->
        </div> <!-- cd-timeline-block -->

        <div class="cd-timeline-block">
          <div class="cd-timeline-img cd-location">
            <h5>&nbsp;ECI</h5>
          </div> <!-- cd-timeline-img -->

          <div class="cd-timeline-content">
            <h4>SR Received Date : 20-07-2020</h4>
            <h4>Notice Sending Date to CEO : 20-06-2020</h4>
            <span class="cd-date">Notice Issued by ECI <br />
            Action Type : Complete</span>
          </div> <!-- cd-timeline-content -->
        </div> <!-- cd-timeline-block -->

        <div class="cd-timeline-block">
          <div class="cd-timeline-img cd-movie">
            <h5>CEO</h5>
          </div> <!-- cd-timeline-img -->

          <div class="cd-timeline-content">
            <h4>Notice Received Date to CEO : 20-06-2020</h4>
            <h4>Notice Sending Date to DEO : 20-06-2020</h4>
            <span class="cd-date">Notice Send to DEO by CEO</span>
          </div> <!-- cd-timeline-content -->
        </div> <!-- cd-timeline-block -->

        <div class="cd-timeline-block">
          <div class="cd-timeline-img cd-picture">
            <!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/148866/cd-icon-picture.svg" alt="Picture">  -->
            <h5>DEO</h5>
          </div> <!-- cd-timeline-img -->

          <div class="cd-timeline-content">
            <h4>Notice Received Date : 20-07-2020</h4>
            <h4>Notice Replied Date to CEO : 05-08-2020</h4>
            <span class="cd-date">Status to CEO</span>
          </div> <!-- cd-timeline-content -->
        </div> <!-- cd-timeline-block -->

          <div class="cd-timeline-block">
          <div class="cd-timeline-img cd-movie">
            <h5>CEO</h5>
          </div> <!-- cd-timeline-img -->

          <div class="cd-timeline-content">
            <h4>Notice Replied Received Date DEO : 20-06-2020</h4>
            <h4>Notice Replied Sending Date to ECI : 20-06-2020</h4>
            <span class="cd-date">Notice Send to ECI by CEO</span>
          </div> <!-- cd-timeline-content -->
        </div> <!-- cd-timeline-block -->

        <div class="cd-timeline-block">
          <div class="cd-timeline-img cd-location">
            <h5>&nbsp;ECI</h5>
          </div> <!-- cd-timeline-img -->

          <div class="cd-timeline-content">
            <h4>Replied Notice Received Date : 20-07-2020</h4>
            <span class="cd-date">Final Action Taken by ECI<br />
            Action Type : Complete</span>
          </div> <!-- cd-timeline-content -->
        </div> <!-- cd-timeline-block -->

      </section> <!-- cd-timeline -->
        <div class="notice-div">
          <h6>Notice Issued by ECI Action Type : Complete</h6>
        </div>

    </div>

    <div class="card-footer">
    <!--<a tabindex="0" class="btn btn-lg btn-danger" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a>
    <span class="d-inline-block" data-toggle="popover" data-content="Disabled popover">
        <button class="btn btn-primary" style="pointer-events: none;" type="button" disabled>Disabled button</button>
    </span> -->
      <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#exampleModalCenter"> Change Status</button>
    </div>
    </div> 

            </div>
        </div>
        
    </section>

<!-- Modal Content Starts Here -->

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <form>
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Recipient:</label>
            <input type="text" class="form-control" id="recipient-name">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Content Ends Here -->

</main>
<script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>

<script>
$(document).ready(function(){
  $('[data-toggle="popover"]').popover();
});
</script>

@endsection
