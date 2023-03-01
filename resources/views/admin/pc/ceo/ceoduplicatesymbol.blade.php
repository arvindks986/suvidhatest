@extends('admin.layouts.pc.report-theme')
@section('title', 'Candidate and Counting Section')
@section('bradcome', 'Duplicate Symbol Candidate Reports')
@section('content') 
  <?php  $st=getstatebystatecode($st_code);  
  date_default_timezone_set('Asia/Kolkata');
  ?> 
<style type="text/css">
      th, td { white-space: normal;}
        <!-- .dataTables_wrapper .row:nth-child(2) .col-sm-12 { overflow: scroll;} -->
        
        html {
              overflow: scroll;
              overflow-x: hidden;
             }
              ::-webkit-scrollbar {    width: 0px; 
              background: transparent;  /* optional: just make scrollbar invisible */
              }

              ::-webkit-scrollbar-thumb {
                background: #ff9800;
                }
              div.dataTables_wrapper {margin:0 auto;} 
  </style>
 <main role="main" class="inner cover mb-3">
<section>
  <div class="container-fluid">
  <div class="row">
  <div class="card text-left" style="width:100%; margin:0 auto;">
        <div class=" card-header">
        <div class=" row">
          <div class="col"><h4>Duplicate Symbol Candidate Reports</h4></div> 
          <div class="col"><p class="mb-0 text-right"><b>State Name:</b> <span class="badge badge-info">{{$st->ST_NAME}}</span> &nbsp;&nbsp;  
           <a href="{{url('pcceo/ceo-duplicatesymol-pdf')}}" class="btn btn-info" role="button">Export PDF</a> &nbsp;&nbsp;
           <a href="{{url('pcceo/ceo-duplicatesymol-excel')}}" class="btn btn-info" role="button">Export Excel</a>
          </p>
        </div>
        </div>
        </div>
   
   
       
    <div class="card-body">  
<div class="table-responsive">
  <table id="list-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
              <th>S.No.</th>
              <th>PC No.& Name</th>
              <th>Symbol Name</th>
              <th>Candidate Name</th>
              <th>Party</th>
              <!--<th>Total Count</th>-->
            </tr>
        </thead>
        <tbody> 
      <?php $count = 1; 
     if(!empty($lists)){ ?>
      @foreach($lists as $list) 
    <?php
    //dd($list);
    $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
    $pclist=getpcbypcno($list->st_code,$list->pc_no);
    $symbol_data=getsymbolbyid($list->symbol_id);
    $partyDetails=getpartybyid($list->party_id);
    ?>
        <tr>
        <td>{{ $count++}}</td>
        <td>@if(isset($pclist->PC_NAME)) {{$list->pc_no.'-'.$pclist->PC_NAME}}@endif</td>
        <td>@if(isset($symbol_data)) {{$symbol_data->SYMBOL_DES}} @endif</td>
        <td>@if(isset($candidatedetails->cand_name)) {{$candidatedetails->cand_name}}@endif</td>
        <td>@if(isset($partyDetails->PARTYNAME)) {{$partyDetails->PARTYNAME}} @endif</td> 
        <!--<td>@if(isset($list->cnt)) {{$list->cnt}} @endif</td> -->  
        </tr>
            @endforeach
          <?php } else { ?>
          <tr>
            <td class="col-md-6" colspan='6'> <p>No Records  Founds </p></td>
          </tr>   
          <?php }  ?>
        </tbody>
    </table>
    </div>
    </div>
  </div>
  </div>
  </section>
  </main>
@endsection
