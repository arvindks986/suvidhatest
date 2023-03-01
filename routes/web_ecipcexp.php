<?php
//created by Niraj for expendature on ECI Level


#########################start by Niraj #############################
//Route::get('/expdashboard', 'Expenditure\EciExpenditureController@expdashboard');

Route::match(array('GET','POST'),'/EciExpdashboard/', 'Expenditure\EciExpenditureController@dashboard');
Route::get('/dataentryStart/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBydataentryStart');
Route::get('/finalizeData/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByfinalizeData');
Route::get('/logedaccount/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBylogedaccount');
Route::get('/notintime/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBynotintime');
Route::get('/formatedefects/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByformatedefects');
Route::get('/ronotagree/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByronotagree');
Route::get('/understatedexpense/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByunderstatedexpense');
Route::get('/dataentrydefects/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBydataentrydefects');
Route::get('/partyfund/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBypartyfund');
Route::get('/othersfund/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByothersfund');
Route::get('/exeedceiling/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByexeedceiling');
Route::get('/getpcbystate', 'Expenditure\EciExpenditureController@getpclist'); 


//dashboard current status
Route::match(array('GET','POST'),'/statusExpdashboard', 'Expenditure\EciExpenditureController@statusdashboard');
Route::get('/pendingdataentry/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getpendingcandidateList');
Route::get('/partiallypending/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getpartiallypendingcandidateList');
Route::get('/filedData/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByfiledData');
Route::get('/defaulter/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getdefaultercandidateList');
Route::get('/finalbyceo/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListfinalbyCEO');
Route::get('/finalbyeci-report/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListfinalbyECI');

//MIS Report
Route::match(array('GET','POST'),'/mis-officer', 'Expenditure\EciExpenditureController@getOfficersmis');
Route::get('/EciOfficerMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersmisEXL');
Route::get('/EciOfficerMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersmisPDF');
Route::get('/allcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@finalCandidateList');
Route::get('/allcandidateEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@finalCandidateListEXL');
Route::get('/allcandidatePDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@finalCandidateListPDF');

Route::get('/pendingatro/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatRO');
Route::get('/pendingatroEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatROEXL');
Route::get('/pendingatroPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatROPDF');

Route::get('/pendingatceo/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatCEO');
Route::get('/pendingatceoEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatCEOEXL');
Route::get('/pendingatceoPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatCEOPDF');

Route::get('/pendingateci/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatECI');
Route::get('/pendingateciEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatECIEXL');
Route::get('/pendingateciPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListpendingatECIPDF');

Route::get('/finalbyeci/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListfinalbyECI');
Route::get('/finalbyeciEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListfinalbyECIEXL');
Route::get('/finalbyeciPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getcandidateListfinalbyECIPDF');

//Date 9-06-2019
Route::get('/disqualifiedbyeci/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getdisqualifiedcandidateListbyECI');
Route::get('/disqualifiedbyeciEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getdisqualifiedcandidateListbyECIEXL');

Route::match(array('GET','POST'),'/mis-candidate', 'Expenditure\EciExpenditureController@getCandidatemis');
Route::get('/EciCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getCandidatesmisEXL');
Route::get('/EciCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getCandidatemisPDF');

Route::get('/filedcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@filedcandidateData');
Route::get('/EcifiledCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@filedcandidateDataEXL');
Route::get('/EcifiledCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@filedcandidateDataPDF');

Route::get('/notfiledcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notfiledcandidateData');
Route::get('/EciNotfiledCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notfiledcandidateDataEXL');
Route::get('/EciNotfiledCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notfiledcandidateDataPDF');


Route::get('/notintimecandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notintimecandidateData');
Route::get('/EcinotintimeCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notintimecandidateDataEXL');
Route::get('/EcinotintimeCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@notintimecandidateDataPDF');

Route::get('/defaultercandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@defaultercandidateData');
Route::get('/EciDefaulterCandidateMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@defaultercandidateDataEXL');
Route::get('/EciDefaulterCandidateMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@defaultercandidateDataPDF');
Route::get('/getCandTracking/{candidate_id}', 'Expenditure\EciExpenditureController@getCandTracking');

//date 01-07-2019
Route::get('/Ecistartedcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@Ecistartedcandidate');
//date 01-07-2019
Route::get('/Ecinotstarted/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@Ecinotstarted');

//date 01-07-2019
Route::get('/EcifinalbyDEO/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@EcifinalbyDEO');
Route::get('/EcifinalbyDEOMISEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@EcifinalbyDEOMISEXL');
Route::get('/EcifinalbyDEOMISPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@EcifinalbyDEOMISPDF');

//Report Section
Route::match(array('GET','POST'),'/report-officer', 'Expenditure\EciExpenditureController@getOfficersreport');
Route::get('/EciOfficerReportEXL/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersreportEXL');
Route::get('/EciOfficerReportPDF/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getOfficersreportPDF');

//date 19-08-2019
Route::match(array('GET','POST'),'/fund-nationalparties', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpenditure');
Route::get('/fund-nationalparties-graph', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpendituregraph');
Route::match(array('GET','POST'),'/fund-nationalpartiesavggraph', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpenditureAvgGraph');
Route::match(array('GET','POST'),'/fund-nationalpartiesnationgraph', 'Expenditure\EciExpenditureController@getNationlPartyWiseExpenditureNationGraph');

//Notice Section 

Route::get('/noticeatceo/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatCEO');
Route::get('/noticeatceoEXL/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatCEOEXL');
Route::get('/noticeatdeo/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatDEO');
Route::get('/noticeatdeoEXL/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController@getnoticeatDEOEXL');

// Summary Analytics Section Date 16-09-2019

Route::match(array('GET','POST'),'/analytic-summary/{record?}', 'Expenditure\EciExpenditureController@getanalyticsummary');


// MIS for PC Election May-2014

Route::match(array('GET','POST'),'/mis-officer2014', 'Expenditure\EciExpenditureController2014@getOfficersmis');
Route::get('/EciOfficerMISEXL2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getOfficersmisEXL');
Route::get('/EciOfficerMISPDF2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getOfficersmisPDF');

Route::get('/allcandidate2014/{st_code}/{pc_no}',     'Expenditure\EciExpenditureController2014@finalCandidateList');
Route::get('/allcandidateEXL2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@finalCandidateListEXL');
Route::get('/allcandidatePDF2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@finalCandidateListPDF');

Route::get('/EcifinalbyDEO2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@EcifinalbyDEO');
Route::get('/EcifinalbyDEOMISEXL2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@EcifinalbyDEOMISEXL');
Route::get('/EcifinalbyDEOMISPDF2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@EcifinalbyDEOMISPDF');

Route::get('/pendingatro2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatRO');
Route::get('/pendingatroEXL2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatROEXL');
Route::get('/pendingatroPDF2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatROPDF');

Route::get('/pendingatceo2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatCEO');
Route::get('/pendingatceoEXL2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatCEOEXL');
Route::get('/pendingatceoPDF2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatCEOPDF');

Route::get('/pendingateci2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatECI');
Route::get('/pendingateciEXL2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatECIEXL');
Route::get('/pendingateciPDF2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListpendingatECIPDF');

Route::get('/finalbyeci2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListfinalbyECI');
Route::get('/finalbyeciEXL2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListfinalbyECIEXL');
Route::get('/finalbyeciPDF2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getcandidateListfinalbyECIPDF');


Route::get('/noticeatdeo2014/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController2014@getnoticeatDEO');
Route::get('/noticeatdeoEXL2014/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController2014@getnoticeatDEOEXL');
Route::get('/noticeatdeoPDF2014/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController2014@getnoticeatDEOPDF');
Route::get('/noticeatceo2014/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController2014@getnoticeatCEO');
Route::get('/noticeatceoEXL2014/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController2014@getnoticeatCEOEXL');
Route::get('/noticeatceoPDF2014/{st_code}/{ac_no}', 'Expenditure\EciExpenditureController2014@getnoticeatCEOPDF');

//Breach Amount MIS & Report date : 30-12-2019
Route::get('/breach-details/{st_code}/{pc_no}','Expenditure\EciExpenditureController@getbreachAmnt');
Route::any('/breach-report','Expenditure\EciExpenditureController@getbreachAmntMis');
#########################end by Niraj #############################

Route::get('/masterEntry/', 'Expenditure\EciExpenditureController@masterEntry');
Route::post('/storeMasterEntry{mid?}','Expenditure\EciExpenditureController@storeMasterEntry');
Route::get('/ActionOnCandidate', 'Expenditure\EciExpenditureController@ActionOnCandidate');
Route::get('/printingNoticeDeoLetter', 'Expenditure\EciExpenditureController@printingNoticeDeoLetter');
Route::get('/UploadNoticeDeoLetter', 'Expenditure\EciExpenditureController@UploadNoticeDeoLetter');
Route::get('/MasterDataListing', 'Expenditure\EciExpenditureController@MasterDataListing');
Route::post('/saveComment','Expenditure\EciExpenditureController@saveComment');
Route::get('/confirmReport','Expenditure\EciExpenditureController@confirmReport');
Route::get('/editExpenditureReport', 'Expenditure\EciExpenditureController@editExpenditureReport'); 
Route::post('/StoreMisExpenseReport', 'Expenditure\EciExpenditureController@StoreMisExpenseReport'); 

// manish end here
 // manoj start 
  // tracking start here 
    /////////////////////////////tracking//////// 
Route::match(array('GET','POST'),'/GetTrackingReportData', 'Expenditure\EciController@GetTrackingReportData'); 
Route::get('/updateData', 'Expenditure\EciController@updateData'); 
Route::get('/getscrutinyreport','Expenditure\EciExpenditureController@getscrutinyreport');
Route::get('/generatePDF/{id}','Expenditure\EciExpenditureController@generatePDF');
Route::get('/GetProfileECI','Expenditure\EciExpenditureController@GetProfileECI');


 // graph start here 

Route::get('/candidateListBydataentryStartgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBydataentryStartgraph');
Route::get('/candidateListByfinalizeDatagraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByfinalizeDatagraph');
Route::get('/candidateListBylogedaccountgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBylogedaccountgraph');
Route::get('/candidateListBynotintimegraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBynotintimegraph');
Route::get('/candidateListByformatedefectsgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByformatedefectsgraph');
 
Route::get('/candidateListByunderstatedexpensegraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByunderstatedexpensegraph');
 
Route::get('/candidateListBypartyfundgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListBypartyfundgraph');
Route::get('/candidateListByothersfundgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@candidateListByothersfundgraph');
Route::get('/getpendingcandidateListgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getpendingcandidateListgraph');
Route::get('/getpartiallypendingcandidateListgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getpartiallypendingcandidateListgraph');
Route::get('/getdefaultercandidateListgraph/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getdefaultercandidateListgraph');
 // for graph end here 
Route::get('/getprofile','Expenditure\EciExpenditureController@getprofile');
Route::get('/printScrutinyReport/{id}',array('as'=>'printScrutinyReport','uses'=>'Expenditure\EciExpenditureController@printScrutinyReport'));
Route::get('/ecinotification', 'Expenditure\EciNotificationExpenditureController@scrutiny');
Route::get('/eciallscrutiny', 'Expenditure\EciNotificationExpenditureController@allscrutiny');  
//by manoj date 16-09-19
Route::get('/eciallscrutinybyepass', 'Expenditure\EciNotificationExpenditureController@allscrutinyByPass');  
Route::get('/FinalizedcandidateList', 'Expenditure\EciExpenditureController@getcandidateList');
Route::get('/updateStatusReport', 'Expenditure\EciExpenditureController@updateStatusReport');
Route::get('/printTrackingStatus/{id}','Expenditure\EciExpenditureController@printTrackingStatus');
Route::get('/view/{id}','Expenditure\EciExpenditureController@viewByCandidateId');

    // tracking end here
    Route::get('/receivedNotification', 'Expenditure\EciNotificationExpenditureController@receivedNotification'); 
    Route::post('/updateReceived', 'Expenditure\EciNotificationExpenditureController@updateReceived')->name('updateReceived'); 
	 Route::match(array('GET','POST'),'/reports','Expenditure\EciExpenditureController@trackingReport');
    Route::match(array('GET','POST'),'/trackingReportprint/{st_code}/{pc_no}','Expenditure\EciExpenditureController@trackingReportprint');
	    //District wise start

    Route::match(array('GET','POST'),'/district-report', 'Expenditure\EciExpenditureController@getDistrictReport');
    Route::get('/districtreportexl/{st_code}/{district}/{pc_no}', 'Expenditure\EciExpenditureController@getDistrictReportExl');
    Route::get('/districtreportpdf/{st_code}/{district}/{pc_no}', 'Expenditure\EciExpenditureController@getDistrictReportPdf');
    Route::get('/getdistricts/{st_code}', 'Expenditure\EciExpenditureController@Alldistrict');
    Route::get('/getdistrictpcs', 'Expenditure\EciExpenditureController@getAllPCs');

    //District wise end
    // manoj end
Route::get('/return/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getReturn');
//add by niraj 21-01-2020
Route::get('/electedcandidate/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getElectedcand');
Route::get('/electedcand2014/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController2014@getElectedcand2014');
Route::get('/non-return/{st_code}/{pc_no}', 'Expenditure\EciExpenditureController@getNonReturn');
Route::get('/candidate_wise_expenditure','Expenditure\EciExpenditureController@candidate_wise_expenditure');
Route::get('/getPartyWiseExpenditure','Expenditure\EciExpenditureController@getPartyWiseExpenditure');
Route::get('/getPartyWisePDF','Expenditure\EciExpenditureController@getPartyWisePDF');