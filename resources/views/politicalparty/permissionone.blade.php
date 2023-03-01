@extends('layouts.theme')
@section('title', 'Permission')
@section('content')

<main role="main" class="inner cover mb-3 mb-auto">

    <section>
        @if(session::has('msg'))
                            <div class="alert alert-success">
                                {{session()->get('msg')}}
                            </div>
                        @endif
        @if($total>0)
        
        <div class="tabs-inner ">
            <div class="row d-flex align-items-md-stretch">
                <div class="col">
                    <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Total Applied Permission ({{$total[0]->total}})</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Accepted Permission ({{$total[0]->Accepted}})</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Rejected Permission ({{$total[0]->Rejected}})</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-pending-tab" data-toggle="pill" href="#pills-pending" role="tab" aria-controls="pills-pending" aria-selected="false">Pending Permission ({{$total[0]->Pending}})</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" id="pills-cancle-tab" data-toggle="pill" href="#pills-cancle" role="tab" aria-controls="pills-cancel" aria-selected="false">Cancel Permission ({{$total[0]->cancle}})</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @else
            <div class="tabs-inner mt-5">
                <div class="row d-flex align-items-md-stretch">
                    <div class="col">
                        <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Total Applied Permission (0)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Accepted Permission (0) </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Rejected Permission (0) </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-pending-tab" data-toggle="pill" href="#pills-pending" role="tab" aria-controls="pills-pending" aria-selected="false">Pending Permission (0)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-cancle-tab" data-toggle="pill" href="#pills-cancle" role="tab" aria-controls="pills-cancel" aria-selected="false">Cancel Permission (0)</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
         @endif
        @if (Session::has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @endif
        @if(count($errors))
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <br/>
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </section>
    <section class="dashboard-header section-padding">
        <div class="container-fluid">
            <!-- <div class="line"></div> -->
            <div class="row">
                <div class="col">
                    <div class="tab-content tabular-pane" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $data)
                                    <tr>
                                        @if($data->approved_status == 0)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/getpermissiondetails')}}/{{$data->permission_id}}/{{$data->approved_status}}/{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @elseif($data->approved_status == 1)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/getpermissiondetails')}}/{{$data->permission_id}}/{{$data->approved_status}}/{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @elseif($data->approved_status == 2)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/getpermissiondetails')}}/{{$data->permission_id}}/{{$data->approved_status}}/{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @elseif($data->approved_status == 3)
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('/getpermissiondetails')}}/{{$data->permission_id}}/{{$data->approved_status}}/{{$data->location_id}}">{{$data->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        @endif

                                        <td>{{$data->permission_name}}</td>
                                        @if(($data->permission_mode)==1) 
                                        <td><b>Online</b></td>
                                        @else
                                        <td><b>Offline</b></td>
                                        @endif
                                        <td>{{GetReadableDateForm($data->created_at)}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($data->approved_status == 0 && $data->cancel_status != 1) {{'Pending'}}
                                                @elseif($data->approved_status == 1 && $data->cancel_status != 1) {{'In progress'}}
                                                @elseif($data->approved_status == 2 && $data->cancel_status != 1){{'Accept'}}
                                                @elseif($data->approved_status == 3 && $data->cancel_status != 1){{'Reject'}}
                                                @elseif($data->cancel_status == 1){{'Cancelled'}} 
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif 
                                </tbody>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $rdata)
                                    @if($rdata->approved_status == 2 && $rdata->cancel_status != 1)
                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('getpermissiondetails')}}/{{$rdata->permission_id}}/{{$rdata->approved_status}}/{{$rdata->location_id}}">{{$rdata->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        
                                        
                                        <td>{{$rdata->permission_name}}</td>
                                        @if(($rdata->permission_mode)==1) 
                                        <td><b>Online</b></td>
                                        @else
                                        <td><b>Offline</b></td>
                                        @endif
                                        <td>{{GetReadableDateForm($rdata->created_at)}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($rdata->approved_status == 2 && $rdata->cancel_status != 1){{'Accept'}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif 
                                </tbody>

                            </table>
                        </div>


                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $adata)
                                    @if($adata->approved_status == 3 && $adata->cancel_status != 1)
                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('getpermissiondetails')}}/{{$adata->permission_id}}/{{$adata->approved_status}}/{{$adata->location_id}}">{{$adata->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                        
                                        
                                        <td>{{$adata->permission_name}}</td>
                                        @if(($adata->permission_mode)==1) 
                                        <td><b>Online</b></td>
                                        @else
                                        <td><b>Offline</b></td>
                                        @endif
                                        <td>{{GetReadableDateForm($adata->created_at)}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                                @if($adata->approved_status == 3 && $adata->cancel_status != 1){{'Reject'}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif 
                                </tbody>

                            </table>
                        </div>
                        
                        <div class="tab-pane fade" id="pills-pending" role="tabpanel" aria-labelledby="pills-pending-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $pendingdata)
                                    @if($pendingdata->approved_status == 0 && $pendingdata->cancel_status != 1)

                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('getpermissiondetails')}}/{{$pendingdata->permission_id}}/{{$pendingdata->approved_status}}/{{$pendingdata->location_id}}">{{$pendingdata->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                       
                                        
                                        <td>{{$pendingdata->permission_name}}</td>
                                        @if(($pendingdata->permission_mode)==1) 
                                        <td><b>Online</b></td>
                                        @else
                                        <td><b>Offline</b></td>
                                        @endif
                                        <td>{{GetReadableDateForm($pendingdata->created_at)}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                               @if($pendingdata->approved_status == 0 && $pendingdata->cancel_status != 1){{'Pending'}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif 
                                </tbody>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="pills-cancle" role="tabpanel" aria-labelledby="pills-cancle-tab">
                            <table id="list-table" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Permission Type</th>
                                        <th>Permission Applied Mode</th>
                                        <th>Date of Submission</th>
                                        <th>Status</th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($permissionDetails))
                                    @foreach($permissionDetails as $cdata)
                                    @if($cdata->cancel_status == 1)

                                    <tr>
                                        <td><a class="btn btn-outline-danger btn-block" style=" text-align: left;" href="{{url('getpermissiondetails')}}/{{$cdata->permission_id}}/{{$cdata->approved_status}}/{{$cdata->location_id}}">{{$cdata->permission_id}}<i class="fa fa-edit float-right font-size01"></i></a></td>
                                       
                                        
                                        <td>{{$cdata->permission_name}}</td>
                                        @if(($cdata->permission_mode)==1) 
                                        <td><b>Online</b></td>
                                        @else
                                        <td><b>Offline</b></td>
                                        @endif
                                        <td>{{GetReadableDateForm($cdata->created_at)}}</td>
                                        <td>
                                            <div class="text-warning text-center">
                                               @if($cdata->cancel_status == 1){{'Cancelled'}}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
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
@endsection