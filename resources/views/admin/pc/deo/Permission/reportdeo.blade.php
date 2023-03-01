@extends('admin.layouts.pc.theme')
@section('title', 'List Candidate')
@section('content')
<main role="main" class="inner cover mb-3 mb-auto">
	<br/>
    <section id="details">
	
<div class="container-fluid">
<form name ="report" method="post"  action="{{url('/pcdeo/reportdates')}}"> 
{{csrf_field()}}
<div class="row">

<div class="col-sm-3  row">
<label for="state" class="col-sm-4 col-form-label">AC</label>
<div class="col-sm-8 distt">
<select name="ac" id="ac" class="form-control">
<option value="0">-- All Ac --</option>
@foreach($distvalue as $dist)
<option value="{{$dist->AC_NO }}"> 
{{$dist->AC_NAME }}
</option>
@endforeach 
</select>
</div>
</div>

<div class="col-sm-6  row">
<label for="ac" class="col-sm-2 col-form-label">Date</label>
<div class="col-sm-8 distt">
<div class='input-group date datetimepicker1' id=''>
<input type="text" autocomplete = "off" id="demo" placeholder='Search via Date' name="datefilter" class="form-control" >

</div>
</div>
</div>
<div class="col-sm-1  row">
<input type="submit"  value="Export Excel" name="excel" class="btn btn-primary getdata">
</div>
<div class="col-sm-1  row">
</div>
<div class="col-sm-1  row">
<input type="submit"  value="Export PDF" name="pdf" class="btn btn-primary getdata">
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
