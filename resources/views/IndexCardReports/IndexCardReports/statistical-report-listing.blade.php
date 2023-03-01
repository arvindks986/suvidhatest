@extends('IndexCardReports.layouts.IndexReportTheme')
@section('title', 'AC Wise Index Card Report')

@section('bradcome', 'Index Card Ac Wise')

@section('content')


<style> 

img#theImg{
    display: none;
}
.cent{
    text-align: center;
}
.fa-eye:before {
    content: "\f06e";
    color: #f15d86;
    font-size: 20px;
    margin: auto;
}


</style>
<?php if(Auth::user()->designation == 'ROAC'){
			$prefix 	= 'roac';
		}else if(Auth::user()->designation == 'CEO'){	
			$prefix 	= 'acceo';
		}else if(Auth::user()->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if(Auth::user()->role_id == '7'){
			$prefix 	= 'eci';
		}   ?>


<section class="">
    <div class="container-fluid">
        <div class="row">
            <div class="card text-left mt-5" style="width:100%; margin:0 auto;">
                <div class=" card-header">
                    <div class=" row">
                        <div class="col"><h4> Election Commission Of India, General Elections, {{getElectionYear()}}</h4></div> 

                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive" style="width: 100%;">
                        <!-- Content goes Here -->

		<div class="col-sm-10" style="margin:auto;">				
		<form class="form-inline" style="text-align:center;" method = "post" action="{!! url(''.$prefix.'/statistical-report-listing') !!}">
			@csrf 
            <div class="col-sm-8 form-group">
                <label class="col-sm-4 control-label"><b style="width: 100%;text-align: right;">Select State : &nbsp;</b></label>
		<select class="col-sm-8 form-control" name="st_code"required>
			<option value="" class="form-control">Select State</option>
			@foreach($stateList as $state)
			<option value="{{$state->ST_CODE}}" @if(isset($_POST['st_code']) && ($_POST['st_code']== $state->ST_CODE))    selected @endif>{{$state->ST_NAME}}</option>
			@endforeach
		</select>
        </div>


<div class="col-sm-4 text-left">
		<button class="btn btn-success" style="margin:0px 5px;background-color:#dc3545;color:#fff;border:none;" type="submit">Submit</button>
        </div>
		</form>
		</div>


					@if(isset($_POST['st_code']))
                        <div class="col-sm-12 text-center">
                            <h5 class="p-3">Statistical Reports</h5>
                        </div>
                        <table class="table table-bordered" style="width: 100%;overflow: hidden;">
                            <thead>
                            <th>SL. No.</th>
                            <th>Report Name</th>
                            <th style="overflow: hidden;"><p style="text-align: center;">View Report</p></th>
                            </thead>
                            <tbody>
                                  
                                <tr><td>1.</td>
                                    <td><a href="{!! url('/'.$prefix.'/other-abbreviations-and-description/'.$_POST['st_code']) !!}" target="_blank">Other Abbreviations and Description 
                                        </a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/other-abbreviations-and-description/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                                <tr><td>2.</td>
                                    <td><a href="{!! url('/'.$prefix.'/list-of-successful-candidates/'.$_POST['st_code']) !!}" target="_blank">List of Successful Candidates</a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/list-of-successful-candidates/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                                <tr><td>3.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/list-of-political-parties-participated/'.$_POST['st_code']) !!}">List Of Political Parties Participated</a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/list-of-political-parties-participated/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>
                                <tr><td>4.</td>
                                    <td><a href="{!! url('/'.$prefix.'/highlights/'.$_POST['st_code']) !!}" target="_blank">Highlights
                                        </a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/highlights/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                                <tr><td>5.</td>
                                    <td><a href="{!! url('/'.$prefix.'/performance-of-political-parties/'.$_POST['st_code']) !!}" target="_blank">Performance of Political Parties</a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/performance-of-political-parties/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                                <tr><td>6.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/electorsdatasummary/'.$_POST['st_code']) !!}">Electors Data Summary</a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/electorsdatasummary/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr><tr><td>7.</td>
                                    <td><a href="{!! url('/'.$prefix.'/performance-of-women-candidates/'.$_POST['st_code']) !!}" target="_blank">Performance of Women Candidates
                                        </a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/performance-of-women-candidates/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>
                                  <tr><td>8.</td>
                                    <td><a href="{!! url('/'.$prefix.'/constituency-data-summary/'.$_POST['st_code']) !!}" target="_blank">Constituency Data Summary
                                        </a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/constituency-data-summary/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                                <tr><td>9.</td>
                                    <td><a href="{!! url('/'.$prefix.'/candidate-data-summary/'.$_POST['st_code']) !!}" target="_blank">Candidate Data Summary</a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/candidate-data-summary/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                                <tr><td>10.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/detailed-results/'.$_POST['st_code']) !!}">Detailed Results</a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/detailed-results/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                                <tr><td>11.</td>
                                    <td><a target="_blank" href="{!! url('/'.$prefix.'/annxure/'.$_POST['st_code']) !!}">ANNXURE - 1 (ELECTORS DATA SUMMARY )</a></td>
                                    <td class="cent"><a target="_blank" href="{!! url('/'.$prefix.'/annxure/'.$_POST['st_code']) !!}"><i  class="far fa-eye fa-2x"></i></a></td>
                                </tr>

                              
                            </tbody>
                        </table>

                    @endif
					
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection