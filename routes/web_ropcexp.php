<?php
//created by Niraj for expendature on ROPC Level




Route::get('/candidateList', 'Expenditure\ROPCExpenditureController@getcandidateList');
Route::get('/ROExpdashboard', 'Expenditure\ROPCExpenditureController@dashboard');
Route::get('/dataentryStart', 'Expenditure\ROPCExpenditureController@candidateListBydataentryStart');
Route::get('/finalizeData', 'Expenditure\ROPCExpenditureController@candidateListByfinalizeData');
Route::get('/logedaccount', 'Expenditure\ROPCExpenditureController@candidateListBylogedaccount');
Route::get('/notintime', 'Expenditure\ROPCExpenditureController@candidateListBynotintime');
Route::get('/formatedefects', 'Expenditure\ROPCExpenditureController@candidateListByformatedefects');
Route::get('/ronotagree', 'Expenditure\ROPCExpenditureController@candidateListByronotagree');
Route::get('/understatedexpense', 'Expenditure\ROPCExpenditureController@candidateListByunderstatedexpense');
Route::get('/dataentrydefects', 'Expenditure\ROPCExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfund', 'Expenditure\ROPCExpenditureController@candidateListBypartyfund');
Route::get('/othersfund', 'Expenditure\ROPCExpenditureController@candidateListByothersfund');
Route::get('/exeedceiling', 'Expenditure\ROPCExpenditureController@candidateListByexeedceiling');
//dashboard current status
Route::get('/statusExpdashboard', 'Expenditure\ROPCExpenditureController@statusdashboard');
Route::get('/pendingdataentry', 'Expenditure\ROPCExpenditureController@getpendingcandidateList');
Route::get('/partiallypending', 'Expenditure\ROPCExpenditureController@getpartiallypendingcandidateList');
Route::get('/filedData', 'Expenditure\ROPCExpenditureController@candidateListByfiledData');
Route::get('/defaulter', 'Expenditure\ROPCExpenditureController@getdefaultercandidateList');
Route::get('/finalbyceo', 'Expenditure\ROPCExpenditureController@candidateListfinalbyCEO');
Route::get('/finalbyeci', 'Expenditure\ROPCExpenditureController@candidateListfinalbyECI');
Route::get('/rotracking-status', 'Expenditure\ROPCExpenditureController@tarcking');

//MIS Report
Route::get('/mis-officer', 'Expenditure\ROPCExpenditureController@getOfficersmis');
Route::get('/mis-candidate', 'Expenditure\ROPCExpenditureController@getCandidatemis');


//Notice Section 

Route::get('/noticeatdeo', 'Expenditure\ROPCExpenditureController@getnoticeatDEO');
Route::get('/noticeatdeoEXL', 'Expenditure\ROPCExpenditureController@getnoticeatDEOEXL');

###################end Niraj rout ###################


//manoj start here  
Route::get('/viewbyid/{id}', 'Expenditure\ROPCExpenditureController@viewById')->name('viewbyid');
Route::post('/deoForm', 'Expenditure\ROPCExpenditureController@deoForm')->name('deoForm');
//Route::post('/defectdeoform', 'Expenditure\ROPCExpenditureController@defectdeoform')->name('defectform');
Route::get('/deoformview/{id}','Expenditure\ROPCExpenditureController@deoFormView');
Route::post('/updateAccountDeoForm','Expenditure\ROPCExpenditureController@updateAccountDeoForm');
Route::post('/updateDefectDeoForm','Expenditure\ROPCExpenditureController@updateDefectDeoForm');
// for graph 
    Route::get('/ExpDataEntrySummaryReport', 'Expenditure\ROPCExpenditureController@ExpDataEntrySummaryReport');
    Route::get('/summary-graph/{id}', 'Expenditure\ROPCExpenditureController@getSummaryGraphData');
     // graph for individual start here
      Route::get('/candidateListBydataentryStartGraph', 'Expenditure\ROPCExpenditureController@candidateListBydataentryStartGraph');
      Route::get('/candidateListByfinalizeDatagraph', 'Expenditure\ROPCExpenditureController@candidateListByfinalizeDatagraph');
      Route::get('/logedaccountgraph', 'Expenditure\ROPCExpenditureController@candidateListBylogedaccountgraph');
      Route::get('/notintime', 'Expenditure\ROPCExpenditureController@candidateListBynotintime');
     Route::get('/formatedefectsgraph', 'Expenditure\ROPCExpenditureController@candidateListByformatedefectsgraph');
Route::get('/ronotagree', 'Expenditure\ROPCExpenditureController@candidateListByronotagree');
Route::get('/understatedexpensegraph', 'Expenditure\ROPCExpenditureController@candidateListByunderstatedexpense');
//Route::get('/dataentrydefects', 'Expenditure\ROPCExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfundgraph', 'Expenditure\ROPCExpenditureController@candidateListBypartyfundgraph');
Route::get('/othersfundgraph', 'Expenditure\ROPCExpenditureController@candidateListByothersfundgraph');
// status 
Route::get('/getpendingcandidateListgraph', 'Expenditure\ROPCExpenditureController@getpendingcandidateListgraph');
Route::get('/getpartiallypendingcandidateListgraph', 'Expenditure\ROPCExpenditureController@getpartiallypendingcandidateListgraph');
Route::get('/getdefaultercandidateListgraph', 'Expenditure\ROPCExpenditureController@getdefaultercandidateListgraph');

      // graph for individual end here
     // tracking start here 
    Route::get("/tracking","Expenditure\ROPCExpenditureController@getTrackingByROUserId");
    Route::get('/getscrutinyreport','Expenditure\ROPCExpenditureController@getscrutinyreport');
    Route::get('/getprofile','Expenditure\ROPCExpenditureController@getprofile');
        Route::get('/generatePDF/{id}','Expenditure\ROPCExpenditureController@generatePDF');
  Route::get('/confirmReport','Expenditure\ROPCExpenditureController@confirmReport');
  Route::get('/printScrutinyReport/{id}',array('as'=>'printScrutinyReport','uses'=>'Expenditure\ROPCExpenditureController@printScrutinyReport'));
Route::get('/GetProfileRO','Expenditure\ROPCExpenditureController@GetProfileRO');
   Route::get('/printTrackingStatus/{id}','Expenditure\ROPCExpenditureController@printTrackingStatus');

    // tracking end here
    Route::post('/update_understated_file1','Expenditure\ROPCExpenditureController@update_understated_file1');
    Route::post('/update_understated_file2','Expenditure\ROPCExpenditureController@update_understated_file2');
    Route::post('/update_understated_file4','Expenditure\ROPCExpenditureController@update_understated_file4');
    Route::post('/uploadsigned','Expenditure\ROPCExpenditureController@uploadsigned');
     Route::post('/updateNoticeFile','Expenditure\ROPCExpenditureController@updateNoticeFile');
    Route::get('/tracking-status','Expenditure\ROPCExpenditureController@tracking_status');
     Route::get('/reports','Expenditure\ROPCExpenditureController@trackingReport');
      Route::get('/trackingReportprint','Expenditure\ROPCExpenditureController@trackingReportprint');
   Route::get('/view/{id}','Expenditure\ROPCExpenditureController@viewByCandidateId');
   // ECRP
   Route::get('/ecrp-registration', 'Expenditure\ROPCExpenditureController@ecrpRegistration');
Route::post('/saveEcrpRegistration', 'Expenditure\ROPCExpenditureController@saveEcrpRegistration');
Route::get('/getdistrictsbystate', 'Expenditure\ROPCExpenditureController@getdistrictsbystate');
Route::post('/assignEcrpRegistration', 'Expenditure\ROPCExpenditureController@assignEcrpRegistration');
Route::get('/getEcrpList', 'Expenditure\ROPCExpenditureController@getEcrpList');
Route::get('/getParty', 'Expenditure\ROPCExpenditureController@getParty');
Route::get('/getECRPCandidateList/{stcode}', 'Expenditure\ROPCExpenditureController@getECRPCandidateList');
Route::get('/getFiledStatementList', 'Expenditure\ROPCExpenditureController@getFiledStatementList');
   // end ECRP
// Manoj end here
// manish start here
Route::post('/updateUnderstatedDetail', 'Expenditure\ROPCExpenditureController@updateUnderstatedDetail'); 
Route::post('/UpdateSourceFundData', 'Expenditure\ROPCExpenditureController@UpdateSourceFundData'); 
Route::post('/UpdatePartyFundData', 'Expenditure\ROPCExpenditureController@UpdatePartyFundData'); 
Route::post('/SaveExpenseData', 'Expenditure\ROPCExpenditureController@SaveExpenseData'); 
Route::post('/DeleteSourceFundData','Expenditure\ROPCExpenditureController@DeleteSourceFundData');
Route::post('/DeleteUnderStatedData','Expenditure\ROPCExpenditureController@DeleteUnderStatedData');
Route::post('/FinalizedData','Expenditure\ROPCExpenditureController@FinalizedData');
// manish end here
 


//////abstrac form ///////////////
Route::get('/candidateList_abstract', 'Expenditure\ROPCExpenditureController@candidateList_abstract');
Route::get('/annuxure/{id}', 'Expenditure\ROPCExpenditureController@annuxure');
Route::post('/SaveAnnuxureData', 'Expenditure\ROPCExpenditureController@SaveAnnuxureData');



/////////////////////////////tracking////////
Route::get('/GetTrackingReportData', 'Expenditure\ROPCExpenditureController@GetTrackingReportData'); 
Route::get('/editExpenditureData/{id}', 'Expenditure\ROPCExpenditureController@editExpenditureData'); 
Route::post('/StoreMisExpenseReport', 'Expenditure\ROPCExpenditureController@StoreMisExpenseReport'); 
Route::get('/updateData', 'Expenditure\ROPCExpenditureController@updateData'); 
Route::get('/editExpenditureReport', 'Expenditure\ROPCExpenditureController@editExpenditureReport'); 

//
Route::get('/return', 'Expenditure\ROPCExpenditureController@getReturn');
Route::get('/non-return', 'Expenditure\ROPCExpenditureController@getNonReturn');
Route::get('/updateStatusReport', 'Expenditure\ROPCExpenditureController@updateStatusReport');