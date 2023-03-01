<?php
Route::group(['middleware' => ['auth:admin', 'auth']], function(){
	Route::get('test',function(){echo "<pre>"; print_r(Session::get('admin_login_details')); die;});
	Route::get('IndexCardDataReport', 'IndexCardReports\IndexCardDataReport\IndexCardReportController@indexCardReport')->name('IndexCardDataReport');
	Route::get('IndexCardDataReportPDF', 'IndexCardReports\IndexCardDataReport\IndexCardReportController@indexCardReport')->name('IndexCardDataReport');

	Route::any('Political_party_Wise_Deposits_Forfeited',
	'IndexCardReports\Political_party_Wise_Deposits_ForfeitedController@index');

	Route::get('StateWiseNoofElectorsView', 'IndexCardReports\StatisticatReport\StatisticatReportController@StateWiseNoofElectors')->name('StateWiseNoofElectorsView');
	Route::get('StateWiseNoofElectorsPDF', 'IndexCardReports\StatisticatReport\StatisticatReportController@StateWiseNoofElectors')->name('StateWiseNoofElectorsPDF');
	Route::get('StateWiseNoofElectorsCSV', 'IndexCardReports\StatisticatReport\StatisticatReportController@StateWiseNoofElectors')->name('StateWiseNoofElectorsCSV');

	Route::get('constituencyPCWiseView', 'IndexCardReports\StatisticatReport\StatisticatReportController@constituencyPCWise')->name('constituencyPCWiseView');
	Route::get('constituencyPCWisePDF', 'IndexCardReports\StatisticatReport\StatisticatReportController@constituencyPCWise')->name('constituencyPCWisePDF');
	Route::get('constituencyPCWiseCSV', 'IndexCardReports\StatisticatReport\StatisticatReportController@constituencyPCWise')->name('constituencyPCWiseCSV');
	
		
	
//Neha Sah routes start

//finalised pc report

Route::get('FinalisedPCReport','IndexCardReports\FinalisedReportPCController@getFinalisedPCReport');
Route::get('FinalisedPCReportPDF','IndexCardReports\FinalisedReportPCController@getFinalisedPCReportPDF');	
	
//not finalised pc report

Route::get('NotFinalisedPCReport','IndexCardReports\FinalisedReportPCController@getNotFinalisedPCReport');
Route::get('NotFinalisedPCReportPDF','IndexCardReports\FinalisedReportPCController@getNotFinalisedPCReportPDF');

//no of finalized pc report 

Route::get('NoofFinalizedPC','IndexCardReports\FinalisedReportPCController@getNoofFinalizedPC');
Route::get('NoofFinalizedPCPDF','IndexCardReports\FinalisedReportPCController@getNoofFinalizedPCPDF');

//party details report
Route::get('PartyDetailsReportVol2','IndexCardReports\StatisticalReports\StatisticalReportsVolumeTwoController@getPartyDetailsReportVol2');
Route::get('PartyDetailsReportPDFVol2','IndexCardReports\StatisticalReports\StatisticalReportsVolumeTwoController@getPartyDetailsReportPDFVol2');

//voter information

Route::get('voterInformation','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getVoterInformation');
Route::get('voterInformationPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getVoterInformation');
Route::get('voterInformationExcel','StatisticalReports\StatisticalReportsVolumeOneController@getVoterInformation');

// indexcard brief report
Route::get('indexCardBriefReport','IndexCardReports\IndexCardReportPC\IndexCardReportPCController@getIndexCardReportPC');
Route::get('indexCardBriefReportPDF','IndexCardReports\IndexCardReportPC\IndexCardReportPCController@getIndexCardReportPCPDF');


//participation of Women in national parties

Route::get('ParticipationofWomenInNationalParties','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInNationalParties');
Route::get('ParticipationofWomenInNationalPartiesPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInNationalParties');

//participation of Women As independent candidate

Route::get('ParticipationofWomenAsIndependentCandidates','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenAsIndependentCandidates');
Route::get('ParticipationofWomenAsIndependentCandidatesPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenAsIndependentCandidates');

//participation of Women in registered parties

Route::get('ParticipationofWomenInRegisteredParties','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInRegisteredParties');
Route::get('ParticipationofWomenInRegisteredPartiesPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInRegisteredParties');

//Constituency Wise Detailed Result
Route::get('ConstituencyWiseDetailedResult','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');
Route::get('ConstituencyWiseDetailedResultPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResultPDF');

//StatisticalReportCurrent

//vol 2
Route::get('StatewiseSeatWon','IndexCardReports\StatisticalReportsCurrent\StatisticalReportsCurrentVolumeTwoController@getStatewiseSeatWon');
Route::get('StatewiseSeatWonPDF','IndexCardReports\StatisticalReportsCurrent\StatisticalReportsCurrentVolumeTwoController@getStatewiseSeatWon');

Route::get('PCWiseDistributionVotesPolled','IndexCardReports\StatisticalReportsCurrent\StatisticalReportsCurrentVolumeTwoController@getPCWiseDistributionVotesPolled');
Route::get('PCWiseDistributionVotesPolledPDF','IndexCardReports\StatisticalReportsCurrent\StatisticalReportsCurrentVolumeTwoController@getPCWiseDistributionVotesPolled');

// Neha Sah routes end
	
Route::get('admin','IndexCardReports\hello@index');
	Route::get('Political_party_Wise_Deposits_ForfeitedPDF',
		'IndexCardReports\Political_party_Wise_Deposits_ForfeitedController@index');
		
		
	#Jitendra Singh Code Start
	
	Route::any('numberandtypesofconstituencies', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies')->name('numberandtypesofconstituencies');
	
	Route::any('numberandtypesofconstituencies_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies_pdf')->name('numberandtypesofconstituencies_pdf');
	
	Route::any('numberandtypesofconstituencies_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies_xlsx')->name('numberandtypesofconstituencies_xlsx');

	Route::any('listofpoliticalpartiesparticipated', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated')->name('listofpoliticalpartiesparticipated');
	Route::any('listofpoliticalpartiesparticipated_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated_pdf')->name('listofpoliticalpartiesparticipated_pdf');
	
	Route::any('listofpoliticalpartiesparticipated_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated_xls')->name('listofpoliticalpartiesparticipated_xls');
	
	Route::any('statewisenumberelectors', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors')->name('statewisenumberelectors');
	Route::any('statewisenumberelectors_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors_pdf')->name('statewisenumberelectors_pdf');
	
	Route::any('statewisenumberelectors_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors_xls')->name('statewisenumberelectors_xls');
	
	Route::any('performanceOfWownCandidates', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@performanceOfWownCandidates')->name('performanceOfWownCandidates');
	Route::any('performanceOfWownCandidates_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@performanceOfWownCandidates_pdf')->name('performanceOfWownCandidates_pdf');
	
	Route::any('performanceOfWownCandidates_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@performanceOfWownCandidates_xls')->name('performanceOfWownCandidates_xls');

				
	Route::any('participationofWomeneletorsinPoll','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll')->name('participationofWomeneletorsinPoll');
	Route::any('participationofWomeneletorsinPoll_pdf','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll_pdf')->name('participationofWomeneletorsinPoll_pdf');	
	Route::any('participationofWomeneletorsinPoll_xls','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll_xls')->name('participationofWomeneletorsinPoll_xls');
	
	Route::get('scheduleloksabhahighlights','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights')->name('scheduleloksabhahighlights');
	Route::get('scheduleloksabhahighlights_pdf','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights_pdf')->name('scheduleloksabhahighlights_pdf');	
	Route::get('scheduleloksabhahighlights_xls','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights_xls')->name('scheduleloksabhahighlights_xls');

	
	#Jitendra Singh Code End


// Praveen Route Start Here
Route::any('Political_party_Wise_Deposits_Forfeited',
'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

Route::get('Political_party_Wise_Deposits_ForfeitedPDF',
		'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

Route::get('AssemblySegmentWiseInformationElectors',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

Route::any('StateWiseCandidateDataSummary',
	'IndexCardReports\State_Wise_Candidate_Data_Summry\State_Wise_Candidate_Data_SummryController@index');

Route::any('StateWiseCandidateDataSummaryPDF',
	'IndexCardReports\State_Wise_Candidate_Data_Summry\State_Wise_Candidate_Data_SummryController@index');




// Praveen Route Ends Here


	
		
		
		
		
	});
?>