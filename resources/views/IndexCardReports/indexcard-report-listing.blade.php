@extends('IndexCardReports.layouts.theme')
@section('title', 'Index Card Report')
@section('bradcome', 'PC Wise Indexcard Report')
@section('content')

<style> 

img#theImg {
    display: none;
}
</style>

<?php //$st=getstatebystatecode($user_data->st_code);   ?>
<section class="dev">
    <div class="container">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, 2019<br>(PC Wise Indexcard Reports)</h4></div> 

                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <!-- Content goes Here -->
                        <div class="col-sm-12 text-center">
                            <h5>PC Wise Indexcard Reports</h5>
                        </div>
                        <table class="table table-bordered tablecenterreport" style="table-layout: fixed;">
                            <thead>
                            <th width="5%">SL. No.</th>
                            <th>Report Name</th>
                            <th>view</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td><a href="{{'IndexCardDataReport'}}">Index Card Data Report</a></td>
                                    <td> <a style="margin-left: 35%;" href="{{'IndexCardDataReport'}}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td><a href="{{'indexCardBriefReport'}}">Index Card Brief Report</a></td>
                                    <td><a style="margin-left: 35%;" href="{{'indexCardBriefReport'}}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Content ends Here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection