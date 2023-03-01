<style type="text/css">
.collapse:not(.show) {
    display: block;
}
nav.navbar-default.mainmenu ul ul li {
    border-bottom: 1px solid #f6f1f1;
}




.nav-header li a{color: #fff !important;    border-bottom: .5px solid #d0cfcf66;}
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
</style> 
<style type="text/css" >        
        @media print {     
    .myheader{display: none !important;}
    .footer{display: none !important;}
    .slicknav_menu{display: none !important;}
    .mybradcom{display: none !important;}
} 
    </style>
 <header class="header myheader">
   <nav>
      <div class="nav-header">
    <div class="container-fluid d-flex flex-md-row align-items-md-center">
    <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
     <!-- ROAC Login Section-->
       @if($user_data->role_id=='19' || $user_data->role_id=='21')
        <?php   

      $check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
      if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
                $cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
              }
      $seched=getschedulebyid($ele_details->ScheduleID);
      $sechdul=checkscheduledetails($seched); //dd($sechdul);
 ?>
        <ul class="float-right">
          <li class="active"><a href="{{url('/roac/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
          <ul class="dropdown">
             @if($cand_finalize_ro=='0')
             
            <li><a rel="" href="{{url('/roac/createnomination')}}" class="dropdown-item"> <span>Nomination</span></a></li>
            <li><a rel="" href="{{url('/roac/multiplenomination')}}" class="dropdown-item"> <span>Multiple Nomination</span></a></li>
           @endif
            <li><a rel="" href="{{url('/roac/candidateaffidavit')}}" class="dropdown-item"> <span>Upload Affidavit</span></a></li>
             
             
            <li><a rel="" href="{{url('/roac/counteraffidavit')}}" class="dropdown-item"> <span>Upload Counter Affidavit</span></a></li>
             
            <li><a rel="" href="{{url('/roac/listnomination')}}" class="dropdown-item"> <span>List of Applicants</span></a></li>
             @if($cand_finalize_ro=='0')
             
            <li><a rel="" href="{{url('/roac/scrutiny-candidates')}}" class="dropdown-item"> <span>Scrutiny of Candidates</span></a></li>
             <li><a rel="" href="{{url('/roac/accepted-candidate')}}" class="dropdown-item">Mark validly nominated candidates</a></li>
            <li><a rel="" href="{{url('/roac/withdrawn-candidates')}}" class="dropdown-item"> <span>Withdrawl of Candidates</span></a></li>
            <li><a rel="" href="{{url('/roac/symbol-upload')}}" class="dropdown-item">Assign Symbol </a></li>
             @endif   
            
           <li><a rel="" href="{{url('/roac/contested-application')}}" class="dropdown-item"> Contesting Candidates</a></li> 
             
            </ul>
          </li>
           <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/roac/permission/allmasters')}}" class="dropdown-item">Add/Update Master Data </a></li>
                <li><a rel="" href="{{url('/roac/permission/offlinePermission')}}" class="dropdown-item"> Offline permission Module</a></li>
                <li><a rel="" href="{{url('/roac/permission/allPermissionRequest')}}" class="dropdown-item"> Accept/Reject permission</a></li>
                <li><a rel="" href="{{url('/roac/permission/agentCreation')}}" class="dropdown-item"> Create Agent</a></li>
            </ul>

         </li>
          <li><a href="javascript:void(0)" >Voter Turnout<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <!--<li><a rel="" href="{{url('/roac/voting/list-schedule')}}" class="dropdown-item">List of Poll Turnout</a></li>-->
				<li><a rel="" href="{{url('/roac/ElectorsDetails')}}" class="dropdown-item">Electors Details</a></li>
                <li><a rel="" href="{{url('/roac/voting/schedule-entry')}}" class="dropdown-item">End of Poll Turnout</a></li>
                
            </ul>

         </li>   
          
          
           <li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
         <!-- <li><a rel="" href="{{url('/roac/counting/prepare-counting')}}" class="dropdown-item">Prepare Counting Data</a></li>
          <li><a rel="" href="{{url('/roac/counting/round-schedule')}}" class="dropdown-item"> Round Schedule </a></li>
          <li><a rel="" href="{{url('/roac/counting/counting-data-entry')}}" class="dropdown-item"> Counting Data Entry </a></li>
          <li><a rel="" href="{{url('/roac/counting/postal-data-entry')}}" class="dropdown-item"> Postal Ballot Votes </a></li>
          <li><a rel="" href="{{url('/roac/counting/round-wise-entry')}}" class="dropdown-item">Round Wise Entry Details</a></li>
          <li><a rel="" href="{{url('/roac/counting/counting-results')}}" class="dropdown-item"> Results Declaration </a></li>-->
           
           </ul> 
          </li> 
       <!--<li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
             <li><a rel="" href="{{url('/roac/electors-ropollingstationlist')}}" class="dropdown-item"> <span>Electors & PS Info</span></a></li>
            </ul>
          </li> -->
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
          <ul class="dropdown">
		  <li><a rel="" href="{{url('/roac/datewisereport')}}" class="dropdown-item">Nomination Report</a></li> 
		   <!-- #waseem -->
          <li><a rel="" href="{{url('/roac/report/scrutiny')}}" class="dropdown-item">Scrutiny Report</a></li> 
		  
	<li><a rel="" href="{{url('/roac/reportro')}}" class="dropdown-item">Datewise Permission Report</a></li> 
		    	
	<li><a rel="" href="{{url('/roac/permissionraw')}}" class="dropdown-item"> Permission Raw Report</a></li> 
          
	<li><a rel="" href="{{url('/roac/partywise')}}" class="dropdown-item">PartyWise Permission Report</a></li> 
          	
	<li><a rel="" href="{{url('/roac/permissiontype')}}" class="dropdown-item">PermissionWise Report</a></li> 
 
           <!-- <li><a rel="" href="{{url('/roac/permission/permissioncount')}}" class="dropdown-item"> Permission Report</a></li>
          <li><a rel="" href="{{url('/ropc/datewisereport')}}" class="dropdown-item">Nomination Report</a></li>  
          <li><a rel="" href="{{url('/ropc/form3A-report')}}" class="dropdown-item">Form 3A Report</a></li>   
          <li><a rel="" href="{{url('/ropc/form4A-report')}}" class="dropdown-item">Form 4A Report</a></li> -->      
          </ul>
          </li>
          <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <!--<li><a rel="" href="{{url('/roac/changepassword')}}" class="dropdown-item"> Change Password</a></li>-->
            <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
      
        </ul>
         @endif
        <!-- End RO ACModel-->


      

        <!-- CEOAC Login Section-->
       @if($user_data->role_id=='4')
        <ul class="float-right">
          <li class="active"><a href="{{url('/acceo/dashboard')}}">Home</a></li>
	      <!--NIRAJ LINKS START-->
         <li class="nav-item dropdown top-notify-two mr-5">
            <a class="nav-link" href="{{url('/acceo/notification')}}">
          Expenditure<span class="arrow-down"></span><span class="span"><?php echo session()->get('countscrutiny'); ?></span></a>
           <ul class="dropdown">
          <li class="dropdown-submenu">
            <a class="dropdown-item" href="javascript:void(0)">Dashboard</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{url('/acceo/expdashboard')}}">Analytical Dashboard</a></li>
              <li><a class="dropdown-item" href="{{url('/acceo/statusExpdashboard')}}">Status Dashboard</a></li>
            </ul>
          </li>
		  
		   <li class="dropdown-submenu">
                  <a class="dropdown-item" href="javascript:void(0)">MIS</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('/acceo/mis-officer')}}">Officer MIS</a></li>
                  </ul>
              </li>

		     <!-- <li><a rel="" href="{{url('/pcceo/candidateList')}}" class="dropdown-item">Tracking</a></li>-->
		     <li class="top-notify-two"><a href="{{url('/acceo/allscrutiny')}}" class="dropdown-item">Notification</a></li>
			 <!--  <li><a class="dropdown-item" href="{{url('/acceo/FinalizedcandidateList')}}">Finalized Candidate</a></li>  -->
            </ul>
          </li>
		  
		   <!--NIRAJ LINKS ENDS-->
		  
        <!-- <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <!--<li><a rel="" href="{{url('/acceo/change-password')}}" class="dropdown-item"> Change Password</a></li>-->
            <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
            </ul>
          </li>
          
        </ul>
         @endif
        <!-- End CEO AC Model-->
		<!--  CEO Agent  AC Model-->
         @if($user_data->role_id=='23')
        <ul class="float-right">
          <li class="active"><a href="{{url('/acceo/dashboard')}}">Home</a></li>

      <li><a href="{{url('acceo/voting/list-schedule/state')}}">Poll Turn Out</a></li>

          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <ul class="dropdown">
            <li><a rel="" href="{{url('/acceo/candidate-finalize')}}" class="dropdown-item"> <span>List of Nomination Finalize</span></a></li>
            
            </ul>
          </li>
            <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
             <ul class="dropdown">
<!--                <li><a rel="" href="{{url('/acceo/allmasters')}}" class="dropdown-item">Add/Update Master Data </a></li>-->
				<li><a rel="" href="{{url('/acceo/offlinePermission')}}" class="dropdown-item"> Offline permission Module</a></li>
<!--                 <li><a rel="" href="{{url('/acceo/allPermissionRequest')}}" class="dropdown-item"> Accept/Reject permission</a></li>-->
                <li><a rel="" href="{{url('/acceo/permissioncount')}}" class="dropdown-item"> Permission Report</a></li>
<!--                <li><a rel="" href="{{url('/acceo/agentCreation')}}" class="dropdown-item"> Create CEO-Agent</a></li>-->
              </ul>
              
         </li>
          <!--<li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
            <li><a rel="" href="#" class="dropdown-item">comming </a></li>
            <li><a rel="" href="#" class="dropdown-item">Comming </a></li>
             
           </ul>
          </li>-->
           <li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/acceo/officer-details')}}" class="dropdown-item"> <span>Update Officer Details</span></a></li>
			  <li><a rel="" href="{{url('/acceo/electors-pollingstationlist')}}" class="dropdown-item"> <span>Electors & PS Info</span></a></li>
            <!--<li><a rel="" href="{{url('/acceo/psinfo')}}" class="dropdown-item"> <span>PS Details & AMF</span></a></li>-->
            </ul> 
          </li>
       
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
				<li><a rel="" href="{{url('/acceo/nomination-report')}}" class="dropdown-item">Nomination Report</a></li>
				<!-- waseem asgar -->
              <li><a rel="" href="{{url('/acceo/report/scrutiny')}}" class="dropdown-item">Scrutiny Report</a></li> 
              <li><a rel="" href="{{url('/acceo/reportceo')}}" class="dropdown-item">DateWise Permission Report</a></li> 
			  <li><a rel="" href="{{url('/acceo/ceoreport')}}" class="dropdown-item">Permission Raw Report</a></li>
			 <li><a rel="" href="{{url('/acceo/partywise')}}" class="dropdown-item">PartyWise Permission Report</a></li>
			  <li><a rel="" href="{{url('/acceo/permissiontype')}}" class="dropdown-item">PermissionWise Report</a></li>
              <!-- End waseem asgar -->
				
              <!--<li><a rel="" href="{{url('/acceo/aclist')}}" class="dropdown-item">List Of acs With Candidate Details</a></li>
              <li><a rel="" href="{{ url('acceo/duplicate-symbol-view') }}" class="dropdown-item">Duplicate Symbols</a></li>
              <li><a rel="" href="{{url('/acceo/duplicateparties')}}" class="dropdown-item"> Duplicate Parties  </a></li>
              
              
              <li><a rel="" href="{{url('/acceo/candidate-symbol-no-200')}}" class="dropdown-item">List of Candidates with Symbol No 200</a></li>
      <li><a rel="" href="{{url('/acceo/login-detail')}}" class="dropdown-item">CEO Officer Login Report</a></li>-->
	  
	        <!--PRADEEP REPORTS LINKS STARTS-->
              <li><a rel="" href="{{url('/acceo/CountingStatus')}}" class="dropdown-item">Counting Status Report</a></li>
              <li><a rel="" href="{{url('/acceo/CeoElectionSchedule')}}" class="dropdown-item">Election Schedule</a></li>
              <!--PRADEEP REPORTS LINKS ENDS-->

            </ul>
          </li>
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <!--<li><a rel="" href="{{url('/acceo/change-password')}}" class="dropdown-item"> Change Password</a></li>-->
            <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
            </ul>
         @endif
        <!--  End CEO Agent  AC Model-->

        <!-- DEOAC Login Section-->
       @if($user_data->role_id=='5' || $user_data->role_id=='24')
        <ul class="float-right">
          <li class="active"><a href="{{url('/acdeo/dashboard')}}">Home</a></li>
      
      <!-- Sajal Modify 20-MAy-2019 -->     
      <li class="nav-item dropdown orangebg-dropdown">
        <a class="nav-link" href="javascript:void(0)"  >
          Expenditure<span class="arrow-down"></span></a>
        <ul class="dropdown-menu"> 
        <li><a class="dropdown-item" href="{{url('/acdeo/scrutinyExpenditure')}}">Fill DEO's Scrutiny Report</a></li>
          <li class="dropdown-submenu">
            <a class="dropdown-item" href="#">Dashboard </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{url('/acdeo/expdashboard')}}">Analytical Dashboard</a></li>
              <li><a class="dropdown-item" href="{{url('/acdeo/statusdashboard')}}">Status Dashboard</a></li>  
            </ul>
          </li>          
         <li><a class="dropdown-item" href="{{url('/acdeo/Summary')}}">DEO's Summary Report</a></li>                               
        </ul>
      </li>
	  <!--------Niraj Link Ends--->
      
        <!--<li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
             <li><a rel="" href="{{url('/acdeo/changepassword')}}" class="dropdown-item"> Change Password</a></li>-->
       <li><a rel="" href="{{url('/logout')}}" class="dropdown-item"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>
      
        </ul>
         @endif
        <!-- End DEO ac Model-->

        <!-- ECIAC ECI Expenditure Level Login Dashboard Header-->
       @if($user_data->role_id=='28')
        <ul class="float-right mr-3">
          <li class="active mr-4"><a href="{{url('/eci-expenditure/expdashboard')}}">Home</a></li>
	        <li class="nav-item dropdown top-notify-two mr-5">
            <a class="nav-link" href="{{url('/eci-expenditure/eciallscrutiny')}}">
            Expenditure<span class="arrow-down"></span><span class="span"><?php echo session()->get('ecicountscrutiny'); ?></span></a>
            <ul class="dropdown-menu"> 
      				 <li class="dropdown-submenu">
      					<a class="dropdown-item" href="javascript:void(0)">Dashboard</a>
      					<ul class="dropdown-menu">
      					  <li><a class="dropdown-item" href="{{url('/eci-expenditure/expdashboard')}}">Analytical Dashboard</a></li>
      					  <li><a class="dropdown-item" href="{{url('/eci-expenditure/statusdashboard')}}">Current Status Dashboard</a></li>
      					</ul>
      				</li>
      		    <li class="dropdown-submenu">
                  <a class="dropdown-item" href="javascript:void(0)">MIS</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('/eci-expenditure/mis-officer')}}">Officer MIS</a></li>
                    <li><a class="dropdown-item" href="{{url('/eci-expenditure/mis-candidate')}}">Candidate MIS</a></li>
					<li><a class="dropdown-item" href="{{url('/eci-expenditure/report-officer')}}">Summary Status Report</a></li>
                  </ul>
              </li>
      		    <li class="dropdown-submenu">
                  <a class="dropdown-item" href="javascript:void(0)">Reports</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('/eci-expenditure/reports')}}">DEO's Summary Report</a></li>
					 <li><a class="dropdown-item" href="{{url('/eci-expenditure/fund-nationalparties')}}">Fund By National Parties</a></li>
					 <li><a class="dropdown-item" href="{{url('/eci-expenditure/district-report')}}">District Wise Status Report</a></li>
                  </ul>
              </li>
      		    
			     <li class="dropdown-submenu">
              <a class="dropdown-item" href="javascript:void(0)">Notification</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{url('/eci-expenditure/eciallscrutiny')}}">Received Via CEO</a></li>
                <li><a class="dropdown-item" href="{{url('/eci-expenditure/eciallscrutinybyepass')}}">Received Via ECI</a></li>  
              </ul>
            </li>
            <!--  <li><a class="dropdown-item" href="{{url('/eci-expenditure/FinalizedcandidateList')}}">Finalized Candidate</a></li>-->
            </ul>
          </li>
          <li><a href="{{url('/logout')}}" class="nav-link logout"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
        </ul>
         @endif
        <!-- End of ECIAC ECI Expenditure Level Login Dashboard Header-->
      </div>
      </div>
      <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div>
    </nav>
   </header>