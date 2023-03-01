<?php
Route::group(['prefix' => 'pcceo', 'as' => 'pcceo::', 'middleware' => ['auth:admin', 'auth']], function(){
  Route::get('/preMediaDashboard', 'Admin\CeoMediaController@dashboard')->name('preCertDashboard'); 
  Route::get('/', 'Admin\CeoMediaController@dashboard');
  Route::get('/applicantlist','Admin\CeoMediaController@listApplicants');
  Route::get('/changestatusapplicantlist','Admin\CeoMediaController@changestatusapplicantlist');
  Route::get('/finalizeApplicantList','Admin\CeoMediaController@finalizeApplicantList');
  Route::get('/otp_sender', 'Admin\CeoMediaController@SendOTP');
  Route::post('/changeCommentStatus', 'Admin\CeoMediaController@changeCommentStatus');
  Route::get('/otp_verify', 'Admin\CeoMediaController@otp_verify');
  Route::get('/printCertificate/{applicantid}','Admin\CeoMediaController@printCertificate');
  Route::get('/pdf_genertor/{applicantid}', 'Admin\CeoMediaController@pdfgenertor');
  Route::get('/{applicantid}/applicationDetails','Admin\CeoMediaController@applicationDetails')->name('applicationDetails');
  Route::get('/getReferenceDetail','Admin\CeoMediaController@getReferenceDetail')->name('getReferenceDetail');
  
  /********************* Agent Url ***********************/
	Route::get('/application', 'Admin\ceoAgentMediaController@application');
	Route::post('/application', 'Admin\ceoAgentMediaController@insertApplication')->name('ceoagent.application');
	Route::post('/saveCandKyc', 'Admin\ceoAgentMediaController@saveCandKyc')->name('ceoagent.saveCandKyc');
	Route::post('/saveCandAppDetails', 'Admin\ceoAgentMediaController@saveCandAppDetails')->name('ceoagent.saveCandAppDetails');
	Route::post('/saveCandAdDetail', 'Admin\ceoAgentMediaController@saveCandAdDetail')->name('ceoagent.saveCandAdDetail');
	Route::get('/getPartyDistricts', 'Admin\ceoAgentMediaController@getPartyDistricts');
	Route::get('/getDistricts', 'Admin\ceoAgentMediaController@getDistricts');
	Route::get('/getParty', 'Admin\ceoAgentMediaController@getpartyByPartyType');
	Route::get('/getConst', 'Admin\ceoAgentMediaController@getConst');
	Route::get('/getConstPC', 'Admin\ceoAgentMediaController@getConstPC');
	Route::get('/applicationListing','Admin\ceoAgentMediaController@applicationListing')->name('ceoagent.applicationListing');
	//Route::get('preview-form/3',function(){return view()});
	Route::get('/printReceipt/{applicantid}','Admin\ceoAgentMediaController@printReceipt');
	Route::get('/preview-form/{applicantid}','Admin\ceoAgentMediaController@thankYou');
	Route::get('/getBenifitorDistricts', 'Admin\ceoAgentMediaController@getBenifitorDistricts');
	Route::get('/application-update/{applicantid}','Admin\ceoAgentMediaController@edit');
	Route::post('/application-update/{applicantid}','Admin\ceoAgentMediaController@updateApplication');
	Route::get('/getChannelList','Admin\ceoAgentMediaController@getChannelList');
	Route::get('/receipt_pdf/{applicantid}', 'Admin\ceoAgentMediaController@pdfreceiptgenertor');
}); 


?>