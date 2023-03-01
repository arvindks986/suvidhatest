<!--<style type="text/css">
.collapse:not(.show) {
    display: block;
}
nav.navbar-default.mainmenu ul ul li {
    border-bottom: 1px solid #f6f1f1;
}
.nav-header li a{color: #fff !important;  border-bottom: .5px solid #d0cfcf66;}
/******************************Drop-down menu work on hover**********************************/
.mainmenu{background: none;border: 0 solid;margin: 0;padding: 0;min-height:20px;float: left;}
@media only screen and (min-width: 767px) {
.mainmenu .collapse ul li{position:relative;}
.mainmenu .collapse ul li:hover> ul{display:block}
.mainmenu .collapse ul ul{position:absolute;top:100%;left:0;min-width:250px;display:none}
/*******/
.mainmenu .collapse ul ul li{position:relative;display: -webkit-box;}
.mainmenu .collapse ul ul li:hover> ul{display:block}
.mainmenu .collapse ul ul ul{position:absolute;top:0;left:100%;min-width:250px;display:none}
/*******/
.mainmenu .collapse ul ul ul li{position:relative}
.mainmenu .collapse ul ul ul li:hover ul{display:block}
.mainmenu .collapse ul ul ul ul{position:absolute;top:0;left:-100%;min-width:250px;display:none;z-index:1}

}
</style>-->
<!--<style type="text/css" >        
        @media print {     
    .myheader{display: none !important;}
    .footer{display: none !important;}
    .slicknav_menu{display: none !important;}
    .mybradcom{display: none !important;}
} 
    </style>-->
<header class="header">
  <nav>
    <div class="nav-header">
      <div class="container-fluid d-flex flex-md-row align-items-md-center">
        <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;"
              src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;">
              Election Commission of India</span> </a></div>
        <!-- ROAC Login Section-->
        <div class="col-xs-1"><span class="text-white" style="font-size:30px;cursor:pointer" onclick="openNavR()"><small
              class="text-white" style="font-size: 18px; position: relative; top: -5px;">MENU
              &nbsp;&nbsp;</small>☰</span>
        </div>
  
  <div id="mySidenavR" class="sidenavR">

    <div class="Closedbtn">
      <a href="javascript:void(0)" class="closebtn" onclick="closeNavR()">
        <span>Close &nbsp;</span>×</a>
    </div>

      @if($user_data->role_id=='18' ) 
 <!-- ROPC Header Login Section-->
        <ul class="float-right mainmenu">
            <li><a   href="{{url('/ropc/statusExpdashboard')}}">Home</a></li>
           <li><a   href="{{url('/ropc/candidateList')}}">Fill DEO's Scrutiny Report</a></li> 
           <li><a href="{{url('/ropc/reports')}}" class="slider-menu__link">DEO's Summary Report</a></li>
           <li><a rel="" href="{{url('/logout')}}" class=""> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
        </ul>



        @endif

       @if($user_data->role_id=='0000' )
       <?php //print_r($user_data); echo $cand_finalize_ceo; echo $cand_finalize_ro; print_r($sechdul);  
        if(isset($ele_details))
              $seched=getschedulebyid($ele_details->ScheduleID);
              else 
                $seched=getschedulebyid($edetails->ScheduleID);;
                $sechdul=checkscheduledetails($seched);
              
       ?>
      <!-- ROPC Header Login Section-->
  <ul class="float-right mr-5 pr-2 mainmenu">
      <li class="inactive"><a href="{{url('/ropc/dashboard')}}">Home</a></li>
      <li class="nav-item dropdown top-notify-two active">
        <a class="nav-link" href="javascript:void(0)">
          Expenditure<span class="arrow-down"></span><!--<span class="span">12</span>--></a>
        <ul class="dropdown-menu"> 
        <li><a   href="{{url('/ropc/candidateList')}}">Fill DEO's Scrutiny Report</a></li>                               
            <li class="dropdown-submenu">
              <a   href="javascript:void(0)">Dashboard </a>
              <ul class="dropdown-menu">
                <li><a   href="{{url('/ropc/ROExpdashboard')}}">Analytical</a></li>
                <li><a   href="{{url('/ropc/statusExpdashboard')}}">Current Status</a></li>  
  			   <!-- <li><a   href="{{url('/ropc/rotracking-status')}}">Summary Status</a></li>  -->
              </ul>
            </li>
             <li><a   href="{{url('/ropc/reports')}}">DEO's Summary Report</a></li>
             <!--<li class="dropdown-submenu">
              <a   href="javascript:void(0)">ECRP</a>
              <ul class="dropdown-menu">
                <li><a   href="{{url('/ropc/ecrp-registration')}}">ECRP Registration</a></li>
                <li><a   href="{{url('/ropc/statusExpdashboard')}}">Current Status</a></li> 
           <li><a   href="{{url('/ropc/rotracking-status')}}">Summary Status</a></li>
              </ul>
            </li>-->
            <!--<li><a   href="{{url('/ropc/candidateList_abstract')}}">Abstract Statement Form</a></li>
            <li><a   href="{{url('/ropc/candidateList')}}">Tracking</a></li>-->
        </ul>
      </li>


       <li><a rel="" href="{{url('/logout')}}" class=""> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
      

      <!--<li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
       <ul class="dropdown">
       <li><a rel="" href="{{url('/ropc/changepassword')}}"  > Change Password</a></li>
        <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
       </ul>
     </li>-->
  </ul>
    @endif




  <!-- End of ROPC Header Login Section-->

      <!-- ROACLogin Section===============================================================================-->
       @if($user_data->role_id=='19'|| $user_data->role_id=='17' || $user_data->role_id=='20' )
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/aro/permission/permissioncount')}}">Home</a></li>
          <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="{{url('/aro/permission/allmasters')}}"  >Add/Update Master Data </a></li>
            <li><a rel="" href="{{url('/aro/permission/offlinePermission')}}"  > Offline permission Module</a></li>
            <li><a rel="" href="{{url('/aro/permission/allPermissionRequest')}}"  > Accept/Reject permission</a></li>
<!--                <li><a rel="" href="{{url('/ropc/manualforward')}}"  >Manual Forward</a></li>-->
           <li><a rel="" href="{{url('/aro/permission/agentCreation')}}"  > Create Agent</a></li>
          </ul>
         </li>
        <li><a href="javascript:void(0)" >Poll Turnout<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="{{url('/aro/voting/estimate-turnout-entry')}}"  >Estimate Turnout Entry</a></li>
            <li><a rel="" href="{{url('/aro/voting/schedule-entry')}}"  >End of Poll Turnout </a></li> 
            <li><a rel="" href="{{url('/aro/voting/ElectorsDetails')}}"  >Electors Details</a></li>
          </ul>
        </li>   
        <li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
           <!--<ul class="dropdown">
            <li><a rel="" href="{{url('/aro/counting/round-schedule')}}"  > Round Schedule </a></li>
            <li><a rel="" href="{{url('/aro/counting/counting-data-entry')}}"  > Counting Data Entry </a></li>
            <li><a rel="" href="{{url('/aro/counting/round-wise-entry')}}"  >Round Wise Entry Details</a></li>
          </ul> -->
        </li> 
        <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="{{url('/aro/permission/permissioncount')}}"  > Permission Report</a></li>
             <!-------Start niraj header------------->
            <li><a rel="" href="{{url('/aro/permission/permissionraw-report')}}"  >Permission Raw Report</a></li> 
            <li><a rel="" href="{{url('/aro/permission/permissionpartywise-report')}}"  >Partywise Permission Report</a></li>
            <li><a rel="" href="{{url('/aro/permission/permissiondatewise-report')}}"  >DateWise Permission Report</a></li>
            <li><a rel="" href="{{url('/aro/permission/permissiontype-report')}}"  >Permission Type Report</a></li>
		     <!-------End Niraj Header-------------------->
          </ul>
        </li>
        <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="{{url('/ropc/changepassword')}}"  > Change Password</a></li>
             <li><a rel="" href="{{url('/logout')}}"  ><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
        </li>
        </ul>
         @endif
         @if($user_data->role_id=='21')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/ropc/dashboard')}}">Home</a></li>
         
           <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
             <li><a rel="" href="{{url('/aro/permission/offlinePermission')}}"  > Offline permission Module</a></li>
            </ul>
         </li>
           <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="{{url('/aro/permission/permissioncount')}}"  > Permission Report</a></li> 
          </ul>
          </li> 
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="{{url('/ropc/changepassword')}}"  > Change Password</a></li>
             <li><a rel="" href="{{url('/logout')}}"  ><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
        </ul>
         @endif
        <!-- End RO AC Model-->

       <!--CEOPC Header Login Section-->
       @if($user_data->role_id=='4')
    <ul class="float-right mr-3 mainmenu">
      <li class="active"><a href="{{url('/pcceo/dashboard')}}">Home</a></li>
      <li class="nav-item dropdown top-notify-two mr-5">
        <a class="nav-link" href="{{url('/pcceo/notification')}}">
        Expenditure<span class="arrow-down"></span><span class="span"><?php echo session()->get('countscrutiny'); ?></span></a>
      </li>

         <li><a href="javascript:void(0)" >Dashboard<span class="arrow-down"></span></a>
                 <ul class="dropdown">
              <li><a   href="{{url('/pcceo/CeoExpdashboard')}}">Analytical Dashboard</a></li>
          <li><a   href="{{url('/pcceo/statusExpdashboard')}}">Current Status Dashboard</a></li>
     
        </ul>
      </li>

      <li><a href="javascript:void(0)" >MIS<span class="arrow-down"></span></a>
                 <ul class="dropdown">
              <li><a   href="{{url('/pcceo/mis-officer')}}">Officer MIS</a></li>
     
        </ul>
      </li>
     
     <!-- <li><a rel="" href="{{url('/pcceo/candidateList')}}"  >Tracking</a></li>-->
     <li class="top-notify-two"><a href="{{url('/pcceo/allscrutiny')}}"  >Notification</a></li>
      <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> 
        <i class="fa fa-sign-out"></i></a>
      </li>
    </ul>
    
    
      <!--<li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
       <ul class="dropdown">
         <li><a rel="" href="{{url('/pcceo/change-password')}}"  > Change Password</a></li>
         
       </ul>
    </li> -->
     </li>
  </ul>
     @endif
       <!--End of CEOPC Header Login Section-->

		@if($user_data->role_id=='23')
			 <li class="active"><a href="{{url('/pcceo/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">Scrutiny Report<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href=""  >Fill CEO Scrutiny Report </a></li>
           <!-- <li><a rel="" href="#"  >Comming </a></li>-->
            </ul>
          </li>
			 <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="{{url('/pcceo/change-password')}}"  > Change Password</a></li>
            <li><a rel="" href="{{url('/logout')}}"  > <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
		@endif

        <!-- DEOPC Login Section-->
       @if($user_data->role_id=='5')
      <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/pcdeo/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
             	<li><a rel="" href="{{url('/pcdeo/officer-details')}}"  > <span> Update Officer Details</span></a></li>
            </ul>
          </li>
		      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/pcdeo/changepassword')}}"  > Change Password</a></li>
			        <li><a rel="" href="{{url('/logout')}}"  ><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>
        </ul>
         @endif
		
      <!--RO PC AGENT URLS STARTS-->
         @if($user_data->role_id=='22')
        <ul class="float-right mainmenu">
          <li class="active"><a href="{{url('/ropc/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/ropc/permission/offlinePermission')}}"  > Offline permission Module</a></li>
              <li><a rel="" href="{{url('/ropc/permission/permissioncount')}}"  > Permission Report</a></li> 
            </ul>
          </li>
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
              <li><a rel="" href="{{url('/ropc/changepassword')}}"  > Change Password</a></li>
              <li><a rel="" href="{{url('/logout')}}"  > <span class="d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
        </ul>
        @endif
        <!--RO PC AGENT URLS ENDS-->
		<!--DEO PC AGENT URLS STARTS-->
       @if($user_data->role_id=='24')
       <li class="active"><a href="{{url('/pcdeo/dashboard')}}">Home</a></li>
       <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
        <ul class="dropdown">
         <li><a rel="" href="{{url('/pcdeo/offlinePermission')}}"  > Offline permission Module</a></li>
         <li><a rel="" href="{{url('/pcdeo/permissioncount')}}"  > Permission Report</a></li>
        </ul>
        </li>
        <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="{{url('/pcdeo/changepassword')}}"  > Change Password</a></li>
             <li><a rel="" href="{{url('/logout')}}"  ><span class="d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
          </ul>
        </li>
       @endif
       <!--DEO PC AGENT URLS ENDS-->
        <!-- End DEO PC Model-->

  <!-- ECIPC Header Login Section-->
  @if($user_data->role_id=='28')
  <!--<ul class="float-right mr-5 pr-4 mainmenu">
      <li class="inactive"><a href="{{url('/eci-expenditure/EciExpdashboard')}}">Home</a></li>
      <li class="active nav-item dropdown top-notify-two">
        <a class="nav-link" href="{{url('/eci-expenditure/ecinotification/')}}">
          Expenditure<span class="arrow-down"></span><span class="span"><?php echo session()->get('ecicountscrutiny'); ?></span></a>
        <ul class="dropdown-menu">                           
           
            <li class="dropdown-submenu">
              <a   href="javascript:void(0)">Dashboard</a>
              <ul class="dropdown-menu">
                <li><a   href="{{url('/eci-expenditure/EciExpdashboard')}}">Analytical Dashboard</a></li>
                <li><a   href="{{url('/eci-expenditure/statusExpdashboard')}}">Current Status Dashboard</a></li>  
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a   href="javascript:void(0)">MIS</a>
              <ul class="dropdown-menu">
                <li><a   href="{{url('/eci-expenditure/mis-officer')}}">Officer MIS</a></li>
                <li><a   href="{{url('/eci-expenditure/mis-candidate')}}">Candidate MIS</a></li>  
				 <li><a   href="{{url('/eci-expenditure/mis-officer2014')}}">MIS-2014</a></li>	
				 <li><a   href="{{url('/eci-expenditure/report-officer')}}">DEO's Scrutiny Status Report</a></li>
				 

              </ul>
            </li> 
			  <li class="dropdown-submenu">
            <a   href="javascript:void(0)">Reports</a>
            <ul class="dropdown-menu">
			     <li><a   href="{{url('/eci-expenditure/reports')}}">DEO's Scrutiny Summary Report</a></li>
				 <li><a   href="{{url('/eci-expenditure/fund-nationalparties')}}">Fund By National Parties</a></li>
				  <li><a   href="{{url('/eci-expenditure/district-report')}}">District Wise Status Report</a></li>
				    <li><a   href="{{url('/eci-expenditure/breach-report')}}">Breaching Report</a></li>

              
            </ul>
          </li>
		   <li class="dropdown-submenu">
              <a   href="javascript:void(0)">Notification</a>
              <ul class="dropdown-menu">
                <li><a   href="{{url('/eci-expenditure/eciallscrutiny')}}">Received Via CEO</a></li>
                <li><a   href="{{url('/eci-expenditure/eciallscrutinybyepass')}}">Received Via ECI</a></li>  
              </ul>
            </li>

        
        </ul>
      </li>
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
           <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
             <li><a rel="" href="{{url('/logout')}}"  ><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
         <li class="mr-5"><a href="javascript:void(0)"></a>&nbsp;&nbsp;</li>
  </ul>-->
  
  
  
  
  
  <ul class="float-right mainmenu">
             <li class="active"><a href="{{url('/eci-expenditure/EciExpdashboard')}}">Home</a></li>
             <li><a href="{{url('/eci-expenditure/ecinotification/')}}">Expenditure<span class="arrow-down"></span></a>
         <ul class="dropdown">
         <!--<li><a href="{{ url('/eci-index/indexcardpc') }}">Dashboard</a></li>-->
		 
		  <li>
              <a   href="javascript:void(0)">Dashboard</a>
              <ul>
                <li><a href="{{url('/eci-expenditure/EciExpdashboard')}}">Analytical Dashboard</a></li>
				<li><a href="{{url('/eci-expenditure/statusExpdashboard')}}">Current Status Dashboard</a>
				</li> 
              </ul>
            </li>
			
         <!--<li><a href="{{ url('/eci-index/indexcardbriefed') }}">MIS</a></li>-->
		 
		 <li>
              <a   href="javascript:void(0)">MIS</a>
              <ul>
                <li><a   href="{{url('/eci-expenditure/mis-officer')}}">Officer MIS</a></li>
                <li><a   href="{{url('/eci-expenditure/mis-candidate')}}">Candidate MIS</a></li>  
				 <li><a   href="{{url('/eci-expenditure/mis-officer2014')}}">MIS-2014</a></li>
				 <li><a   href="{{url('/eci-expenditure/report-officer')}}">DEO's Scrutiny Status Report</a>
				 </li>
				 

              </ul>
            </li>
			
         <!--<li><a href="{{ url('/eci-index/statistical-report-listing') }}">Reports</a></li> -->
		 
		 <li>
            <a   href="javascript:void(0)">Reports</a>
            <ul>
			     <li>
				 <a   href="{{url('/eci-expenditure/reports')}}">DEO's Scrutiny Summary Report</a></li>
				 <li><a   href="{{url('/eci-expenditure/fund-nationalparties')}}">Fund By National Parties</a></li>
				 <li> <a   href="{{url('/eci-expenditure/district-report')}}">District Wise Status Report</a></li>
				   <li> <a   href="{{url('/eci-expenditure/breach-report')}}">Breaching Report</a>
					</li>
			  
            </ul>
          </li>
		  
		  <li>
              <a   href="javascript:void(0)">Notification</a>
              <ul>
                <li>
				<a   href="{{url('/eci-expenditure/eciallscrutiny')}}">Received Via CEO</a></li>
                <li><a   href="{{url('/eci-expenditure/eciallscrutinybyepass')}}">Received Via ECI</a></li> 				  
				  
              </ul>
            </li>
		  
               </ul>
             </li>
            
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
              <li><a rel="" href="{{url('/profile/password')}}"  > Change Password</a></li>
              <li><a rel="" href="{{url('/profile/pin')}}"  > Change PIN</a></li>
                <li><a rel="" href="{{url('/logout')}}"  ><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>  
         
</ul>
  
  
  
  
  
  @endif
<!-- End of ECIPC Header Login Section-->
  </div>
  </div>
     <!--  <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div> -->
    </nav>
	<!-- Global site tag (gtag.js) - Google Analytics by niraj_15_03_2019 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-136115909-1"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'UA-136115909-1');
</script>
   </header>

   <script src="{{ asset('admintheme/js/jquery.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('admintheme/js/jquery.slicknav.js') }}"></script>
<script type="text/javascript" src="{{ asset('admintheme/js/slider-menu.jquery.js') }}"></script>

    <script>
      $(function(){$('.mainmenu').slicknav();	});	
    </script>
    <script>
    ( function( $ ) {
      $( function() {
        $( '.mainmenu' ).sliderMenu();
      });
    })( jQuery );
    </script>
<script type="text/javascript">
  function openNav() {
      document.getElementById("mySidenav").style.width = "250px";
  }

  function closeNav() {
      document.getElementById("mySidenav").style.width = "0";
  }

  function openNavR() {
      document.getElementById("mySidenavR").style.width = "250px";
  }

  function closeNavR() {
      document.getElementById("mySidenavR").style.width = "0";
  }
</script>