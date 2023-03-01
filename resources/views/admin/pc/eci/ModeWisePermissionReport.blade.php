@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content')
<?php
//echo "<pre>";
//print_r($report);
//exit;
?>
<main role="main" class="inner cover mb-3 mb-auto">
    <br/>
    <section id="details">
         <div class="row">
                <div class="col-sm-12 text-center mb-3">
                    <h5 style="text-decoration: underline">Permission Report</h5>
                </div>
            </div>
        <div class="container-fluid">
            <form name = "report" method="post"  action="{{url('/eci/modewisepermissionreport')}}"> 
                {{csrf_field()}}
                <div class="row">
                    
                    <div class="col-sm-3 row">
                        <label for="state" class="col-sm-4 col-form-label">Election Type</label>
                        <div class="col-sm-8 distt">
                            <select name="elect" id="state" class="form-control">
                                <option value="0">-- All Election --</option>                             
                             
                                <option value="2">BYE Election</option>
                            </select>
                        </div>
                    </div>
                    

                    <div class="col-sm-3  row">
                        <label for="state" class="col-sm-4 col-form-label">State</label>
                        <div class="col-sm-8 distt">
                            <select name="state" id="state" class="form-control">
                                <option value="0">-- All State --</option>
                                @foreach($statevalue as $State)
                                <option value="{{$State->ST_CODE }}"> 
                                    {{$State->ST_NAME }}
                                </option>
                                @endforeach 
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3 row">
                        <div class="col-sm-12 distt">
                            <select name="pmode" class="form-control" id="selectprmsn">
                                    <option value="0">Select Permission Mode</option>
                                    <option value="1">Offline</option>
                                    <option value="2">Online</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3 row">
                        <div class="col-sm-12 distt">
                            <select name="pname" class="form-control" id="selectprmsn">
                                    <option value="0">Select Permission Type</option>
                                    <option value="all">Select All</option>
                                   @if(!empty($getAllPermissiontype))
                                    @foreach($getAllPermissiontype as $pdata)
                                    <option value="{{$pdata->id}}" {{ (collect(old('pname'))->contains($pdata->id)) ? 'selected':'' }}>{{$pdata->permission_name}}</option>
                                    @endforeach
                                    @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1  row">
                        <input type="submit"  value="Submit" name="submit" class="btn btn-primary getdata">
                    </div>
                </div>
            </form>
            <div class="row">
                 <div class="col-sm-12 text-right">
                <form name ="report" method="post"  action="{{url('/eci/modewisepermissionreport')}}">
                {{csrf_field()}}
                <input type="hidden" name="pname" value="{{$pname}}" class="form-control" >
                <input type="hidden" name="state" class="form-control" value="{{$state}}">
                <input type="hidden" name="elect" class="form-control" value="{{$elect}}">
                <input type="hidden" name="pmode" class="form-control" value="{{$pmode}}">
                    <div class="float-right mt-5">
                        <input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
                        <input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
                    </div>
                </form>
                </div>
            </div>
             <div class="row">
                <div class="col-sm-12 mt-2">
                    <table id="list-table" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>State Name</th>
                                @if($pname != '0')
                                <th>Permission Name</th>
                                @endif
                                <th>Total request</th>
                                <th>Approved</th>
                                <th>Rejected</th>
                                <th>Inprogress</th>
                                <th>Pending</th>
                                <th>Cancel</th>
                                <th>Permission Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                           @php $counttotal = 0;$countaccept = 0;$countreject = 0;$countinprogress = 0;$countpending = 0;$countcancel = 0; @endphp
                                @if(!empty($report))
                                @foreach($report as $key => $data)
                                @php 
                                $counttotal = $counttotal + $data->Total;
                                $countaccept = $countaccept + $data->Accepted;
                                $countreject = $countreject + $data->Rejected;
                                $countinprogress = $countinprogress + $data->Inprogress;
                                $countpending = $countpending + $data->Pending;
                                $countcancel = $countcancel + $data->Cancel;
                                @endphp
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->ST_NAME}}</td>
                                    @if($pname != '0')
                                    <td>{{$data->permission_name}}</td>
                                    @endif
                                    <td>{{$data->Total}}</td>
                                    <td>{{$data->Accepted}}</td>
                                    <td>{{$data->Rejected}}</td>
                                    <td>{{$data->Inprogress}}</td>
                                    <td>{{$data->Pending}}</td>
                                    <td>{{$data->Cancel}}</td>
                                    
                                    <td>
                                        @if($data->permission_mode == '0')
                                         {{'Offline'}}
                                        @else
                                        {{'Online'}}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                                <tr>
                                    @if($pname != '0')
                                    <td colspan="3"><a href="javascript::void(0)"><span>Grand Total</span></a></td>
                                    @else
                                    <td colspan="2"><a href="javascript::void(0)"><span>Grand Total</span></td>
                                    @endif
                                    <td><a href="javascript::void(0)">{{$counttotal}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countaccept}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countreject}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countinprogress}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countpending}}</a></td>
                                    <td><a href="javascript::void(0)">{{$countcancel}}</a></td><td></td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection

