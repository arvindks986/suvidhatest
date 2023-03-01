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
                      <p><strong>Date of Submission of Scrutiny Report to DEO : </strong> 23-07-2019</p>
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

          <div class="card-body mb-4">
           <div class="col-lg-4 col-md-4 col-sm-12 boxNew">
            <ul>
             <h6>Notice Issued by ECI<br>Action Type : Complete</h6>

              <li><span></span>
                <div>
                <div class="title">SR Submission Date to CEO : 20-06-2020</div>
                  <!--<a href="" data-toggle="tooltip" data-placement="top" title="SR Submission Date to CEO : 20-06-2020">Info</a>-->
                </div>
                <span class="number"><span>DEO</span>
                <a href="">SR sending to CEO</a></span>
              </li>
              <li>
                <div><span></span>
                  <div class="title">SR Received Date : 20-06-2020</div>
                  <div class="title">SR Sending Date to CEO : 20-06-2020</div>
                </div>
                <span class="number"><span>CEO</span><a href="">SR sending to ECI by CEO</a></span>
              </li>
              <li>
                <div><span></span>
                  <div class="title">SR Received Date : 20-07-2020</div>
                </div>
                <span class="number"><span>ECI</span>
                <a href="">Notice issued to CEO</a>
                </span>
              </li>
              <li>
                <div><span></span>
                  <div class="title">SR Received Date : 20-07-2020</div>
                </div>
                <span class="number"><span>CEO</span>
                <a href="">Notice issued to DEO</a>
                </span>
              </li>
              <li>
                <div><span></span>
                  <div class="title">SR Received Date : 20-07-2020</div>
                </div>
                <span class="number"><span>DEO</span>
                <a href="">Replied notice to ECI</a>
                </span>
              </li>
              <li>
                <div><span></span>
                  <div class="title">SR Received Date : 20-07-2020</div>
                </div>
                <span class="number"><span>ECI</span>
                <a href="">&nbsp;</a>
                </span>
              </li>
            </ul>
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
