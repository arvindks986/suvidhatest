<?php
//created by Niraj for expendature on ROPC Level


#########################start by Niraj #############################
Route::match(array('GET','POST'),'/CeoExpdashboard', 'Expenditure\PCCeoExpenditureController@dashboard');
Route::get('/dataentryStart/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBydataentryStart');
Route::get('/finalizeData/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByfinalizeData');
Route::get('/logedaccount/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBylogedaccount');
Route::get('/notintime/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBynotintime');
Route::get('/formatedefects/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByformatedefects');
Route::get('/ronotagree/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByronotagree');
Route::get('/understatedexpense/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByunderstatedexpense');
Route::get('/dataentrydefects/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfund/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBypartyfund');
Route::get('/othersfund/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByothersfund');
Route::get('/exeedceiling/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByexeedceiling');
Route::get('/getpcbystate', 'Expenditure\NotificationExpenditureController@getpclist'); 

//dashboard current status
Route::match(array('GET','POST'),'/statusExpdashboard', 'Expenditure\PCCeoExpenditureController@statusdashboard');
Route::get('/pendingdataentry/{pc}', 'Expenditure\PCCeoExpenditureController@getpendingcandidateList');
Route::get('/partiallypending/{pc}', 'Expenditure\PCCeoExpenditureController@getpartiallypendingcandidateList');
Route::get('/filedData/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByfiledData');
Route::get('/defaulter/{pc}', 'Expenditure\PCCeoExpenditureController@getdefaultercandidateList');
Route::get('/finalbyceo/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListfinalbyCEO');
Route::get('/finalbyeci/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListfinalbyECI');

//Notice Section 

Route::get('/noticeatceo/{pc}', 'Expenditure\PCCeoExpenditureController@getnoticeatCEO');
Route::get('/noticeatceoEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getnoticeatCEOEXL');

//MIS Report Date 21-08-2019
Route::match(array('GET','POST'),'/mis-officer', 'Expenditure\PCCeoExpenditureController@getOfficersmis');
Route::get('/OfficerMISEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getOfficersmisEXL');
Route::get('/OfficerMISPDF/{pc}', 'Expenditure\PCCeoExpenditureController@getOfficersmisPDF');

Route::get('/expallcandidate/{pc}', 'Expenditure\PCCeoExpenditureController@finalCandidateList');
Route::get('/expallcandidateEXL/{pc}', 'Expenditure\PCCeoExpenditureController@finalCandidateListEXL');
Route::get('/expallcandidatePDF/{pc}', 'Expenditure\PCCeoExpenditureController@finalCandidateListPDF');

Route::get('/expstartedcandidate/{pc}', 'Expenditure\PCCeoExpenditureController@getStartedcandidateMIS');
Route::get('/expstartedcandidateEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getStartedcandidateMISEXL');
Route::get('/expstartedcandidatePDF/{pc}', 'Expenditure\PCCeoExpenditureController@getStartedcandidateMISPDF');

Route::get('/expnotstarted/{pc}', 'Expenditure\PCCeoExpenditureController@getNotstartedMIS');
Route::get('/expnotstartedEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getNotstartedMISEXL');
Route::get('/expnotstartedPDF/{pc}', 'Expenditure\PCCeoExpenditureController@getNotstartedMISPDF');

Route::get('/expfinalbyDEO/{pc}', 'Expenditure\PCCeoExpenditureController@getfinalbyDEO');
Route::get('/expfinalbyDEOMISEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getfinalbyDEOMISEXL');
Route::get('/expfinalbyDEOMISPDF/{pc}', 'Expenditure\PCCeoExpenditureController@getfinalbyDEOMISPDF');


Route::get('/exppendingatro/{pc}', 'Expenditure\PCCeoExpenditureController@getcandidateListpendingatRO');
Route::get('/exppendingatroEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getcandidateListpendingatROEXL');
Route::get('/exppendingatroPDF/{pc}', 'Expenditure\PCCeoExpenditureController@getcandidateListpendingatROPDF');

Route::get('/exppendingatceo/{pc}', 'Expenditure\PCCeoExpenditureController@getcandidateListpendingatCEO');
Route::get('/exppendingatceoEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getcandidateListpendingatCEOEXL');
Route::get('/exppendingatceoPDF/{pc}', 'Expenditure\PCCeoExpenditureController@getcandidateListpendingatCEOPDF');

Route::get('/expnotintimecandidate/{pc}', 'Expenditure\PCCeoExpenditureController@getnotintimecandidateData');
Route::get('/expnotintimeCandidateMISEXL/{pc}', 'Expenditure\PCCeoExpenditureController@getnotintimecandidateDataEXL');
Route::get('/expnotintimeCandidateMISPDF/{pc}', 'Expenditure\PCCeoExpenditureController@getnotintimecandidateDataPDF');
#########################end by Niraj #############################

// manish end here
 // manoj start 
  // tracking start here 
    /////////////////////////////tracking////////
Route::get('/GetTrackingReportData', 'Expenditure\PCCeoExpenditureController@GetTrackingReportData'); 
Route::get('/editExpenditureData/{id}', 'Expenditure\PCCeoExpenditureController@editExpenditureData'); 
Route::post('/StoreMisExpenseReport', 'Expenditure\PCCeoExpenditureController@StoreMisExpenseReport'); 
Route::get('/updateData', 'Expenditure\PCCeoExpenditureController@updateData'); 
Route::get('/getscrutinyreport','Expenditure\PCCeoExpenditureController@getscrutinyreport');
Route::post('/saveComment','Expenditure\PCCeoExpenditureController@saveComment');
Route::get('/confirmReport','Expenditure\PCCeoExpenditureController@confirmReport');
Route::get('/generatePDF/{id}','Expenditure\PCCeoExpenditureController@generatePDF');



//for graph start here
Route::get('/candidateListBydataentryStartgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBydataentryStartgraph');
Route::get('/candidateListByfinalizeDatagraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByfinalizeDatagraph');
Route::get('/candidateListBylogedaccountgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBylogedaccountgraph');
Route::get('/candidateListBynotintimegraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBynotintimegraph');
Route::get('/candidateListByformatedefectsgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByformatedefectsgraph');
Route::get('/getpendingcandidateListgraph/{pc}', 'Expenditure\PCCeoExpenditureController@getpendingcandidateListgraph');
Route::get('/getpartiallypendingcandidateListgraph/{pc}', 'Expenditure\PCCeoExpenditureController@getpartiallypendingcandidateListgraph');
Route::get('/getdefaultercandidateListgraph/{pc}', 'Expenditure\PCCeoExpenditureController@getdefaultercandidateListgraph');
 
Route::get('/candidateListByunderstatedexpensegraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByunderstatedexpensegraph');
  
Route::get('/candidateListBypartyfundgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListBypartyfundgraph');
Route::get('/candidateListByothersfundgraph/{pc}', 'Expenditure\PCCeoExpenditureController@candidateListByothersfundgraph');
Route::get('/printScrutinyReport/{id}',array('as'=>'printScrutinyReport','uses'=>'Expenditure\PCCeoExpenditureController@printScrutinyReport'));
Route::get('/getprofile','Expenditure\PCCeoExpenditureController@getprofile');
Route::get('/editExpenditureReport', 'Expenditure\PCCeoExpenditureController@editExpenditureReport'); 
Route::get('/GetProfileCEO','Expenditure\PCCeoExpenditureController@GetProfileCEO');
Route::get('/view/{id}','Expenditure\PCCeoExpenditureController@viewByCandidateId');

Route::post('/updateReceived', 'Expenditure\NotificationExpenditureController@updateReceived')->name('updateReceived'); 
// manoj end
//Shishir sharma
Route::get('/notification', 'Expenditure\NotificationExpenditureController@scrutiny');
Route::get('/allscrutiny', 'Expenditure\NotificationExpenditureController@allscrutiny');
Route::get('/printTrackingStatus/{id}','Expenditure\PCCeoExpenditureController@printTrackingStatus');
 
Route::get('/return/{pc}', 'Expenditure\PCCeoExpenditureController@getReturn');
Route::get('/non-return/{pc}', 'Expenditure\PCCeoExpenditureController@getNonReturn');
Route::get('/FinalizedcandidateList', 'Expenditure\PCCeoExpenditureController@getcandidateList')->name('FinalizedcandidateList');
Route::get('/updateStatusReport', 'Expenditure\PCCeoExpenditureController@updateStatusReport');