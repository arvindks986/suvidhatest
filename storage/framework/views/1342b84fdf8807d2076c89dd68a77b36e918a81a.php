 
<header class="header">
   <nav>
      <div class="nav-header">
    <div class="container-fluid d-flex flex-md-row align-items-md-center">
    <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;" src="<?php echo e(asset('theme/img/logo/eci-logo.png')); ?>" alt="" />&nbsp;<span class="text" style="color:#fff;"> Election Commission of India</span> </a></div>
     <!-- ROAC Login Section--> 
      
       <?php if($user_data->role_id=='19' || $user_data->role_id=='21'): ?>
         
        <?php    
          $finalize=candidate_finalizebyro(Auth::user()->st_code,Auth::user()->ac_no,'AC');

        ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/roac/dashboard')); ?>"> Home</a></li>
       <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
          <ul class="dropdown">
           
            <?php if((!$finalize) or $finalize->finalized_ac=='0'): ?>
            <li><a rel="" href="<?php echo e(url('/roac/createnomination')); ?>" class="dropdown-item"> <span>Nomination</span></a></li>
            <li><a rel="" href="<?php echo e(url('/roac/multiplenomination')); ?>" class="dropdown-item"> <span>Multiple Nomination</span></a></li>
           
            <li><a rel="" href="<?php echo e(url('/roac/candidateaffidavit')); ?>" class="dropdown-item"> <span>Upload Affidavit</span></a></li>
             
             
            <li><a rel="" href="<?php echo e(url('/roac/counteraffidavit')); ?>" class="dropdown-item"> <span>Upload Counter Affidavit</span></a></li>
             <?php endif; ?>
            <li><a rel="" href="<?php echo e(url('/roac/listnomination')); ?>" class="dropdown-item"> <span>List of Applicants</span></a></li>
            
           <?php if((!$finalize) or $finalize->finalized_ac=='0'): ?>
            <li><a rel="" href="<?php echo e(url('/roac/scrutiny-candidates')); ?>" class="dropdown-item"> <span>Scrutiny of Candidates</span></a></li>
             <li><a rel="" href="<?php echo e(url('/roac/withdrawn-candidates')); ?>" class="dropdown-item"> <span>Withdrawl of Candidates</span></a></li>
             <li><a rel="" href="<?php echo e(url('/roac/accepted-candidate')); ?>" class="dropdown-item">Mark validly nominated candidates</a></li>
           
            <li><a rel="" href="<?php echo e(url('/roac/symbol-upload')); ?>" class="dropdown-item">Assign Symbol </a></li>
           <?php endif; ?>
            
           <li><a rel="" href="<?php echo e(url('/roac/contested-application')); ?>" class="dropdown-item"> Contesting Candidates</a></li>
           <li><a rel="" href="<?php echo e(url('/roac/ElectorsDetails')); ?>" class="dropdown-item"> Electors Details</a></li>      
             
            </ul>
          </li>
          <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="<?php echo e(url('/roac/permission/allmasters')); ?>" class="dropdown-item">Add/Update Master Data </a></li>
                <li><a rel="" href="<?php echo e(url('/roac/permission/offlinePermission')); ?>" class="dropdown-item"> Offline permission Module</a></li>
                <li><a rel="" href="<?php echo e(url('/roac/permission/allPermissionRequest')); ?>" class="dropdown-item"> Accept/Reject permission</a></li>
                <li><a rel="" href="<?php echo e(url('/roac/permission/agentCreation')); ?>" class="dropdown-item"> Create Agent</a></li>
            </ul>

         </li> 
		 <li><a href="javascript:void(0)" >Voter Turnout<span class="arrow-down"></span></a>
            <ul class="dropdown">
                <li><a rel="" href="<?php echo e(url('/roac/turnout/estimate-turnout-entry')); ?>" class="dropdown-item">Estimate Turnout Entry</a></li>
                <li><a rel="" href="<?php echo e(url('/roac/turnout/schedule-entry')); ?>" class="dropdown-item">End of Poll Turnout </a></li> 
                <li><a rel="" href="<?php echo e(url('/roac/turnout/ElectorsDetails')); ?>" class="dropdown-item">Electors Details</a></li>
                <li><a rel="" href="<?php echo e(url('/roac/turnout/RoPsWiseDetails')); ?>?state=<?php echo e($user_data->st_code); ?>&ac_id=<?php echo e($user_data->ac_no); ?>" class="dropdown-item">PS Wise Voter Turn Out</a></li>
                
            </ul>  

         </li> 
		 <?php if(Auth::user() && Auth::user()->st_code == 'S24' && Auth::user()->ac_no == '228'): ?>
<li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
<ul class="dropdown">

<li><a rel="" href="<?php echo url('/roac/booth-app/voter-list') ?>" class="dropdown-item">Booth Slip</a></li>

<li><a rel="" href="<?php echo  url('/roac/booth-app/officer-list') ?>" class="dropdown-item">Assign Officer</a></li>

<li><a rel="" href="<?php echo  url('/roac/booth-app/dashboard') ?>" class="dropdown-item">
Dashboard</a>
</li>
</ul>
</li>
<?php endif; ?> 
          
     <li><a href="javascript:void(0)" >Counting<span class="arrow-down"></span></a>
          <ul class="dropdown">
         <!--  <li><a rel="" href="<?php echo e(url('/roac/counting/prepare-counting')); ?>" class="dropdown-item">Prepare Counting Data</a></li> -->
          <li><a rel="" href="<?php echo e(url('/roac/counting/round-schedule')); ?>" class="dropdown-item">1.- Round Schedule </a></li>
          <li><a rel="" href="<?php echo e(url('/roac/counting/counting-data-entry')); ?>" class="dropdown-item">2.- EVM Votes Data Entry </a></li>
          <li><a rel="" href="<?php echo e(url('/roac/counting/postal-data-entry')); ?>" class="dropdown-item">3.- Postal Ballot Votes </a></li>
       
          <li><a rel="" href="<?php echo e(url('/roac/counting/counting-results')); ?>" class="dropdown-item">4.- Results Declaration </a></li>
           
           </ul> 
      </li>
       <?php /*  <li><a href="javascript:void(0)" >Booth Counting<span class="arrow-down"></span></a>
            <ul class="dropdown">
            <li><a rel="" href="{{url('/roac/counting/counting-center-details')}}" class="dropdown-item"> Counting Center Details </a></li>
            <li><a rel="" href="{{url('/roac/counting/polling-station-wisevote-entry')}}" class="dropdown-item"> PS Wise Vote Entry </a></li>
            <li><a rel="" href="{{url('/roac/counting/tabulating-trend-results')}}" class="dropdown-item"> Tabulating Trends / Results</a></li>
          </ul> 
      </li>  */?>
      <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
          <ul class="dropdown">
              <li><a rel="" href="<?php echo e(url('/roac/datewisereport')); ?>" class="dropdown-item">Nomination Report</a></li> 
       <!-- #waseem -->
               <li><a rel="" href="<?php echo e(url('/roac/report/scrutiny')); ?>" class="dropdown-item">Scrutiny Report</a></li> 
      
                  <li><a rel="" href="<?php echo e(url('/roac/reportro')); ?>" class="dropdown-item">Datewise Permission Report</a></li> 
                          
                  <li><a rel="" href="<?php echo e(url('/roac/permissionraw')); ?>" class="dropdown-item"> Permission Raw Report</a></li> 
                          
                  <li><a rel="" href="<?php echo e(url('/roac/partywise')); ?>" class="dropdown-item">PartyWise Permission Report</a></li> 
                            
                  <li><a rel="" href="<?php echo e(url('/roac/permissiontype')); ?>" class="dropdown-item">PermissionWise Report</a></li>   
 
             <li><a rel="" href="<?php echo e(url('/roac/permission/permissioncount')); ?>" class="dropdown-item"> Permission Report</a></li>
        <?php /*  <li><a rel="" href="{{url('/ropc/datewisereport')}}" class="dropdown-item">Nomination Report</a></li>  
          <li><a rel="" href="{{url('/ropc/form3A-report')}}" class="dropdown-item">Form 3A Report</a></li>   
          <li><a rel="" href="{{url('/ropc/form4A-report')}}" class="dropdown-item">Form 4A Report</a></li> */ ?>    
          </ul>
          </li>
      <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
        <ul class="dropdown">
          <li class="active"><a href="<?php echo e(url('/roac/round-wise-details')); ?>">Round Wise Details</a></li>
          <li><a rel="" href="<?php echo e(url('/roac/datewisereport')); ?>" class="dropdown-item">Nomination Report</a></li> 
          <li><a rel="" href="<?php echo e(url('/roac/constituency-wise-report')); ?>" class="dropdown-item">AC Result Report</a></li>
          <li><a rel="" href="<?php echo e(url('/eci/round-wise-report')); ?>" class="dropdown-item">Round  Wise Report</a></li>
          <li><a rel="" href="<?php echo e(url('/roac/candidate-wise-report')); ?>" class="dropdown-item">Candidate Wise Report</a></li>
          <?php /*<li><a rel="" href="{{url('/roac/form-21-report')}}" class="dropdown-item">Form 21 E Details</a></li> */ ?>
          <li><a rel="" href="<?php echo e(url('/roac/form-21c-report')); ?>" class="dropdown-item">Form 21 C/D Details</a></li>
          <li><a rel="" href="<?php echo e(url('/roac/form-21-report-upload')); ?>" class="dropdown-item">Upload Form 21 C/D</a></li>
        </ul>
       </li>
      <li><a href="javascript:void(0)" >Index Card<span class="arrow-down"></span></a>
         <ul class="dropdown">
            <li><a class="dropdown-item" href="<?php echo e(url('roac/elector/edit?')); ?>ac_no=<?php echo e($user_data->ac_no); ?>&year=2019">Update Electors/Voters</a></li>
            <li><a class="dropdown-item" href="<?php echo url('roac/voters/edit?'); ?>ac_no=<?php echo e($user_data->ac_no); ?>&year=2019">Update Voters</a></li>
            <li><a class="dropdown-item" href="<?php echo url('roac/indexcard/finalize'); ?>">Finalize Index Card</a></li>
            <li><a class="dropdown-item" href="<?php echo url('roac/index-card'); ?>">Index Card Report</a></li>
            <li><a class="dropdown-item" href="<?php echo url('roac/report/candidate'); ?>">List of nominated candidate</a></li>
          </ul>
      </li>
      <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
        <ul class="dropdown">
           <li><a rel="" href="<?php echo e(url('/profile/password')); ?>" class="dropdown-item"> Change Password</a></li>
           <li><a rel="" href="<?php echo e(url('/profile/pin')); ?>" class="dropdown-item"> Change PIN</a></li>
           <li><a rel="" href="<?php echo e(url('/logout')); ?>" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
         </ul>
      </li>
      
        </ul>
         <?php endif; ?>
        <!-- End RO ACModel-->


      

        <!-- CEOAC Login Section-->
       <?php if($user_data->role_id=='4'): ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/acceo/dashboard')); ?>">Home</a></li>
      
          <li><a href="javascript:void(0)" >Voter Turn Out<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <!-- <li><a href="<?php echo e(url('acceo/turnout/CeoPsWiseDetails')); ?>">PS Wise Voter Turnout</a></li> -->
                <li><a class="dropdown-item" href="<?php echo e(url('acceo/turnout/AcCeoEndOfPoll')); ?>">End Of Poll</a></li>
                <li><a href="<?php echo e(url('acceo/turnout/EndOfPollFinalised')); ?>">End Of Poll Finalised</a></li>
              </ul>
          </li>

          <li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
  <ul class="dropdown">
    <li><a rel="" href="<?php echo url('/acceo/booth-app/voter-list') ?>" class="dropdown-item">Booth Slip</a></li>
    <li><a rel="" href="<?php echo  url('/acceo/booth-app/dashboard') ?>" class="dropdown-item">Dashboard</a></li>
  </ul>
</li>


              <li><a href="<?php echo e(url('acceo/voting/list-schedule/state')); ?>">Poll Turn Out</a></li>

          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <ul class="dropdown">
            <li><a rel="" href="<?php echo e(url('/acceo/candidate-finalize')); ?>" class="dropdown-item"> <span>List of Nomination Finalize</span></a></li>
            
            </ul>
          </li>
          <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
             <ul class="dropdown">
                <li><a rel="" href="<?php echo e(url('/acceo/allmasters')); ?>" class="dropdown-item">Add/Update Master Data </a></li>
        <li><a rel="" href="<?php echo e(url('/acceo/offlinePermission')); ?>" class="dropdown-item"> Offline permission Module</a></li>
                 <li><a rel="" href="<?php echo e(url('/acceo/allPermissionRequest')); ?>" class="dropdown-item"> Accept/Reject permission</a></li>
                <li><a rel="" href="<?php echo e(url('/acceo/permissioncount')); ?>" class="dropdown-item"> Permission Report</a></li>
                <li><a rel="" href="<?php echo e(url('/acceo/agentCreation')); ?>" class="dropdown-item"> Create CEO-Agent</a></li>
              </ul>
              
         </li> 
        
        <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
                  <li><a rel="" href="<?php echo e(url('/acceo/nomination-report')); ?>" class="dropdown-item">Nomination Report</a></li>
        <!-- waseem asgar -->
                 <li><a rel="" href="<?php echo e(url('/acceo/report/scrutiny')); ?>" class="dropdown-item">Scrutiny Report</a></li> 
         <li><a rel="" href="<?php echo e(url('/acceo/districtvalue')); ?>" class="dropdown-item">DistrictWise Permission Report</a></li>
             <li><a rel="" href="<?php echo e(url('/acceo/reportceo')); ?>" class="dropdown-item">DateWise Permission Report</a></li> 
        <li><a rel="" href="<?php echo e(url('/acceo/ceoreport')); ?>" class="dropdown-item">Permission Raw Report</a></li>
       <li><a rel="" href="<?php echo e(url('/acceo/partywise')); ?>" class="dropdown-item">PartyWise Permission Report</a></li>
        <li><a rel="" href="<?php echo e(url('/acceo/permissiontype')); ?>" class="dropdown-item">PermissionWise Report</a></li>
       
        
              <!-- End waseem asgar -->
        
              <!--<li><a rel="" href="<?php echo e(url('/acceo/aclist')); ?>" class="dropdown-item">List Of acs With Candidate Details</a></li>
              <li><a rel="" href="<?php echo e(url('acceo/duplicate-symbol-view')); ?>" class="dropdown-item">Duplicate Symbols</a></li>
              <li><a rel="" href="<?php echo e(url('/acceo/duplicateparties')); ?>" class="dropdown-item"> Duplicate Parties  </a></li>
              
              
              <li><a rel="" href="<?php echo e(url('/acceo/candidate-symbol-no-200')); ?>" class="dropdown-item">List of Candidates with Symbol No 200</a></li>
      <li><a rel="" href="<?php echo e(url('/acceo/login-detail')); ?>" class="dropdown-item">CEO Officer Login Report</a></li>-->
    
          <!--PRADEEP REPORTS LINKS STARTS-->
              <li><a rel="" href="<?php echo e(url('/acceo/CountingStatus')); ?>" class="dropdown-item">Counting Status Report</a></li>
              <li><a rel="" href="<?php echo e(url('/acceo/CeoElectionSchedule')); ?>" class="dropdown-item">Election Schedule</a></li>
              <!--PRADEEP REPORTS LINKS ENDS-->

            </ul>
          </li>
     <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
                 <li><a rel="" href="<?php echo e(url('/acceo/CountingStatus')); ?>" class="dropdown-item">Counting Status Report</a></li>
                 <li><a rel="" href="<?php echo e(url('/acceo/schedule-report')); ?>" class="dropdown-item">Scheduled Rounds Report</a></li>
                 <li><a rel="" href="<?php echo e(url('/acceo/constituency-wise-report')); ?>" class="dropdown-item">AC Result Report</a></li>
                 <li><a rel="" href="<?php echo e(url('/eci/round-wise-report')); ?>" class="dropdown-item">Round  Wise Report</a></li>
                 <li><a rel="" href="<?php echo e(url('/acceo/candidate-wise-report')); ?>" class="dropdown-item">Candidate Wise Report</a></li>
                 <li><a rel="" href="<?php echo e(url('/acceo/form21-download')); ?>" class="dropdown-item"> <span>Download Form 21 C/D</span></a></li>
            </ul>
          </li>
      
      <li><a href="javascript:void(0)" >Index Card<span class="arrow-down"></span></a>
            <ul class="dropdown">
      
              <!--WASEEM LINKS STARTS-->
                  <li><a class="dropdown-item" href="<?php echo url('acceo/elector/edit'); ?>">Update Electors</a></li>
                  <li><a class="dropdown-item" href="<?php echo url('acceo/voters/edit'); ?>">Update Voters</a></li>
                  <li><a class="dropdown-item" href="<?php echo url('acceo/indexcard/finalize'); ?>">Finalize AC's</a></li>
                 <li><a class="dropdown-item" href="<?php echo url('acceo/index-card'); ?>">Index Card Report</a></li>
                 <li><a rel="" href="<?php echo e(url('/acceo/indexcard/IndexCardFinalize')); ?>" class="dropdown-item">Index Card Finalized Report</a></li>
                 <li><a class="dropdown-item" href="<?php echo url('/acceo/report/candidate'); ?>">List of nominated candidate</a></li>
                               <!--WASEEM LINKS ENDS-->
            </ul>
          </li>
      <!-- Expenditure Section Start -->
        <li class="inactive"><a href="<?php echo e(url('/acceo/statusExpdashboard')); ?>">Expenditure</a></li>
       <!-- Expenditure Section End -->
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
       <li><a rel="" href="<?php echo e(url('/acceo/officer-details')); ?>" class="dropdown-item"> <span>Update Officer Details</span></a></li>
            <li><a rel="" href="<?php echo e(url('/acceo/officer/reset-password')); ?>" class="dropdown-item"> Officer's Pin Reset</a></li>
    
           <li><a rel="" href="<?php echo e(url('/profile/password')); ?>" class="dropdown-item"> Change Password</a></li>
           <li><a rel="" href="<?php echo e(url('/profile/pin')); ?>" class="dropdown-item"> Change PIN</a></li>
            <li><a rel="" href="<?php echo e(url('/logout')); ?>" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li> 
        </ul>
         <?php endif; ?>
        <!-- End CEO AC Model-->
    <!--  CEO Agent  AC Model-->
         <?php if($user_data->role_id=='23'): ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/acceo/dashboard')); ?>">Home</a></li>

      <li><a href="<?php echo e(url('acceo/voting/list-schedule/state')); ?>">Poll Turn Out</a></li>

          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
           <ul class="dropdown">
            <li><a rel="" href="<?php echo e(url('/acceo/candidate-finalize')); ?>" class="dropdown-item"> <span>List of Nomination Finalize</span></a></li>
            
            </ul>
          </li>
            <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
             <ul class="dropdown">
      <li><a rel="" href="<?php echo e(url('/acceo/allmasters')); ?>" class="dropdown-item">Add/Update Master Data </a></li>-->
        <li><a rel="" href="<?php echo e(url('/acceo/offlinePermission')); ?>" class="dropdown-item"> Offline permission Module</a></li>
     <li><a rel="" href="<?php echo e(url('/acceo/allPermissionRequest')); ?>" class="dropdown-item"> Accept/Reject permission</a></li> 
                <li><a rel="" href="<?php echo e(url('/acceo/permissioncount')); ?>" class="dropdown-item"> Permission Report</a></li>
     <li><a rel="" href="<?php echo e(url('/acceo/agentCreation')); ?>" class="dropdown-item"> Create CEO-Agent</a></li>-->
              </ul>
              
         </li>
          
           <li><a href="javascript:void(0)" >Master Data<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="<?php echo e(url('/acceo/officer-details')); ?>" class="dropdown-item"> <span>Update Officer Details</span></a></li>
        <li><a rel="" href="<?php echo e(url('/acceo/electors-pollingstationlist')); ?>" class="dropdown-item"> <span>Electors & PS Info</span></a></li>
            <!--<li><a rel="" href="<?php echo e(url('/acceo/psinfo')); ?>" class="dropdown-item"> <span>PS Details & AMF</span></a></li>-->
            </ul> 
          </li>
       
          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
        <li><a rel="" href="<?php echo e(url('/acceo/nomination-report')); ?>" class="dropdown-item">Nomination Report</a></li>
        <!-- waseem asgar -->
              <li><a rel="" href="<?php echo e(url('/acceo/report/scrutiny')); ?>" class="dropdown-item">Scrutiny Report</a></li> 
              <li><a rel="" href="<?php echo e(url('/acceo/reportceo')); ?>" class="dropdown-item">DateWise Permission Report</a></li> 
        <li><a rel="" href="<?php echo e(url('/acceo/ceoreport')); ?>" class="dropdown-item">Permission Raw Report</a></li>
       <li><a rel="" href="<?php echo e(url('/acceo/partywise')); ?>" class="dropdown-item">PartyWise Permission Report</a></li>
        <li><a rel="" href="<?php echo e(url('/acceo/permissiontype')); ?>" class="dropdown-item">PermissionWise Report</a></li>
              <!-- End waseem asgar -->
        
              <!--<li><a rel="" href="<?php echo e(url('/acceo/aclist')); ?>" class="dropdown-item">List Of acs With Candidate Details</a></li>
              <li><a rel="" href="<?php echo e(url('acceo/duplicate-symbol-view')); ?>" class="dropdown-item">Duplicate Symbols</a></li>
              <li><a rel="" href="<?php echo e(url('/acceo/duplicateparties')); ?>" class="dropdown-item"> Duplicate Parties  </a></li>
              
              
              <li><a rel="" href="<?php echo e(url('/acceo/candidate-symbol-no-200')); ?>" class="dropdown-item">List of Candidates with Symbol No 200</a></li>
      <li><a rel="" href="<?php echo e(url('/acceo/login-detail')); ?>" class="dropdown-item">CEO Officer Login Report</a></li>-->
    
          <!--PRADEEP REPORTS LINKS STARTS-->
              <li><a rel="" href="<?php echo e(url('/acceo/CountingStatus')); ?>" class="dropdown-item">Counting Status Report</a></li>
              <li><a rel="" href="<?php echo e(url('/acceo/CeoElectionSchedule')); ?>" class="dropdown-item">Election Schedule</a></li>
              <!--PRADEEP REPORTS LINKS ENDS-->

            </ul>
          </li>
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <!--<li><a rel="" href="<?php echo e(url('/acceo/change-password')); ?>" class="dropdown-item"> Change Password</a></li>-->
            <li><a rel="" href="<?php echo e(url('/logout')); ?>" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
            </ul>*/ ?>
         <?php endif; ?>
        <!--  End CEO Agent  AC Model-->

        <!-- DEOAC Login Section-->
       <?php if($user_data->role_id=='5' || $user_data->role_id=='24'): ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/acdeo/dashboard')); ?>">Home</a></li>
      <!-- Expenditure Section Start -->
      <li class="inactive"><a href="<?php echo e(url('/acdeo/expdashboard')); ?>">Expenditure</a></li>
       <!-- Expenditure Section End -->
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
          
          </li>
            <li><a href="javascript:void(0)" >Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
              <?php if($user_data->st_code=='U01' || $user_data->st_code=='U02' || $user_data->st_code=='U03' || $user_data->st_code=='U04' || $user_data->st_code=='U05' || $user_data->st_code=='U06' || $user_data->st_code=='U07' || $user_data->st_code=='S16'): ?>
                <li><a rel="" href="<?php echo e(url('/acdeo/allmasters')); ?>" class="dropdown-item">Add/Update Master Data </a></li>
                <?php endif; ?>
        <li><a rel="" href="<?php echo e(url('/acdeo/offlinePermission')); ?>" class="dropdown-item"> Offline permission Module</a></li>
                <li><a rel="" href="<?php echo e(url('/acdeo/allPermissionRequest')); ?>" class="dropdown-item"> Accept/Reject permission</a></li>
                 <li><a rel="" href="<?php echo e(url('/acdeo/agentCreation')); ?>" class="dropdown-item"> Create DEO-Agent</a></li>
          </ul>

         </li>
           
          
        <li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
  <ul class="dropdown">
    <li><a rel="" href="<?php echo url('/acdeo/booth-app/voter-list') ?>" class="dropdown-item">Booth Slip</a></li>
    <li><a rel="" href="<?php echo  url('/acdeo/booth-app/dashboard') ?>" class="dropdown-item">Dashboard</a></li>
  </ul>
</li>


          <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <!--<li><a rel="" href="<?php echo e(url('/acdeo/datewisereport')); ?>" class="dropdown-item">Nomination Report</a></li>
               <li><a rel="" href="<?php echo e(url('/acdeo/permissioncount')); ?>" class="dropdown-item"> Permission Report</a></li>-->
          <li><a rel="" href="<?php echo e(url('/acdeo/reportdeo')); ?>" class="dropdown-item">DateWise Permission Report</a></li>
        <li><a rel="" href="<?php echo e(url('/acdeo/permissionraw')); ?>" class="dropdown-item">Permission Raw Report</a></li>
        <li><a rel="" href="<?php echo e(url('/acdeo/partywise')); ?>" class="dropdown-item">PartyWise Permission Report</a></li>
        <li><a rel="" href="<?php echo e(url('/acdeo/permissiontype')); ?>" class="dropdown-item">PermissionWise Report</a></li>
            </ul>
          </li>
      
        

      <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
       <li><a rel="" href="<?php echo e(url('/acdeo/schedule-report')); ?>" class="dropdown-item">Scheduled Rounds Report</a></li>
       <li><a rel="" href="<?php echo e(url('/acdeo/constituency-wise-report')); ?>" class="dropdown-item">AC Result Report</a></li>
           <li><a rel="" href="<?php echo e(url('/eci/round-wise-report')); ?>" class="dropdown-item">Round  Wise Report</a></li>
            <li><a rel="" href="<?php echo e(url('/acdeo/candidate-wise-report')); ?>" class="dropdown-item">Candidate Wise Report</a></li>
            </ul>
          </li>
         <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="<?php echo e(url('/profile/password')); ?>" class="dropdown-item"> Change Password</a></li>
           <li><a rel="" href="<?php echo e(url('/profile/pin')); ?>" class="dropdown-item"> Change PIN</a></li>
            <li><a rel="" href="<?php echo e(url('/logout')); ?>" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li> 
        </ul>
         <?php endif; ?>
        <!-- End DEO ac Model-->
    
    
    <!-- Index Card Eci Login Section-->
       <?php if($user_data->role_id=='27'): ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/eci-index/dashboard')); ?>">Home</a></li>
          <li><a href="#">Index Card<span class="arrow-down"></span></a>
      <ul class="dropdown">
      <li><a href="<?php echo e(url('eci-index/index-card')); ?>">Index Card Report</a></li>
      <li><a href="<?php echo e(url('eci-index/statistical-report-listing')); ?>">Statistical Reports</a></li>
      <li><a rel="" href="<?php echo e(url('/eci-index/indexcard/IndexCardFinalizeTotal')); ?>" class="dropdown-item">Index Card Finalize</a></li>
            </ul>
          </li>
         
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="<?php echo e(url('/profile/password')); ?>" class="dropdown-item"> Change Password</a></li>
           <li><a rel="" href="<?php echo e(url('/profile/pin')); ?>" class="dropdown-item"> Change PIN</a></li>
             <li><a rel="" href="<?php echo e(url('/logout')); ?>" class="dropdown-item"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
      
        </ul>
        <?php endif; ?>
    
    <!-- Index Card Eci Login Section-->
       <?php if($user_data->role_id=='28'): ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/eci-expenditure/dashboard')); ?>">Home</a></li>
          <li><a href="#">Expenditure<span class="arrow-down"></span></a>
      <ul class="dropdown">
      
            </ul>
          </li>
         
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="<?php echo e(url('/profile/password')); ?>" class="dropdown-item"> Change Password</a></li>
           <li><a rel="" href="<?php echo e(url('/profile/pin')); ?>" class="dropdown-item"> Change PIN</a></li>
             <li><a rel="" href="<?php echo e(url('/logout')); ?>" class="dropdown-item"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>  
      
        </ul>
        <?php endif; ?>

        <!-- ECIac Login Section-->
       <?php if($user_data->role_id=='7'): ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/eci/dashboard')); ?>">Home</a></li>
      
       <li><a href="javascript:void(0)" >Index Card<span class="arrow-down"></span></a>
            <ul class="dropdown">
                 <li><a class="dropdown-item" href="<?php echo url('eci/index-card'); ?>">Index Card Report</a></li>
                 <li><a rel="" href="<?php echo e(url('/eci/indexcard/IndexCardFinalizeTotal')); ?>" class="dropdown-item">Index Card Finalize</a></li>
                 <li><a class="dropdown-item" href="<?php echo url('eci/report/candidate'); ?>">List of nominated candidate</a></li>
                               <!--WASEEM LINKS ENDS-->
            </ul>
          </li>

<li><a href="javascript:void(0)">Voter Turn Out<span class="arrow-down"></span></a>
  <ul class="dropdown">
    <li><a href="<?php echo e(url('eci/voting/list-schedule')); ?>">Poll Turn Out</a></li>
    <li><a href="<?php echo e(url('eci/turnout/end-of-poll')); ?>">End Of Poll</a></li>
    <li><a href="<?php echo e(url('eci/turnout/EndOfPollFinalised')); ?>">End Of Poll Finalised</a></li>
  </ul>
</li>

<li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
  <ul class="dropdown">
    <li><a rel="" href="<?php echo url('/eci/booth-app/voter-list') ?>" class="dropdown-item">Booth Slip</a></li>
    <li><a rel="" href="<?php echo  url('/eci/booth-app/dashboard') ?>" class="dropdown-item">Dashboard</a></li>
  </ul>
</li>

      
    
      
      <?php if($user_data->id=='1'): ?>
                 
           <li><a href="javascript:void(0)" >Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
      <!--PRADEEP LINKS STARTS-->
             <li><a rel="" href="<?php echo e(url('/eci/EciActiveUsers')); ?>" class="dropdown-item">Active Users Report</a></li>
             <li><a rel="" href="<?php echo e(url('/eci/EciElectionSchedule')); ?>" class="dropdown-item">Election Schedule</a></li>
             <li><a rel="" href="<?php echo e(url('/eci/EciPartyData')); ?>" class="dropdown-item">Party Data Report</a></li>
             <li><a rel="" href="<?php echo e(url('/eci/EciSymbolData')); ?>" class="dropdown-item">Symbol Data Report</a></li>
             <li><a rel="" href="<?php echo e(url('/eci/EciPhaseInfoData')); ?>" class="dropdown-item">Valid Nomination Report</a></li>
             <li><a rel="" href="<?php echo e(url('/eci/EciNominationFinalized')); ?>" class="dropdown-item">ACs Finalized</a></li>
        <!-- waseem asgar -->
            <li><a rel="" href="<?php echo e(url('/eci/report/scrutiny/state')); ?>" class="dropdown-item">Scrutiny Report</a></li> 
       
 <!--PRADEEP LINKS ENDS-->
      
       
       <!--PRADEEP LINKS ENDS-->
       
       <li><a rel="" href="<?php echo e(url('/eci/report')); ?>" class="dropdown-item">DateWise Permission Report</a></li>
       <li><a rel="" href="<?php echo e(url('/eci/partywise')); ?>" class="dropdown-item">PartyWise Permission Report</a></li>
       <li><a rel="" href="<?php echo e(url('/eci/permissiontype')); ?>" class="dropdown-item">PermissionWise Report</a></li>
        
            </ul>
          </li>
      <?php endif; ?>
      
      <li><a href="javascript:void(0)" >Counting Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
      <li><a rel="" href="<?php echo e(url('/eci/EciCountingStatusReport')); ?>" class="dropdown-item">Counting Status Report</a></li>
      <li><a rel="" href="<?php echo e(url('/eci/schedule-report')); ?>" class="dropdown-item">Scheduled Rounds Report</a></li>
      <li><a rel="" href="<?php echo e(url('/eci/constituency-wise-report')); ?>" class="dropdown-item">AC Result Report</a></li>
          <li><a rel="" href="<?php echo e(url('/eci/round-wise-report')); ?>" class="dropdown-item">Round  Wise Report</a></li>
            <li><a rel="" href="<?php echo e(url('/eci/candidate-wise-report')); ?>" class="dropdown-item">Candidate Wise Report</a></li>
      <li><a rel="" href="<?php echo e(url('/eci/form21c-download')); ?>" class="dropdown-item">Download Form21 C/D</a></li>
      <!--<li><a rel="" href="<?php echo e(url('/eci/form21-report')); ?>" class="dropdown-item">Count Report Form 21 C/D</a></li> -->
            </ul>
          </li>
      <li><a href="javascript:void(0)" >Account<span class="arrow-down"></span></a>
           <ul class="dropdown">
           <li><a rel="" href="<?php echo e(url('/profile/password')); ?>" class="dropdown-item"> Change Password</a></li>
           <li><a rel="" href="<?php echo e(url('/profile/pin')); ?>" class="dropdown-item"> Change PIN</a></li>
            <li><a rel="" href="<?php echo e(url('/logout')); ?>" class="dropdown-item"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
           </ul>
         </li>
        </ul>
         <?php endif; ?>
        <!-- End ECI ac Model-->
    <!--Start ECI Expenditure AC Model-->
         <?php if($user_data->role_id=='28'): ?>
        <ul class="float-right">
          <li class="active"><a href="<?php echo e(url('/eci-expenditure/expdashboard')); ?>">Home</a></li>
          <li class=""><a href="<?php echo e(url('/eci-expenditure/mis-officer')); ?>">Expenditure</a></li>
          <li><a href="<?php echo e(url('/logout')); ?>" class="nav-link logout"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
        
        </ul>
         <?php endif; ?>
        <!-- End ECI Expenditure AC Model-->
    
      </div>
      </div>
      <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div>
    </nav>
   </header><?php /**PATH E:\xampp\htdocs\suvidha\resources\views/admin/includes/ac/adminheader.blade.php ENDPATH**/ ?>