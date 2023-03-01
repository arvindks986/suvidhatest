<!DOCTYPE HTML>
      <html lang="{{ app()->getLocale() }}">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="poppins" content="all,follow">
    <input type="hidden" name="base_url" id="base_url" value="<?php echo url('/'); ?>" />
    <title>Index Card Reports</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
     <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/v4-shims.css">
    <link rel="stylesheet" type="text/css" href="{{asset('theme/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/bootstrap-datetimepicker.css') }}">
    <!-- Font Awesome CSS-->
      <link rel="stylesheet" href="{{ asset('theme/css/font-awesome.min.css') }}">    <!-- Fontastic Custom icon font-->
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="{{ asset('theme/css/fontastic.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/table.css') }}">
     <!-- Google fonts - Poppins -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="{{ asset('theme/css/grasp_mobile_progress_circle-1.0.0.min.css') }}">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="{{ asset('theme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{ asset('theme/css/style.red.css') }}" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{ asset('theme/css/slicknav.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/custom.css') }}">
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{ asset('theme/img/favicon.ico') }}">

   
    
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
      @include('admin.includes.ac.adminheader')
    <!--header end-->
 
  <main class="mb-auto">
    <!--bradcom start-->
      @include('IndexCardReports.layouts.indexCardbradcom')
    <!--bradcom end-->
      
      
      <!--main content start-->
      @yield('content')
      <!--main content end-->
   </main>
 
  <!--footer start-->
      @include('admin.includes.ac.adminfooter')
    <!--footer end-->
<!-- end main -->
 
<!-- JavaScript files-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset('theme/vendor/popper.js/umd/popper.min.js') }}"> </script>
    <script src="{{ asset('theme/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
      <script src="{{ asset('theme/js/datatable.min.js') }}"></script>
    <script src="{{ asset('theme/js/bootstrap4.min.js') }}"></script>
    <script src="{{ asset('theme/js/jquery.slicknav.js') }}"></script>
   <!--  <script src="{{ asset('theme/js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script> -->
    <script src="{{ asset('theme/vendor/jquery.cookie/jquery.cookie.js') }}"> </script>
   <!--  <script src="{{ asset('theme/vendor/chart.js/Chart.min.js') }}"></script> -->
    <script src="{{ asset('theme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
  <!--   <script src="{{ asset('theme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script> -->
       
    <!-- Main File-->
   <!--  <script src="{{ asset('theme/js/front.js') }}"></script> -->
   <!--  <script type="text/javascript" src="{{ asset('theme/js/dataTables.fixedColumns.min.js') }}"></script> -->
    <script src="{{ asset('theme/js/moment.min.js') }}"  type="text/JavaScript"></script> 
	
	<script type="text/javascript" src="{{ asset('theme/js/daterangepicker.js') }}"></script>
  <!--  <script src="{{ asset('theme/js/bootstrap-datepicker.js') }}"  type="text/JavaScript"></script>  -->
    <script src="{{ asset('theme/js/bootstrap-datetimepicker.js') }}"  type="text/JavaScript"></script>
    <script src="{{ asset('js/common.js')}}" type="text/JavaScript"></script>  
<script>
$(document).ready(function() {
    var table = $('#example').DataTable({       
        scrollX:        true,
        scrollCollapse: true,
        paging:         true,
        fixedColumns:   {
            leftColumns: 3,
            rightColumns: 1
        }
    });
  
  $('.datepicker').datetimepicker({
       format: "D-M-YY"
  });
      
      

 $('.affidavit').bind('change', function () {
  var filename = $(".affidavit").val();
  if (/^\s*$/.test(filename)) {
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen..."); 
  }
  else {
            $(".file-upload").addClass('active');
            $("#noFile").text(filename.replace("C:\\fakepath\\", "")); 
      }
});
  
  
  
}); 
</script> 
  
<script>$(document).ready(function() {
    $('#example').DataTable();
    $('#list-table').DataTable();
  });
</script>
<script type="text/javascript">
$(document).ready(function(){
 $('.table').wrap('<div class="table-responsive"></div>');
});
</script>
<script>
	$(function(){
		$('#menu').slicknav();
	});
</script>

<script>
jQuery(document).ready(function($) {
    $(".alert").delay(7000).fadeOut(500);});

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
@yield('script')
  </body>
</html>