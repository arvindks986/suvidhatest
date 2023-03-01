@extends('admin.layouts.pc.theme')
@section('bradcome', 'Date Wise Permission Report')
@section('content')
 <?php 
 //echo "<pre>";
 //print_r($perm);
 //exit;
 $pc=getpcbypcno($user_data->st_code,$user_data->pc_no);
 $ac=getacbyacno($user_data->st_code,$user_data->ac_no);
 //dd($ac);
 ?>
<main role="main" class="inner cover mb-3 mb-auto">
	<br/>
<section id="details">
<div class="container-fluid">
<form name ="report" method="post"  action="{{url('/aro/permission/permissiondatewise-report')}}"> 
{{csrf_field()}}
<div class="row">
<div class="col-sm-3  row">
    <label for="state" class="col-sm-8 col-form-label">PC Name : {{ $pc->PC_NAME}}</label>
            <div class="col-sm-8 distt">
            <input type="hidden" name="pc_no" value="{{ $pc->PC_NO}}">
            </div>
</div>
<div class="col-sm-4  row">
<label for="ac" class="col-sm-8 col-form-label">AC Name: {{ $ac->AC_NAME}}</label>
<div class="col-sm-8 distt"> <input type="hidden" name="ac_no" value="{{ $ac->AC_NO}}">
</div>
</div>

<div class="col-sm-3  row">
<label for="ac" class="col-sm-2 col-form-label">Date</label>
<div class="col-sm-8 distt">
<div class='input-group date datetimepicker1' id=''>
<input type="text" autocomplete = "off" id="demo" placeholder='Search via Date' name="datefilter" class="form-control" >
</div>
</div>
</div>
<div class="col-sm-2  ">
<input type="submit"  value="Export Excel" name="submit" class="btn btn-primary getdata">
<input type="submit"  value="Export Pdf" name="submit" class="btn btn-primary getdata">

</div>
</div>
</form>
    </div>
    </section>
</main>
@endsection
@section('script')


<link href="{{url('/admintheme/css/daterangepicker.css')}}" rel="stylesheet" id="bootstrap-css">
<script type="text/javascript" src="{{url('/admintheme/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{url('admintheme/js/daterangepicker.js')}}"></script>
<script>
        var selectdate = "";
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        if(dd<10) {
            dd = '0'+dd
        } 

        if(mm<10) {
            mm = '0'+mm
        } 

        today = yyyy + '-' + mm + '-' + dd;
        var start_date = today;
        var end_date = today;
        $('#demo').daterangepicker({
                "ranges": {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]      
            },
            "startDate": Date.now(),
            "endDate": Date.now(),
        autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            }
        }, function(start, end, label) {
        //alert("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
        //alert(start.format('YYYY-MM-DD'));
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');

        });
  $('#demo').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + '~' + picker.endDate.format('YYYY-MM-DD'));
	   //alert(picker.startDate.format('YYYY-MM-DD') + '~' + picker.endDate.format('YYYY-MM-DD'));
	 
	
  });

  $('#demo').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });
</script>
    @endsection
