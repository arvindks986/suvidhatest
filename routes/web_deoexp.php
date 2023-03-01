<?php


    Route::match(array('GET','POST'),'/statusExpdashboard', 'Expenditure\PCDeoExpenditureController@statusdashboard');                                             
 
    Route::get('/reports','Expenditure\PCDeoExpenditureController@trackingReport');
    Route::get('/trackingReportprint','Expenditure\PCDeoExpenditureController@trackingReportprint');
    Route::get('/pendingdataentry/{pc}', 'Expenditure\PCDeoExpenditureController@getpendingcandidateList');
    Route::get('/partiallypending/{pc}', 'Expenditure\PCDeoExpenditureController@getpartiallypendingcandidateList');
    



?>