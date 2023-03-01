@extends('admin.layouts.pc.expenditure-theme')
@section('title', 'Candidate Nomintion Details')
@section('bradcome', 'Candidate List')
@section('description', '')
@section('content') 
@php
  $st_code=!empty($st_code) ? $st_code : '0';
  $cons_no=!empty($cons_no) ? $cons_no : '0';
  $st=getstatebystatecode($st_code);
 
  $distname=getdistrictbydistrictno($st_code,$user_data->dist_no);
  $pcdetails=getpcbypcno($st_code, $cons_no); 
  $pcName=!empty($pcdetails->PC_NAME) ? $pcdetails->PC_NAME : 'ALL';
  $stateName=!empty($st->ST_NAME) ? $st->ST_NAME : 'ALL';
// echo $st_code.'cons_no=>'.$cons_no;
@endphp 
<main role="main" class="inner cover mb-1">     
    <section class="breadcrumb-section">
        <div class="container-fluid">
            <div class=" row">
                <div class="col-md-5 mt-2 mb-2"><h5 class="mr-auto">Contested Candidate List: {{$count}}</h5></div> 
                <div class="col-md-7 mt-2 mb-2 text-right"><p class="mb-0 text-right">
                        <b>State Name:</b> 
                        <span class="badge badge-info">{{$stateName}}</span> &nbsp;&nbsp; 
                        <b></b><span class="badge badge-info"></span>&nbsp;&nbsp; 
                        <b>PC:</b> <span class="badge badge-info">{{ $pcName }}</span>
                        <a href="{{url('/pcceo/expallcandidatePDF')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">PDF Download</a> &nbsp;&nbsp;
                        <a href="{{url('/pcceo/expallcandidateEXL')}}/{{base64_encode($cons_no)}}" class="btn btn-info" role="button">Export Excel</a> &nbsp;&nbsp;
                        <b></b><a href="{{url('/pcceo/mis-officer')}}"> <button type="button" id="Back" class="btn btn-primary">Back</button></a>

                    </p></div>
            </div> <!-- end row -->
        </div>

    </section>
    <section class="mt-5">
        <div class="container-fluid">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card text-left" style="width:100%;">
            <!--SELECT CANDIDATE-->
            <div class="card-body" id="demo" class="collapse show">  
            <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <tr>
          <th>PC No & Name</th>
          <th>Candidate Name</th>
          <th>Party Name</th>
          <!--<th>Date Of Lodging</th>-->
         
        </tr>
        </thead>
<?php $j=0;  ?>
    @if(!empty($totalContestedCandidatedata))
    @foreach($totalContestedCandidatedata as $candDetails)  
      <?php
      //dd($candDetails);
       $pcDetails=getpcbypcno($candDetails->st_code,$candDetails->pc_no);
       $date = new DateTime($candDetails->created_at);
       //echo $date->format('d.m.Y'); // 31.07.2012
       $lodgingDate=$date->format('d-m-Y'); // 31-07-2012
      // dd($candDetails);
        $j++; 
       $stDetails=getstatebystatecode($candDetails->st_code);
      // dd($candDetails);
        $j++; 
        ?>
<tr>
<td>@if(!empty($candDetails->pc_no)) {{ $candDetails->pc_no}} - {{ $pcDetails->PC_NAME}} @endif</td>
<td>@if(!empty($candDetails->cand_name)) {{$candDetails->cand_name}} @endif</td>
<td>@if(!empty($candDetails->PARTYNAME)) {{$candDetails->PARTYNAME}} @endif</td>
<!--<td>@if(!empty($lodgingDate)) {{$lodgingDate}} @endif</td>-->
</tr>
@endforeach 
@endif 
<tbody>
             </tbody>
            </table>
                        </div>
                    </div>
                    <!--END OF SELECT CANDIDATE-->
                </div>
                <!-- <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="card text-left" style="width:100%;">

                        <div class="card-body"  class="collapse show">
                            @if($count>0)
                            <div id="barchart"></div>
                            @else
                            No data for graph. 
                            @endif
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </section>
</main>

@endsection