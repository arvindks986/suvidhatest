<?php
Route::group(['prefix' => 'pcdeo', 'as' => 'pcdeo::', 'middleware' => ['auth:admin', 'auth']], function(){
	
    Route::get('/preMediaDashboard', 'Admin\DeoMediaController@dashboard');
    Route::get('/', 'Admin\DeoMediaController@dashboard');
	Route::get('/applicantlist','Admin\DeoMediaController@listApplicants');
    Route::get('/changestatusapplicantlist','Admin\DeoMediaController@changestatusapplicantlist');
    Route::get('/finalizeApplicantList','Admin\DeoMediaController@finalizeApplicantList');
    Route::get('/otp_sender', 'Admin\DeoMediaController@SendOTP');
    Route::post('/changeCommentStatus', 'Admin\DeoMediaController@changeCommentStatus');
    Route::get('/otp_verify', 'Admin\DeoMediaController@otp_verify');
	Route::get('/printCertificate/{applicantid}','Admin\DeoMediaController@printCertificate');
    Route::get('/pdf_genertor/{applicantid}', 'Admin\DeoMediaController@pdfgenertor');
	Route::get('/{applicantid}/applicationDetails','Admin\DeoMediaController@applicationDetails')->name('applicationDetails');
    Route::get('/getReferenceDetail','Admin\DeoMediaController@getReferenceDetail')->name('getReferenceDetail');
    Route::get('/printStatus/{applicantid}','Admin\DeoMediaController@printStatus')->name('printStatus');
	
	
    /********************* Agent Url ***********************/
	Route::get('/application', 'Admin\deoAgentMediaController@application');
	Route::post('/application', 'Admin\deoAgentMediaController@insertApplication')->name('deoagent.application');
	Route::post('/saveCandKyc', 'Admin\deoAgentMediaController@saveCandKyc')->name('deoagent.saveCandKyc');
	Route::post('/saveCandAppDetails', 'Admin\deoAgentMediaController@saveCandAppDetails')->name('deoagent.saveCandAppDetails');
	Route::post('/saveCandAdDetail', 'Admin\deoAgentMediaController@saveCandAdDetail')->name('deoagent.saveCandAdDetail');
	Route::get('/getPartyDistricts', 'Admin\deoAgentMediaController@getPartyDistricts');
	Route::get('/getDistricts', 'Admin\deoAgentMediaController@getDistricts');
	Route::get('/getParty', 'Admin\deoAgentMediaController@getpartyByPartyType');
	Route::get('/getConst', 'Admin\deoAgentMediaController@getConst');
	Route::get('/getConstPC', 'Admin\deoAgentMediaController@getConstPC');
	Route::get('/getConstBenifitAC', 'Admin\deoAgentMediaController@getConst');
	Route::get('/getConstBenifitPC', 'Admin\deoAgentMediaController@getConstPC');
	Route::get('/applicationListing','Admin\deoAgentMediaController@applicationListing')->name('deoagent.applicationListing');
	Route::get('/printReceipt/{applicantid}','Admin\deoAgentMediaController@printReceipt');
	Route::get('/preview-form/{applicantid}','Admin\deoAgentMediaController@thankYou');
	Route::get('/getBenifitorDistricts', 'Admin\deoAgentMediaController@getBenifitorDistricts');
	Route::get('/application-update/{applicantid}','Admin\deoAgentMediaController@edit');
	Route::post('/application-update/{applicantid}','Admin\deoAgentMediaController@updateApplication');
	Route::get('/getChannelList','Admin\deoAgentMediaController@getChannelList');
	Route::get('/receipt_pdf/{applicantid}', 'Admin\deoAgentMediaController@pdfreceiptgenertor');
	Route::get('/getApplicantDistricts', 'Admin\deoAgentMediaController@getDistricts');
	Route::get('/pdf_genertor/{applicantid}', 'Admin\deoAgentMediaController@pdfgenertor');
	Route::get('/printCertificate/{applicantid}','Admin\deoAgentMediaController@printCertificate');
}); 
?>