<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$databaseName = DB::connection()->getDatabaseName();
$counting_preparation = DB::table('setting')->select('*')->where('key', 'counting_preparation')->first();
$counting = DB::table('setting')->select('*')->where('key', 'counting')->first();


$is_counting_enable =  @$counting->value;
$is_prepration_enable = @$counting_preparation->value;
$st_code  = '';
$ac_no    = 0;
$dist_no  = 0;
$allowed_st_code  = [];
$allowed_acs      = [];
$allowed_dist_no  = [];
if (Auth::user()) {
  $st_code  = Auth::user()->st_code;
  $dist_no  = Auth::user()->dist_no;
  $ac_no    = Auth::user()->ac_no;
}
$setting = \App\models\Admin\SettingModel::get_setting_cache();
if (!empty($setting['booth_app'])) {
  foreach ($setting['booth_app'] as $iterate_booth_app) {
    if ($iterate_booth_app['states'] == $st_code) {
      $allowed_st_code[] = $st_code;
    }
    if ($iterate_booth_app['states'] == $st_code && $iterate_booth_app['districts'] == $dist_no) {
      $allowed_dist_no[] = $dist_no;
    }
    if ($iterate_booth_app['states'] == $st_code && $iterate_booth_app['districts'] == $dist_no && in_array($ac_no, $iterate_booth_app['acs'])) {
      $allowed_acs[] = $ac_no;
    }
  }
}
// Enable Booth App menu only for those ROs who is present in boothapp_enable_acs table 
if (Auth::user()->role_id == 19) {
  $booth_acs = DB::table('boothapp_enable_acs')->where('st_code', $st_code)->where('dist_no', $dist_no)->where('ac_no', $ac_no)->first();
  if ($booth_acs) {
    $allowed_st_code = [$st_code];
    $allowed_acs = [$ac_no];
    $allowed_dist_no = [$dist_no];
  }
} else if (Auth::user()->role_id == 4 || Auth::user()->role_id == 5) {
  $booth_acs = DB::table('boothapp_enable_acs')->where('st_code', $st_code)->first();
  if ($booth_acs) {
    $allowed_st_code = [$st_code];
    $allowed_acs = [$ac_no];
    $allowed_dist_no = [$dist_no];
  }
}

?>
<header class="header">
  <nav>
    <div class="nav-header">
      <div class="container-fluid d-flex flex-md-row align-items-md-center">
        <div class="float-left mr-auto"><a href="#" class="navbar-brand "><img style="max-width: 40px;" src="{{ asset('theme/img/logo/eci-logo.png') }}" alt="" />&nbsp;<span class="text" style="color:#fff;">Election Commission of India</span> </a></div>
        <!-- ROAC Login Section-->
        <div class="col-xs-1"><span class="text-white" style="font-size:30px;cursor:pointer" onclick="openNavR()"><small class="text-white" style="font-size: 18px; position: relative; top: -5px;">MENU
              &nbsp;&nbsp;</small>☰</span>
        </div>
        <div id="mySidenavR" class="sidenavR">
          <div class="Closedbtn">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNavR()">
              <span>Close &nbsp;</span>×</a>
          </div>
          @if($user_data->role_id=='18' )
          <?php $ro = candidate_finalizebyro(Auth::user()->st_code, Auth::user()->pc_no, 'PC'); ?>
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/ropc/dashboard')}}">Home</a></li>
           
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
            <ul class="dropdown">
              @if((!$ro) or $ro->finalized_ac=='0')
              <li><a rel="" href="{{url('/ropc/createnomination')}}"> <span>Nomination</span></a></li>
              <li><a rel="" href="{{url('/ropc/multiplenomination')}}"> <span>Multiple Nomination</span></a></li>
              <li><a rel="" href="{{url('/ropc/candidateaffidavit')}}"> <span>Upload Affidavit</span></a></li>
              <li><a rel="" href="{{url('/ropc/candidateiscriminal')}}">Update Criminal Antecedents </a></li>
              <li><a rel="" href="{{url('/ropc/counteraffidavit')}}"> <span>Upload Counter Affidavit</span></a></li>
              @endif
              <li><a rel="" href="{{url('/ropc/listnomination')}}"> <span>List of Applicants</span></a></li>
              @if((!$ro) or $ro->finalized_ac=='0')
              <li><a rel="" href="{{url('/ropc/scrutiny-candidates')}}"> <span>Scrutiny of Candidates</span></a></li>
              <li><a rel="" href="{{url('/ropc/withdrawn-candidates')}}"> <span>Withdrawl of Candidates</span></a></li>
              <li><a rel="" href="{{url('/ropc/accepted-candidate')}}"> Mark validly nominated candidates</a></li>
              <li><a rel="" href="{{url('/ropc/symbol-upload')}}">Assign Symbol </a></li>
              @endif
              <!-- <li><a rel="" href="{{url('/ropc/ac-wise-electors-details')}}"> AC Wise Electors Details</a></li> -->
              <li><a rel="" href="{{url('/ropc/contested-application')}}"> Contesting Candidates</a></li>
            </ul>
          </li>
          <li><a href="javascript:void(0)">Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/ropc/permission/allmasters')}}">Add/Update Master Data </a></li>
              <li><a rel="" href="{{url('/ropc/permission/offlinePermission')}}"> Offline permission Module</a></li>
              <li><a rel="" href="{{url('/ropc/permission/allPermissionRequest')}}"> Accept/Reject permission</a></li>
              <li><a rel="" href="{{url('/ropc/permission/agentCreation')}}"> Create Agent</a></li>
            </ul>
          </li>
          <li><a href="javascript:void(0)">Voter Turnout<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/ropc/ac-wise-electors-details')}}"> AC Wise Electors Details</a></li>
              <li><a rel="" href="{{url('/ropc/voting/estimated-turnout')}}">Estimated Voter Turnout</a></li>
              <li><a rel="" href="{{url('/ropc/voting/list-schedule')}}">End of Voter Turnout</a></li>
              <li><a href="{{url('ropc/RoPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
            </ul>
          </li>
          <!-- Counting  -->
          @if($is_counting_enable)
          <li><a href="javascript:void(0)">Counting<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li class="active"><a href="{{url('/ropc/counting-details')}}">1- AC Wise Details</a></li>
              <li><a rel="" href="{{url('/ropc/counting/postal-data-entry')}}">2- Postal Ballot Votes </a></li>
              @if($user_data->st_code=="S09" and ($user_data->pc_no=="1" || $user_data->pc_no=="2" | $user_data->pc_no=="3"))
              <li><a rel="" href="{{url('/ropc/counting/migrate-votes')}}">3- Migrant Votes </a></li>
              <li><a rel="" href="{{url('/ropc/counting/counting-results')}}">4- Results Declaration </a></li>
              @else
              <li><a rel="" href="{{url('/ropc/counting/counting-results')}}">3- Results Declaration </a></li>
              @endif
            </ul>
          </li>
          @endif
          <!-- End Counting  -->
          <li><a href="javascript:void(0)">Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/ropc/datewisereport')}}">Nomination Report</a></li>
              <li><a rel="" href="{{url('/ropc/report/scrutiny')}}">Scrutiny Report</a></li>
              <!--<li><a rel="" href="{{url('/ropc/form-3A-report')}}"  >Form 3A</a></li>  -->
              <li><a rel="" href="{{url('/ropc/form-4-report')}}">Form 4</a></li>
              <li><a rel="" href="{{url('/ropc/permission/permissioncount')}}"> Permission Report</a></li>
              <li><a rel="" href="{{url('/ropc/reportpc')}}">DateWise Permission Report</a></li>
              <li><a rel="" href="{{url('/ropc/permissionraw')}}">Permission Raw Report</a></li>
              <li><a rel="" href="{{url('/ropc/partywise')}}">PartyWise Permission Report</a></li>
              <li><a rel="" href="{{url('/ropc/permissiontype')}}">PermissionWise Report</a></li>
            </ul>
          </li>
          <!-- Counting Report  -->
          @if($is_prepration_enable)
          <li><a href="javascript:void(0)">Counting Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/ropc/counting/round-report')}}">Roundwise Report</a></li>
              <li><a rel="" href="{{url('/ropc/counting/listac')}}">List Of Finalized ACs </a></li>
              <li><a rel="" href="{{url('/ropc/schedule-report')}}"> Scheduled Rounds Report</a></li>
              <li><a rel="" href="{{url('/ropc/constituency-wise-report')}}">PC Result Report</a></li>
              <li><a rel="" href="{{url('/ropc/round-wise-report-pcwise')}}">PC Wise Round Report</a></li>
              <li><a rel="" href="{{url('/eci/round-wise-report')}}"> AC Wise Round Report</a></li>
              <li><a rel="" href="{{url('/ropc/candidate-wise-report')}}"> Candidate Wise Report</a></li>
              <li><a rel="" href="{{url('/ropc/form-21-report')}}"> Form 21 E Details</a></li>
              <li><a rel="" href="{{url('/ropc/form-21c-report')}}">Form 21 C/D Details</a></li>
              <li><a rel="" href="{{url('/ropc/form-21-report-upload')}}">Upload FORM 21 C/D</a></li>
            </ul>
          </li>
          @endif
          <!-- End Counting Report  -->
          <!-- Expenditure Header-->
          <li class="inactive"><a href="{{url('/ropc/statusExpdashboard')}}">Expenditure</a></li>
          <!-- Expenditure End Header-->
          <li><a href="javascript:void(0)">Index Card<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a href="{{url('ropc/elector/edit?')}}pc_no={{$user_data->pc_no}}&year=2019">Update Electors/Voters</a></li>
              <li><a href="{!! url('ropc/voters/edit?') !!}pc_no={{$user_data->pc_no}}&year=2019">Update Voters</a></li>
              <li><a href="{!! url('ropc/indexcard/finalize') !!}">Finalize Index Card</a></li>
              <li><a rel="" href="{{url('/ropc/report/candidate')}}">List of nominated candidate</a></li>
              <li><a href="{{ url('/ropc/indexcardpc') }}">Index Card Report</a></li>
              <li><a href="{{ url('/ropc/indexcardbriefed') }}">IndexCard Briefed Report</a></li>
              <!--<li><a href="{!! url('ropc/indexcard/upload-indexcard') !!}">Upload Index card</a></li>-->
              <!-- <li><a href="{{url('/ropc/indexcard/get-complains')}}">My Change Request</a></li>
                <li><a href="{{url('/ropc/indexcard/indexcardpc?complain=1')}}">Request for Change</a></li>-->
            </ul>
          </li>
          <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
              <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
              <li><a rel="" href="{{url('/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>
          </ul>
          @endif
          <!-- End RO PC Model-->
          <!-- Index Card Eci Login Section-->
          @if($user_data->role_id=='27')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/eci-index/dashboard')}}">Home</a></li>
            <li><a href="#">Index Card<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a href="{{ url('/eci-index/indexcardpc') }}">Index Card Report</a></li>
                <li><a href="{{ url('/eci-index/indexcardbriefed') }}">IndexCard Briefed Report</a></li>
                <li><a href="{{ url('/eci-index/statistical-report-listing') }}">Statistical Reports</a></li>
                <li><a href="{{url('eci-index/bye-election-verify-report')}}">Bye-Election Index Card Report</a></li>
                <li><a rel="" href="{{url('/eci-index/indexcardview/IndexCardFinalizeViewTotal')}}">Index Card Finalize Report</a></li>
                <li><a href="{!! url('/eci-index/indexcard/get-indexcard-eci') !!}">Uploaded Index Cards</a></li>
                <li><a rel="" href="{{url('/eci-index/indexcard/get-complains')}}">De-Finalize Constituency</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('/logout')}}"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul>
          @endif
          <!-- ECIPC Expenditure Login Section-->
          @if($user_data->role_id=='28')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/eci-expenditure/EciExpdashboard')}}">Home</a></li>
            <li class="inactive"><a href="{{url('/eci-expenditure/mis-officer')}}">Expenditure</a></li>
            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('/logout')}}"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul>
          @endif
          <!-- End ECI PC Expenditure Model-->

          <!-- ROACLogin Section===============================================================================-->
          @if($user_data->role_id=='19'|| $user_data->role_id=='17' || $user_data->role_id=='20' )
          <ul class="float-right mainmenu">
            <!--<li class="active"><a href="{{url('/aro/dashboard')}}">Home</a></li>-->
            <li class="active"><a href="{{url('/aro/permission/permissioncount')}}">Home</a></li>
            @if($user_data->role_id == '20' && Auth::user()->st_code=='S01')
            <li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/aro/booth-app-revamp/officer-list')}}">Assign Officer</a></li>
                <li><a rel="" href="{{url('aro/booth-app-revamp/electors-verification-by-ps')}}">Verify Electors</a></li>
                <li><a rel="" href="{{url('/aro/booth-app-revamp/dashboard')}}">Dashboard</a></li>
                <li><a rel="" href="{{url('/aro/booth-app-revamp/exempted-boothapp-pollingstation')}}">View Exempted Turnout Polling station </a></li>
                <li><a rel="" href="{{url('/aro/booth-app-revamp/view-exempted-pollingstation')}}"> Exempt Polling station Turnout</a></li>
                {{-- <li><a rel="" href="{{url('/aro/booth-app-revamp/get-form-17-a')}}">Forms</a>
            </li> --}}
            <li><a href="{{url('aro/booth-app-revamp/officer-assignment-report/ac/ps')}}">Officer Assignment Report</a></li>
            <li><a href="{{url('aro/booth-app-revamp/poll-turnout-report/state/ac')}}">Poll Turnout Report</a></li>
            <li><a href="{{url('aro/booth-app-revamp/poll-event-report')}}">Poll Event Report</a></li>
          </ul>
          </li>
          @endif

          <li><a href="javascript:void(0)">Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/aro/permission/allmasters')}}">Add/Update Master Data </a></li>
              <li><a rel="" href="{{url('/aro/permission/offlinePermission')}}"> Offline permission Module</a></li>
              <li><a rel="" href="{{url('/aro/permission/allPermissionRequest')}}"> Accept/Reject permission</a></li>
              <!-- <li><a rel="" href="{{url('/ropc/manualforward')}}"  >Manual Forward</a></li>-->
              <li><a rel="" href="{{url('/aro/permission/agentCreation')}}"> Create Agent</a></li>
            </ul>
          </li>
          <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/aro/voting/ElectorsDetails')}}">Electors Details</a></li>
            </ul>
          </li>
          <li><a href="javascript:void(0)">Poll Turnout<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/aro/voting/polling-station-electors-details')}}">Import Polling Station</a></li>
              <li><a rel="" href="{{url('/aro/voting/ElectorsDetails')}}">Electors Details</a></li>
              <li><a rel="" href="{{url('/aro/voting/estimate-turnout-entry')}}">Estimate Turnout Entry</a></li>
              <!-- <li><a rel="" href="{{url('/aro/voting/schedule-entry')}}"  >End of Poll Turnout </a></li> -->
              <li><a rel="" href="{{url('/aro/voting/PsWiseDetails')}}">PS Wise Voter Turn Out</a></li>
            </ul>
          </li>
          <!-- Counting -->
          @if($is_prepration_enable)
          <li><a href="javascript:void(0)">Counting<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/aro/counting/round-schedule')}}">1- Round Schedule </a>
              </li>
              @if($is_counting_enable)
              <li><a rel="" href="{{url('/aro/counting/counting-data-entry')}}">2- EVM Votes Data Entry </a></li>
              @endif
            </ul>
          </li>
          @endif

          @if($is_prepration_enable)
          <li><a href="javascript:void(0)">Counting Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/eci/round-wise-report')}}">AC Wise Round Report</a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End Counting -->

          <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
              <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
              <li><a rel="" href="{{url('/logout')}}"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>

          </ul>
          @endif


          @if($user_data->role_id=='21')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/aro/dashboard')}}">Home</a></li>
            <li><a rel="" href="{{url('/aro/permission/offlinePermission')}}"> Offline permission Module</a></li>

            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('/logout')}}"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>

          </ul>
          @endif
          <!-- End RO AC Model-->

          <!-- CEOPC Login Section-->
          @if($user_data->role_id=='4')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/pcceo/dashboard')}}">Home</a></li>
            <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/pcceo/candidate-finalize')}}"> <span>List of Nomination Finalize</span></a></li>

              </ul>
            </li>
            @if(in_array($st_code,$allowed_st_code))
            <li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('pcceo/booth-app-revamp/dashboard')}}">Dashboard</a></li>
                <li><a href="{{url('pcceo/booth-app-revamp/officer-assignment-report/ac')}}">Officer
                    Assignment Report</a></li>
                <li><a href="{{url('pcceo/booth-app-revamp/elector-verify-report')}}">Electors verification
                    report</a></li>
                <li><a href="{!! url('pcceo/booth-app-revamp/poll-material/ac') !!}">Poll Material
                    Report</a></li>
                <li><a href="{{url('pcceo/booth-app-revamp/poll-turnout-report')}}">Poll Turnout Report</a></li>
                <li><a href="{{url('pcceo/booth-app-revamp/poll-event-report')}}">Poll Event Report</a></li>
                <li><a href="{{url('pcceo/booth-app-revamp/exemted-ps-count-report')}}">Exempted PS Count Report</a>
                </li>
                <li><a href="{{url('pcceo/booth-app-revamp/poll-turnout-report-exempted')}}">Exempt Turnout Report</a></li>

              </ul>
            </li>
            @endif

            <li><a href="javascript:void(0)">Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/pcceo/allmasters')}}">Add/Update Master Data </a></li>
                <li><a rel="" href="{{url('/pcceo/offlinePermission')}}"> Offline permission Module</a></li>
                <li><a rel="" href="{{url('/pcceo/allPermissionRequest')}}"> Accept/Reject permission</a></li>
                <li><a rel="" href="{{url('/pcceo/permissioncount')}}"> Permission Report</a></li>
                <li><a rel="" href="{{url('/pcceo/agentCreation')}}"> Create CEO-Agent</a></li>

              </ul>

            </li>
            <li><a href="javascript:void(0)">Voter Turn Out<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a href="{{url('pcceo/PcCeoPSElectoralDefinalzied')}}">PS Electoral Definalized</a></li>
                <li><a href="{{url('pcceo/PcCeoEstimatePollTurnoutPc')}}">Estimated Poll Percentage</a></li>
                <li><a href="{{url('pcceo/PcCeoEstimatePollTurnoutAc')}}">AC Wise Report</a></li>
                <li><a href="{{url('pcceo/PcCeoMissedAc')}}">ACs Not Filled</a></li>
                <?php /*<li><a   href="{{url('pcceo/PcCeoCloseOfPoll')}}">Comparison Report</a></li>*/ ?>
                <li><a href="{{url('pcceo/PcCeoEndOfPoll')}}">End Of Poll</a></li>
                <!--<li><a   href="{{url('/pcceo/end-of-poll-finalize')}}"  >End of Poll Finalize</a></li>-->
                <li><a href="{{url('pcceo/CeoPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
                <li><a href="{{url('pcceo/PcCeoMissedModifyAc')}}">Enable Round For Missed/Modify Turnout</a></li>
              </ul>
            </li>
            @if($is_prepration_enable)
            <li><a href="javascript:void(0)">Counting Report<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/pcceo/CountingStatus')}}">Counting Status Report</a></li>
                <li><a rel="" href="{{url('/pcceo/schedule-report')}}"> <span>Scheduled Rounds Report</span></a></li>
                <li><a rel="" href="{{url('/pcceo/constituency-wise-report')}}">PC Result Report</a></li>
                <li><a rel="" href="{{url('/pcceo/round-wise-report-pcwise')}}">PC Wise Round Report</a></li>
                <li><a rel="" href="{{url('/eci/round-wise-report')}}">AC Wise Round Report</a></li>
                <li><a rel="" href="{{url('/pcceo/candidate-wise-report')}}">Candidate Wise Report</a></li>
                <li><a rel="" href="{{url('/pcceo/form21-download')}}"> <span>Download Form 21 C/D</span></a></li>
              </ul>
            </li>
            @endif


            <li><a href="javascript:void(0)">Index Card<span class="arrow-down"></span></a>
              <ul class="dropdown">

                <!--WASEEM LINKS STARTS-->
                <li><a href="{!! url('pcceo/elector/edit') !!}">Update Electors</a></li>
                <li><a href="{!! url('pcceo/voters/edit') !!}">Update Voters</a></li>
                <li><a href="{!! url('pcceo/indexcard/finalize') !!}">Finalize PCs</a></li>
                <li><a rel="" href="{{url('/pcceo/report/candidate')}}">List of nominated candidate</a></li>
                <li><a href="{{ url('/pcceo/indexcardpc') }}">Index Card Report</a></li>
                <li><a href="{{ url('/pcceo/indexcardbriefed') }}">IndexCard Briefed Report</a></li>
                <li><a rel="" href="{{url('/pcceo/indexcardview/IndexCardFinalizeView')}}">Index Card Finalized Report</a></li>
                <!--WASEEM LINKS ENDS-->
                <li><a href="{{url('/pcceo/indexcard/get-complains')}}">My Change Request</a></li>
                <li><a href="{{url('/pcceo/indexcard/indexcardpc?complain=1')}}">Request for Change</a></li>

                <li><a href="{!! url('pcceo/indexcard/upload-indexcard') !!}">Upload Index card</a></li>
                <li><a href="{!! url('pcceo/indexcard/get-uploaded-indexcard') !!}">List of uploaded Index cards</a></li>


              </ul>
            </li>





            <li><a href="javascript:void(0)">Report<span class="arrow-down"></span></a>
              <ul class="dropdown">

                <li><a rel="" href="{{url('/pcceo/pclist')}}">List Of PCs With Candidate Details</a></li>
                <li><a rel="" href="{{ url('pcceo/duplicate-symbol-view') }}">Duplicate Symbols</a></li>
                <li><a rel="" href="{{url('/pcceo/duplicateparties')}}"> Duplicate Parties </a></li>
                <li><a rel="" href="{{url('/pcceo/nomination-report')}}">Nomination Report</a></li>
                <!-- waseem asgar -->
                <li><a rel="" href="{{url('/pcceo/report/scrutiny')}}">Scrutiny Report</a></li>
                <!-- End waseem asgar -->
                <li><a rel="" href="{{url('/pcceo/candidate-symbol-no-200')}}">List of Candidates with Symbol No 200</a></li>
                <li><a rel="" href="{{url('/pcceo/login-detail')}}">CEO Officer Login Report</a></li>
                <!--<li><a rel="" href="{{url('/pcceo/CountingStatus')}}"  >Counting Status Report</a></li>-->
                <li><a rel="" href="{{url('/pcceo/CeoElectionSchedule')}}">Election Schedule</a></li>
                <li><a rel="" href="{{url('/pcceo/districtvalue')}}">DistrictWise Permission Report</a></li>
                <li><a rel="" href="{{url('/pcceo/reportceo')}}">DateWise Permission Report</a></li>
                <li><a rel="" href="{{url('/pcceo/rawreport')}}">Permission Raw Report</a></li>
                <li><a rel="" href="{{url('/pcceo/partywise')}}">PartyWise Permission Report</a></li>
                <li><a rel="" href="{{url('/pcceo/permissiontype')}}">PermissionWise Report</a></li>
                <!--<li><a rel="" href="{{url('/pcceo/pendingreport')}}"  >Pending Permission Report</a></li>-->
              </ul>
            </li>

            <!-- Expenditure CEO Header -->
            <li class="inactive"><a href="{{url('/pcceo/statusExpdashboard')}}">Expenditure</a></li>
            <!-- Expenditure CEO end Header -->
            <li><a href="javascript:void(0)">State-wise List<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/eci/ac_pc_list')}}">All AC's & PC's</a></li>
              </ul>
            </li>




            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/pcceo/officer-details')}}"> <span>Update Officer Details</span></a></li>
                <li><a rel="" href="{{url('/pcceo/officer/reset-password')}}"> Officer's Pin Reset</a></li>




                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>

          </ul>
          </li>

          </ul>
          @endif
          <!-- End CEO PC Model-->
          @if($user_data->role_id=='23')
          <li class="active"><a href="{{url('/pcceo/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/pcceo/offlinePermission')}}"> Offline permission Module</a></li>
            </ul>

          </li>
          <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
              <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
              <li><a rel="" href="{{url('/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>

          @endif

          <!-- DEOPC Login Section-->
          @if($user_data->role_id=='5')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/pcdeo/dashboard')}}">Home</a></li>
            <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
              <!--<ul class="dropdown">
              <li><a rel="" href="#"  >comming </a></li>
               <li><a rel="" href="#"  >Comming </a></li>
               </ul>-->
            </li>

            @if($user_data->role_id == '5' && in_array($st_code,$allowed_st_code) && in_array($dist_no,$allowed_dist_no))
            <li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('pcdeo/booth-app-revamp/dashboard')}}">Dashboard</a></li>
                <li><a href="{{url('pcdeo/booth-app-revamp/officer-assignment-report/ac/ps')}}">Officer
                    Assignment Report</a></li>
                <li><a href="{{url('pcdeo/booth-app-revamp/elector-verify-report/state/ac')}}">Electors verification
                    report</a></li>
                <li><a href="{{url('pcdeo/booth-app-revamp/poll-material/ac') }}">Poll Material
                    Report</a></li>
                <li><a href="{{url('pcdeo/booth-app-revamp/poll-turnout-report/state/ac')}}">Poll Turnout Report</a>
                </li>
                <li><a href="{{url('pcdeo/booth-app-revamp/poll-event-report')}}">Poll Event Report</a></li>
                <li><a href="{{url('pcdeo/booth-app-revamp/exemted-ps-count-report/state/ac')}}">Exempted PS Count
                    Report</a></li>
              </ul>
            </li>
            @endif

            <li><a href="javascript:void(0)">Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
                @if($user_data->st_code=='U01' || $user_data->st_code=='U02' || $user_data->st_code=='U03' || $user_data->st_code=='U04' || $user_data->st_code=='U05' || $user_data->st_code=='U06' || $user_data->st_code=='U07' || $user_data->st_code=='S16')
                <li><a rel="" href="{{url('/pcdeo/allmasters')}}">Add/Update Master Data </a></li>
                @endif
                <li><a rel="" href="{{url('/pcdeo/offlinePermission')}}"> Offline permission Module</a></li>
                <li><a rel="" href="{{url('/pcdeo/allPermissionRequest')}}"> Accept/Reject permission</a></li>
                <li><a rel="" href="{{url('/pcdeo/agentCreation')}}"> Create DEO-Agent</a></li>
              </ul>

            </li>
            <li><a href="javascript:void(0)">Report<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/pcdeo/datewisereport')}}">Nomination Report</a></li>
                <li><a rel="" href="{{url('/pcdeo/permissioncount')}}"> Permission Report</a></li>
                <li><a rel="" href="{{url('/pcdeo/reportdeo')}}">DateWise Permission Report</a></li>
                <li><a rel="" href="{{url('/pcdeo/permissionraw')}}">Permission Raw Report</a></li>
                <li><a rel="" href="{{url('/pcdeo/partywise')}}">PartyWise Permission Report</a></li>
                <li><a rel="" href="{{url('/pcdeo/permissiontype')}}">PermissionWise Report</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0)">Index Card<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a href="{{ url('/pcdeo/indexcardpc') }}">Index Card Report</a></li>
                <li><a href="{{ url('/pcdeo/indexcardbriefed') }}">IndexCard Briefed Report</a></li>
              </ul>
            </li>

            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('/logout')}}"><span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>

          </ul>
          @endif

          <!--RO PC AGENT URLS STARTS-->
          @if($user_data->role_id=='22')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/ropc/dashboard')}}">Home</a></li>
            <li><a href="javascript:void(0)">Permission<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/ropc/permission/offlinePermission')}}"> Offline permission Module</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
                <li><a rel="" href="{{url('/logout')}}"> <span class="d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>

          </ul>
          @endif
          <!--RO PC AGENT URLS ENDS-->
          <!--DEO PC AGENT URLS STARTS-->
          @if($user_data->role_id=='24')
          <li class="active"><a href="{{url('/pcdeo/dashboard')}}">Home</a></li>
          <li><a href="javascript:void(0)">Permission<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/pcdeo/offlinePermission')}}"> Offline permission Module</a></li>
            </ul>

          </li>

          <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('//profile/password')}}"> Change Password</a></li>
              <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
              <li><a rel="" href="{{url('/logout')}}"><span class="d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>

          @endif
          <!--DEO PC AGENT URLS ENDS-->
          <!-- End DEO PC Model-->




          <!-- eci subagent -->
          @if($user_data->role_id == '26')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/eci-agent/dashboard')}}">Home</a></li>

            <!-- waseem 2019-04-09 -->
            {{-- <li><a href="javascript:void(0)">Phase 1 Voter Turnout<span class="arrow-down"></span></a>
               <ul class="dropdown">
                 
                 <li><a href="{{url('eci-agent/report/voting/end-of-poll')}}?phase=1">End of Poll</a></li>
            <li><a href="{{url('eci-agent/report/voting/close-of-poll')}}?phase=1">End of Poll Comparision Report</a></li>
          </ul>
          </li> --}}




          <li><a href="javascript:void(0)">Voter Turnout<span class="arrow-down"></span></a>
            <ul class="dropdown">

              {{-- <li><a href="{{url('eci-agent/report/voting/close-of-poll')}}">End of Poll Comparision Report</a>
          </li> --}}
          <li><a href="{{url('eci-agent/report/voting/list-schedule')}}">Estimate Poll Percentage</a></li>
          <li><a href="{{url('eci-agent/report/voting/get_missed')}}">Estimate Poll Percentage not Filled by ACs</a></li>
          <li><a href="{{url('eci-agent/report/voting/end-of-poll')}}">End of Poll Detailed Turnout Report</a></li>
          {{-- <li><a href="{{url('eci-agent/report/voting/compare')}}">Comparision Report(Estimated Turnout)</a></li> --}}

          </ul>
          </li>

          <li><a href="javascript:void(0)">Counting Report<span class="arrow-down"></span></a>
            <ul class="dropdown">

              <?php if (Auth::user() && Auth::id() != '7000') { ?>
                <li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}">Counting Status Report</a></li>
                <li><a href="{{url('eci/schedule-report')}}">Scheduled Rounds Report</a></li>
                <li><a rel="" href="{{url('/eci/constituency-wise-report')}}">PC Result Report</a></li>
                <li><a rel="" href="{{url('/eci/round-wise-report-pcwise')}}">PC Wise Round Report</a></li>
                <li><a rel="" href="{{url('/eci/round-wise-report')}}">AC Wise Round Report</a></li>
                <!-- LINKS Start-->
                <li><a rel="" href="{{url('/eci/candidate-wise-report')}}">Candidate Wise Report</a></li>
              <?php } ?>

              <li><a rel="" href="{{url('/eci/form21c-download')}}">Download Form 21 C/D</a></li>
              <li><a rel="" href="{{url('/eci/form21-report')}}">Count Report Form 21 C/D</a></li>

              <li><a rel="" href="{{url('/eci/winning-candidate-list')}}">Elected Members Report</a></li>
              <li><a rel="" href="{{url('/eci/result-report')}}">Result Report</a></li>


              <!-- LINKS ENDS-->
            </ul>
          </li>


          <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
            </ul>
          </li>

          </ul>
          @endif

          @if($user_data->role_id=='7' && Auth::user()->officername == 'PLANDIV' )
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/eci/dashboard')}}">Home</a></li>
            <li><a href="javascript:void(0)">Voter Turnout<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a href="{{url('eci/report/voting/list-schedule')}}">Estimate Poll Percentage</a></li>
                <li><a href="{{url('eci/report/voting/get_missed')}}">Estimate Poll Percentage not Filled by ACs</a></li>
                <li><a href="{{url('eci/report/voting/end-of-poll')}}">End of Poll Detailed Voter turnout</a></li>
                <li><a href="{{url('eci/EciEndOfPollFinalised')}}">End Of Poll Turnout Finalised Report</a></li>
                <li><a href="{{url('eci/EciPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
                <li><a href="{{url('eci/EnableClosePollEntry?round=6')}}">Enable Close of poll Entry</a></li>
                <li><a href="{{url('eci/PcECIPSElectoralDefinalzied')}}">PS Electoral Definalized</a></li>
              </ul>
            </li>
            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul>
          @endif

          @if($user_data->role_id=='7' && Auth::user()->officername == 'ECIREPORTS' )
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/eci/dashboard')}}">Home</a></li>
            <li><a href="javascript:void(0)">Counting Report<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}">Counting Status Report</a></li>
                <li><a href="{{url('eci/schedule-report')}}">Scheduled Rounds Report</a></li>
                <li><a rel="" href="{{url('/eci/constituency-wise-report')}}">PC Result Report</a></li>
                <li><a rel="" href="{{url('/eci/round-wise-report-pcwise')}}">PC Wise Round Report</a></li>
                <li><a rel="" href="{{url('/eci/round-wise-report')}}">AC Wise Round Report</a></li>
                <!-- LINKS Start-->
                <li><a rel="" href="{{url('/eci/candidate-wise-report')}}">Candidate Wise Report</a></li>

                <li><a rel="" href="{{url('/eci/form21c-download')}}">Download Form 21 C/D</a></li>
                <li><a rel="" href="{{url('/eci/form21-report')}}">Count Report Form 21 C/D</a></li>

                <li><a rel="" href="{{url('/eci/winning-candidate-list')}}">Elected Members Report</a></li>
                <li><a rel="" href="{{url('/eci/result-report')}}">Result Report</a></li>

                <!-- LINKS ENDS-->
              </ul>
            </li>
            <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>
              </ul>
            </li>
          </ul>
          @endif
          <!-- ECIPC Login Section-->
          @if(($user_data->role_id=='7' || $user_data->role_id=='25') && Auth::user()->officername != 'PLANDIV' && Auth::user()->officername != 'ECIREPORTS')
          <ul class="float-right mainmenu">
            <li class="active"><a href="{{url('/eci/dashboard')}}">Home</a></li>

            @if($user_data->role_id == '7')



            <!-- <li class="active"><a href="{{url('/eci/dashboard')}}">1) Home</a></li> -->
            <li><a href="javascript:void(0)">Candidate<span class="arrow-down"></span></a>
              <ul>
                <!-- Online Nomination Count Report -look_eci -->

                <li><a rel="" href="{{url('/eci/report/scrutiny/state')}}">2.1) Scrutiny Report</a></li>
                <li><a rel="" href="{{url('/eci/EciPhaseInfoData')}}">2.2) Valid Nomination Report</a></li>
                <li><a rel="" href="{!! url('eci/list-of-nomination') !!}">2.3) Candidate Wise Nomination Report</a></li>
                <li><a rel="" href="{{url('eci/ca-candidate-list')}}">2.4) Candidates CA Report</a></li>
                <!-- End Online Nomination Count Report -->
                <!--PRADEEP LINKS STARTS-->
                <!--   <li><a rel="" href="{{url('/eci/EciPhaseInfoData')}}">2.6) Valid Nomination Report</a></li>
               
                <li><a rel="" href="{{url('/eci/EciNominationFinalized')}}">2.8) FORM 7A Finalized Report</a></li>
                <li><a rel="" href="{{url('eci/ca-candidate-list')}}">2.9) Candidates CA Report</a></li> -->

              </ul>
            </li>





            <li><a href="javascript:void(0)">Booth App<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/eci/booth-app-revamp/dashboard')}}">Dashboard</a></li>

                <li><a href="{{url('eci/booth-app-revamp/officer-assignment-report')}}">4.2) Officer Assignment Report</a></li>

                <li><a href="{{url('eci/booth-app-revamp/elector-verify-report')}}">4.3) Electors verification report</a></li>

                <li><a href="{{url('eci/booth-app-revamp/poll-turnout-report')}}">4.5) Poll Turnout Report</a></li>
                <li><a href="{{url('eci/booth-app-revamp/poll-event-report?phase_no=5')}}">4.6) Poll Event Report</a></li>

                <li><a href="{{url('eci/booth-app-revamp/exemted-ps-count-report')}}">4.7) Exempted PS Count Report</a>
                </li>
                <li><a href="{{url('eci/booth-app-revamp/poll-turnout-report-exempted')}}">Exempt Turnout Report</a></li>


              </ul>
            </li>
            @endif
            <li><a href="#">Index Card<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a rel="" href="{{url('/eci/report/candidate')}}">List of nominated candidate</a></li>
                <li><a href="{{ url('/eci/indexcardpc') }}">Index Card Report</a></li>
                <li><a href="{{ url('/eci/indexcardbriefed') }}">IndexCard Briefed Report</a></li>
                <li><a rel="" href="{{url('/eci/indexcardview/IndexCardFinalizeViewTotal')}}">Index Card Finalize Report</a></li>
                <li><a href="{!! url('eci/indexcard/get-indexcard-eci') !!}">Uploaded Index cards</a></li>
                <li><a rel="" href="{{url('/eci/indexcard/get-complains')}}">Index Card Update Request</a></li>
              </ul>
            </li>
            @if(Auth::user() && (Auth::user()->officername == 'ECIECI' || Auth::user()->officername == 'ECIECI2'))
            <li><a href="javascript:void(0)">Voter Turnout<span class="arrow-down"></span></a>
              <ul class="dropdown">
                <li><a href="{{url('eci/report/voting/list-schedule')}}">Estimate Poll Percentage</a></li>
                <li><a href="{{url('eci/report/voting/get_missed')}}">Estimate Poll Percentage not Filled by ACs</a></li>
                <li><a href="{{url('eci/report/voting/end-of-poll')}}">End of Poll Detailed Voter turnout</a></li>
                {{-- <li><a href="{{url('eci/report/voting/close-of-poll')}}">End of Poll Comparision Report</a>
            </li> --}}
            {{-- <li><a href="{{url('eci/report/voting/compare')}}">Comparision Report(Estimated Turnout)</a></li> --}}
            <li><a href="{{url('eci/EciEndOfPollFinalised')}}">End Of Poll Turnout Finalised Report</a></li>
            {{-- <li><a href="{{url('eci/report/voting/end-of-poll-summary')}}">End of Poll Consolidate Report</a></li> --}}
            <li><a href="{{url('eci/EciPsWiseDetails')}}">PS Wise Voter Turnout</a></li>
            @if(Auth::user()->officername == 'ECIECI2' || Auth::user()->officername == 'PLANDIV')
            <li><a href="{{url('eci/EnableClosePollEntry?round=6')}}">Enable Close of poll Entry</a></li>
            <li><a href="{{url('eci/PcECIPSElectoralDefinalzied')}}">PS Electoral Definalized</a></li>
            @endif
          </ul>
          </li>

          <li><a href="javascript:void(0)">Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <!--PRADEEP LINKS STARTS-->
              <li><a rel="" href="{{url('/eci/EciActiveUsers')}}">Active Users Report</a></li>
              <li><a rel="" href="{{url('/eci/EciElectionSchedule')}}">Election Schedule</a></li>
              <li><a rel="" href="{{url('/eci/EciPartyData')}}">Party Data Report</a></li>
              <li><a rel="" href="{{url('/eci/EciSymbolData')}}">Symbol Data Report</a></li>
              <li><a rel="" href="{{url('/eci/EciPhaseInfoData')}}">Valid Nomination Report</a></li>
              <!-- waseem asgar -->
              <li><a rel="" href="{{url('/eci/report/scrutiny/state')}}">Scrutiny Report</a></li>
              <!-- end waseem asgar -->
              <li><a rel="" href="{{url('/eci/EciNominationFinalized')}}">PCs Finalized</a></li>
              <!--PRADEEP LINKS ENDS-->
              <!--   <li><a rel="" href="{{url('/eci/report')}}">DateWise Permission Report</a></li>
              <li><a rel="" href="{{url('/eci/partywise')}}">PartyWise Permission Report</a></li>
              <li><a rel="" href="{{url('/eci/permissiontype')}}">PermissionWise Report</a></li> -->
              <!--<li><a rel="" href="#"  > Report 3 </a></li>-->
            </ul>
          </li>
          <li><a href="javascript:void(0)">Permission Report<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/eci/report')}}">DateWise Permission Report</a></li>
              <li><a rel="" href="{{url('/eci/partywise')}}">PartyWise Permission Report</a></li>
              <li><a rel="" href="{{url('/eci/permissiontype')}}">PermissionWise Report</a></li>

              <li><a rel="" href="{{url('/eci/districtreport')}}">District DateWise Permission
                  Report</a></li>
              <li><a rel="" href="{{url('/eci/permissionmasterreport')}}">State wise Permission Master Report</a></li>
              <li><a rel="" href="{{url('/eci/modewisepermissionreport')}}">Permission Modewise Report</a></li>


            </ul>
          </li>
          @endif

          <li><a href="javascript:void(0)">Counting Report<span class="arrow-down"></span></a>
            <ul class="dropdown">

              <?php if (Auth::user() && Auth::id() != '7000') { ?>
                <li><a rel="" href="{{url('/eci/EciCountingStatusReport')}}">Counting Status Report</a></li>
                <li><a href="{{url('eci/schedule-report')}}">Scheduled Rounds Report</a></li>
                <li><a rel="" href="{{url('/eci/constituency-wise-report')}}">PC Result Report</a></li>
                <li><a rel="" href="{{url('/eci/round-wise-report-pcwise')}}">PC Wise Round Report</a></li>
                <li><a rel="" href="{{url('/eci/round-wise-report')}}">AC Wise Round Report</a></li>
                <!-- LINKS Start-->
                <li><a rel="" href="{{url('/eci/candidate-wise-report')}}">Candidate Wise Report</a></li>
              <?php } ?>

              <li><a rel="" href="{{url('/eci/form21c-download')}}">Download Form 21 C/D</a></li>
              <li><a rel="" href="{{url('/eci/form21-report')}}">Count Report Form 21 C/D</a></li>

              <li><a rel="" href="{{url('/eci/winning-candidate-list')}}">Elected Members Report</a></li>
              <li><a rel="" href="{{url('/eci/result-report')}}">Result Report</a></li>

              <!-- LINKS ENDS-->
            </ul>
          </li>

          <li><a href="javascript:void(0)">State-wise List<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <li><a rel="" href="{{url('/eci/ac_pc_list')}}">All AC's & PC's</a></li>
            </ul>
          </li>

          <li><a href="javascript:void(0)">Account<span class="arrow-down"></span></a>
            <ul class="dropdown">
              <?php if (Auth::user() && Auth::id() == '1') { ?>
                <li><a rel="" href="{{url('/eci/setting/broadcast')}}">Broadcast Message to officers</a></li>
              <?php } ?>
              <?php if (Auth::user() && Auth::id() != '7000') { ?>
                <li><a rel="" href="{{url('/profile/password')}}"> Change Password</a></li>
                <li><a rel="" href="{{url('/profile/pin')}}"> Change PIN</a></li>
              <?php } ?>
              <li><a rel="" href="{{url('/logout')}}"> <span class="d-none d-sm-inline-block">Logout</span> <i class="fa fa-sign-out"></i></a></li>

            </ul>
          </li>

          </ul>
          @endif
          <!-- End ECI PC Model-->



          {{-- end of navbar items  --}}

        </div>

      </div>
    </div>
    <!--     <div class="nav-bg-header">
        <div class="navbar-header"> <span></span> <span></span> <span></span> </div>
        <a href="" class="title-mobile">Election Commission of India</a>
      </div> -->

  </nav>
</header>

<script src="{{ asset('admintheme/js/jquery.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('admintheme/js/jquery.slicknav.js') }}"></script>
<script type="text/javascript" src="{{ asset('admintheme/js/slider-menu.jquery.js') }}"></script>

<script>
  $(function() {
    $('.mainmenu').slicknav();
  });
</script>
<script>
  (function($) {
    $(function() {
      $('.mainmenu').sliderMenu();
    });
  })(jQuery);
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

<?php
$setting = \App\models\Admin\SettingModel::get_first_result('config');
if ($setting) {
?>

  <div class="alert-warning text-center">
    <marquee>
      {{$setting['value']}}
    </marquee>
  </div>

<?php }
