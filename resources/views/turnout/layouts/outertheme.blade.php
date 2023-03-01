<!DOCTYPE HTML>
      <html lang="{{ app()->getLocale() }}">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="poppins" content="all,follow">
    <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
    <title>Dynamic Voter turnout </title>
    <meta name="description" content="">
    <meta name="keywords" content="">
     <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">    <!-- jQuery Circle-->
		<link rel="stylesheet" href="{{asset('admintheme/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{asset('admintheme/css/animate.css') }}">

		<link rel="stylesheet" href="{{asset('admintheme/css/bootstrap-datetimepicker.css')}}"> 
		<link rel="stylesheet" href="{{asset('admintheme/css/daterangepicker.css')}}"> 
		<link rel="stylesheet" href="{{asset('admintheme/vendor/font-awesome/css/font-awesome.min.css')}}">    <!-- Fontastic Custom icon font-->
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="{{asset('admintheme/css/fontastic.css')}}">
    <link rel="stylesheet" href="{{asset('admintheme/css/table.css')}}">     <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="{{asset('admintheme/css/slicknav.css')}}">    <!-- Favicon-->
    <link rel="stylesheet" href="{{asset('admintheme/css/jquery.stickytable.css')}}">    <!-- Favicon-->
	
    <!-- <link rel="stylesheet" href="{{asset('admintheme/css/footable.bootstrap.min.css')}}">    --> 
    <link rel="stylesheet" href="{{asset('admintheme/css/style.red.css')}}" id="theme-stylesheet">    
    <link rel="stylesheet" href="{{asset('admintheme/css/custom.css')}}">    <!-- Favicon-->
	<link rel="shortcut icon" href="{{asset('admintheme/img/favicon.ico')}}">
  <link href="{!! url('admintheme/css/jquery.toast.css') !!}" rel="stylesheet">

 
<script>
       window.Laravel = <?php echo json_encode([
           'csrfToken' => csrf_token(),
       ]); ?>
   </script>

   <script type="text/javascript">
       var APP_URL = {!! json_encode(url('/')) !!}
       var csrf  = <?php echo json_encode([ csrf_token(),]); ?>
   
   </script>

</head>

<body class="d-flex flex-column h-100">
<!--  <div class="border-top"></div> -->

  <!--header start-->
      @include('turnout.includes.header')
    <!--header end-->
  <main>
 
    
      
      <!--main content start-->
      @yield('content')
      <!--main content end-->
   
 </main> 
  <!--footer start-->
      @include('turnout.includes.footer')
    <!--footer end-->
<!-- end main -->
 
<!-- JavaScript files-->
   
  <script type="text/javascript" src="{{ asset('admintheme/js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('admintheme/js/moment.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('admintheme/js/daterangepicker.js') }}"></script>
  <script type="text/javascript" src="{{ asset('admintheme/js/jquery.slicknav.js') }}"></script>
  <script src="{{ asset('admintheme/vendor/popper.js/umd/popper.min.js') }}"> </script>
  <script src="{{ asset('admintheme/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admintheme/js/datatable.min.js') }}"></script>
  <script src="{{ asset('admintheme/js/bootstrap4.min.js') }}"></script>
   
    <script src="{{ asset('admintheme/vendor/jquery.cookie/jquery.cookie.js') }}"> </script>
    <script src="{{ asset('admintheme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('admintheme/js/bootstrap-datetimepicker.js') }}"></script>
    <!-- <script src="{{ asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script> -->
    
  <!--   <script src="{{ asset('admintheme/vendor/chart.js/Chart.min.js') }}"></script> -->
    <!-- Main File-->
  <script type="text/javascript" src="{{ asset('admintheme/js/jquery.slicknav.js') }}"></script>
 <!--  <script type="text/javascript" src="{{ asset('admintheme/js/footable.js') }}"></script> -->
  <script type="text/javascript" src="{{ asset('admintheme/js/jquery.stickytable.js') }}"></script>
 <!--  <script type="text/javascript" src="{{ asset('admintheme/js/dataTables.fixedColumns.min.js') }}"></script> -->
  <script src="{{ asset('admintheme/js/bootstrap-datepicker.js') }}"  type="text/JavaScript"></script>   

<script>
$(document).ready(function() {
 
	$('.datepicker').datetimepicker({
	     format: "DD-MM-YYYY"
	});
			
}); 
</script>	
	
<script>$(document).ready(function() {
    $('#example').DataTable();
    $('#list-table').DataTable();
});
  
</script>
<script type="text/javascript">
if($(window).width() < 767)
{
   $(document).ready(function(){
 $('.datatable').wrap('<div class="table-responsive"></div>');
});
} else {
   $(document).ready(function(){
 $('.datatable').wrap('<div class="sticky-table sticky-ltr-cells"></div>');
});
}
</script>
<script type="text/javascript">
if($(window).width() < 767)
{
   $(document).ready(function(){
 $('.table').wrap('<div class="table-responsive"></div>');
});
} else {
  
}


</script>
<script type="text/javascript">
	 $(function () {
                $('.datetimepicker').datetimepicker();
            });
</script>
<script type="text/javascript">

var x, i, j, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("custom-select");
for (i = 0; i < x.length; i++) {
        selElmnt = x[i].getElementsByTagName("select")[0];
        /*for each element, create a new DIV that will act as the selected item:*/
        a = document.createElement("DIV");
        a.setAttribute("class", "select-selected");
        a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
        x[i].appendChild(a);
        /*for each element, create a new DIV that will contain the option list:*/
        b = document.createElement("DIV");
        b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < selElmnt.length; j++) {
              /*for each option in the original select element,
              create a new DIV that will act as an option item:*/
              c = document.createElement("DIV");
              c.innerHTML = selElmnt.options[j].innerHTML;
              c.addEventListener("click", function(e) {
              /*when an item is clicked, update the original select box,
              and the selected item:*/
              var y, i, k, s, h;
              s = this.parentNode.parentNode.getElementsByTagName("select")[0];
              h = this.parentNode.previousSibling;
        for (i = 0; i < s.length; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
                s.selectedIndex = i;
                h.innerHTML = this.innerHTML;
                y = this.parentNode.getElementsByClassName("same-as-selected");
            for (k = 0; k < y.length; k++) {
                y[k].removeAttribute("class");
            }
                this.setAttribute("class", "same-as-selected");
                break;
          }
        }
              h.click();
    });
          b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
      this.classList.toggle("select-arrow-active");
    });
}
function closeAllSelect(elmnt) {
  /*a function that will close all select boxes in the document,
  except the current select box:*/
  var x, y, i, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  for (i = 0; i < y.length; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < x.length; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}
/*if the user clicks anywhere outside the select box,
then close all select boxes:*/
document.addEventListener("click", closeAllSelect);

</script>
<script>
	$(function(){

    if($('#menu').length){
		  $('#menu').slicknav();
    }

	});
</script>

 


  </body>
</html>