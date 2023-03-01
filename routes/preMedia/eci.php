<?php
Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){
	Route::get('/AddAgent','Admin\eciAgentMediaController@agent');
	Route::post('/addagent', 'Admin\eciAgentMediaController@addagent');
	Route::get('/ViewAgent', 'Admin\eciAgentMediaController@ViewAgent');
	Route::get('/EditAgentView/{id}', 'Admin\eciAgentMediaController@EditAgentView');
	Route::post('/UpdateAgent/', 'Admin\eciAgentMediaController@UpdateAgent');
	Route::post('/agentstatus','Admin\eciAgentMediaController@EditAgentStatus');
}); 
?>