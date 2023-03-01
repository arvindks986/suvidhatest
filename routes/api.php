<?php
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
	Route::post('userlogin', 'API\UsersController@login');
	Route::post('verifyotp', 'API\UsersController@verifyOtp');
    Route::post('nominationlisting', 'API\UsersController@nominationlisting');
	Route::post('nominationstatus', 'API\UsersController@nominationstatus');
	Route::post('permissionlistview', 'API\UsersController@permissionlistview');
	Route::post('permissionpreview', 'API\UsersController@permissionpreview');
	Route::post('logout','API\UsersController@logout');
	Route::group(['middleware' => 'auth:api'], function(){
		//Route::post('nominationstatus', 'API\UserController@nominationstatus');
	});
	 Route::group(['middleware' => ['Encrypt','XSS','change_to_current']], function(){
		Route::post('nodallogin', 'API\NodalLoginApi@login');
		Route::post('nodalverifyotp', 'API\NodalLoginApi@verifyOtp');
		Route::post('nodallogout', 'API\NodalLoginApi@logout');
		Route::post('permissionlist','API\NodalLoginApi@permissionlist');
		Route::post('permissionupdate','API\NodalLoginApi@permissionupdate');
		Route::post('notificationlist','API\NodalLoginApi@notificationlist');
		Route::post('clearnotificationlist','API\NodalLoginApi@clearnotificationlist');
		Route::get('nodalappversion','API\NodalLoginApi@appversion');
	});
	
	Route::post('officerlogin', 'API\OfficerController@authenticate');
	Route::post('offlogout', 'API\OfficerController@logout');
	Route::post('officelogout', 'API\OfficerController@officerlogout');

	
	########################New API Candidate##################################
	
		Route::post('getaclisting', 'API\CandidateController@getAcListing');
		Route::post('getcountingac', 'API\CandidateController@getCountingAc');
		Route::post('getstate', 'API\CandidateController@getStateByPhase');
		Route::get('getelectiontypedetails', 'API\CandidateController@getElectionTypeDetails');
		Route::get('getstatus', 'API\CandidateController@getStatus');
		Route::post('getcandidatelist', 'API\CandidateController@getCandidateList');
		Route::post('getcandidatedetails', 'API\CandidateController@getCandidateDetails');
		Route::post('getelectionschedul', 'API\CandidateController@getelectionschedul');
		Route::post('getSchedule', 'API\CandidateController@getSchedule');
	########################--New API For Voter Turnout by ChanderKant ##################################

########################--New API For Voter Turnout by ChanderKant ##################################

###### Loging/Logout [By MAYANK] Currenty not in use/bypassed
Route::post('officerlogin', 'API\OfficerController@authenticate');
Route::post('officelogout', 'API\OfficerController@logout');

###### PAGE CALLS ######
Route::post('Home_PT', 'API\VtController@HomePt'); /// For Home Page
Route::post('PCwise_PT', 'API\VtController@PcwisePt'); /// For Summary Report of All PC or PC of selected State
Route::post('DistrictWise_PT', 'API\VtController@DistwisePt'); /// For Summary Report of All PC or PC of selected State
//Route::post('Distwise_PT', 'API\VtController@DistwisePt'); /// For Summary Report of All District or District of selected State
Route::post('PC2ACwise_PT', 'API\VtController@PC2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
Route::post('DIST2ACwise_PT', 'API\VtController@Dist2AcwisePt'); /// For Summary Report of All AC or AC of selected PC
Route::post('AC_PT', 'API\VtController@AcPt'); /// For Current Poll turnout status of selected AC
Route::post('State_PhaseWise', 'API\VtController@PhaseWiseState'); /// For Poll turnout status of all States
Route::post('PC_PhaseWise', 'API\VtController@PhaseWisePC'); /// For Poll turnout status of all PC in state
Route::post('AC_PhaseWise', 'API\VtController@PhaseWiseAC'); /// For Poll turnout status of all AC in PC
Route::post('FinalHome_PhaseWise', 'API\VtController@FinalHome'); /// For Poll turnout status of Single AC

###### FILTERS CALLS
Route::post('ElectionType_PT', 'API\VtController@ElectionTypePt'); /// For List of available election types
Route::post('PhaseList_PT', 'API\VtController@PhaseListPt'); /// For List of phases in selected election type
Route::post('StateList_PT', 'API\VtController@StateListingPT'); /// For List of All Polling States in selected Phase and Election
Route::post('PcList_PT', 'API\VtController@PcListingPT'); /// For List of All Polling PC in selected State
Route::post('PCWiseAcList_PT', 'API\VtController@PC2AcListingPT'); /// For List of All Polling AC in selected PC
Route::post('DistWiseAcList_PT', 'API\VtController@Dist2AcListingPT'); /// For List of All Polling AC in selected District
Route::post('DistrictList_PT', 'API\VtController@DistListingPT'); /// For List of All Polling District in selected State
Route::post('GetPollDate', 'API\VtController@PollDate'); /// For List of All Polling District in selected State

###### Data Entry Call 
Route::post('Add_PT', 'API\VtController@AddPT'); /// For adding round wise data of Poll Turnout
Route::post('AC_PTAdmin', 'API\VtController@AcPtAdmin'); /// For Current Poll turnout status of selected AC for
Route::post('Phase_State', 'API\VtController@PhaseByState'); /// Phase by State
Route::post('ObserverList', 'API\VtController@ObList'); /// Get Observer List
Route::post('CountingRoundwiseAC', 'API\VtController@CountingACList'); /// Roundwise and PartyWise Counting in an AC
Route::post('PCwisePostal', 'API\VtController@postalPC'); /// Vote Count in a PC
Route::post('ACPCCounting', 'API\VtController@acInPcCounting'); /// List of AC in a PC
Route::post('PartyAC', 'API\VtController@partyInAc'); /// Party Details in an AC
Route::post('CountingPC', 'API\VtController@pcCounting'); /// Counting Data of a given PC
Route::post('CountingState', 'API\VtController@stateCounting'); /// Counting Data of a given State
Route::post('SpeedState', 'API\VtController@stateSpeed'); /// Counting Data of a given State
//Route::post('AES', 'API\VtController@aestest'); /// Counting Data of a given State
Route::post('LeadingPC', 'API\VtController@PCleading'); /// Winning Leading data and margin of a PC
###### Loging/Logout [Encrypted with BruteForce Protection by Chanderkant]######
Route::post('SecureLogin', 'API\OfficerController@loginSecure');
Route::post('SecureLogout', 'API\OfficerController@logoutSecure');
Route::post('CountingStatus', 'API\VtController@nationalStatus'); /// Nationwide Counting Status
Route::post('SendNote', 'API\VtController@notification'); /// Send Mobile Notifications

//getElectionByDate
Route::get('getElectionByDate', 'API\CommonApiController@getElectionByDate');