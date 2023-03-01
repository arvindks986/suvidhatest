<?php

//Media Login
Route::get('/media-login', 'UserMediaController@loginajaxRequest');
Route::post('/mediadologin','UserMediaController@loginajaxRequestPost');
Route::post('/mediaotpverification','UserMediaController@verifyOTP')->name('otp-verification');
Route::post('/mediauresendotp','UserMediaController@resendOTP')->name('resendotp');

//Route::get('/getDistricts', 'UserMediaController@getDistricts');
Route::group(['prefix' => 'media', 'as' => 'media::'], function(){
	Route::get('/dashboard', 'UserMediaController@dashboard');
	Route::get('/profile', 'UserMediaController@profile')->name('profile');
	Route::post('/profile', 'UserMediaController@validatechangeprofile')->name('profile');
	Route::get('/application', 'UserMediaController@application');
	Route::post('/application', 'UserMediaController@insertapplication')->name('user.application');
	Route::get('/{applicantid}/applicationDetails','UserMediaController@applicationDetails')->name('applicationDetails');
	Route::post('/saveCandKyc', 'UserMediaController@saveCandKyc')->name('user.saveCandKyc');
	Route::post('/saveCandAppDetails', 'UserMediaController@saveCandAppDetails')->name('user.saveCandAppDetails');
	Route::post('/saveCandAdDetail', 'UserMediaController@saveCandAdDetail')->name('user.saveCandAdDetail');
	Route::get('/getPartyDistricts', 'UserMediaController@getPartyDistricts');
	Route::get('/getDistricts', 'UserMediaController@getDistricts');
    Route::get('/getApplicantDistricts', 'UserMediaController@getDistricts');
    Route::get('/getConstBenifitAC', 'Admin\deoAgentMediaController@getConst');
	Route::get('/getConstBenifitPC', 'Admin\deoAgentMediaController@getConstPC');
    Route::get('/getParty', 'UserMediaController@getpartyByPartyType');
    Route::get('/getConst', 'UserMediaController@getConst');
	Route::get('/getConstPC', 'UserMediaController@getConstPC');
	Route::get('/applicationListing','UserMediaController@applicationListing')->name('applicationListing');
	Route::get('/pendingapplication','UserMediaController@getPendingApplication')->name('pendingapplication');
	Route::get('/printCertificate/{applicantid}','UserMediaController@printCertificate');
	Route::get('/printReceipt/{applicantid}','UserMediaController@printReceipt');
	Route::get('/thankyou/{applicantid}','UserMediaController@thankYou');
	Route::get('/getBenifitorDistricts', 'UserMediaController@getBenifitorDistricts');
	Route::get('/application-update/{applicantid}','UserMediaController@edit');
    Route::post('/application-update/{applicantid}','UserMediaController@updateApplication');
	Route::get('/getChannelList','UserMediaController@getChannelList');
	Route::get('/receipt_pdf/{applicantid}', 'UserMediaController@pdfreceiptgenertor');
	Route::get('/pdf_genertor/{applicantid}', 'UserMediaController@pdfgenertor');
	Route::post('/changeCommentStatus/{applicantid}','UserMediaController@AppealChangeStatus');
}); 
?>