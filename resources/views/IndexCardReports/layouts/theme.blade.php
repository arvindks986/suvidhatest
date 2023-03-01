<!DOCTYPE HTML>
      <html lang="{{ app()->getLocale() }}">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-9" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="poppins" content="all,follow">
  
    <title>Index Card Report</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
     <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">    <!-- jQuery Circle-->
		<link rel="stylesheet" href="{{asset('admintheme/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{asset('admintheme/css/bootstrap-datetimepicker.css')}}"> 
		<link rel="stylesheet" href="{{asset('admintheme/css/daterangepicker.css')}}"> 
		<link rel="stylesheet" href="{{asset('admintheme/vendor/font-awesome/css/font-awesome.min.css')}}">
    

    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.3.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/additional-methods.js"></script>
                
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/v4-shims.css">
  
                <!-- Fontastic Custom icon font-->
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="{{asset('admintheme/css/fontastic.css')}}">
    <link rel="stylesheet" href="{{asset('admintheme/css/table.css')}}">     <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="{{asset('admintheme/css/slicknav.css')}}">    <!-- Favicon-->
    <link rel="stylesheet" href="{{asset('admintheme/css/footable.bootstrap.min.css')}}">    
    <link rel="stylesheet" href="{{asset('admintheme/css/style.red.css')}}" id="theme-stylesheet">    
    <link rel="stylesheet" href="{{asset('admintheme/css/custom.css')}}">    <!-- Favicon-->
	<link rel="shortcut icon" href="{{asset('admintheme/img/favicon.ico')}}">
   <link rel="stylesheet" type="text/css" href="{{asset('multiselect/css/bootstrap-multiselect.css')}}">
   
   <link rel="stylesheet" type="text/css" href="{{asset('multiselect/css/font-awesome.min.css')}}">

<!--     
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.bootstrap.min.css" />
<link rel="stylesheet" href="{{ asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">   
<link rel="stylesheet" href="{{ asset('admintheme/css/grasp_mobile_progress_circle-1.0.0.min.css') }}">	  -->
<script>
       window.Laravel = <?php echo json_encode([
           'csrfToken' => csrf_token(),
       ]); ?>
   </script>

   <script type="text/javascript">
       var APP_URL = {!! json_encode(url('/')) !!}
       var csrf  = <?php echo json_encode([ csrf_token(),]); ?>
   
   </script>
<style>
  
  .container {
    max-width: 1294px;
}

/*
 .card-header{position: relative;}
 .card-header:before{
        background-image: url(http://10.199.104.246:82/indexnew/public/img/img.png);
    background-repeat: no-repeat;
    position: absolute;
  top: 65px;
       content: '';
    height: 43%;
    width: 190px;
    left: 6px;
    }

*/

.table th {
    background: #f0587e;
    color: #fff;
}

.fa-eye:before {
    content: "\f06e";
    color: #666;
    font-size: 20px;
    margin: auto;

}

.card-header p.mb-0.text-right img:nth-child(2) {
    width: 61px !important;
    display: table-row;
    margin: 0;
    padding: 0;
}

#theImg{
	display:none;
}

</style>


</head>

<!--<div id="loading">
<img id="loading-image" src="http://10.199.104.246:82/indexnew/public/img/ajax.gif" alt="Loading..." />
</div>
-->


<body class="d-flex flex-column h-100">


<!--  <div class="border-top"></div> -->

  <!--header start-->
      @include('admin.includes.pc.adminheader')
    <!--header end-->
  <main>
 
    <!--bradcom start-->
      <section class="breadcrumb-section">
		<div class="container-fluid">
		<div class="row">
		  <div class="col">
			<ul id="breadcrumb" class="pt-2 mr-auto">
			  <li><a href="#"><span class="icon icon-home"> </span></a></li>
			  <li><a href="#"><span class="icon icon-beaker"> </span> Index Card Report</a></li>
			  <li><span class="icon icon-double-angle-right"></span> @yield('bradcome')</li>  
			</ul>
			<div class="nav-header welcome float-right">
		   <ul class="float-right">
			   <li><a href="javascript:void(0)"> Welcome :- {{$user_data->designation}} LoginId:- {{$user_data->officername}}</a> </li>
			  </ul>
			  <!--<input type="hidden" value="{{$_SERVER["SERVER_ADDR"]}}" readonly>-->
		</div>
		  </div>
		</div>
		</div>
		</section> 
    <!--bradcom end-->
      <?php /*if($user_data->role_id == '27'){  ?>
	  <section class="note">
		<div class="container-fluid">
		<div class="row">
		  <div class="col">
			<div class="alert alert-warning" style="font-size: 26px;">
			   *All Index Card are not finalized from all states. Reports may vary. 
			</div>
		  </div>
		</div>
		</div>
		</section> 
	  
	  <?php } */ ?>
      
      <!--main content start-->
      @yield('content')
      <!--main content end-->
   
 </main> 
  <!--footer start-->
      @include('admin.includes.pc.adminfooter')
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
   <!--  <script src="{{ asset('admintheme/js/grasp_mobile_progress_circle-1.0.0.min.js') }}"></script> -->
    <script src="{{ asset('admintheme/vendor/jquery.cookie/jquery.cookie.js') }}"> </script>
    <script src="{{ asset('admintheme/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('admintheme/js/bootstrap-datetimepicker.js') }}"></script>
    <!-- <script src="{{ asset('admintheme/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script> -->
    
  <!--   <script src="{{ asset('admintheme/vendor/chart.js/Chart.min.js') }}"></script> -->
    <!-- Main File-->
  <script type="text/javascript" src="{{ asset('admintheme/js/jquery.slicknav.js') }}"></script>
  <script type="text/javascript" src="{{ asset('admintheme/js/footable.js') }}"></script>
 <!--  <script type="text/javascript" src="{{ asset('admintheme/js/dataTables.fixedColumns.min.js') }}"></script> -->
  <script src="{{ asset('admintheme/js/bootstrap-datepicker.js') }}"  type="text/JavaScript"></script>   
<script type="text/javascript" src="{{asset('multiselect/js/bootstrap-multiselect.js')}}"></script>
  
<script>
$(document).ready(function() {
//    var table = $('#example').DataTable({       
      ///  scrollX:        true,
     //   scrollCollapse: true,
     //   paging:         true,
     //   fixedColumns:   {
      //      leftColumns: 3,
       //     rightColumns: 1
      //  }
   // });
	
	$('.datepicker').datetimepicker({
	     format: "DD-MM-YYYY"
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
	 $(function () {
                $('.datetimepicker').datetimepicker();
            });
</script>
<script type="text/javascript">
$(document).ready(function(){
 $('.table').wrap('<div class="table-responsive"></div>');
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

<!-- <script>
	jQuery(function($){
	$('.Toggletable').footable();
	$('#theDiv').prepend('<img id="theImg" src="theImg.png" />')

});
</script>  -->






<script>
$('#rStartDate').datetimepicker({
});
</script>


<script>
$('#rEndDate').datetimepicker({
});
</script>






<script> 

$('.card-header h4').append('<img id="theImg" src="http://10.199.104.246:82/indexnew/public/img/img.png"/>');

</script>

<script>
	$(function(){
		$('#menu').slicknav();
	});
</script>
@yield('script')
  </body>
</html>