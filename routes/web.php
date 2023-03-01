<?php  
//    created by Sachchidanand    this mid not maintain your file  thanks refreshCaptcha  
Route::get('/clear-cache', function() {
   Artisan::call('cache:clear');
   Artisan::call('view:clear');
   Artisan::call('config:cache');
   return "Cache is cleared";
});

//sachchidanand   turnout  out of login
Route::get('/change-database', function(){
    return redirect("officer-login");
});
include_once('onlinepcnomination.php');

Route::post('/change-database', 'Admin\AdminController@change_database'); 
Route::get('/publish-vtr', 'Admin\PublishController@update_turnout_index');	
Route::get('/publish-vtr-show', 'Admin\PublishController@show_turnout_index');
Route::get('/test/publish-vtr', 'Admin\PublishTestController@update_turnout_index');	
Route::get('/test/publish-vtr-show', 'Admin\PublishTestController@show_turnout_index');
Route::get('/turnout/voter-turnout-details', 'turnout\VoterturnoutController@index'); 
Route::get('/turnout/state-wise-details','turnout\VoterturnoutController@report_pc');
Route::get('/gen-election-test-login', 'Admin\AdminController@boothapp_login');
//end turnout


Route::get('/clear-sleep', 'Admin\Common\CommonController@index');
Route::get('/profile/pin', 'Admin\Profile\TwoStepPinController@index');
Route::post('/profile/pin/update_via_web', 'Admin\Profile\TwoStepPinController@update_via_web');
Route::post('/profile/pin/update', 'Admin\Profile\TwoStepPinController@update');

Route::get('/profile/password', 'Admin\Profile\PasswordController@index');
Route::post('/profile/password/update', 'Admin\Profile\PasswordController@update');

Route::post('/profile/logout', 'Admin\Profile\CustomAuthController@logout');
Route::post('/auth/login/step1', 'Admin\AdminController@ajax_login_step1');
Route::post('/auth/login/step2', 'Admin\AdminController@ajax_login_step2');

//two step web login
Route::post('/auth/login/two_step_login1', 'Admin\AdminController@two_step_login1');
Route::get('/auth/login/verify/pin', 'Admin\AdminController@two_step_pin');
Route::post('/auth/login/two_step_login2', 'Admin\AdminController@two_step_login2');

Route::post('/profile/password/validate', 'Admin\Profile\PasswordController@update_by_ajax');
Route::post('/profile/pin/validate', 'Admin\Profile\PasswordController@validate_pin');




//NOTIFICATION FOR COUNTING
Route::get('/notification', 'Admin\Notification\CountingNotificationController@notification'); 
Route::post('/notificationCurl', 'Admin\Notification\CountingNotificationController@notificationCurl'); 

//PC LIST
Route::get('/get_pc_list', 'Admin\PollingStation\EciPollingStationController@get_pc_list');
//ASSEMBLY LIST
Route::get('/get_ac_list_by_st_pc/{state}/{pc_id}', 'Admin\PollingStation\EciPollingStationController@get_ac_list_by_st_pc');

   
Route::get('/officer-login', 'Admin\AdminController@index');
	
//forgot password
Route::get('/forgot', 'Admin\AdminController@get_forgot');
Route::post('/forgot/post', 'Admin\AdminController@post_forgot');
Route::get('/forgot/new/{id}', 'Admin\AdminController@enter_new_password');
Route::get('/forgot/otp', 'Admin\AdminController@get_otp');
Route::post('/forgot/otp/verifying', 'Admin\AdminController@verify_otp');
Route::post('/forgot/resend', 'Admin\AdminController@resend_otp');
Route::post('/forgot/post-new', 'Admin\AdminController@update_forgot');
	
Route::get('/', 'HomeController@index');
Route::POST('/admin-postlogin', 'Admin\AdminController@postlogin');
Route::GET('/admin-postlogin', 'Admin\AdminController@postlogin');
Route::GET('/refresh_captcha', 'Admin\HomeController1@refreshCaptcha');
Route::get('/otpverification', 'Admin\AdminController@otpverification');
Route::POST('/verifyloginotp', 'Admin\AdminController@verifyloginotp');
Route::POST('/resendotp', 'Admin\AdminController@resendotp');
Route::get('/adminhome', 'Admin\HomeController1@index');
Route::get('logout','Admin\HomeController1@logout');
Route::post('/postlogin', 'Admin\HomeController1@postlogin');
Auth::routes();
Route::get('pdfview',array('as'=>'pdfview','uses'=>'Admin\RoPCController@pdfview'));
Route::get('/updateprofile', 'Admin\HomeController1@updateprofile');
Route::get('/updateprofile/{uid}','UservarificationController@index');
Route::get('/otpvarify/{uid}/{otp}','UservarificationController@otpvarify');
Route::get('/updateuserpass/{userid}/{password}','UservarificationController@updateuserpass');
Route::get('/resendotp/{uid}','UservarificationController@resendotp');

// ROUTES FOR CANDIDATE LOGIN STARTS
Route::get('/get_captcha/{config?}', function (\Mews\Captcha\Captcha $captcha, $config = 'default') {
  return $captcha->src($config);
})->middleware('clean_url');
//CANDIDATE LOGIN ROUTS STARTS
Route::POST('/user-postlogin', 'UserController@postlogin');
//otp page show
Route::get('/mobileotp/{mobile}', 'UserController@mobileotp')->name('otp');
//CUSTOM LOGIN
Route::post('/customlogin', 'UserController@customlogin');
//Resend Mobile otp 
Route::post('resendotp', 'UserController@resendotp');
//home
Route::get('/home', 'HomeController@userhome');
Route::get('/candidate-roletype', 'TempHomeController@roletype');
Route::get('/apply-nomination-step-1','Nomination\NominationController@apply_nomination_step_1');
//CANDIDATE LOGIN ROUTS ENDS

// ROUTES FOR CANDIDATE LOGIN ENDS 

/* RO PC Section   ============================================================= */
Route::group(['middleware' => 'adminsession'], function () {   // check session here  usersession
  /* RO PC Section   ============================================================= */
  Route::group(['prefix' => 'ropc', 'as' => 'ropc::', 'middleware' => ['auth:admin', 'auth','ro_only']], function(){
	  Route::get('/report/candidate', 'Admin\Eci\Report\CandidateController@get_candidates');
  	Route::group(['prefix' => 'indexcard', 'as' => 'pcceo::', 'middleware' => ['ro_only']], function(){
	  	Route::get('/finalize','Admin\Ceo\ElectorVoterController@finalize');
		  Route::get('/finalize/post',function(){
		    return redirect('pcceo/indexcard/finalize');
		  });
		  Route::post('/finalize/post','Admin\Ceo\ElectorVoterController@post_finalize');
      Route::get('indexcardpc', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcarddata');
      Route::get('upload-indexcard', 'Admin\Indexcard\IndexcardController@upload_indexcard_request');
      Route::post('upload-indexcard/post', 'Admin\Indexcard\IndexcardController@post_upload_indexcard_file');
		  //complain waseem add ropc
			Route::post('post-complain-indexcard','Admin\Indexcard\ComplainController@post_complain_indexcard');
			Route::get('post-complain-indexcard',function(){
				return redirect('/ropc/indexcard/get-complains');
			});
			Route::get('get-complains','Admin\Indexcard\ComplainController@get_complains_list');
  	});
	  //waseem 2019-04-12
    Route::group(['prefix' => 'elector', 'as' => 'ropc::', 'middleware' => ['ro_only']], function(){
      Route::get('/edit','Admin\Ceo\ElectorVoterController@edit_elector_form');
      Route::get('/post',function(){
        return redirect('pcceo/elector/edit');
      });
      Route::post('/post','Admin\Ceo\ElectorVoterController@post_elector_form');
    });

    Route::group(['prefix' => 'voters', 'as' => 'ropc::', 'middleware' => ['ro_only']], function(){
      Route::get('/edit','Admin\Ceo\ElectorVoterController@edit_voters_form');
      Route::get('/post',function(){
        return redirect('pcceo/voters/edit');
      });
      Route::post('/post','Admin\Ceo\ElectorVoterController@post_voters_form');
    });

    //ALAM - ALL PC Wise Report
    Route::get('/constituency-wise-report','Admin\ConstituencyWiseReport\ConstituencyWiseReportController@index'); 
    Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedPcByStateId');  
    Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedAc');  
    Route::post('/get-condidate-details-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCondidfateListpkpk');
    Route::post('/get-all-result-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCompleteResult');
    Route::post('/csvDownload-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@csvDownload');
    //
    
    //ALAM - ALL PC Wise Report
    Route::get('/round-wise-report-pcwise','Admin\PcWiseRoundReport\PcWiseRoundReportController@index'); 
    Route::post('/get-pc-by-state-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedPcByStateId');	
    Route::post('/get-ac-by-state-and-pc-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedAc');	
    Route::post('/get-condidate-details-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCondidfateListpkpk');
    Route::post('/get-all-result-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCompleteResult');
    Route::post('/csvDownload-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@csvDownload');
    //

    //Sanjay Routes Start
    Route::get('/candidate-wise-report', 'Report\VoterTypeWiseReportController@reportIndex');
    Route::get('/candidate-wise-report-getpc-state/{state}', 'Report\VoterTypeWiseReportController@getPcByState');
    Route::get('/candidate-wise-report-get-party/{pc}/{state}', 'Report\VoterTypeWiseReportController@getPartyByPc');
    Route::post('/candidate-wise-report-search', 'Report\VoterTypeWiseReportController@getReport');
    Route::post('/candidate-wise-report-excel', 'Report\VoterTypeWiseReportController@getReportExcel');
    Route::post('/candidate-wise-report-pdf', 'Report\VoterTypeWiseReportController@getReportPdf');
    Route::get('/form-21-report', 'Report\VoterTypeWiseReportController@getForm21');
    Route::post('/form-21-report-view', 'Report\VoterTypeWiseReportController@getFormView');
    Route::get('/form-21-report-pdf/{statevalue?}/{pcvalue?}', 'Report\VoterTypeWiseReportController@getForm21Pdf');
    Route::post('/form-21-report-excel', 'Report\VoterTypeWiseReportController@getForm21Excel');
    //Form 21C Start 
    Route::get('/form-21c-report', 'Report\Form21CReportController@getForm21C');
    Route::get('/form-21c-report-pdf', 'Report\Form21CReportController@getForm21CPdf');
    //Form 21 Upload Start 
    Route::get('/form-21-report-upload', 'Report\Form21CReportController@getForm21CUpload');
    Route::post('/form-21-report-upload', 'Report\Form21CReportController@storeFile');
    
    //Sanjay Routes Ends
    
    //SCHEDULE REPORT START- GUNAJIT
    Route::get('/schedule-report','Admin\CountingReport\ROScheduleReportController@scheduleReport');
    Route::post('/schedule-report','Admin\CountingReport\ROScheduleReportController@scheduleReport');
    Route::get('/schedule-report-pdf/{ac_id}','Admin\CountingReport\ROScheduleReportController@scheduleReportPDF');
    Route::get('/schedule-report-excel/{ac_id}','Admin\CountingReport\ROScheduleReportController@scheduleReportExcel');	
    //SCHEDULE REPORT END- GUNAJIT
    
    //ROPC PC POLLING STATION STARTS
    Route::get('/RoPsWiseDetails/', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetails');
    Route::get('/RoPsWiseDetails/excel', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetailsExcel');
    Route::get('/RoPsWiseDetails/pdf', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetailsPdf');
    Route::post('/RoPsWiseDetailsUpdate/', 'Admin\PollingStation\RoPollingStationController@RoPsWiseDetailsUpdate');
    Route::post('/RoPCPsDefinalizeUpdate', 'Admin\PollingStation\RoPollingStationController@RoPCPsDefinalizeUpdate');
    Route::post('/RoPCFinalizeUpdate', 'Admin\PollingStation\RoPollingStationController@RoPCFinalizeUpdate');
    //ROPC PC POLLING STATION ENDS
    
    Route::get('/dashboard', 'Admin\RoPCController@index');  
    Route::get('/appointment_request', 'Admin\CandNomination\PreScrutiny\PreScrutinyController@appointment_request');
    Route::get('/', 'Admin\RoPCController@index');
    // Route::get('/dashboard', 'Admin\PCCountingController@counting_dashboard'); 
    //Route::get('/', 'Admin\PCCountingController@counting_dashboard');
    Route::get('/candidateiscriminal/{nomid?}', 'Admin\RoPCnomController@candidateiscriminal');
    Route::post('/uploadiscriminal', 'Admin\RoPCnomController@uploadiscriminal');
    Route::get('/createnomination', 'Admin\RoPCnomController@createnomination');
    Route::post('/createnomination', 'Admin\RoPCnomController@insertnomination');
    Route::get('/multiplenomination', 'Admin\RoPCnomController@multiplenomination');
    Route::post('/newmultiplenomination', 'Admin\RoPCnomController@insertmultiplenomination');
    Route::get('/getSymbol', 'Admin\RoPCnomController@getSymbol');
    Route::get('/getDistricts', 'Admin\RoPCnomController@getDistricts');
    Route::get('/getcandidateexit', 'Admin\RoPCnomController@getcandidateexit');
    Route::get('/getallac', 'Admin\RoPCnomController@getaclist'); 
    Route::get('/listnomination', 'Admin\RoPCController@listnomination');   
    Route::get('/candidateaffidavit/{lid?}', 'Admin\RoPCnomController@candidateaffidavit');
    Route::post('/verifycandidateaffidavit', 'Admin\RoPCnomController@candstoreaffidavit')->name('ro.candstoreaffidavit');
    Route::get('/counteraffidavit', 'Admin\RoPCnomController@countercandaffidavit');
    Route::post('/counteraffidavit', 'Admin\RoPCnomController@storecountercandaffidavit')->name('ro.storecounteraffidavit'); 
    Route::get('/counteraffidavitdetails/{nomid?}', 'Admin\RoPCnomController@counteraffidavitdetails');
    Route::get('/updatenomination/{nomid?}', 'Admin\RoPCnomController@updatenominationform');
    Route::POST('/newupdatenomination/{nomid?}', 'Admin\RoPCnomController@updatenomination'); 
    Route::get('/viewnomination/{nomid?}', 'Admin\RoPCController@viewnomination');
    Route::POST('/duplicate-drop', 'Admin\RoPCController@duplicate_drop');
    Route::get('/scrutiny-candidates', 'Admin\RoPCController@listallcandidate'); 
    Route::get('/withdrawn-candidates', 'Admin\RoPCController@withdrawn_candidates'); 
    Route::get('/marks-vip/{id}/{val}', 'Admin\RoPCController@marksvip');
    Route::get('/change-status/{id}/{val}', 'Admin\RoPCController@change_status');
    Route::POST('/statusvalidation', 'Admin\RoPCController@statusvalidation');
    Route::POST('/statusvalidation_reject', 'Admin\RoPCController@statusvalidation_reject');
    Route::POST('/withstatusvalidation', 'Admin\RoPCController@withstatusvalidation');
    Route::get('/contested-application', 'Admin\RoPCController@accepted_application');
    Route::POST('/change-sequence', 'Admin\RoPCController@change_sequence');   
    Route::get('/symbol-upload', 'Admin\RoPCController@symbol_upload'); 
    Route::get('/assign-symbol/{nom_id}', 'Admin\RoPCController@assign_symbol'); 
    Route::POST('/updatesymbol', 'Admin\RoPCController@updatesymbol');  //
    Route::POST('/finalize-candidate', 'Admin\RoPCController@finalize_candidate');
    Route::get('/finalize-ac', 'Admin\RoPCController@finalize_ac');
    Route::get('/public-affidavit', 'Admin\RoPCController@public_affidavit');
    Route::get('/ballotpaperpdfview', 'Admin\RoPCController@ballotpaperpdfview');
    Route::get('/accepted-candidate', 'Admin\RoPCController@accepted_candidate');
    Route::POST('/finalaccepted', 'Admin\RoPCController@finalaccepted');
    Route::get('/ac-wise-electors-details', 'Admin\RoPCController@ac_wise_electors_details');
    Route::POST('/verifyac-wise-electors-details', 'Admin\RoPCController@verifyac_wise_electors_details');
    // Counting Section   counting-data-entry  finalize-ac 
    // Counting Section   counting-data-entry  finalize-ac  
    Route::get('/counting-details','Admin\PCCountingController@counting_details');
    Route::get('/counting-details/{$dis_ac}','Admin\PCCountingController@counting_details');
    Route::POST('/ac-wise-counting','Admin\PCCountingController@ac_wise_counting');
    // sachchida report section
    Route::get('/form-3A-report', 'Admin\nomination\FormreportController@form_3a_report'); 
    Route::Post('/form-3A-report', 'Admin\nomination\FormreportController@form_3a_report');
    Route::get('/download-form-3A-report/{date}', 'Admin\nomination\FormreportController@download_form_3a_report');

    Route::get('/form-4-report', 'Admin\nomination\FormreportController@form_4_report');
    Route::get('/download-form-4-report', 'Admin\nomination\FormreportController@download_form_4_report');

    //end of Sachchidanand  ropc/download-form-3A-report/2019-07-16
    //waseem 2019-04-30
    Route::get('/counting-details/edit/{ac_no}/{round}','Admin\PCCountingController@counting_details_edit');
    Route::post('/counting-details/update','Admin\PCCountingController@update_round_by_ro');
      
    Route::group(['prefix' => 'counting', 'as' => 'counting::', 'middleware' => ['auth:admin', 'auth']], function(){ 

    //2019-05-07
    Route::post('/verify_winner_by_name','Admin\PCCountingController@verify_winner_by_name');
    Route::post('/result_declared_by_lottery','Admin\PCCountingController@result_declared_by_lottery');
    
    Route::get('/round-report','Admin\PCCountingController@get_round_report'); 
    Route::get('/round-report/pdf','Admin\PCCountingController@export_round_report');



    Route::get('/prepare-counting','Admin\PCCountingController@prepear_counting');        
    Route::get('/listac', 'Admin\PCCountingController@listac');
    Route::get('/activate_allac', 'Admin\PCCountingController@activate_allac');

    Route::get('/postal-data-entry', 'Admin\PCCountingController@postal_data_entry');
    Route::POST('/verifypostalentry', 'Admin\PCCountingController@verifypostalentry');
    Route::get('/counting-results', 'Admin\PCCountingController@counting_results');
    Route::get('/counting-finalized', 'Admin\PCCountingController@counting_finalized');
    Route::get('/results-declaration', 'Admin\PCCountingController@results_declaration');
    Route::post('/results-declaration', 'Admin\PCCountingController@results_declaration');
    Route::get('/results-verified', 'Admin\PCCountingController@results_verified');
    Route::POST('/counting-finalized-verify', 'Admin\PCCountingController@counting_finalized_verify');
    Route::POST('/tenders-votes', 'Admin\PCCountingController@tenders_votes');
    Route::get('/migrate-votes', 'Admin\PCCountingController@migrate_votes');
    Route::POST('/verify-migrate-votes', 'Admin\PCCountingController@verify_migrate_votes');

    Route::GET('/migrant_pdf', 'Admin\PCCountingController@migrant_pdf');
    Route::POST('/migrant_pdf', 'Admin\PCCountingController@migrant_pdf');
    Route::GET('/pdf', 'Admin\PCCountingController@pdf');
    Route::GET('/ballot_pdf', 'Admin\PCCountingController@ballot_pdf');
    Route::POST('/pdf', 'Admin\PCCountingController@pdf');
    Route::POST('/ballot_pdf', 'Admin\PCCountingController@ballot_pdf');
  });

  Route::group(['prefix' => 'voting', 'as' => 'voting::', 'middleware' => ['auth:admin', 'auth']], function(){ 
    Route::get('/create-schedule','Admin\PollDayController@index'); 
    Route::POST('/verify-schedule','Admin\PollDayController@veryfy_schedule');       
    Route::get('/list-schedule','Admin\PollDayController@list_schedule'); 
    Route::get('/estimated-turnout','Admin\PollDayController@estimated_turnout');  
    Route::POST('/estimated-turnout-change','Admin\PollDayController@estimated_turnout_change');
    Route::POST('/end-of-poll-change','Admin\PollDayController@end_of_poll_change');
  });

  // permission Routes strat
  Route::group(['prefix' => 'permission', 'as' => 'permission::', 'middleware' => ['auth:admin', 'auth']], function(){  
    Route::get('/allmasters', 'Admin\ROPCPermissionController@allMasters');
    Route::get('/offlinePermission','Admin\ROPCPermissionController@OfflinePermission');
    Route::post('/offlinePermission','Admin\ROPCPermissionController@OfflinePermission');
    Route::post('/getPS','Admin\ROPCPermissionController@getPS');
    Route::post('/getlocation','Admin\ROPCPermissionController@getLocation');
    Route::post('/UserDetails','Admin\ROPCPermissionController@UserDetails');
    Route::post('/getUserDetails','Admin\ROPCPermissionController@getUserDetails');
    Route::get('/allPermissionRequest','Admin\ROPCPermissionController@AllPermissionRequest')->name('test.p');
    Route::get('/getAcceptpermissiondetails/{id}','Admin\ROPCPermissionController@getpermissiondetails');
    Route::get('/getpermissiondetails/{id}','Admin\ROPCPermissionController@getpermissiondetails');
    Route::post('/getpermissiondetailsview','Admin\ROPCPermissionController@getpermissiondetailsview');
    Route::post('/uploadnodaldoc','Admin\ROPCPermissionController@UploadNodaldoc');
    Route::post('/updateaction','Admin\ROPCPermissionController@UpdateAction');
    //   Route::get('/addps','Admin\ROPCPermissionController@AddPS');
    //   Route::post('/AddPSData','Admin\ROPCPermissionController@AddPSData');
    Route::get('/viewps','Admin\ROPCPermissionController@ViewPS');
    Route::post('/getallacps','Admin\ROPCPermissionController@getallACPS');
    //   Route::get('/editps/{id}','Admin\ROPCPermissionController@EditPS');
    //   Route::post('/editps','Admin\ROPCPermissionController@EditPS');

    //   Route::get('/addauthority','Admin\ROPCPermissionController@AddAuthority');
    //   Route::post('/addauthoritydata','Admin\ROPCPermissionController@AddAuthorityData');
    Route::get('/viewauthority','Admin\ROPCPermissionController@ViewAuthority');
    Route::post('/getallacauthority','Admin\ROPCPermissionController@getallACAuthority');
    //   Route::get('/editauthority/{id}','Admin\ROPCPermissionController@EditAuthority');
    //   Route::post('/editauthority','Admin\ROPCPermissionController@EditAuthority');
    //   Route::get('/addlocation','Admin\ROPCPermissionController@AddLocation');
    //   Route::post('/AddLocationinsert','Admin\ROPCPermissionController@AddLocationinsert');
    Route::get('/viewaddlocation','Admin\ROPCPermissionController@viewaddlocation');
    Route::post('/getallacloc','Admin\ROPCPermissionController@getallACloc');
    Route::post('/getalldistrict','Admin\ROPCPermissionController@getalldistrict');
    Route::post('/getpcalldistrict','Admin\ROPCPermissionController@getpcalldistrict');
    //   Route::get('/locationeditpermsn/{id}','Admin\ROPCPermissionController@locationeditpermsn');
    //   Route::post('/updateLocationval','Admin\ROPCPermissionController@updateLocationval');

    Route::get('/agentCreation','Admin\ROPCPermissionController@AgentCreation');
    Route::post('/addagent','Admin\ROPCPermissionController@AddAgent');
    Route::get('/viewagent','Admin\ROPCPermissionController@ViewAgent');
    Route::get('/editagent/{id}','Admin\ROPCPermissionController@EditAgent');
    Route::post('/editagent','Admin\ROPCPermissionController@EditAgent');
    Route::post('/agentstatus','Admin\ROPCPermissionController@EditAgentStatus');
    Route::get('/permissioncount','Admin\ROPCPermissionController@PermissionCount');
    Route::post('/permissioncountdetails','Admin\ROPCPermissionController@PermissionCountDetails');
    Route::get('/permissiondetailsview/{id}/{loc_id}/{status}','Admin\ROPCPermissionController@PermissionDetailsView');
    Route::get('/manualforward','Admin\ROPCPermissionController@ManualForward');
    Route::get('/manulaforwarddownload/{id}','Admin\ROPCPermissionController@ManualForwardDownload');
    //load map
    Route::get('/getlocat', 'Admin\ROPCPermissionController@getlocationList');
    Route::get('/getlatl', 'Admin\ROPCPermissionController@getlatlongs');

    Route::get('/generate-pdf/{id}','Admin\ROPCPermissionController@generatePDF');
  });


  //end Permission routes
  #######################By Niraj 16-2-19############################
  Route::get('/electors-ropollingstationlist', 'Admin\RoPCReportController@electorsropollingstationList')->name('ro.electorsropollingstationList');
  Route::post('/electors-ropollingstation', 'Admin\RoPCReportController@electorsropollingstationStore');
  Route::get('/roofficer-logindetails', 'Admin\RoPCReportController@roOfficerLogindetailsList');
  Route::get('/changepassword', 'Admin\RoPCReportController@changePassword');
  Route::post('/changepassword', 'Admin\RoPCReportController@changePasswordStore');

  ######################end By Niraj   statrt nazar##############################################
  Route::get('/pcrologin-detail-excel', 'Admin\RoPCReportController@loginDetailExcel');
  Route::get('/login-detail-pdf', 'Admin\RoPCReportController@logindetailpdf');
  #####################Datewise Report by Mayank###################################
  Route::get('/datewisereport', 'Admin\PCRoreportController@datewisereport');
  Route::POST('/range-datewisereport', 'Admin\PCRoreportController@datewisereport_range');
  Route::get('/reportspdfview/{date}/{consti}','Admin\PCRoreportController@reportspdfview')->name('reportspdfview');
  Route::get('/reportexcelview/{date}/{consti}','Admin\PCRoreportController@reportexcelview')->name('reportexcelview');
  #####################End Datewise Report by Mayank##############################
  //waseem 2019-03-28
  Route::get('/report/scrutiny', 'Admin\ReportController@get_report');
  Route::get('/report/scrutiny/excel', 'Admin\ReportController@downlaod_to_excel');
  Route::get('/report/scrutiny/pdf', 'Admin\ReportController@pdf');
  Route::get('/report/scrutiny/detail/{id}', 'Admin\ReportController@detail');
  Route::get('/candidate/detail-by-nomination/{id}', 'Admin\CandidateController@detail');
  //end waseem 
  //----------------------------Divya-------------------------------------------//
      
  Route::get('/reportpc','Admin\PCRoreportsController@reportpc');
      
  Route::post('/reportdates','Admin\PCRoreportsController@reportdates');
  Route::get('/partywise','Admin\PCRoreportsController@partywise');
    
  Route::get('/permissiontype','Admin\PCRoreportsController@permissiontype');

  Route::get('/permissionraw','Admin\PCRoreportsController@permissionraw');
  //----------------------------Divya-------------------------------------------//
  ##########Expendature Routes include here ###################
  include("web_ropcexp.php");
  ##########Expendature Routes include here ###################
});

//end of ROPC Sachchidanand
/* ARO of r Pc Election Section   ============================================================= */
 
/* ARO of r Pc Election Section   ============================================================= */

Route::group(['prefix' => 'aro', 'as' => 'aro::', 'middleware' => ['auth:admin', 'auth','aro_only']], function(){
  // Route::get('/dashboard', 'Admin\PermissionController@PermissionCount');
  //Route::get('/', 'Admin\PermissionController@PermissionCount');
  Route::get('/dashboard', 'Admin\PCCountingController@dashboardcounting');
  Route::get('/', 'Admin\PCCountingController@dashboardcounting');
    

  //ALAM - ALL PC Wise Report
  Route::get('/constituency-wise-report','Admin\ConstituencyWiseReport\ConstituencyWiseReportController@index'); 
  Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedPcByStateId');  
  Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedAc');  
  Route::post('/get-condidate-details-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCondidfateListpkpk');
  Route::post('/get-all-result-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCompleteResult');
  Route::post('/csvDownload-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@csvDownload');
  //
  //ALAM - ALL PC Wise Report
  Route::get('/round-wise-report-pcwise','Admin\PcWiseRoundReport\PcWiseRoundReportController@index'); 
  Route::post('/get-pc-by-state-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedPcByStateId');	
  Route::post('/get-ac-by-state-and-pc-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedAc');	
  Route::post('/get-condidate-details-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCondidfateListpkpk');
  Route::post('/get-all-result-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCompleteResult');
  Route::post('/csvDownload-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@csvDownload');
  //

	
  // Counting Section   counting-data-entry  finalize-ac 
  Route::group(['prefix' => 'counting', 'as' => 'counting::', 'middleware' => ['auth:admin', 'auth']], function(){      
    Route::get('/round-schedule', 'Admin\PCCountingController@round_schedule');
    Route::POST('/verifyround', 'Admin\PCCountingController@verifyround');
    Route::get('/counting-data-entry', 'Admin\PCCountingController@counting_data_entry');
    Route::get('/counting-data-entry/{rid?}', 'Admin\PCCountingController@counting_data_entry'); 
    Route::POST('/counting-data-entry-edit', 'Admin\PCCountingController@counting_data_entry_edit');
    Route::POST('/verifycounting-data-entry', 'Admin\PCCountingController@verifycounting');
    Route::GET('/counting-evm-finalized', 'Admin\PCCountingController@counting_evm_finalized');
    Route::POST('/finalize_evm_rounds', 'Admin\PCCountingController@finalize_evm_rounds');
    Route::POST('/finalize-ac-counting', 'Admin\PCCountingController@finalize_ac_counting');
    Route::GET('/round-wise-entry', 'Admin\PCCountingController@round_wise_entry');

    Route::GET('/pdf', 'Admin\PCCountingController@pdf');
    Route::GET('/ballot_pdf', 'Admin\PCCountingController@ballot_pdf');
    Route::POST('/pdf', 'Admin\PCCountingController@pdf');
    Route::POST('/ballot_pdf', 'Admin\PCCountingController@ballot_pdf');
  });


  Route::group(['prefix' => 'voting', 'as' => 'voting::', 'middleware' => ['auth:admin', 'auth']], function(){ 
    Route::get('/schedule-entry','Admin\PollDayController@schedule_entry'); 
    Route::POST('/verify-schedule','Admin\PollDayController@aro_schedule_entry'); 
    Route::get('/schedule-entry/{round?}','Admin\PollDayController@schedule_entry');
    Route::get('/estimate-turnout-entry','Admin\PollDayController@estimate_turnout_entry'); 
    Route::POST('/estimated-entry','Admin\PollDayController@estimated_entry');  
    Route::get('/defreeze-round/{r?}','Admin\PollDayController@defreeze_round'); 
    Route::get('/finalize-turnout','Admin\PollDayController@finalize_turnout'); 

    // URL STARTS FOR ARO
    Route::get('/ElectorsDetails','Admin\ElectorsDetailsController@ElectorsDetails'); 
    Route::post('/ElectorsDetailsUpdate','Admin\ElectorsDetailsController@ElectorsDetailsUpdate'); 
    Route::get('/PsWiseDetails','Admin\ElectorsDetailsController@PsWiseDetails'); 
    Route::post('/PsWiseDetailsUpdate','Admin\ElectorsDetailsController@PsWiseDetailsUpdate'); 		 
    Route::post('/PsWiseFinalize','Admin\ElectorsDetailsController@PsWiseFinalize');
    Route::get('/polling-station-electors-details', 'Admin\ElectorsDetailsController@PollingStationElectorsDetails');
    Route::post('/polling-station-electors-details-export', 'Admin\ElectorsDetailsController@PollingStationElectorsDetailsExport');
    Route::post('/polling-station-electors-details-update', 'Admin\ElectorsDetailsController@PollingStationElectorsDetailsUpdate');
    Route::post('/polling-station-electors-details-finalized', 'Admin\ElectorsDetailsController@PollingStationElectorsFinalized');
    Route::post('/polling-station-import', 'Admin\ElectorsDetailsController@PollingStationImport');
    // URL ENDS FOR ARO
  });

  // permission Routes strat
  Route::group(['prefix' => 'permission', 'as' => 'permission::', 'middleware' => ['auth:admin', 'auth']], function(){  
    Route::get('/allmasters', 'Admin\PermissionController@allMasters');
    Route::get('/offlinePermission','Admin\PermissionController@OfflinePermission');
    Route::post('/offlinePermission','Admin\PermissionController@OfflinePermission');
    Route::post('/UserDetails','Admin\PermissionController@UserDetails');
    Route::post('/getUserDetails','Admin\PermissionController@getUserDetails');
    Route::get('/allPermissionRequest','Admin\PermissionController@AllPermissionRequest')->name('test.p');
    Route::get('/getAcceptpermissiondetails/{id}','Admin\PermissionController@getpermissiondetails');
    Route::get('/getpermissiondetails/{id}','Admin\PermissionController@getpermissiondetails');
    Route::post('/getpermissiondetailsview','Admin\PermissionController@getpermissiondetailsview');
    Route::post('/uploadnodaldoc','Admin\PermissionController@UploadNodaldoc');
    Route::post('/updateaction','Admin\PermissionController@UpdateAction');
    Route::get('/addps','Admin\PermissionController@AddPS');
    Route::post('/AddPSData','Admin\PermissionController@AddPSData');
    Route::get('/viewps','Admin\PermissionController@ViewPS');
    Route::get('/editps/{id}','Admin\PermissionController@EditPS');
    Route::post('/editps','Admin\PermissionController@EditPS');
    Route::get('/addpermission','Admin\PermissionController@AddPermission');
    Route::post('/AddPermissionData','Admin\PermissionController@AddPermissionData');
    Route::get('/viewpermsn','Admin\PermissionController@ViewPerms');
    Route::get('/editpermsn/{id}','Admin\PermissionController@EditPrmsn');
    Route::post('/editpermsn','Admin\PermissionController@EditPrmsn');
    Route::get('/addauthority','Admin\PermissionController@AddAuthority');
    Route::post('/addauthoritydata','Admin\PermissionController@AddAuthorityData');
    Route::get('/viewauthority','Admin\PermissionController@ViewAuthority');
    Route::get('/editauthority/{id}','Admin\PermissionController@EditAuthority');
    Route::post('/editauthority','Admin\PermissionController@EditAuthority');
    Route::post('/authoritystatus','Admin\PermissionController@EditAuthorityStatus');
    Route::get('/addlocation','Admin\PermissionController@AddLocation');
    Route::post('/AddLocationinsert','Admin\PermissionController@AddLocationinsert');
    Route::get('/viewaddlocation','Admin\PermissionController@viewaddlocation');
    Route::get('/locationeditpermsn/{id}','Admin\PermissionController@locationeditpermsn');
    Route::post('/updateLocationval','Admin\PermissionController@updateLocationval');

    Route::get('/agentCreation','Admin\PermissionController@AgentCreation');
    Route::post('/addagent','Admin\PermissionController@AddAgent');
    Route::get('/viewagent','Admin\PermissionController@ViewAgent');
    Route::get('/editagent/{id}','Admin\PermissionController@EditAgent');
    Route::post('/editagent','Admin\PermissionController@EditAgent');
    Route::post('/agentstatus','Admin\PermissionController@EditAgentStatus');
    Route::get('/permissioncount','Admin\PermissionController@PermissionCount');
    Route::post('/permissioncountdetails','Admin\PermissionController@PermissionCountDetails');
    Route::get('/permissiondetailsview/{id}/{loc_id}/{status}','Admin\PermissionController@PermissionDetailsView');
    Route::get('/manualforward','Admin\PermissionController@ManualForward');
    Route::get('/manulaforwarddownload/{id}','Admin\PermissionController@ManualForwardDownload');


    //load map
    Route::get('/getlocat', 'Admin\PermissionController@getlocationList');
    Route::get('/getlatl', 'Admin\PermissionController@getlatlongs');

    Route::get('/generate-pdf/{id}','Admin\PermissionController@generatePDF');

    //----------Start Niraj permission routs -------------------------------------------//
    Route::get('/permissionraw-report','Admin\AROPermissionController@permissionrawreport');
    Route::get('/permissionpartywise-report','Admin\AROPermissionController@permissionpartywisereport');
    Route::get('/permissiondatewise-report/','Admin\AROPermissionController@permissionreportbydate');
    Route::post('/permissiondatewise-report/','Admin\AROPermissionController@permissionreportbydateFilter');
    Route::get('/permissiontype-report','Admin\AROPermissionController@permissiontype');
    //-------End Niraj permission routs -------------------------------//
  });
   //end Permission routes

  //for boothapp by vinay
  Route::group(['prefix' => 'booth-app-revamp', 'as' => 'booth-app-revamp::', 'middleware' => ['auth:admin', 'auth']], function(){ 
    Route::get('/officer-list','Admin\BoothAppRevamp\PollingController@get_polling_station');
    Route::get('/officer-list/add/{id}','Admin\BoothAppRevamp\PollingController@add_officer');
    Route::post('/officer-list/post','Admin\BoothAppRevamp\PollingController@post_officer');
    Route::post('/reset_otp','Admin\BoothAppRevamp\PollingController@reset_otp');
    Route::get('import-excel','Admin\BoothAppRevamp\OfficerController@import_excel');
    Route::get('electors-verification-by-ps','Admin\BoothAppRevamp\ElectorsVerificationByPsController@index');
    Route::any('electors-verification-by-ps/post','Admin\BoothAppRevamp\ElectorsVerificationByPsController@post');

    Route::get('exempted-boothapp-pollingstation','Admin\BoothAppRevamp\PollingController@turnout_new');
    Route::get('view-exempted-pollingstation','Admin\BoothAppRevamp\PollingController@view_turnout_new');
    Route::post('view-exempted-pollingstation','Admin\BoothAppRevamp\PollingController@view_turnout_new');
    Route::post('post-exempted-boothapp-pollingstation','Admin\BoothAppRevamp\PollingController@turnout_new_ajax');
    Route::any('update_turnout_pswise','Admin\BoothAppRevamp\PollingController@update_turnout');
    Route::post('exempt-ps-wise','Admin\BoothAppRevamp\PollingController@exempt_ps_wise');
    Route::post('delete_user_pso','Admin\BoothAppRevamp\PollingController@delete_user_pso');

    Route::get('/dashboard','Admin\BoothAppRevamp\DashboardController@dashboard');
    Route::get('/mapped-location-report','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report');
    Route::get('/mapped-location-report/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_excel');
    Route::get('/mapped-location-report/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_pdf');

    Route::get('/mapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac');
    Route::get('/mapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_excel');
    Route::get('/mapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_report_ac_pdf');

    Route::get('/mapped-location-ps-wise-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise');
    Route::get('/mapped-location-ps-wise-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_excel');
    Route::get('/mapped-location-ps-wise-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@mapped_location_ps_wise_pdf');

    Route::get('/unmapped-location-report/state/ac','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report');
    Route::get('/unmapped-location-report/state/ac/excel','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_excel');
    Route::get('/unmapped-location-report/state/ac/pdf','Admin\BoothAppRevamp\MappedLocationController@unmapped_location_report_pdf');

    Route::get('/not-activated-officer','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report');
    Route::get('/not-activated-officer/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_excel');
    Route::get('/not-activated-officer/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_pdf');
    Route::get('/not-activated-officer/ac','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac');
    Route::get('/not-activated-officer/ac/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_excel');
    Route::get('/not-activated-officer/ac/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ac_pdf');
    Route::get('/not-activated-officer/ac/ps','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps');
    Route::get('/not-activated-officer/ac/ps/excel','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_excel');
    Route::get('/not-activated-officer/ac/ps/pdf','Admin\BoothAppRevamp\NotActivatedOfficerController@officer_assignment_report_ps_pdf');
    Route::get('/officers','Admin\BoothAppRevamp\PollingController@get_officers');
    Route::get('/get_voter_turnout','Admin\BoothAppRevamp\PollingController@get_voter_turnout');
    Route::get('referesh_age_graph','Admin\BoothAppRevamp\DashboardController@referesh_age_graph');
    Route::get('get_cumulative_time_data','Admin\BoothAppRevamp\DashboardController@get_cumulative_time_data');
    Route::get('get_voters_by_time','Admin\BoothAppRevamp\DashboardController@get_voters_by_time');
    Route::get('get_doughnut_data','Admin\BoothAppRevamp\DashboardController@get_doughnut_data');
    Route::get('get_gender_data','Admin\BoothAppRevamp\DashboardController@get_gender_data');
    Route::get('load-state-by-phase','Admin\Common\CommonBoothAppController@load_state_by_ajax');
    Route::get('load-ac-by-state','Admin\Common\CommonBoothAppController@load_ac_by_ajax');
    Route::get('load-ps-by-ac','Admin\Common\CommonBoothAppController@load_ps_by_ajax');
    Route::get('/disconnected-ps-report','Admin\BoothAppRevamp\ReportController@getdisconnectedps');
    Route::get('/e-roll-download','Admin\BoothAppRevamp\PollingController@get_e_roll_download');
    Route::get('/poll-event-dashboard','Admin\BoothAppRevamp\ReportController@poll_event_dashboard');
    Route::get('/dashboard_data_analytics','Admin\BoothAppRevamp\ReportController@getanalyticsdashboard');
    Route::get('/form49-ps-report','Admin\BoothAppRevamp\ReportController@getform49count');
    Route::get('/scan-data','Admin\BoothAppRevamp\PollingController@get_scan_data');
    Route::post('/add_download_log/{id}','Admin\BoothAppRevamp\PollingController@add_download_log');
    Route::get('/poll-detail','Admin\BoothAppRevamp\PollingController@get_poll_detail');
    Route::get('/poll-detail/ac','Admin\BoothAppRevamp\PollingController@poll_detail_ac');
    Route::get('get-form-17-a','Admin\BoothAppRevamp\PollingController@get_form_17_a');
    Route::get('download-form-17-a','Admin\BoothAppRevamp\PollingController@download_17_a_form');
    Route::get('download-pro-diary','Admin\BoothAppRevamp\PollingController@download_pro_diary');

    //officer-assignment-report state
    Route::get('/officer-assignment-report','Admin\BoothAppRevamp\BoarController@officer_assignment_report');
    Route::get('/officer-assignment-report/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_excel');
    Route::get('/officer-assignment-report/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_pdf');
    Route::get('/officer-assignment-report/ac','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac');
    Route::get('/officer-assignment-report/ac/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_excel');
    Route::get('/officer-assignment-report/ac/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ac_pdf');
    Route::get('/officer-assignment-report/ac/ps','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps');
    Route::get('/officer-assignment-report/ac/ps/excel','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_excel');
    Route::get('/officer-assignment-report/ac/ps/pdf','Admin\BoothAppRevamp\BoarController@officer_assignment_report_ps_pdf');

    //poll-turnout-report ac
    Route::get('/poll-turnout-report/state/ac','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac');
    Route::get('/poll-turnout-report/state/ac/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_excel');
    Route::get('/poll-turnout-report/state/ac/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ac_pdf');

    //poll-turnout-report ps
    Route::get('/poll-turnout-report/state/ac/ps','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps');
    Route::get('/poll-turnout-report/state/ac/ps/excel','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_excel');
    Route::get('/poll-turnout-report/state/ac/ps/pdf','Admin\BoothAppRevamp\BptoController@poll_turnout_report_ps_pdf');


    Route::get('/poll-event-report','Admin\BoothAppRevamp\ReportController@poll_event_report');
    Route::get('/poll-event-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_report');
    Route::get('/poll-event-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_report');

    Route::get('/poll-event-ps-wise-report','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
    Route::get('/poll-event-ps-wise-report-pdf','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');
    Route::get('/poll-event-ps-wise-report-xls','Admin\BoothAppRevamp\ReportController@poll_event_ps_wise_report');

    Route::post('upload-excel','Admin\BoothAppRevamp\OfficerController@upload_excel');
    Route::post('confirm-import','Admin\BoothAppRevamp\OfficerController@verify_and_import');
  });
});


//eci sub-agent
Route::group(['prefix' => 'eci-agent', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth','eci_agent']], function(){
  //
  Route::group(['prefix' => 'indexcardview', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){

    Route::get('/IndexCardFinalizeView','Admin\index\IndexCardFinalizeController@IndexCardFinalizeView');
    Route::get('/IndexCardFinalizeView/pdf','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewPdf');
    Route::get('/IndexCardFinalizeView/excel','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewExcel');

  });
  Route::get('/dashboard', 'Admin\EciController@dashboard');

  Route::group(['prefix' => 'report/voting', 'middleware' => []], function(){
    Route::get('/list-schedule','Admin\Eci\Report\PolldayTurnoutController@report_state');
    Route::get('/list-schedule/excel','Admin\Eci\Report\PolldayTurnoutController@export_excel_report_state');
    Route::get('/list-schedule/pdf','Admin\Eci\Report\PolldayTurnoutController@export_pdf_report_state');
    Route::get('/list-schedule/state','Admin\Eci\Report\PolldayTurnoutController@report_pc');
    Route::get('/list-schedule/state/excel','Admin\Eci\Report\PolldayTurnoutController@export_excel_report_pc');
    Route::get('/list-schedule/state/pdf','Admin\Eci\Report\PolldayTurnoutController@export_pdf_report_pc');
    Route::get('/list-schedule/state/pc','Admin\Eci\Report\PolldayTurnoutController@report_ac');
    Route::get('/list-schedule/state/pc/excel','Admin\Eci\Report\PolldayTurnoutController@export_excel_report_ac');
    Route::get('/list-schedule/state/pc/pdf','Admin\Eci\Report\PolldayTurnoutController@export_pdf_report_ac'); 


    Route::get('/close-of-poll','Admin\Eci\Report\PolldayCloseOfPollController@pc');
    Route::get('/close-of-poll/excel','Admin\Eci\Report\PolldayCloseOfPollController@export_excel_pc');
    Route::get('/close-of-poll/pdf','Admin\Eci\Report\PolldayCloseOfPollController@export_pdf_pc');


    //end of poll
    Route::get('/end-of-poll','Admin\Eci\Report\PolldayEndOfPollController@report_state');
    Route::get('/end-of-poll/excel','Admin\Eci\Report\PolldayEndOfPollController@export_excel_report_state');
    Route::get('/end-of-poll/pdf','Admin\Eci\Report\PolldayEndOfPollController@export_pdf_report_state');
    Route::get('/end-of-poll/state','Admin\Eci\Report\PolldayEndOfPollController@report_pc');
    Route::get('/end-of-poll/state/excel','Admin\Eci\Report\PolldayEndOfPollController@export_excel_report_pc');
    Route::get('/end-of-poll/state/pdf','Admin\Eci\Report\PolldayEndOfPollController@export_pdf_report_pc');
    Route::get('/end-of-poll/state/pc','Admin\Eci\Report\PolldayEndOfPollController@report_ac');
    Route::get('/end-of-poll/state/pc/excel','Admin\Eci\Report\PolldayEndOfPollController@export_excel_report_ac');
    Route::get('/end-of-poll/state/pc/pdf','Admin\Eci\Report\PolldayEndOfPollController@export_pdf_report_ac');


    Route::get('/list-schedule/state/pc/missed','Admin\Eci\Report\MissingTurnoutController@get_missed_ac');
    Route::get('/list-schedule/state/pc/missed/excel','Admin\Eci\Report\MissingTurnoutController@export_excel_report_missed');
    Route::get('/list-schedule/state/pc/missed/pdf','Admin\Eci\Report\MissingTurnoutController@export_pdf_report_missed');


    Route::get('/get_missed','Admin\Eci\Report\MissingTurnoutController@get_missed');
    Route::get('/get_missed/excel','Admin\Eci\Report\MissingTurnoutController@export_excel_report_ac_missed');
    Route::get('/get_missed/pdf','Admin\Eci\Report\MissingTurnoutController@export_pdf_report_ac_missed');

    Route::get('/compare','Admin\Eci\Report\PolldayCompareController@compare');
    Route::get('/compare/excel','Admin\Eci\Report\PolldayCompareController@export_excel_compare');
    Route::get('/compare/pdf','Admin\Eci\Report\PolldayCompareController@export_pdf_compare');


    Route::get('/close-of-poll','Admin\Eci\Report\PolldayCloseOfPollController@pc');
    Route::get('/close-of-poll/excel','Admin\Eci\Report\PolldayCloseOfPollController@export_excel_pc');
    Route::get('/close-of-poll/pdf','Admin\Eci\Report\PolldayCloseOfPollController@export_pdf_pc');

    //end of poll for all phases
    Route::get('/end-of-poll-summary','Admin\Eci\Report\PolldayEndOfPollSummaryController@report_state');
    Route::get('/end-of-poll-summary/excel','Admin\Eci\Report\PolldayEndOfPollSummaryController@export_excel_report_state');
    Route::get('/end-of-poll-summary/pdf','Admin\Eci\Report\PolldayEndOfPollSummaryController@export_pdf_report_state');

  });

  //waseem 2019-04-09
  Route::get('/voting/list-schedule','Admin\Voting\EciPollDayController@index');
  Route::get('/voting/list-schedule/state','Admin\Voting\EciPollDayController@state');
  Route::get('/voting/list-schedule/state/{id}','Admin\Voting\EciPollDayController@get_ac_by_pc');
  Route::get('/voting/list-schedule/export_excel','Admin\Voting\EciPollDayController@export_excel');
  Route::get('/voting/list-schedule/export_state_excel','Admin\Voting\EciPollDayController@export_state_excel');
  Route::get('/voting/list-schedule/export_state_ac_excel','Admin\Voting\EciPollDayController@export_state_ac_excel');

});

  //end sub-agent

Route::group(['prefix' => 'eci-index', 'middleware' => ['auth:admin', 'auth','eci_index']], function(){
	
  //
  Route::group(['prefix' => 'indexcardview', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){

    Route::get('/IndexCardFinalizeView','Admin\index\IndexCardFinalizeController@IndexCardFinalizeView');
    Route::get('/IndexCardFinalizeView/pdf','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewPdf');
    Route::get('/IndexCardFinalizeView/excel','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewExcel');

    Route::get('/IndexCardFinalizeViewTotal','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewTotal');
    Route::get('/IndexCardFinalizeViewTotal/pdf','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewTotalPdf');
    Route::get('/IndexCardFinalizeViewTotal/excel','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewTotalExcel');

    Route::get('/ConstituencyWiseSummary','Admin\index\ConstituencyWiseSummaryController@ConstituencyWiseSummary');
    Route::get('/ConstituencyWiseSummary/pdf','Admin\index\ConstituencyWiseSummaryController@ConstituencyWiseSummaryPdf');
    Route::get('/ConstituencyWiseSummary/excel','Admin\index\ConstituencyWiseSummaryController@ConstituencyWiseSummaryExcel');

    Route::get('/WomenParticipation','Admin\index\WomenParticipationController@WomenParticipation');
    Route::get('/WomenParticipation/pdf','Admin\index\WomenParticipationController@WomenParticipationPdf');
    Route::get('/WomenParticipation/excel','Admin\index\WomenParticipationController@WomenParticipationExcel');

  });
  
  Route::group(['prefix' => 'indexcard'], function(){
    //complain waseem add eci
    Route::get('get-indexcard-eci', 'Admin\Indexcard\IndexcardController@get_indexcard_for_eci');
    Route::get('get-complains','Admin\Indexcard\ComplainController@get_complains_list');
    Route::get('indexcardpc', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcarddata');
    Route::get('de-finalize-pcs/pdf', 'Admin\Indexcard\ComplainController@deFinalizePcs');
    Route::get('de-finalize-pcs/excel', 'Admin\Indexcard\ComplainController@deFinalizePcs');

    Route::post('get-complains/post','Admin\Indexcard\ComplainController@definalize_indexcard');
    Route::get('get-complains/post',function(){
      return redirect('/eci-index/indexcard/get-complains');
    });

    Route::post('definalize-nomination','Admin\Indexcard\ComplainController@definalize_nomination');
    Route::get('definalize-nomination',function(){
      return redirect('/eci-index/indexcard/get-complains');
    });
    
    Route::post('definalize-counting','Admin\Indexcard\ComplainController@definalize_counting');
    Route::get('definalize-counting',function(){
      return redirect('/eci-index/indexcard/get-complains');
    });
  });
  
  Route::get('/dashboard', 'Admin\EciIndexController@dashboard'); 
});



 /* End of ARO of   Pc Election Section   ============================================================= */
Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){
	
	Route::group(['prefix' => 'indexcard', 'middleware' => ['eci']], function(){
		Route::get('get-indexcard-eci', 'Admin\Indexcard\IndexcardController@get_indexcard_for_eci');
		Route::post('upload-indexcard/post', 'Admin\Indexcard\IndexcardController@post_indexcard_accepted');
		Route::get('upload-indexcard/post', function(){
		  return redirect('/eci/indexcard/get-indexcard-eci');
		});
		//complain waseem add eci
		Route::get('get-complains','Admin\Indexcard\ComplainController@get_complains_list');
		Route::get('indexcardpc', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcarddata');

		Route::post('get-complains/post','Admin\Indexcard\ComplainController@definalize_indexcard');
		Route::get('get-complains/post',function(){
		  return redirect('/ropc/indexcard/get-complains');
		});
	});
	
	
	Route::any('result-report','Admin\ResultSheetController@result_report');
	
	
	
	//  
   Route::group(['prefix' => 'indexcardview', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth','eci']], function(){

    Route::get('/IndexCardFinalizeView','Admin\index\IndexCardFinalizeController@IndexCardFinalizeView');
    Route::get('/IndexCardFinalizeView/pdf','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewPdf');
    Route::get('/IndexCardFinalizeView/excel','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewExcel');

    Route::get('/IndexCardFinalizeViewTotal','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewTotal');
    Route::get('/IndexCardFinalizeViewTotal/pdf','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewTotalPdf');
    Route::get('/IndexCardFinalizeViewTotal/excel','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewTotalExcel');
	
	 Route::get('/ConstituencyWiseSummary','Admin\index\ConstituencyWiseSummaryController@ConstituencyWiseSummary');
    Route::get('/ConstituencyWiseSummary/pdf','Admin\index\ConstituencyWiseSummaryController@ConstituencyWiseSummaryPdf');
    Route::get('/ConstituencyWiseSummary/excel','Admin\index\ConstituencyWiseSummaryController@ConstituencyWiseSummaryExcel');

Route::get('/WomenParticipation','Admin\index\WomenParticipationController@WomenParticipation');
    Route::get('/WomenParticipation/pdf','Admin\index\WomenParticipationController@WomenParticipationPdf');
    Route::get('/WomenParticipation/excel','Admin\index\WomenParticipationController@WomenParticipationExcel');

  });
	
	Route::get('/report/candidate', 'Admin\Eci\Report\CandidateController@get_candidates')->middleware('eci');

  Route::group(['prefix' => 'setting', 'middleware' => ['eci']], function(){
    Route::get('/setting','Admin\Eci\Setting\SettingController@index');
    Route::post('/setting/save','Admin\Eci\Setting\SettingController@save');
	
	
	
	Route::get('/broadcast','Admin\Eci\Setting\SettingController@broadcast');
    Route::post('/broadcast/save','Admin\Eci\Setting\SettingController@save_broadcast');
	
  });

  //2019-05-14
  Route::group(['prefix' => 'officer', 'middleware' => ['eci']], function(){
    Route::get('/reset-password','Admin\Eci\Profile\OfficerResetPinController@index');
    Route::post('/update-pin','Admin\Eci\Profile\OfficerResetPinController@update_pin');
    Route::post('/update-password','Admin\Eci\Profile\OfficerResetPinController@update_password');
	
	//definalize
    Route::get('/de-finalize-list','Admin\Eci\Profile\DefinalizeController@index');
    Route::get('/de-finalize-ballot/{id}','Admin\Eci\Profile\DefinalizeController@de_finalize_ro');
    Route::get('/de-finalize-result/{id}','Admin\Eci\Profile\DefinalizeController@de_finalize_result');
	
  });
  
//ALAM - ALL PC Wise Report
Route::get('/constituency-wise-report','Admin\ConstituencyWiseReport\ConstituencyWiseReportController@index'); 
Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedPcByStateId');  
Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedAc');  
Route::post('/get-condidate-details-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCondidfateListpkpk');
Route::post('/get-all-result-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCompleteResult');
Route::post('/csvDownload-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@csvDownload');
//

    //ALAM - ALL PC Wise Report
Route::get('/round-wise-report-pcwise','Admin\PcWiseRoundReport\PcWiseRoundReportController@index'); 
Route::post('/get-pc-by-state-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedPcByStateId');	
Route::post('/get-ac-by-state-and-pc-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedAc');	
Route::post('/get-condidate-details-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCondidfateListpkpk');
Route::post('/get-all-result-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCompleteResult');
Route::post('/csvDownload-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@csvDownload');
//

  
	//Sanjay Routes Start
	Route::get('/candidate-wise-report', 'Report\VoterTypeWiseReportController@reportIndex');
	Route::get('/candidate-wise-report-getpc-state/{state}', 'Report\VoterTypeWiseReportController@getPcByState');
	Route::get('/candidate-wise-report-get-party/{pc}/{state}', 'Report\VoterTypeWiseReportController@getPartyByPc');
	Route::post('/candidate-wise-report-search', 'Report\VoterTypeWiseReportController@getReport');
	Route::post('/candidate-wise-report-excel', 'Report\VoterTypeWiseReportController@getReportExcel');
	Route::post('/candidate-wise-report-pdf', 'Report\VoterTypeWiseReportController@getReportPdf');\
	Route::get('/winning-candidate-list', 'Report\WinningCandidateReportController@getPdfView');
	Route::get('/winning-candidate-list-pdf', 'Report\WinningCandidateReportController@getDownloadPdf');
	Route::get('/national-parties-performance', 'Report\WinningCandidateReportController@getNationalPerformance');
	Route::get('/national-parties-performance-pdf', 'Report\WinningCandidateReportController@getNationalPerformancePdf');
	
	Route::get('/election-statistics', 'Report\ElectionStaticsReportController@getElectionStatics');
	Route::get('/election-statistics-pdf', 'Report\ElectionStaticsReportController@getElectionStaticsPdf');
	Route::get('/election-statistics-excel', 'Report\ElectionStaticsReportController@getElectionStaticsExcel');
  Route::get('/candidate-profile', 'Report\ElectionStaticsReportController@getCandidatePersonalDetais');
  Route::get('/candidate-profile-excel', 'Report\ElectionStaticsReportController@getCandidatePersonalDetaisExcel');
  Route::get('/candidate-profile/{win_status}','Report\ElectionStaticsReportController@getCandidatePersonalDetaisFilter');
  Route::get('/candidate-profile-excel/{win_status}', 'Report\ElectionStaticsReportController@getCandidatePersonalDetaisExcelFilter');

	//Sanjay Routes Ends

	//ALAM - Round Wise Report
	Route::get('/round-wise-report','Admin\RoundWiseReport\RoundWiseReportController@index'); 
	Route::post('/get-pc-by-state-id-eci', 'Admin\RoundWiseReport\RoundWiseReportController@getMatchedPcByStateId');	
	Route::post('/get-ac-by-state-and-pc-id-eci', 'Admin\RoundWiseReport\RoundWiseReportController@getMatchedAc');	
	Route::post('/get-condidate-details-eci', 'Admin\RoundWiseReport\RoundWiseReportController@getCondidfateListpkpk');
	Route::post('/get-all-result-eci', 'Admin\RoundWiseReport\RoundWiseReportController@getCompleteResult');
	Route::post('/csvDownload', 'Admin\RoundWiseReport\RoundWiseReportController@csvDownload');
	//
	//Form21 REPORT COUNT
	Route::get('/form21-report','Admin\CountingReport\Form21ReportController@form21Report');
	Route::post('/form21-report','Admin\CountingReport\Form21ReportController@form21Report')->name('eci.download.form21.report');
	//Form21 Download
	Route::get('/form21c-download','Admin\CountingReport\FormDownloadController@form21download');
	Route::post('/form21c-download','Admin\CountingReport\FormDownloadController@form21download')->name('eci.download.form21c');

      //SCHEDULE REPORT START- GUNAJIT
	Route::get('/schedule-report','Admin\CountingReport\ScheduleReportController@scheduleReport');
	Route::post('/schedule-report','Admin\CountingReport\ScheduleReportController@scheduleReport');
	Route::get('/state-by-pc/{s_code}','Admin\CountingReport\ScheduleReportController@pcList');
	Route::get('/pc-by-ac/{s_code}/{pc_id}','Admin\CountingReport\ScheduleReportController@acList');
	Route::get('/schedule-report-pdf/{s_code}/{pc_id}/{ac_id}','Admin\CountingReport\ScheduleReportController@scheduleReportPDF');
	Route::get('/schedule-report-excel/{s_code}/{pc_id}/{ac_id}','Admin\CountingReport\ScheduleReportController@scheduleReportExcel');
	//SCHEDULE REPORT END- GUNAJIT

Route::group(['prefix' => 'report/voting', 'middleware' => ['eci']], function(){
    Route::get('/list-schedule','Admin\Eci\Report\PolldayTurnoutController@report_state');
    Route::get('/list-schedule/excel','Admin\Eci\Report\PolldayTurnoutController@export_excel_report_state');
    Route::get('/list-schedule/pdf','Admin\Eci\Report\PolldayTurnoutController@export_pdf_report_state');
    Route::get('/list-schedule/state','Admin\Eci\Report\PolldayTurnoutController@report_pc');
    Route::get('/list-schedule/state/excel','Admin\Eci\Report\PolldayTurnoutController@export_excel_report_pc');
    Route::get('/list-schedule/state/pdf','Admin\Eci\Report\PolldayTurnoutController@export_pdf_report_pc');
    Route::get('/list-schedule/state/pc','Admin\Eci\Report\PolldayTurnoutController@report_ac');
    Route::get('/list-schedule/state/pc/excel','Admin\Eci\Report\PolldayTurnoutController@export_excel_report_ac');
    Route::get('/list-schedule/state/pc/pdf','Admin\Eci\Report\PolldayTurnoutController@export_pdf_report_ac'); 
	
	
	Route::get('/close-of-poll','Admin\Eci\Report\PolldayCloseOfPollController@pc');
    Route::get('/close-of-poll/excel','Admin\Eci\Report\PolldayCloseOfPollController@export_excel_pc');
    Route::get('/close-of-poll/pdf','Admin\Eci\Report\PolldayCloseOfPollController@export_pdf_pc');


    //end of poll
    Route::get('/end-of-poll','Admin\Eci\Report\PolldayEndOfPollController@report_state');
    Route::get('/end-of-poll/excel','Admin\Eci\Report\PolldayEndOfPollController@export_excel_report_state');
    Route::get('/end-of-poll/pdf','Admin\Eci\Report\PolldayEndOfPollController@export_pdf_report_state');
    Route::get('/end-of-poll/state','Admin\Eci\Report\PolldayEndOfPollController@report_pc');
    Route::get('/end-of-poll/state/excel','Admin\Eci\Report\PolldayEndOfPollController@export_excel_report_pc');
    Route::get('/end-of-poll/state/pdf','Admin\Eci\Report\PolldayEndOfPollController@export_pdf_report_pc');
    Route::get('/end-of-poll/state/pc','Admin\Eci\Report\PolldayEndOfPollController@report_ac');
    Route::get('/end-of-poll/state/pc/excel','Admin\Eci\Report\PolldayEndOfPollController@export_excel_report_ac');
    Route::get('/end-of-poll/state/pc/pdf','Admin\Eci\Report\PolldayEndOfPollController@export_pdf_report_ac');


    Route::get('/list-schedule/state/pc/missed','Admin\Eci\Report\MissingTurnoutController@get_missed_ac');
    Route::get('/list-schedule/state/pc/missed/excel','Admin\Eci\Report\MissingTurnoutController@export_excel_report_missed');
    Route::get('/list-schedule/state/pc/missed/pdf','Admin\Eci\Report\MissingTurnoutController@export_pdf_report_missed');


    Route::get('/get_missed','Admin\Eci\Report\MissingTurnoutController@get_missed');
    Route::get('/get_missed/excel','Admin\Eci\Report\MissingTurnoutController@export_excel_report_ac_missed');
    Route::get('/get_missed/pdf','Admin\Eci\Report\MissingTurnoutController@export_pdf_report_ac_missed');

	Route::get('/compare','Admin\Eci\Report\PolldayCompareController@compare');
    Route::get('/compare/excel','Admin\Eci\Report\PolldayCompareController@export_excel_compare');
    Route::get('/compare/pdf','Admin\Eci\Report\PolldayCompareController@export_pdf_compare');


Route::get('/close-of-poll','Admin\Eci\Report\PolldayCloseOfPollController@pc');
    Route::get('/close-of-poll/excel','Admin\Eci\Report\PolldayCloseOfPollController@export_excel_pc');
    Route::get('/close-of-poll/pdf','Admin\Eci\Report\PolldayCloseOfPollController@export_pdf_pc');
	
	//end of poll for all phases
    Route::get('/end-of-poll-summary','Admin\Eci\Report\PolldayEndOfPollSummaryController@report_state');
    Route::get('/end-of-poll-summary/excel','Admin\Eci\Report\PolldayEndOfPollSummaryController@export_excel_report_state');
    Route::get('/end-of-poll-summary/pdf','Admin\Eci\Report\PolldayEndOfPollSummaryController@export_pdf_report_state');


  });




	
	
	
	//waseem 2019-04-09
Route::get('/voting/list-schedule','Admin\Voting\EciPollDayController@index');
  Route::get('/voting/list-schedule/state','Admin\Voting\EciPollDayController@state');
  Route::get('/voting/list-schedule/state/{id}','Admin\Voting\EciPollDayController@get_ac_by_pc');
  
  
  Route::get('/voting/list-schedule/export_excel','Admin\Voting\EciPollDayController@export_excel');
  Route::get('/voting/list-schedule/export_state_excel','Admin\Voting\EciPollDayController@export_state_excel');
  Route::get('/voting/list-schedule/export_state_ac_excel','Admin\Voting\EciPollDayController@export_state_ac_excel');


     //waseem 2019-03-28
  Route::get('/report/scrutiny/state', 'Admin\EciScrutinyController@get_report_by_state');
  Route::get('/report/scrutiny/state/excel', 'Admin\EciScrutinyController@state_downlaod_to_excel');
  Route::get('/report/scrutiny/state/pdf', 'Admin\EciScrutinyController@state_wise_pdf');

  Route::get('/report/scrutiny', 'Admin\EciScrutinyController@get_report');
  Route::get('/report/scrutiny/excel', 'Admin\EciScrutinyController@downlaod_to_excel');
  Route::get('/report/scrutiny/pdf', 'Admin\EciScrutinyController@constancy_wise_pdf');
  
  Route::get('/report/scrutiny/detail/{id}', 'Admin\EciScrutinyController@detail');
  Route::get('/report/scrutiny/detail-by-nomination/{id}', 'Admin\CandidateController@detail');
  //end waseem 
  
		Route::get('/dashboard', 'Admin\EciController@dashboard'); 
		Route::get('/', 'Admin\EciController@dashboard');
		Route::get('/createschedule', 'Admin\EciController@createschedule');
    Route::get('/generateofficersloginname', 'Admin\EciController@generateofficersloginname');
		Route::get('/createschedulenew', 'Admin\EciController@createschedulenew');
		Route::POST('/electiondetailsnew', 'Admin\EciController@insertnewelecschedule');
		Route::get('/electiondetailsnew/{schid}', 'Admin\EciController@electiondetailsnew');
		Route::get('/scheduledetailsnew', 'Admin\EciController@scheduledetailsnew');
		Route::get('/saveelectiondetails', 'Admin\EciController@saveelectiondata');
		Route::get('/savesortabledata', 'Admin\EciController@savesortabledata');
		Route::get('/schedulelisting', 'Admin\EciController@schedulelisting');
		Route::POST('/createschedule', 'Admin\EciController@validateschedule'); 
		Route::GET('/updateschedule/{sid}', 'Admin\EciController@updateschedule'); 
		Route::POST('/updateschedule', 'Admin\EciController@editschedule'); 
		Route::get('/election-details', 'Admin\EciController@electiondetails');
		Route::Post('/election-details', 'Admin\EciController@showelectiondetails');
		Route::GET('/election-details/{sid}', 'Admin\EciController@electiondetails');
		Route::POST('/showelectiondetails', 'Admin\EciController@showelectiondetails');
		//Route::get('/showelectiondetails', 'Admin\EciController@showelectiondetails'); delete-election 
		Route::POST('/assigndetails', 'Admin\EciController@assigndetails');
		Route::POST('/update-assign', 'Admin\EciController@update_assign');
		Route::get('/election-list', 'Admin\EciController@election_list'); 
		Route::get('/view-election-details/{st_code}/{sech_id}/{phaseno}/{cons_type}', 'Admin\EciController@view_election_details');
		Route::get('/update-election-details/{st_code}/{sech_id}/{phaseno}/{cons_type}', 'Admin\EciController@update_election_details');
		Route::get('/delete-election/{st_code}/{cons_type}', 'Admin\EciController@delete_election');
		Route::get('/unassign/{st_code}/{const_no}/{sched_id}/{stphase}', 'Admin\EciController@unassign'); 
		Route::get('/sendnominationmessage', 'Admin\EciController@sendnominationmessage');
		Route::get('/updatesymbole', 'Admin\EciController@updatesymbole');
		Route::get('/electrosdataupdate', 'Admin\EciController@electrosdataupdate'); 
		Route::get('/generate-counting-data','Admin\EciController@generate_counting_data');
		 Route::get('/calculate-totalvotes','Admin\EciController@calculate_totalvotes');
     Route::get('/winning-leading','Admin\EciController@winning_leading');
	  Route::get('/dummy-pswise-dataentry','Admin\EciController@dummy_pswise_dataentry');
	   // Master Data creation  database entry 
    Route::get('/common/generate-counting-data','Admin\Common\ECICommonController@generate_counting_data');
    Route::get('/common/generateofficersloginname', 'Admin\Common\ECICommonController@generateofficersloginname'); 
    Route::get('/common/insertcountingfinalize', 'Admin\Common\ECICommonController@insertcountingfinalize'); 
    Route::get('/common/insertfinalizeac', 'Admin\Common\ECICommonController@insertfinalize');
    Route::get('/common/loginupdate', 'Admin\Common\ECICommonController@loginupdate');
     Route::get('/common/generate-voterturnout-data', 'Admin\Common\ECICommonController@generate_voterturnout_data');
   Route::get('/common/sendnominationmessage', 'Admin\Common\ECICommonController@sendnominationmessage');
   // end script master
	// ROUTES STARTS

    //ACTIVE USERS REPORT
    Route::get('/EciActiveUsers', 'Admin\EciReportController@EciActiveUsers'); 
    //ACTIVE USERS EXCEL REPORT
    Route::get('/EciActiveUsersReportExcel', 'Admin\EciReportController@EciActiveUsersReportExcel');
    //ACTIVE USERS PDF REPORT
    Route::get('/EciActiveUsersPdf', 'Admin\EciReportController@EciActiveUsersPdf');
    //NOMINATION REPORT
    Route::get('/EciNominationReport', 'Admin\EciReportController@EciNominationReport'); 
    //NOMINATION EXCEL REPORT
    Route::get('/EciNominationExcelReport', 'Admin\EciReportController@EciNominationExcelReport'); 
    //NOMINATION EXCEL REPORT
    Route::get('/EciNominationReportPdf', 'Admin\EciReportController@EciNominationReportPdf'); 
    //COUNTING STATUS REPORT
    Route::get('/EciCountingStatusReport', 'Admin\EciReportController@EciCountingStatusReport');
    //COUNTING STATUS EXCEL REPORT
    Route::get('/EciCountingExcelStatus', 'Admin\EciReportController@EciCountingExcelStatus');
    //COUNTING STATUS PDF REPORT
    Route::get('/EciCountingStatusReportPdf', 'Admin\EciReportController@EciCountingStatusReportPdf');
    //PARTY REPORT
    Route::get('/EciPartyData', 'Admin\EciReportController@EciPartyData');
    //PARTY EXCEL REPORT
    Route::get('/EciPartyDataExcel', 'Admin\EciReportController@EciPartyDataExcel');
    //PARTY PDF REPORT
    Route::get('/EciPartyDataPdf', 'Admin\EciReportController@EciPartyDataPdf');
    //SYMBOL REPORT
    Route::get('/EciSymbolData', 'Admin\EciReportController@EciSymbolData');
    //SYMBOL EXCEL REPORT
    Route::get('/EciSymbolDataExcel', 'Admin\EciReportController@EciSymbolDataExcel');
    //SYMBOL PDF REPORT
    Route::get('/EciSymbolDataPdf', 'Admin\EciReportController@EciSymbolDataPdf');
    //SCHEDULE REPORT
    Route::get('/EciElectionSchedule', 'Admin\EciReportController@EciElectionSchedule');
    //SCHEDULE EXCEL REPORT
    Route::get('/EciElectionScheduleExcel', 'Admin\EciReportController@EciElectionScheduleExcel');
    //ECI ELECTION SCHEDULE
    Route::get('/EciElectionSchedulePdf', 'Admin\EciReportController@EciElectionSchedulePdf');
     //EciCustomReportFilter
    Route::match(array('GET','POST'),'/EciCustomReportFilter/', 'Admin\EciReportController@EciCustomReportFilter');
    //ECI CUSTOM REPORT FILTER GET 
    Route::get('/EciCustomReportFilterGet/{state_code}/{ScheduleList?}/', 'Admin\EciReportController@EciCustomReportFilterGet');
    //ECI CUSTOM REPORT FILTER GET EXCEL
    Route::get('/EciCustomReportFilterGetExcel/{state_code}/{ScheduleList?}', 'Admin\EciReportController@EciCustomReportFilterGetExcel');
    //ECI CUSTOM REPORT FILTER GET PDF
    Route::get('/EciCustomReportFilterGetPdf/{state_code}/{ScheduleList?}', 'Admin\EciReportController@EciCustomReportFilterGetPdf');
    //ECI NOMINATION STATE WISE REPORT 
    Route::get('/EciNominationStateWiseReport/{stcode}/{ScheduleList?}', 'Admin\EciReportController@EciNominationStateWiseReport');
    //ECI NOMINATION STATE WISE EXCEL REPORT 
    Route::get('/EciNominationStateWiseExcelReport/{stcode}/{phase?}', 'Admin\EciReportController@EciNominationStateWiseExcelReport');
    //ECI NOMINATION STATE WISE PDF REPORT 
    Route::get('/EciNominationStateWisePdf/{stcode}/{phase?}', 'Admin\EciReportController@EciNominationStateWisePdf');
    //ECI NOMINATION STATE WISE REPORT 
    Route::get('/EciNominationPcWiseReport/{stcode}/{pcno}', 'Admin\EciReportController@EciNominationPcWiseReport');
    //ECI NOMINATION STATE WISE EXCEL REPORT 
    Route::get('/EciNominationPcWiseExcelReport/{stcode}/{pcno}', 'Admin\EciReportController@EciNominationPcWiseExcelReport');
    //ECI NOMINATION STATE WISE REPORT 
    Route::get('/EciNominationPcWisePdf/{stcode}/{pcno}', 'Admin\EciReportController@EciNominationPcWisePdf');
    //ECI CANDIDATE NOMINATION PROILE PDF DOWNLOAD 
    Route::get('/EciViewNominationPdf/{nom_id}/{cand_id}', 'Admin\EciReportController@EciViewNominationPdf');
    //ECI CANDIDATE NOMINATION PROILE DETAILS 
    Route::get('/EciViewNomination/{nom_id}/{cand_id}', 'Admin\EciReportController@EciViewNomination');
    //ECI STATE PHASE WISE NOMINATION DATA
    Route::post('/EciNominationStatePhase', 'Admin\EciReportController@EciNominationStatePhase');
    //ECI STATE PHASE WISE NOMINATION DATA EXCEL 
    Route::get('/EciNominationStatePhaseExcel/{ScheduleList}', 'Admin\EciReportController@EciNominationStatePhaseExcel');
    //ECI PC PHASE WISE NOMINATION 
    Route::match(array('GET','POST'),'/EciNominationPcPhaseFilter/', 'Admin\EciReportController@EciNominationPcPhaseFilter');
    //ECI PC PHASE WISE PDF NOMINATION 
    Route::get('/EciNominationPcPhaseFilterPdf/{stcode}/{phase}', 'Admin\EciReportController@EciNominationPcPhaseFilterPdf');
    //ECI NOMINATION STATE WISE EXCEL REPORT 
    Route::get('/EciNominationPcPhaseFilterExcel/{stcode}/{phase}', 'Admin\EciReportController@EciNominationPcPhaseFilterExcel');
    //ECI PHASE NOMINATION CAND WISE REPORT
    Route::get('/EciPhaseInfoData', 'Admin\EciReportController@EciPhaseInfoData'); 






    // Candidate Details
   Route::get('list-of-nomination', 'Admin\Nomination\NominationController@get_candidates')->middleware('by_pass_security');
   Route::match(array('GET', 'POST'), '/ca-candidate-list', 'Admin\EciController@get_ca_cand_list');
   Route::get('/get-ac', 'Admin\EciController@get_ac');
    Route::get('/get-state', 'Admin\EciController@get_state');
    Route::get('/get-district', 'Admin\EciController@get_district');
    Route::post('/ca-candidate-list-pdf', 'Admin\EciController@get_ca_cand_list_pdf');
    Route::post('/ca-candidate-list-excel', 'Admin\EciController@get_ca_cand_list_excel');
    

   Route::get('/count-report', 'Admin\Nomination\Report\NomReportController@get_report');
      Route::get('/count-report-export/pdf', 'Admin\Nomination\Report\NomReportController@get_report_pdf');
      Route::get('/count-report-export/excel', 'Admin\Nomination\Report\NomReportController@get_report_excel');

// End Candidate  










    //ECI PHASE NOMINATION CAND WISE EXCEL REPORT
    Route::get('/EciPhaseInfoDataExcel', 'Admin\EciReportController@EciPhaseInfoDataExcel'); 
    //ECI PHASE NOMINATION CAND WISE PDF REPORT
    Route::get('/EciPhaseInfoDataPdf', 'Admin\EciReportController@EciPhaseInfoDataPdf'); 
    //ECI PHASE NOMINATION CAND WISE FORM
    Route::post('/EciPhaseInfoDataCandWiseForm/', 'Admin\EciReportController@EciPhaseInfoDataCandWiseForm');
    //ECI PHASE NOMINATION CAND WISE REPORT
    Route::get('/EciPhaseInfoDataCandWise/{phaseid}', 'Admin\EciReportController@EciPhaseInfoDataCandWise');
    //ECI PHASE NOMINATION CAND WISE EXCEL REPORT
    Route::get('/EciPhaseInfoDataCandWiseExcel/{phaseid}', 'Admin\EciReportController@EciPhaseInfoDataCandWiseExcel'); 
    //ECI PHASE NOMINATION CAND WISE PDF REPORT
    Route::get('/EciPhaseInfoDataCandWisePdf/{phaseid}', 'Admin\EciReportController@EciPhaseInfoDataCandWisePdf'); 
    //ECI NOMINATION FINALIZED REPORT
    Route::get('/EciNominationFinalized', 'Admin\EciReportController@EciNominationFinalized');
    //ECI NOMINATION FINALIZED EXCEL REPORT
    Route::get('/EciNominationFinalizedExcel', 'Admin\EciReportController@EciNominationFinalizedExcel'); 
    //ECI NOMINATION FINALIZED PDF REPORT
    Route::get('/EciNominationFinalizedPdf', 'Admin\EciReportController@EciNominationFinalizedPdf'); 
    //ECI NOMINATION FINALIZED PHASE WISE REPORT
    Route::post('/EciNominationFinalizedByPhaseIdForm/', 'Admin\EciReportController@EciNominationFinalizedByPhaseIdForm');
    //ECI NOMINATION FINALIZED PHASE WISE REPORT
    Route::get('/EciNominationFinalizedByPhaseId/{phaseid}', 'Admin\EciReportController@EciNominationFinalizedByPhaseId');
    //ECI NOMINATION FINALIZED PHASE WISE EXCEL REPORT
    Route::get('/EciNominationFinalizedByPhaseIdExcel/{phaseid}', 'Admin\EciReportController@EciNominationFinalizedByPhaseIdExcel');
    //ECI NOMINATION FINALIZED PHASE WISE PDF REPORT
    Route::get('/EciNominationFinalizedByPhaseIdPdf/{phaseid}', 'Admin\EciReportController@EciNominationFinalizedByPhaseIdPdf');
    //ECI NOMINATION FINALIZED STATE AND PHASE WISE REPORT
    Route::get('/EciNominationFinalizedByStatePhaseId/{phaseid}/{statecode}', 'Admin\EciReportController@EciNominationFinalizedByStatePhaseId');
    //ECI NOMINATION FINALIZED STATE AND PHASE WISE EXCEL REPORT
    Route::get('/EciNominationFinalizedByStatePhaseIdExcel/{phaseid}/{statecode}', 'Admin\EciReportController@EciNominationFinalizedByStatePhaseIdExcel');
    //ECI NOMINATION FINALIZED STATE AND PHASE WISE PDF REPORT
    Route::get('/EciNominationFinalizedByStatePhaseIdPdf/{phaseid}/{statecode}', 'Admin\EciReportController@EciNominationFinalizedByStatePhaseIdPdf');




    // Candidate Details
   Route::get('list-of-nomination', 'Admin\Nomination\NominationController@get_candidates')->middleware('by_pass_security');
   Route::match(array('GET', 'POST'), '/ca-candidate-list', 'Admin\EciController@get_ca_cand_list');
   Route::get('/get-ac', 'Admin\EciController@get_ac');
    Route::get('/get-state', 'Admin\EciController@get_state');
    Route::get('/get-district', 'Admin\EciController@get_district');
    Route::post('/ca-candidate-list-pdf', 'Admin\EciController@get_ca_cand_list_pdf');
    Route::post('/ca-candidate-list-excel', 'Admin\EciController@get_ca_cand_list_excel');
    

   Route::get('/count-report', 'Admin\Nomination\Report\NomReportController@get_report');
      Route::get('/count-report-export/pdf', 'Admin\Nomination\Report\NomReportController@get_report_pdf');
      Route::get('/count-report-export/excel', 'Admin\Nomination\Report\NomReportController@get_report_excel');

// End Candidate   





    
	
	//ECI PC POLL TURN OUT STARTS
     Route::get('/EciPollTurnOutPcWise/', 'Admin\EciReportController@EciPollTurnOutPcWise');
     Route::get('/EciPollTurnOutPcWiseExcel/', 'Admin\EciReportController@EciPollTurnOutPcWiseExcel');
     Route::get('/EciPollTurnOutAcWise/', 'Admin\EciReportController@EciPollTurnOutAcWise');
     Route::get('/EciPollTurnOutAcWiseExcel/', 'Admin\EciReportController@EciPollTurnOutAcWiseExcel');
     Route::get('/EciCompPollRoundReport/', 'Admin\EciReportController@EciCompPollRoundReport');
     Route::get('/EciCompPollRoundReportExcel/', 'Admin\EciReportController@EciCompPollRoundReportExcel');
     
    //ECI PC POLL TURN OUT ENDS


    //ECI PC POLL PERCENT STARTS
    Route::get('/EciPollPercent/', 'Admin\EciReportController@EciPollPercent');
    Route::get('/EciPollPercentExcel/', 'Admin\EciReportController@EciPollPercentExcel');
    Route::get('/EciPollPercentPdf/', 'Admin\EciReportController@EciPollPercentPdf');
    Route::get('/EciPollPercentPcWise/state', 'Admin\EciReportController@EciPollPercentPcWise');
    Route::get('/EciPollPercentPcWiseExcel/state', 'Admin\EciReportController@EciPollPercentPcWiseExcel');
    Route::get('/EciPollPercentPcWisePdf/state', 'Admin\EciReportController@EciPollPercentPcWisePdf');
    
    //ECI PC POLL PERCENT ENDS
	
	//ECI END OF POLL FINALSED STARTS
    Route::get('/EciEndOfPollFinalised/', 'Admin\EciEndOfPollFinalisedController@EciEndOfPollFinalised');
    Route::get('/EciEndOfPollFinalised/excel', 'Admin\EciEndOfPollFinalisedController@EciEndOfPollFinalisedExcel');
    Route::get('/EciEndOfPollFinalised/pdf', 'Admin\EciEndOfPollFinalisedController@EciEndOfPollFinalisedPdf');
    //ECI END OF POLL FINALSED ENDS
	
	//ECI PC POLLING STATION STARTS
    Route::get('/EciPsWiseDetails/', 'Admin\PollingStation\EciPollingStationController@EciPsWiseDetails');
    Route::get('/EciPsWiseDetails/excel', 'Admin\PollingStation\EciPollingStationController@EciPsWiseDetailsExcel');
    Route::get('/EciPsWiseDetails/pdf', 'Admin\PollingStation\EciPollingStationController@EciPsWiseDetailsPdf');
    Route::get('/EnableClosePollEntry/', 'Admin\PollingStation\EciPollingStationController@getEciPcsListForMissedEntry');
    Route::post('/enable-modification-acs', 'Admin\PollingStation\EciPollingStationController@enbale_modified_acs');
    Route::get('/PcECIPSElectoralDefinalzied', 'Admin\PollingStation\EciPollingStationController@PcECIPSElectoralDefinalzied');
    Route::post('/PcECIPSElectoralDefinalziedUpdate', 'Admin\PollingStation\EciPollingStationController@PcECIPSElectoralDefinalziedUpdate');

    //ECI PC POLLING STATION ENDS
    

    // ROUTES ENDS
	
		
	//Divya Shukla
	
	   Route::get('/report', 'Admin\ReportEciController@report'); 
	   Route::post('/permissioncountdetails','Admin\ReportEciController@permissionCountDetails');
	   Route::post('/getacpermission','Admin\ReportEciController@getacpermission');
       Route::get('/reportcount/{status}', 'Admin\ReportEciController@reportcount'); 
	   Route::get('Acvalue','Admin\ReportEciController@Acvalue');
	   Route::get('getDistrictsval','Admin\ReportEciController@getDistrictsval');
	   Route::get('getACListsval','Admin\ReportEciController@getACListsval');
	   Route::post('getalldistpermission','Admin\ReportEciController@getalldistpermission');
	   Route::post('getallstatepermission','Admin\ReportEciController@getallstatepermission');
	   Route::get('/getdatewise','Admin\ReportEciController@getdatewise');
	   Route::get('/exportallrec','Admin\ReportEciController@exportallrec');
	   Route::get('/exportstatedistrict/{state}/{district}','Admin\ReportEciController@exportstatedistrict');
	   Route::get('/exportstatereport/{state}/','Admin\ReportEciController@exportstatereport');
	   Route::get('/reportacall/{state}/{district}/{ac}','Admin\ReportEciController@reportacall');
	   Route::get('/permissiondetailsview/{id}/{loc_id}/{status}','Admin\ReportEciController@PermissionDetailsView');
	   Route::get('/permissionreport','Admin\ReportEciController@permissionreport');
	   Route::get('/getpermissionvalue','Admin\ReportEciController@getpermissionvalue');
	   Route::post('/reportdates','Admin\ReportEciController@reportdates');
	   Route::get('/exportpermission/{state}/{pid}','Admin\ReportEciController@exportpermission');
	   Route::get('/statereport','Admin\ReportEciController@statereport');
	   Route::post('/permissioncountnewdetails','Admin\ReportEciController@permissioncountnewdetails');
	   Route::get('/partywise','Admin\PermissiontypeController@partywise');
	   Route::get('/permissiontype','Admin\PermissiontypeController@permissiontype');
	   Route::post('/reportdatesview','Admin\ReportEciController@reportdatesview');
     Route::get('/reportdatesview','Admin\ReportEciController@reportdatesview');

	   // Route::get('/partywise','Admin\PermissiontypeController@partywise');
     Route::post('/partywise','Admin\PermissiontypeController@partywise');
 Route::get('/partywisedetails/{ele}/{pid}/{pname}/{status}','Admin\PermissiontypeController@partywisedetails');
Route::post('/permissiontypes','Admin\PermissiontypeController@permissiontypes');
 Route::get('/permissiontypes','Admin\PermissiontypeController@permissiontypes');
Route::get('/permissionwisedetails/{ele}/{pname}/{status}','Admin\PermissiontypeController@permissionwisedetails');
 
Route::post('/permissiontype','Admin\PermissiontypeController@permissiontype');
// 30-11-2022

 Route::get('/districtwisereportdetails/{st}/{dist}/{ele}/{dt}/{status}','Admin\ReportEciController@districtwisereportdetails');
    
Route::post('/districtwisereportview','Admin\ReportEcidistrictController@districtwisereportview');
    Route::get('/districtwisereportview','Admin\ReportEcidistrictController@districtwisereportview');
    Route::get('/districtreport','Admin\ReportEcidistrictController@districtreport');
    Route::post('/districtwisereport','Admin\ReportEcidistrictController@districtwisereport');
    Route::get('/permissionmasterreport','Admin\ReportEciController@PermissionMasterReport');
    Route::post('/permissionmasterreport','Admin\ReportEciController@PermissionMasterReport');
   Route::match(array('GET', 'POST'),'/modewisepermissionreport','Admin\ReportEciController@modewisepermissionreport');
    


		
});


Route::group(['prefix' => 'eci-expenditure', 'middleware' => ['auth:admin', 'auth','eci_expenditure']], function(){
	
 //Route::get('/dashboard', 'Admin\EciIndexController@dashboard'); 
 ##########Expendature Routes include here ###################
 include("web_ecipcexp.php");
 ##########Expendature Routes include here ###################
  
});
 
Route::group(['prefix' => 'pcceo', 'as' => 'pcceo::', 'middleware' => ['auth:admin', 'auth']], function(){
		
	Route::get('/report/candidate', 'Admin\Eci\Report\CandidateController@get_candidates')->middleware('ceo');
	
	Route::group(['prefix' => 'indexcard', 'middleware' => ['ceo']], function(){
		Route::get('upload-indexcard', 'Admin\Indexcard\IndexcardController@upload_indexcard_request');
		Route::post('upload-indexcard/post', 'Admin\Indexcard\IndexcardController@post_upload_indexcard_file');
		Route::get('get-uploaded-indexcard', 'Admin\Indexcard\IndexcardController@get_uploaded_indexcard');
	    Route::get('indexcardpc', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcarddata');
		Route::get('/finalize','Admin\Ceo\ElectorVoterController@get_ceo_finalize');
		Route::get('/finalize/post',function(){
		  return redirect('pcceo/indexcard/finalize');
		});
		Route::post('/finalize/post','Admin\Ceo\ElectorVoterController@post_ceo_finalize');
		
		Route::post('post-complain-indexcard','Admin\Indexcard\ComplainController@post_complain_indexcard');
		Route::get('post-complain-indexcard',function(){
		  return redirect('/ropc/indexcard/get-complains');
		});
		Route::get('get-complains','Admin\Indexcard\ComplainController@get_complains_list');
		
		
	  });
	
	 Route::group(['prefix' => 'indexcardview', 'as' => 'pcceo::', 'middleware' => ['auth:admin', 'auth']], function(){

    Route::get('/IndexCardFinalizeView','Admin\index\IndexCardFinalizeController@IndexCardFinalizeView');
    Route::get('/IndexCardFinalizeView/pdf','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewPdf');
    Route::get('/IndexCardFinalizeView/excel','Admin\index\IndexCardFinalizeController@IndexCardFinalizeViewExcel');

  });
	
	
	
	//waseem 2019-04-12
  Route::group(['prefix' => 'elector', 'as' => 'pcceo::', 'middleware' => ['ceo']], function(){
    Route::get('/edit','Admin\Ceo\ElectorVoterController@edit_elector_form');
    Route::get('/post',function(){
      return redirect('pcceo/elector/edit');
    });
    Route::post('/post','Admin\Ceo\ElectorVoterController@post_elector_form');
  });
	Route::group(['prefix' => 'voters', 'as' => 'pcceo::', 'middleware' => ['ceo']], function(){
		Route::get('/edit','Admin\Ceo\ElectorVoterController@edit_voters_form');
		Route::get('/post',function(){
		  return redirect('pcceo/voters/edit');
		});
		Route::post('/post','Admin\Ceo\ElectorVoterController@post_voters_form');
	});

  //2019-05-14
  Route::group(['prefix' => 'officer', 'middleware' => ['ceo']], function(){
    Route::get('/reset-password','Admin\Ceo\Profile\OfficerResetPinController@index');
    Route::post('/update-password','Admin\Ceo\Profile\OfficerResetPinController@update_password');
	
	//Route::get('/couting-data','Admin\Ceo\Profile\ResetCountingDataController@index');
    //Route::post('/reset-counting-data','Admin\Ceo\Profile\ResetCountingDataController@reset_counting_data');
    //Route::post('/reset-counting-data-state','Admin\Ceo\Profile\ResetCountingDataController@reset_counting_state');

  });

  //ALAM - ALL PC Wise Report
Route::get('/constituency-wise-report','Admin\ConstituencyWiseReport\ConstituencyWiseReportController@index'); 
Route::post('/get-pc-by-state-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedPcByStateId');  
Route::post('/get-ac-by-state-and-pc-id-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getMatchedAc');  
Route::post('/get-condidate-details-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCondidfateListpkpk');
Route::post('/get-all-result-eci-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@getCompleteResult');
Route::post('/csvDownload-pcwise-constituency', 'Admin\ConstituencyWiseReport\ConstituencyWiseReportController@csvDownload');
//
      //ALAM - ALL PC Wise Report
Route::get('/round-wise-report-pcwise','Admin\PcWiseRoundReport\PcWiseRoundReportController@index'); 
Route::post('/get-pc-by-state-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedPcByStateId');	
Route::post('/get-ac-by-state-and-pc-id-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getMatchedAc');	
Route::post('/get-condidate-details-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCondidfateListpkpk');
Route::post('/get-all-result-eci-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@getCompleteResult');
Route::post('/csvDownload-pcwise', 'Admin\PcWiseRoundReport\PcWiseRoundReportController@csvDownload');
//

	//Sanjay Routes Start
	Route::get('/candidate-wise-report', 'Report\VoterTypeWiseReportController@reportIndex');
	Route::get('/candidate-wise-report-getpc-state/{state}', 'Report\VoterTypeWiseReportController@getPcByState');
	Route::get('/candidate-wise-report-get-party/{pc}/{state}', 'Report\VoterTypeWiseReportController@getPartyByPc');
	Route::post('/candidate-wise-report-search', 'Report\VoterTypeWiseReportController@getReport');
	Route::post('/candidate-wise-report-excel', 'Report\VoterTypeWiseReportController@getReportExcel');
	Route::post('/candidate-wise-report-pdf', 'Report\VoterTypeWiseReportController@getReportPdf');
	//Sanjay Routes Ends
	
	//Form21 Download
	Route::get('/form21-download','Admin\CountingReport\CEOFormDownloadController@form21download');
	Route::post('/form21-download','Admin\CountingReport\CEOFormDownloadController@form21download')->name('eci.download.form21');
	
    //SCHEDULE REPORT START- GUNAJIT
	Route::get('/schedule-report','Admin\CountingReport\CEOScheduleReportController@scheduleReport');
	Route::post('/schedule-report','Admin\CountingReport\CEOScheduleReportController@scheduleReport');
	Route::get('/pc-by-ac/{s_code}/{pc_id}','Admin\CountingReport\CEOScheduleReportController@acList');
	Route::get('/schedule-report-pdf/{pc_id}/{ac_id}','Admin\CountingReport\CEOScheduleReportController@scheduleReportPDF');
	Route::get('/schedule-report-excel/{pc_id}/{ac_id}','Admin\CountingReport\CEOScheduleReportController@scheduleReportExcel');
	//SCHEDULE REPORT END- GUNAJIT
	
	//PCCEO PC POLLING STATION STARTS
    Route::get('/CeoPsWiseDetails/', 'Admin\PollingStation\CeoPollingStationController@CeoPsWiseDetails');
    Route::get('/CeoPsWiseDetails/excel', 'Admin\PollingStation\CeoPollingStationController@CeoPsWiseDetailsExcel');
    Route::get('/CeoPsWiseDetails/pdf', 'Admin\PollingStation\CeoPollingStationController@CeoPsWiseDetailsPdf');
	Route::post('/CeoPsWiseDetailsUpdate/', 'Admin\PollingStation\CeoPollingStationController@CeoPsWiseDetailsUpdate');
	
	
  // Rocky Code Here
	Route::post('/CeoPsDefinalizeUpdate/', 'Admin\PollingStation\CeoPollingStationController@CeoPsDefinalizeUpdate');
	Route::post('/CeoPsFinalizeUpdate/', 'Admin\PollingStation\CeoPollingStationController@CeoPsFinalizeUpdate');
	Route::post('/publish-turnout/', 'Admin\PollingStation\CeoPollingStationController@finalize_turnout');
	Route::post('/publish-all-turnout/', 'Admin\PollingStation\CeoPollingStationController@finalize_all_turnout');
	Route::get('/turnout-publish-status-list/', 'Admin\PollingStation\CeoPollingStationController@getAcFinalizeList');
  //PCCEO PC POLLING STATION ENDS
	
	//waseem 2019-04-12
  Route::get('/voting/list-schedule/acwise','Admin\Voting\CeoAcWiseTurnOutController@report');
  Route::get('/voting/list-schedule/acwise/export','Admin\Voting\CeoAcWiseTurnOutController@export');
	
	
	//waseem 2019-04-09
Route::get('/voting/list-schedule/state','Admin\Voting\CeoPollDayController@state');
Route::get('/voting/list-schedule/state/{id}','Admin\Voting\CeoPollDayController@get_ac_by_pc'); 
	//waseem 2019-03-28
  Route::get('/report/scrutiny', 'Admin\CeoReportController@get_report');
  Route::get('/report/scrutiny/excel', 'Admin\CeoReportController@downlaod_to_excel');
  Route::get('/report/scrutiny/pdf', 'Admin\CeoReportController@pdf');
  Route::get('/report/scrutiny/detail/{id}', 'Admin\CeoReportController@detail');
  Route::get('/report/scrutiny/detail-by-nomination/{id}', 'Admin\CandidateController@detail');
  //end waseem 

  Route::get('/dashboard', 'Admin\PCCeoController@dashboard');
 Route::get('/', 'Admin\PCCeoController@dashboard');
 Route::GET('/showdashboard/{cand_status}/{constituency}/{search}', 'Admin\PCCeoController@showdashboard');
 Route::get('/datewisereport', 'Admin\PCCeoController@datewisereport');
 Route::POST('/range-datewisereport', 'Admin\PCCeoController@datewisereport_range');
 Route::get('/candidate-finalize', 'Admin\PCCeoController@candidate_finalize');
 Route::get('/candidate-definalize/{acno}/{actype}', 'Admin\PCCeoController@candidate_definalize');
 Route::post('/definalizevalidation', 'Admin\PCCeoController@definalizevalidation');
 Route::POST('/updateuser', 'Admin\PCCeoController@updateuser');
 Route::get('/download-contesting-candidate/{cons_no}', 'Admin\PCCeoController@download_contesting_candidate');
 Route::get('/list-of-nomination', 'Admin\PCCeoController@get_candidates');
                      ## CEO REPORT SECTION HERE ##
  

  Route::get('/duplicate-symbol-view', 'Admin\PCCeoReportController@duplicatesymbolview');
  Route::get('/ceo-duplicatesymol-pdf', 'Admin\PCCeoReportController@pcceoduplicatesymbolpdf');
  Route::get('/ceo-duplicateparty-pdf', 'Admin\PCCeoReportController@pcceoduplicatepartypdf');
  Route::get('/ceo-duplicatesymol-excel', 'Admin\PCCeoReportController@pcceoduplicatesymbolexcel');
  Route::get('/ceo-duplicateparty-excel', 'Admin\PCCeoReportController@pcceoduplicatepartyexcel');
  Route::get('/ceo-pclist-pdf', 'Admin\PCCeoReportController@pclistpdf');
  Route::get('/ceo-candidatelist-pdf/{pcno}', 'Admin\PCCeoReportController@candidatelistpdf');
  Route::get('/ceo-independante-cand-pdf', 'Admin\PCCeoReportController@independantecandpdf');
  Route::get('/ceo-independante-cand-excel', 'Admin\PCCeoReportController@ceoindependantecandexcel');
  Route::get('/ceo-pclist-excel', 'Admin\PCCeoReportController@ceopclistexcel');
  Route::get('/ceo-candidatelist-excel/{pcno}', 'Admin\PCCeoReportController@candidatelistexcelPC');
  Route::get('/candidate-symbol-no-200', 'Admin\PCCeoReportController@candidatesymbolno200');
  Route::get('/ceo-symbol-no-200-pdf', 'Admin\PCCeoReportController@ceosymbolno_200pdf');
  Route::get('/ceo-symbol-no-200-excel', 'Admin\PCCeoReportController@ceosymbolno_200excel');
  Route::get('/ceo-candidate-summary', 'Admin\PCCeoReportController@ceocandidatesummary');
  Route::get('/login-detail', 'Admin\PCCeoReportController@ceologindetail');
  Route::get('/login-detail-pdf', 'Admin\PCCeoReportController@logindetailpdf');
  Route::get('/login-detail-excel', 'Admin\PCCeoReportController@logindetailexcel');
  ######################end By REPORT ################################

  #######################By Niraj 12-2-19############################
    Route::get('/duplicateparties', 'Admin\PCCeoReportController@duplicatepartieslist');
    Route::get('/pclist', 'Admin\PCCeoReportController@pclist');
    Route::get('/candidatelist/{pcno}', 'Admin\PCCeoReportController@candidateListbyPC');
    Route::get('/independentcandidatelist', 'Admin\PCCeoController@independentcandidatelist');
    Route::get('/electors-pollingstationlist', 'Admin\PCCeoController@electorspollingstationList');
	Route::get('/change-password', 'Admin\PCCeoController@changePassword');
    Route::post('/change-password', 'Admin\PCCeoController@changePasswordStore');
	Route::get('/officer-details', 'Admin\PCCeoController@officerList');
	Route::get('/officer-profile/{id}', 'Admin\PCCeoController@officerProfileUpdate');
	Route::post('/officer-profile', 'Admin\PCCeoController@officerProfileUpdate');
	Route::get('/psinfo', 'Admin\PCCeoController@psinfoList');
	Route::post('/psinfo', 'Admin\PCCeoController@psresultList');
	Route::get('/getallac', 'Admin\PCCeoController@getaclist'); 
	Route::get('/getaclistbypc', 'Admin\PCCeoController@getaclistbyPC');
   // Route::post('/electors-pollingstation', 'Admin\PCCeoController@electorspollingstationStore');
     
    //Route::get('/electors-ropollingstationlist', 'Admin\RoPCReportController@electorsropollingstationList')->name('ro.electorsropollingstationList'); ;
   // Route::post('/electors-ropollingstation', 'Admin\RoPCReportController@electorsropollingstationStore');
	Route::GET('/nomination-report', 'Admin\PCCeoReportController@getNominationreport');
    Route::POST('/datewisenominationreport', 'Admin\PCCeoReportController@nominationadatewisereport');
    Route::get('/datewisecandidatelist/{pcno}/{date}', 'Admin\PCCeoReportController@datewisecandidatelist');
	//date 25-03-19
	Route::get('/candidatelist-pc/{pcno}', 'Admin\PCCeoReportController@nominatedcandListbyPC');
    Route::get('/ViewNominationDetails/{nomid}', 'Admin\PCCeoReportController@ViewNominationDetails');
	Route::get('/nominated-candidatelist-excel/{pcno}', 'Admin\PCCeoReportController@nominatedcandidatelistexcelPC');
	Route::get('/datewisenominated-candidatelist-excel/{pcno}/{date}', 'Admin\PCCeoReportController@datewisenomcandlistexcelPC');
  
  ######################end By Niraj ################################
  
  
  ###################### permission Routes strat
   Route::get('/allmasters', 'Admin\CeoPCPermissionController@allMasters');
   Route::get('/addpermission','Admin\CeoPCPermissionController@AddPermission');
   Route::post('/AddPermissionData','Admin\CeoPCPermissionController@AddPermissionData');
   Route::get('/viewpermsn','Admin\CeoPCPermissionController@ViewPerms');
   Route::get('/editpermsn/{id}','Admin\CeoPCPermissionController@EditPrmsn');
   Route::post('/editpermsn','Admin\CeoPCPermissionController@EditPrmsn');
   Route::post('/getdocdetails','Admin\CeoPCPermissionController@GetdocDetails');
   Route::post('/removepermsn','Admin\CeoPCPermissionController@RemovePermsn');
   Route::get('/addauthority','Admin\CeoPCPermissionController@AddAuthority');
   Route::post('/addauthoritydata','Admin\CeoPCPermissionController@AddAuthorityData');
   Route::get('/viewauthority','Admin\CeoPCPermissionController@ViewAuthority');
   Route::get('/editauthority/{id}','Admin\CeoPCPermissionController@EditAuthority');
   Route::post('/editauthority','Admin\CeoPCPermissionController@EditAuthority');
    Route::get('/agentCreation','Admin\CeoPCPermissionController@AgentCreation');
   Route::post('/addagent','Admin\CeoPCPermissionController@AddAgent');
   Route::get('/viewagent','Admin\CeoPCPermissionController@ViewAgent');
   Route::get('/editagent/{id}','Admin\CeoPCPermissionController@EditAgent');
   Route::post('/editagent','Admin\CeoPCPermissionController@EditAgent');
   Route::post('/agentstatus','Admin\CeoPCPermissionController@EditAgentStatus');
    Route::get('/permissioncount','Admin\CeoPCPermissionController@PermissionCount');
   Route::post('/permissioncountdetails','Admin\CeoPCPermissionController@PermissionCountDetails');
   Route::get('/permissiondetailsview/{id}/{loc_id}/{status}','Admin\CeoPCPermissionController@PermissionDetailsView');
   Route::get('/generate-pdf/{id}','Admin\CeoPCPermissionController@generatePDF');
   Route::get('/EditRestriction','Admin\CeoPCPermissionController@EditRestriction');
   Route::post('/updatedaterestriction','Admin\CeoPCPermissionController@updatedaterestriction');
   
   Route::get('/offlinePermission','Admin\CeoPCPermissionController@OfflinePermission');
   Route::post('/offlinePermission','Admin\CeoPCPermissionController@OfflinePermission');
   Route::post('/getAllPC','Admin\CeoPCPermissionController@getAllPC');
   Route::post('/getAllAC','Admin\CeoPCPermissionController@getAllAC');
   Route::post('/getAllPoliceStation','Admin\CeoPCPermissionController@getAllPoliceStation');
   Route::post('/getlocation','Admin\CeoPCPermissionController@getlocation');
   Route::post('/UserDetails','Admin\CeoPCPermissionController@UserDetails');
   Route::post('/getUserDetails','Admin\CeoPCPermissionController@getUserDetails');
   Route::get('/allPermissionRequest','Admin\CeoPCPermissionController@AllPermissionRequest');
   Route::get('/getAcceptpermissiondetails/{id}','Admin\CeoPCPermissionController@getpermissiondetails');
   Route::get('/getpermissiondetails/{id}','Admin\CeoPCPermissionController@getpermissiondetails');
   Route::post('/getpermissiondetailsview','Admin\CeoPCPermissionController@getpermissiondetailsview');
   Route::post('/uploadnodaldoc','Admin\CeoPCPermissionController@UploadNodaldoc');
   Route::post('/updateaction','Admin\CeoPCPermissionController@UpdateAction');
   
   ######CEO Authority
   Route::get('/addnodals','Admin\CeoPCPermissionController@AddNodals');
   Route::post('/addnodalsdata','Admin\CeoPCPermissionController@AddNodalsData');
   Route::get('/viewnodals','Admin\CeoPCPermissionController@ViewNodals');
   Route::get('/editnodals/{id}','Admin\CeoPCPermissionController@EditNodals');
   Route::post('/editnodals','Admin\CeoPCPermissionController@EditNodals');
   Route::post('/nodalstatus','Admin\CeoPCPermissionController@EditNodalsStatus');
   ######################end Permission routes
   
   // ROUTES STARTS

    //CEO COUNTING STATUS
    Route::get('/CountingStatus', 'Admin\PCCeoReportNewController@CountingStatus'); 
    //CEO COUNTING STATUS EXCEL
    Route::get('/CountingStatusExcel', 'Admin\PCCeoReportNewController@CountingStatusExcel'); 
    //CEO COUNTING STATUS PDF
    Route::get('/CountingStatusPdf', 'Admin\PCCeoReportNewController@CountingStatusPdf'); 
    //CEO ELECTION SCHEDULE
    Route::get('/CeoElectionSchedule', 'Admin\PCCeoReportNewController@CeoElectionSchedule');
    //CEO ELECTION SCHEDULE EXCEL REPORT
    Route::get('/CeoElectionScheduleExcel', 'Admin\PCCeoReportNewController@CeoElectionScheduleExcel');  
     //CeoCustomReportFilter
    Route::match(array('GET','POST'),'/CeoCustomReportFilter/', 'Admin\PCCeoReportNewController@CeoCustomReportFilter');
    //CEO CUSTOM REPORT FILTER GET 
    Route::get('/CeoCustomReportFilterGet/{ScheduleList?}', 'Admin\PCCeoReportNewController@CeoCustomReportFilterGet');
    //CCEO CUSTOM REPORT FILTER GET EXCEL
    Route::get('/CeoCustomReportFilterGetExcel/{ScheduleList?}', 'Admin\PCCeoReportNewController@CeoCustomReportFilterGetExcel');
	
	//POLL TURN OUR ROUTES STARTS
    Route::get('/PcCeoPSElectoralDefinalzied','Admin\PCCeoReportNewController@PcCeoPSElectoralDefinalzied');
    Route::post('/PcCeoPSElectoralDefinalziedUpdate','Admin\PCCeoReportNewController@PcCeoPSElectoralDefinalziedUpdate');
    Route::get('/PcCeoEstimatePollTurnoutPc','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutPc');
    Route::get('/PcCeoEstimatePollTurnoutPcExcel','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutPcExcel');
    Route::get('/PcCeoEstimatePollTurnoutPcPdf','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutPcPdf');
    Route::get('/PcCeoEstimatePollTurnoutAc','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutAc');
    Route::get('/PcCeoEstimatePollTurnoutAcExcel','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutAcExcel');
    Route::get('/PcCeoEstimatePollTurnoutAcPdf','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutAcPdf');

    
    Route::get('/PcCeoEstimatePollTurnoutMissedAc','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutMissedAc');
    Route::get('/PcCeoEstimatePollTurnoutMissedAcExcel','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutMissedAcExcel');
    Route::get('/PcCeoEstimatePollTurnoutMissedAcPdf','Admin\PCCeoReportNewController@PcCeoEstimatePollTurnoutMissedAcPdf');


    Route::get('/PcCeoPollComparison','Admin\PCCeoReportNewController@PcCeoPollComparison');
    Route::get('/PcCeoPollComparisonExcel','Admin\PCCeoReportNewController@PcCeoPollComparisonExcel');
    Route::get('/PcCeoPollComparisonPdf','Admin\PCCeoReportNewController@PcCeoPollComparisonPdf');
    

    Route::get('/PcCeoMissedAc','Admin\PCCeoReportNewController@PcCeoMissedAc');
    Route::get('/PcCeoMissedAcExcel','Admin\PCCeoReportNewController@PcCeoMissedAcExcel');
    Route::get('/PcCeoMissedAcPdf','Admin\PCCeoReportNewController@PcCeoMissedAcPdf');
	
	// Rocky Code Here 
	Route::get('/PcCeoMissedModifyAc','Admin\PCCeoReportNewController@PcCeoMissedModifyAc');
	Route::POST('/enable-modification-acs','Admin\PCCeoReportNewController@enbale_modified_acs');
	Route::POST('/enable-missed-acs','Admin\PCCeoReportNewController@enableAcs');
	
	// Rocky Code Ended Here
	
	//END OF POLL STARTS
    Route::get('/PcCeoCloseOfPoll/', 'Admin\PCCeoReportNewController@PcCeoCloseOfPoll');
    Route::get('/PcCeoCloseOfPollExcel/', 'Admin\PCCeoReportNewController@PcCeoCloseOfPollExcel');
    Route::get('/PcCeoCloseOfPollPdf/', 'Admin\PCCeoReportNewController@PcCeoCloseOfPollPdf');
    //END OF POLL ENDS

    //END OF POLL STARTS
    Route::get('/PcCeoEndOfPoll/', 'Admin\PCCeoReportNewController@PcCeoEndOfPoll');
    Route::get('/PcCeoEndOfPollExcel/', 'Admin\PCCeoReportNewController@PcCeoEndOfPollExcel');
    Route::get('/PcCeoEndOfPollPdf/', 'Admin\PCCeoReportNewController@PcCeoEndOfPollPdf');
    Route::get('/PcCeoEndOfPollAc/', 'Admin\PCCeoReportNewController@PcCeoEndOfPollAc');
    Route::get('/PcCeoEndOfPollAcExcel/', 'Admin\PCCeoReportNewController@PcCeoEndOfPollAcExcel');
    Route::get('/PcCeoEndOfPollAcPdf/', 'Admin\PCCeoReportNewController@PcCeoEndOfPollAcPdf');
    //END OF POLL ENDS

    
    //POLL TURN OUR ROUTES ENDS


   // ROUTES ENDS
   //SACHCHIDANAND ROUTES STARTS

    //CEO END OF POLL FINALIZE STATUS
     Route::get('/end-of-poll-finalize', 'Admin\PCCeoturnoutController@index'); 
     Route::POST('/veryfyend-of-poll-finalize', 'Admin\PCCeoturnoutController@veryfyend_of_poll_finalize'); 
    //CEO ELECTION SCHEDULE
     //----------------------------Divya-------------------------------------------//
    
    Route::get('/reportceo','Admin\ReportCeoController@reportceo');
    
    Route::post('/reportdates','Admin\ReportCeoController@reportdates');
    
    Route::get('/getDistrictsval','Admin\ReportCeoController@getDistrictsval');
	Route::get('/ceoreport/','Admin\RawPermissionController@ceoreport');
	Route::get('/partywise','Admin\ReportCeoController@partywise');
	Route::get('/permissiontype','Admin\ReportCeoController@permissiontype');
	Route::get('/districtvalue','Admin\ReportCeodistrictController@districtvalue');
	Route::post('/reportdate','Admin\ReportCeodistrictController@reportdate');
	Route::get('/rawreport','Admin\CeoRawPermissionController@rawreport');
	Route::post('/rawreportdate','Admin\CeoRawPermissionController@rawreportdate');

  
  //----------------------------Divya-------------------------------------------//
  
   ##########Expendature Routes include here ###################
  include("web_ceoexp.php");
  ##########Expendature Routes include here ###################
  
});  
######################### REPORT By  Mayank ############################
Route::group(['prefix' => 'pcdeo', 'as' => 'pcdeo::', 'middleware' => ['auth:admin', 'auth']], function(){
	
  Route::get('/dashboard', 'Admin\PCDeoController@dashboard');
  Route::get('/', 'Admin\PCDeoController@dashboard');
  Route::get('/datewisereport', 'Admin\PCDeoController@datewisereport')->name('datewisereport'); 
  Route::POST('/range-datewisereport', 'Admin\PCDeoController@datewisereport_range');
  Route::get('/reportspdfview/{date}/{consti}','Admin\PCDeoController@reportspdfview')->name('reportspdfview');
  Route::get('/reportexcelview/{date}/{consti}','Admin\PCDeoController@reportexcelview')->name('reportexcelview');
  
   Route::get('/changepassword', 'Admin\PCDeoController@changePassword');
   Route::post('/changepassword', 'Admin\PCDeoController@changePasswordStore');  
   Route::get('/officer-details', 'Admin\PCDeoController@officerList');
   Route::get('/officer-profile/{id}', 'Admin\PCDeoController@officerProfileUpdate');
   Route::post('/officer-profile', 'Admin\PCDeoController@officerProfileUpdate');
   Route::POST('/updateuser', 'Admin\PCDeoController@updateuser');
   ###################### permission Routes strat
    Route::get('/agentCreation','Admin\DeoPCPermissionController@AgentCreation');
   Route::post('/addagent','Admin\DeoPCPermissionController@AddAgent');
   Route::get('/viewagent','Admin\DeoPCPermissionController@ViewAgent');
   Route::get('/editagent/{id}','Admin\DeoPCPermissionController@EditAgent');
   Route::post('/editagent','Admin\DeoPCPermissionController@EditAgent');
   Route::post('/agentstatus','Admin\DeoPCPermissionController@EditAgentStatus');
   Route::get('/permissioncount','Admin\DeoPCPermissionController@PermissionCount');
   Route::post('/permissioncountdetails','Admin\DeoPCPermissionController@PermissionCountDetails');
   Route::get('/permissiondetailsview/{id}/{loc_id}/{status}','Admin\DeoPCPermissionController@PermissionDetailsView');
   Route::get('/generate-pdf/{id}','Admin\DeoPCPermissionController@generatePDF');
   Route::post('getallacpermission','Admin\DeoPCPermissionController@GetAllACPermission');
   Route::post('getAllAC','Admin\DeoPCPermissionController@getAllAC');
    Route::get('/addps','Admin\DeoPCPermissionController@AddPS');
   Route::post('/AddPSData','Admin\DeoPCPermissionController@AddPSData');
   Route::get('/viewps','Admin\DeoPCPermissionController@ViewPS');
   Route::get('/editps/{id}','Admin\DeoPCPermissionController@EditPS');
   Route::post('/editps','Admin\DeoPCPermissionController@EditPS');
   Route::post('/getallacps','Admin\DeoPCPermissionController@getallACPS');
   Route::get('/addauthority','Admin\DeoPCPermissionController@AddAuthority');
   Route::post('/addauthoritydata','Admin\DeoPCPermissionController@AddAuthorityData');
   Route::get('/viewauthority','Admin\DeoPCPermissionController@ViewAuthority');
   Route::get('/editauthority/{id}','Admin\DeoPCPermissionController@EditAuthority');
   Route::post('/editauthority','Admin\DeoPCPermissionController@EditAuthority');
   Route::post('/authoritystatus','Admin\DeoPCPermissionController@EditAuthorityStatus');
   Route::post('/getallacauthority','Admin\DeoPCPermissionController@getallACAuthority');
   Route::post('/getallacloc','Admin\DeoPCPermissionController@getallACloc');
   Route::post('/getPS','Admin\DeoPCPermissionController@getPS');
   Route::post('/getlocation','Admin\DeoPCPermissionController@getLocation');
   
   
   
   Route::get('/allmasters', 'Admin\DeoPCPermissionController@allMasters');
   Route::get('/offlinePermission','Admin\DeoPCPermissionController@OfflinePermission');
   Route::post('/offlinePermission','Admin\DeoPCPermissionController@OfflinePermission');
   Route::post('/UserDetails','Admin\DeoPCPermissionController@UserDetails');
   Route::post('/getUserDetails','Admin\DeoPCPermissionController@getUserDetails');
   Route::get('/allPermissionRequest','Admin\DeoPCPermissionController@AllPermissionRequest')->name('test.p');
   Route::get('/getAcceptpermissiondetails/{id}','Admin\DeoPCPermissionController@getpermissiondetails');
   Route::get('/getpermissiondetails/{id}','Admin\DeoPCPermissionController@getpermissiondetails');
   Route::post('/getpermissiondetailsview','Admin\DeoPCPermissionController@getpermissiondetailsview');
   Route::post('/uploadnodaldoc','Admin\DeoPCPermissionController@UploadNodaldoc');
   Route::post('/updateaction','Admin\DeoPCPermissionController@UpdateAction');
  
   Route::get('/addpermission','Admin\DeoPCPermissionController@AddPermission');
   Route::post('/AddPermissionData','Admin\DeoPCPermissionController@AddPermissionData');
   Route::get('/viewpermsn','Admin\DeoPCPermissionController@ViewPerms');
   Route::get('/editpermsn/{id}','Admin\DeoPCPermissionController@EditPrmsn');
   Route::post('/editpermsn','Admin\DeoPCPermissionController@EditPrmsn');
   Route::get('/addlocation','Admin\DeoPCPermissionController@AddLocation');
   Route::post('/AddLocationinsert','Admin\DeoPCPermissionController@AddLocationinsert');
   Route::get('/viewaddlocation','Admin\DeoPCPermissionController@viewaddlocation');
   Route::get('/locationeditpermsn/{id}','Admin\DeoPCPermissionController@locationeditpermsn');
   Route::post('/updateLocationval','Admin\DeoPCPermissionController@updateLocationval');
   Route::post('/getalldistrict','Admin\DeoPCPermissionController@getalldistrict');
   //load map
   Route::get('/getlocat', 'Admin\DeoPCPermissionController@getlocationList');
   Route::get('/getlatl', 'Admin\DeoPCPermissionController@getlatlongs');
   ######################end Permission routes
   
   //----------------------------Divya-------------------------------------------//
    
	Route::get('/reportdeo','Admin\ReportDeoController@reportdeo');
   
	Route::post('/reportdates','Admin\ReportDeoController@reportdates');
	
	Route::get('/partywise','Admin\ReportDeoController@partywise');
	
Route::get('/permissiontype','Admin\ReportDeoController@permissiontype');
	
Route::get('/permissionraw','Admin\ReportDeoController@permissionraw');
//----------------------------Divya-------------------------------------------//
  
}); 
############################## End DEO ######################################
});   // end of admin Session
 

// end of Sachchidanand  this mid not maintain your file  thanks getelection 
//sachchidanand created   ###########   MAIN Site #######################

Route::group(['middleware' => 'usersession'], function () {   // check session here  usersession sachchida

Route::group(['prefix' => '', 'as' => '', 'middleware' => ['auth:web', 'auth']], function(){
Route::get('permission', [ 'as' => 'permission', 'uses' => 'Politicalparty\permissionController@index']);
Route::get('total',[ 'as' => 'total', 'uses' =>'Politicalparty\permissionController@totalacceptpermission']);
Route::get('rejected',[ 'as' => 'rejected', 'uses' =>'Politicalparty\permissionController@rejectedpermission']);
Route::get('pending',[ 'as' => 'pending', 'uses' =>'Politicalparty\permissionController@pendingpermission']);
Route::get('applied',[ 'as' => 'applied', 'uses' =>'Politicalparty\permissionController@appliedpermission']);
Route::get('detaildata/{data}','Politicalparty\permissionController@detaildata');
Route::get('view&update','Politicalparty\permissionController@viewupdate');
Route::get('district/{state_id}','Politicalparty\permissionController@getdistrict');
Route::get('ac/{state_id}/{dis_id}','Politicalparty\permissionController@getac');
Route::get('policestation/{state_id}/{ac_id}','Politicalparty\permissionController@getpolicestation');
Route::get('location/{state_id}/{dis_id}/{ac_id}','Politicalparty\permissionController@getlocation');
Route::get('editcreate/{insert_id}','Politicalparty\permissionController@preview');

Route::get('create', 'Politicalparty\permissionController@create');
Route::post('getSelectDetails', 'Politicalparty\permissionController@getSelectDetails');

Route::post('Applypermission', 'Politicalparty\permissionController@store');
Route::Post('update','Politicalparty\permissionController@update');
// Route::get('Applypermission', 'Politicalparty\permissionController@check');//duplicate

Route::get('politicalparty/getlatlong', 'Politicalparty\permissionController@getlatlongs');
Route::get('mapindex', 'Politicalparty\mapController@mapindex');
Route::get('/politicalparty/getDistricts', 'Politicalparty\mapController@getDistricts');
Route::get('/politicalparty/getAcs', 'Politicalparty\permissionController@getACList');
Route::get('politicalparty/getlocations', 'Politicalparty\permissionController@getlocationList');
Route::get('/politicalparty/getlocations', 'Politicalparty\mapController@getlocationList');

Route::get('/politicalparty/getlatlong', 'Politicalparty\mapController@getlatlongs');
Route::post('/politicalparty/select-ajax/{sid}', 'Politicalparty\mapController@selectAjax');
Route::get('/politicalparty/getAcs', 'Politicalparty\mapController@getACList');
Route::post('/politicalparty/saveprofile', 'Politicalparty\mapController@saveprofile'); 
Route::get('/savecalendarvalue', 'Politicalparty\mapController@savecalendarvalue');
Route::get('/update profile','Politicalparty\permissionController@updateprofile');
Route::get('candidatelogout','HomeController@logout');
// route end by manish
// route start by divya
Route::get('/roletype','Politicalparty\permissionController@roletype');
Route::post('/permissionrole','Politicalparty\permissionController@permissionrole');
Route::get('/profile','Politicalparty\permissionController@profile');
Route::get('/getDistrictsval', 'Politicalparty\permissionController@getDistrictsval');
Route::get('/getACListsval', 'Politicalparty\permissionController@getACListsval');
Route::post('/addprofile','Politicalparty\permissionController@addprofile');
Route::get('/getpermissiondetails/{id}/{status}/{location}','Politicalparty\permissionController@getpermissiondetails');

// Extra Pages for Candidate 
Route::get('/Privacy Policy','Politicalparty\permissionController@Privacy');
Route::get('/Content Copyright','Politicalparty\permissionController@Content');
Route::get('/Terms Condition','Politicalparty\permissionController@Terms');
Route::get('/Abbreviations','Politicalparty\permissionController@Abbreviations');

//Route::get('/permissiondistrict/{st}','Politicalparty\permissionController@permissiondistrict');
//Route::get('/permissionAC/{stateID}/{districtID}','Politicalparty\permissionController@permissionAC');
//Route::get('/policeAC/{stateID}/{acID}','Politicalparty\permissionController@policeAC');
//Route::get('/Download Permission/{status}/{id}/{location}','Politicalparty\permissionController@downloadprint');

// get election date route
Route::get('/permissiondistrict/{st}','Politicalparty\permissionController@permissiondistrict');
Route::get('/policeAC/{stateID}/{acID}','Politicalparty\permissionController@policeAC');
Route::get('/Download Permission/{status}/{id}/{location}','Politicalparty\permissionController@downloadprint');
Route::get('/permissionpc/{sid}','Politicalparty\permissionController@permissionpc');
Route::get('/permissionAC/{stateID}/{districtID}/{pc}','Politicalparty\permissionController@permissionAC');
Route::get('/getpc/{sid}/{acic}/{distno}','Politicalparty\permissionController@getpconac');
Route::get('/getpcname/{stateID}/{districtID}','Politicalparty\permissionController@getpcname');
Route::get('/getpollday/{SID}/{pc_id}','Politicalparty\permissionController@getpollday');
Route::get('/datevalidation/{StateId}','Politicalparty\permissionController@statedatevalidation');
});
});
////////////////////Feedback Survey Routes by ChanderKant
/* 
Route::get('/feedback', 'Admin\feedback\FeedbackController@index');
Route::post('/feedback', 'Admin\feedback\FeedbackController@commonResponse');
Route::post('/ajaxGetModule', 'Admin\feedback\FeedbackController@ajaxGetModule');
 */
////////////////////END of Feedback Survey Routes by ChanderKant



Route::group(['prefix' => 'nfd', 'middleware' => ['auth:admin', 'auth']], function () {
  Route::post('send-otp', 'Admin\Nfd\NominationController@send_otp');
  Route::post('verify_otp', 'Admin\Nfd\NominationController@verify_otp');
  Route::get('dashboard', 'Admin\Nfd\DashboardController@dashboard');
  Route::group(['prefix' => 'nomination'], function () {
    Route::post('upload', 'Admin\Nfd\NominationController@upload_files');
    Route::post('upload-affidavit', 'Admin\Nfd\NominationController@upload_affidavit');
    Route::get('/detail/{id}', 'Admin\Nfd\NominationController@view_nomination');
    Route::get('/download/{id}', 'Admin\Nfd\NominationController@download_nomination');
    Route::get('/', 'Admin\Nfd\OtpController@get_otp');
    Route::get('/list', 'Admin\Nfd\NominationController@get_nomination_by_mobile');
  //  Route::get('/apply-nomination-step-1', 'Admin\Nfd\NominationController@apply_nomination_step_1');
    Route::any('/apply-nomination-step-1/post', 'Admin\Nfd\NominationController@save_step_1');
    Route::get('/apply-nomination-step-2', 'Admin\Nfd\NominationController@apply_nomination_step_2');
    Route::any('/apply-nomination-step-2/post', 'Admin\Nfd\NominationController@save_step_2');
    Route::get('/apply-nomination-step-2/{id}', 'Admin\Nfd\NominationController@apply_nomination_step_2');
    Route::get('/apply-nomination-step-3', 'Admin\Nfd\NominationController@apply_nomination_step_3');
    Route::any('/apply-nomination-step-3/post-part-1', 'Admin\Nfd\NominationController@save_step_3');
    Route::get('/apply-nomination-step-4', 'Admin\Nfd\NominationController@apply_nomination_step_4');
    Route::any('/apply-nomination-step-4/post', 'Admin\Nfd\NominationController@save_step_4');
    Route::get('/apply-nomination-step-5', 'Admin\Nfd\NominationController@apply_nomination_step_5');
    Route::any('/apply-nomination-step-5/post', 'Admin\Nfd\NominationController@save_step_5');
    Route::get('/apply-nomination-step-6', 'Admin\Nfd\NominationController@apply_nomination_step_6');
    Route::any('/apply-nomination-step-6/post', 'Admin\Nfd\NominationController@save_step_6');
    Route::get('/apply-nomination-finalize', 'Admin\Nfd\NominationController@apply_nomination_finalize');
    Route::any('/apply-nomination-finalize/post', 'Admin\Nfd\NominationController@save_nomination_finalize');
  });
});

Route::group(['middleware' => ['auth:web', 'auth', 'usersession']], function () {
  Route::get('dashboard-nomination-new', 'TempHomeController@dashboard');
  //Route::get('mlc-nomination-dashboard', 'HomeController@mlc_dashboard');
  //for MLA
  Route::get('/first-login-user-view', 'TempHomeController@first_login_user_view');
});

Route::any('/checkdata', 'PaymentController@checkdata');
Route::get('search-by-epic-cdac-new', 'Admin\Common\CommonController@search_by_epic_cdac');
Route::any('/payment-return-handle', 'PaymentController@payment_return_handle');
Route::any('/payment-verification', 'PaymentController@payment_verification');

