<?php
/****************************Migration*****************************************/
Route::get('indexcardMigration', 'IndexCardReports\MigrationController@indexcardMigration')->name('indexcardMigration');
Route::any('migrationelectiondataacwise/{st_code}/{schedule_id}',
        'IndexCardReports\Election_Data_ACwiseMigrationController@datamigrationacwise')->name('migrationelectiondataacwise');

Route::any('startMigForCandidateVotes/{st_code}','IndexCardReports\dataMigrationCandidateWiseContoller@startMigForCandidateVotes');
/****************************Migration*****************************************/

Route::group(['prefix' => 'eci', 'as' => 'eci::', 'middleware' => ['auth:admin', 'auth']], function(){

			/* Route::get('indexcardpc', function(){
			return 'hello';
			})->name('pcwisedata'); */
			
			
			Route::get('bye-election-verify-report', 'IndexCardReports\ByeElectionReportController@indexcardreportlist');
		
			Route::get('bye-report-listing-verify-checkbox', 'IndexCardReports\ByeElectionReportController@byeverifyreportcheckbox');

			Route::get('statistical-report-listing', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@Statisticalreport');

			Route::any('indexcardpc', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcarddata')->name('indexcardpc');
	
			Route::get('ajaxpccall', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@ajaxpccall')->name('ajaxpccall');
			Route::get('indexcardpcpdf/{st_code}/{pc}', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcarddatapdf')->name('getindexcarddatapdf');
	
			Route::get('indexcardpcexcel/{st_code}/{pc}', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcarddataexcel')->name('getindexcarddataexcel');
	
			Route::any('indexcardbriefed', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcardbriefdata');
					
			Route::get('indexcardbriefedpdf/{st_code}/{pc}', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcardbriefdatapdf')->name('getindexcardbriefdatapdf');

			/// neha's code Start Eci.

			Route::get('StatewiseSeatWon','IndexCardReports\StatisticalReportPC\EciReportOneController@getStatewiseSeatWon')->name('StatewiseSeatWon');
			Route::get('StatewiseSeatWonPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getStatewiseSeatWon')->name('StatewiseSeatWonPDF');
			Route::get('StatewiseSeatWonXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getStatewiseSeatWon')->name('StatewiseSeatWonXls');

			Route::get('ParticipationofWomenInNationalParties','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInNationalParties')->name('ParticipationofWomenInNationalParties');

			Route::get('ParticipationofWomenInNationalPartiesPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInNationalParties')->name('ParticipationofWomenInNationalPartiesPDF');
			Route::get('ParticipationofWomenInNationalPartiesXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInNationalParties')->name('ParticipationofWomenInNationalPartiesXls');

			// Report 29 for  Eci.
			Route::get('ParticipationofWomenAsIndependentCandidates','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenAsIndependentCandidates');
			Route::get('ParticipationofWomenAsIndependentCandidatesPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenAsIndependentCandidates');
			Route::get('ParticipationofWomenAsIndependentCandidatesXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenAsIndependentCandidates');

			// Report 29 for  Eci ends

			Route::get('ParticipationofWomenInRegisteredParties','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInRegisteredParties')->name('ParticipationofWomenInRegisteredParties');;
			Route::get('ParticipationofWomenInRegisteredPartiesPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInRegisteredParties')->name('ParticipationofWomenInRegisteredPartiesPDF');
			Route::get('ParticipationofWomenInRegisteredPartiesXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInRegisteredParties')->name('ParticipationofWomenInRegisteredPartiesXls');

			// Voters Information report Number 10 for eci login

			Route::get('voterInformation','IndexCardReports\StatisticalReportPC\EciReportOneController@getVoterInformation');
			Route::get('voterInformationPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getVoterInformation');
			Route::get('voterInformationXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getVoterInformation');

			// Voters Information report Number 10 for eci login

			Route::get('PCWiseDistributionVotesPolled','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');
			Route::get('PCWiseDistributionVotesPolledPDF','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');
			Route::get('PCWiseDistributionVotesPolledXls','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');

			

			//// Amit's code eci
			Route::get('List-of-successfull-candidate', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@successfullcondidate');
			Route::get('list-of-successfull-candidate-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@successfullcondidatePDF');
			Route::get('list-of-successfull-candidate-excell', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@successfullcondidateexcell');

			Route::get('winning-candidate-analysis-over-total-electors', 'IndexCardReports\StatisticalReportPC\StatisticalReportControllertwo@index');

			Route::get('winning-condidate-analysisover-elector-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportControllertwo@winningcpndidateanalysisoverelectorpdf');
			Route::get('winning-condidate-analysisover-elector-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportControllertwo@winningcpndidateanalysisoverelectorxls');

			Route::get('performance-of-national-partys', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofnationalparties');
			Route::get('performance-of-national-partys-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofnatiionalpartiespdf');
			Route::get('performance-of-national-partys-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@winningcpndidateanalysisoverelectorxls');
			Route::get('State-wise-overseas-electors-voters', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@allstatewiseoverseaselectorsvoter');
			Route::get('State-wise-overseas-electors-voters-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@allstatewiseoverseaselectorsvoterpdf');

			Route::get('State-wise-overseas-electors-voters-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@allstatewiseoverseaselectorsvoterxls');

			Route::get('details-of-repoll-held', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@detailsofrepollheld');
			Route::get('details-of-repoll-held-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@detailsofrepollheld');
			Route::get('details-of-repoll-held-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@detailsofrepollheld');

			Route::get('statewisecandidatedatasummary', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@getcandidateDataSummary');
		   Route::get('statewisecandidatedatasummary_pdf', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@getcandidateDataSummary');
		   Route::get('statewisecandidatedatasummary_xls', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@getcandidateDataSummary');

			Route::get('performance-of-state-partys', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofstateparties');

			Route::get('performance-of-state-partys-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofstateparties');
			Route::get('performance-of-state-partys-excel', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofstateparties');

			/// Amit's code end eci


			#Jitendra Singh Code Start

			Route::any('numberandtypesofconstituencies', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies')->name('numberandtypesofconstituencies');

			Route::any('numberandtypesofconstituencies_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies')->name('numberandtypesofconstituencies_pdf');

			Route::any('numberandtypesofconstituencies_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies')->name('numberandtypesofconstituencies_xls');

			Route::any('listofpoliticalpartiesparticipated', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated')->name('listofpoliticalpartiesparticipated');

			Route::any('listofpoliticalpartiesparticipated_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated')->name('listofpoliticalpartiesparticipated_pdf');

			Route::any('listofpoliticalpartiesparticipated_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated')->name('listofpoliticalpartiesparticipated_xls');

			Route::any('statewisenumberelectors', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors')->name('statewisenumberelectors');
			Route::any('statewisenumberelectors_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors')->name('statewisenumberelectors_pdf');

			Route::any('statewisenumberelectors_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors')->name('statewisenumberelectors_xls');

			Route::any('individualperformanceofwomencandidates', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@individualperformanceOfWownCandidates')->name('individualperformanceofwomencandidates');
			Route::any('individualperformanceofwomencandidates_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@individualperformanceOfWownCandidates')->name('individualperformanceofwomencandidates_pdf');

			Route::any('individualperformanceofwomencandidates_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@individualperformanceOfWownCandidates')->name('individualperformanceofwomencandidates_xls');


			Route::any('participationofWomeneletorsinPoll','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll')->name('participationofWomeneletorsinPoll');
			Route::any('participationofWomeneletorsinPoll_pdf','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll')->name('participationofWomeneletorsinPoll_pdf');
			Route::any('participationofWomeneletorsinPoll_xls','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll')->name('participationofWomeneletorsinPoll_xls');

			Route::get('scheduleloksabhahighlights','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights')->name('scheduleloksabhahighlights');
			Route::get('scheduleloksabhahighlights_pdf','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights')->name('scheduleloksabhahighlights_pdf');
			Route::get('scheduleloksabhahighlights_xls','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights')->name('scheduleloksabhahighlights_xls');

			Route::get('statewisevoterturnout', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisevoterturnout')->name('statewisevoterturnout');
			Route::get('statewisevoterturnout_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisevoterturnout')->name('statewisevoterturnout_pdf');
			Route::get('statewisevoterturnout_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisevoterturnout')->name('statewisevoterturnout_xls');



			Route::get('pollingstationinformation', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pollingstationinformation');
			Route::get('pollingstationinformation_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pollingstationinformation')->name('pollingstationinformation_pdf');
			Route::get('pollingstationinformation_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pollingstationinformation')->name('pollingstationinformation_xls');

			Route::get('pcwisevoterturnout', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pcwisevoterturnout')->name('pcwisevoterturnout');
			Route::get('pcwisevoterturnout_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pcwisevoterturnout')->name('pcwisevoterturnout_pdf');
			Route::get('pcwisevoterturnout_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pcwisevoterturnout')->name('pcwisevoterturnout_xls');


			Route::get('detailsofassemblysegmentofpc', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@detailsofassemblysegmentofpc')->name('pcwisevoterturnout');
			Route::get('detailsofassemblysegmentofpc_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@detailsofassemblysegmentofpc')->name('detailsofassemblysegmentofpc_pdf');
			Route::get('detailsofassemblysegmentofpc_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@detailsofassemblysegmentofpc')->name('detailsofassemblysegmentofpc_xls');


			Route::get('constituencywisedetailedresult','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');
			Route::get('constituencywisedetailedresult_pdf','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');
			Route::get('constituencywisedetailedresult_xls','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');

			#Jitendra Singh Code End


			#Praveen route Start

			// Assembly segment wise information of electors

			Route::get('AssemblySegmentWiseInformationElectors',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			Route::get('AssemblySegmentWiseInformationElectorsPDF',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			Route::get('AssemblySegmentWiseInformationElectorsXLS',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			// Winning Candidate analysis over total voters

			Route::get('winning-condidate-analysis-over-total-voters', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@winningcandidateoverseasevoters');
			Route::get('winning-condidate-analysis-over-total-voters-pdf', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@winningcandidateoverseasevoterspdf');
			Route::get('winning-condidate-analysis-over-total-voters-excel', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@winningcandidateoverseasevotersxls');

			// Political Party  Wise VDeposit forfeited

		    Route::any('Political_party_Wise_Deposits_Forfeited',
				'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

			Route::get('Political_party_Wise_Deposits_ForfeitedPDF',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

			Route::get('Political_party_Wise_Deposits_ForfeitedXLS',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

			// Party Wise Valid Votes and Seat Won

			Route::get('partywiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@partywiseseatwonvalidvotes');

			Route::get('downloadpartywiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadpartywiseseatwonvalidvotes');

			Route::get('downloadpartywiseseatwonvalidvotesXLS', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadpartywiseseatwonvalidvotes');

			// State Wise Valid Votes and Seat Won



      		//performance of unreognised party

      		Route::get('performance-of-unrecognised-partys', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@performanceofunrecognisedparties');
   			Route::get('performance-of-unrecognised-partys-pdf', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@performanceofunrecognisedparties');


   		    // Participation of Women in State Party

   		    Route::get('ParticipationofWomenInStateParties','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@getParticipationofWomenInStateParties')->name('ParticipationofWomenInRegisteredParties');;
			Route::get('ParticipationofWomenInStatePartiesPDF','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@getParticipationofWomenInStateParties')->name('ParticipationofWomenInRegisteredPartiesPDF');
			Route::get('ParticipationofWomenInStatePartiesXls','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@getParticipationofWomenInStateParties')->name('ParticipationofWomenInRegisteredPartiesXls');


			// No of candidates Per consitituency

			Route::get('noofcandidateperconsitituency','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@NoOffCandidatePCWISE');
			Route::get('No-of-candidate-per-consitituency-pdf','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@noofcandidateperconsitituencypdf');
			Route::get('No-of-candidate-per-consitituency-excel','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@noofcandidateperconsitituencyexcel');


			//Partitation of women candidate in poll

			Route::any('participationofwomencandidateinpoll',
				'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@participationofwomencandidateinpoll');

			Route::get('participationofwomencandidateinpollPDF',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@participationofwomencandidateinpoll');

			Route::get('participationofwomencandidateinpollXLS',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@participationofwomencandidateinpoll');


			//Consituency Data Summry

			Route::any('constituencyDataSummaryReport','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReport');

		    Route::any('constituencyDataSummaryReportPDF','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReportPDF');

			Route::any('constituencyDataSummaryReportXLS','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@indexxls')
				->name('constituencyDataSummaryReportXls');

    Route::get('constituencyDataSummaryReportexcel','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@indexxls');



		      //Highlights report eci website

      		Route::get('highlights','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@highlights');
      		Route::get('highlights-pdf','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@highlights');
      		Route::get('highlights-excel','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@highlights');

      		// Constituency Data Summary

      		Route::any('constituencyDataSummaryReport','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReport');

			Route::any('constituencyDataSummaryReportPDF','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReportPDF');

			Route::any('constituencyDataSummaryReportXLS','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReportXls');


			#Praveen route Ends



});

 Route::group(['prefix' => 'eci-index', 'as' => 'eci-index::', 'middleware' => ['auth:admin', 'auth']], function(){

	Route::get('bye-election-verify-report', 'IndexCardReports\ByeElectionReportController@indexcardreportlist');
		
	Route::get('bye-report-listing-verify-checkbox', 'IndexCardReports\ByeElectionReportController@byeverifyreportcheckbox');
	
	
	Route::get('ajaxpccall', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@ajaxpccall')->name('ajaxpccall');

	Route::any('indexcardpc', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcarddata')->name('indexcardpc');
	
 Route::get('indexcardpcpdf/{st_code}/{pc}', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcarddatapdf')->name('getindexcarddatapdf');

	
	Route::any('indexcardbriefed', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcardbriefdata');

	
	
	Route::get('indexcardbriefedpdf/{st_code}/{pc}', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcardbriefdatapdf')->name('getindexcardbriefdatapdf');

	Route::get('indexcardpcexcel/{st_code}/{pc}', 'IndexCardReports\IndexCardDataEci\IndexCardEciController@getindexcarddataexcel')->name('getindexcarddataexcel');

		Route::get('responseindexcard', 'IndexCardReports\IndexCardDataPC\EciIndexCardDataPCFormController@responseindexcard')->name('responseindexcard');
		Route::get('approveddata', 'IndexCardReports\IndexCardDataPC\EciIndexCardDataPCFormController@approveddata')->name('approveddata');
		Route::any('submitindexcard', 'IndexCardReports\IndexCardDataPC\EciIndexCardDataPCFormController@submitindexcard')->name('submitindexcard');
		Route::get('responseeditrequest', 'IndexCardReports\IndexCardDataPC\EciIndexCardDataPCFormController@responseeditrequest')->name('responseeditrequest');

		//Route::get('indexcardpc', 'IndexCardReports\IndexCardDataPC\EciIndexCardDataPCFormController@indexcardpc')->name('indexcardpc');


         Route::get('statistical-report-listing', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@Statisticalreport');


			/// neha's code Start Eci.

			Route::get('StatewiseSeatWon','IndexCardReports\StatisticalReportPC\EciReportOneController@getStatewiseSeatWon')->name('StatewiseSeatWon');
			Route::get('StatewiseSeatWonPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getStatewiseSeatWon')->name('StatewiseSeatWonPDF');
			Route::get('StatewiseSeatWonXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getStatewiseSeatWon')->name('StatewiseSeatWonXls');

			Route::get('ParticipationofWomenInNationalParties','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInNationalParties')->name('ParticipationofWomenInNationalParties');

			Route::get('ParticipationofWomenInNationalPartiesPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInNationalParties')->name('ParticipationofWomenInNationalPartiesPDF');
			Route::get('ParticipationofWomenInNationalPartiesXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInNationalParties')->name('ParticipationofWomenInNationalPartiesXls');

			//report 29 participation of women as independent candidate for eci- index

			Route::get('ParticipationofWomenAsIndependentCandidates','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenAsIndependentCandidates');
			Route::get('ParticipationofWomenAsIndependentCandidatesPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenAsIndependentCandidates');
			Route::get('ParticipationofWomenAsIndependentCandidatesXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenAsIndependentCandidates');

		

			//report 29 participation of women as independent candidate ends  for eci-index
			
			Route::get('statistical-report-listing', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@Statisticalreport');
			Route::get('statistical-report-listing-verify', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@verifyreport');
			Route::get('statistical-report-listing-verify-checkbox', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@verifyreportcheckbox');
			Route::get('statistical-report-listing-verify-all-report', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@verifyallreport');



			Route::get('ParticipationofWomenInRegisteredParties','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInRegisteredParties')->name('ParticipationofWomenInRegisteredParties');;
			Route::get('ParticipationofWomenInRegisteredPartiesPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInRegisteredParties')->name('ParticipationofWomenInRegisteredPartiesPDF');
			Route::get('ParticipationofWomenInRegisteredPartiesXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getParticipationofWomenInRegisteredParties')->name('ParticipationofWomenInRegisteredPartiesXls');

			// voters Information report number 10 for eci index



			Route::get('voterInformation','IndexCardReports\StatisticalReportPC\EciReportOneController@getVoterInformation');
			Route::get('voterInformationPDF','IndexCardReports\StatisticalReportPC\EciReportOneController@getVoterInformation');
			Route::get('voterInformationXls','IndexCardReports\StatisticalReportPC\EciReportOneController@getVoterInformation');

			// Voters Information Route Ends

			Route::get('PCWiseDistributionVotesPolled','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');
			Route::get('PCWiseDistributionVotesPolledPDF','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');
			Route::get('PCWiseDistributionVotesPolledXls','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');

			/// neha's code  end Eci.

			//// Amit's code eci
			Route::get('List-of-successfull-candidate', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@successfullcondidate');
			Route::get('list-of-successfull-candidate-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@successfullcondidatePDF');
			Route::get('list-of-successfull-candidate-excell', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@successfullcondidateexcell');

			Route::get('winning-candidate-analysis-over-total-electors', 'IndexCardReports\StatisticalReportPC\StatisticalReportControllertwo@index');

			Route::get('winning-condidate-analysisover-elector-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportControllertwo@winningcpndidateanalysisoverelectorpdf');
			Route::get('winning-condidate-analysisover-elector-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportControllertwo@winningcpndidateanalysisoverelectorxls');

			Route::get('performance-of-national-partys', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofnationalparties');
			Route::get('performance-of-national-partys-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofnatiionalpartiespdf');
			Route::get('performance-of-national-partys-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@winningcpndidateanalysisoverelectorxls');

			// Report No 11 Eci-index 
			
			Route::get('State-wise-overseas-electors-voters', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@allstatewiseoverseaselectorsvoter');
			Route::get('State-wise-overseas-electors-voters-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@allstatewiseoverseaselectorsvoterpdf');
			Route::get('State-wise-overseas-electors-voters-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@allstatewiseoverseaselectorsvoterxls');

			//Report no 11 eci-index

			Route::get('details-of-repoll-held', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@detailsofrepollheld');
			Route::get('details-of-repoll-held-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@detailsofrepollheld');
			Route::get('details-of-repoll-held-xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@detailsofrepollheld');

			Route::get('statewisecandidatedatasummary', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@getcandidateDataSummary');
		   Route::get('statewisecandidatedatasummary_pdf', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@getcandidateDataSummary');
		   Route::get('statewisecandidatedatasummary_xls', 'IndexCardReports\CandidateDataSummary\CandidateDataSummary@getcandidateDataSummary');

			Route::get('performance-of-state-partys', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofstateparties');

			Route::get('performance-of-state-partys-pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofstateparties');
			Route::get('performance-of-state-partys-excel', 'IndexCardReports\StatisticalReportPC\StatisticalReportController@performanceofstateparties');

			/// Amit's code end eci


			#Jitendra Singh Code Start

			Route::any('numberandtypesofconstituencies', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies')->name('numberandtypesofconstituencies');

			Route::any('numberandtypesofconstituencies_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies')->name('numberandtypesofconstituencies_pdf');

			Route::any('numberandtypesofconstituencies_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies')->name('numberandtypesofconstituencies_xls');

			Route::any('listofpoliticalpartiesparticipated', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated')->name('listofpoliticalpartiesparticipated');

			Route::any('listofpoliticalpartiesparticipated_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated')->name('listofpoliticalpartiesparticipated_pdf');

			Route::any('listofpoliticalpartiesparticipated_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@listofpoliticalpartiesparticipated')->name('listofpoliticalpartiesparticipated_xls');

			Route::any('statewisenumberelectors', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors')->name('statewisenumberelectors');
			Route::any('statewisenumberelectors_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors')->name('statewisenumberelectors_pdf');

			Route::any('statewisenumberelectors_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisenumberelectors')->name('statewisenumberelectors_xls');

			Route::any('individualperformanceofwomencandidates', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@individualperformanceOfWownCandidates')->name('individualperformanceofwomencandidates');
			Route::any('individualperformanceofwomencandidates_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@individualperformanceOfWownCandidates')->name('individualperformanceofwomencandidates_pdf');

			Route::any('individualperformanceofwomencandidates_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@individualperformanceOfWownCandidates')->name('individualperformanceofwomencandidates_xls');


			Route::any('participationofWomeneletorsinPoll','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll')->name('participationofWomeneletorsinPoll');
			Route::any('participationofWomeneletorsinPoll_pdf','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll')->name('participationofWomeneletorsinPoll_pdf');
			Route::any('participationofWomeneletorsinPoll_xls','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@participationofWomeneletorsinPoll')->name('participationofWomeneletorsinPoll_xls');

			Route::get('scheduleloksabhahighlights','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights')->name('scheduleloksabhahighlights');
			Route::get('scheduleloksabhahighlights_pdf','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights')->name('scheduleloksabhahighlights_pdf');
			Route::get('scheduleloksabhahighlights_xls','IndexCardReports\StatisticalReportPC\StatisticalReportPCController@scheduleloksabhahighlights')->name('scheduleloksabhahighlights_xls');

			Route::get('statewisevoterturnout', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisevoterturnout')->name('statewisevoterturnout');
			Route::get('statewisevoterturnout_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisevoterturnout')->name('statewisevoterturnout_pdf');
			Route::get('statewisevoterturnout_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@statewisevoterturnout')->name('statewisevoterturnout_xls');



			Route::get('pollingstationinformation', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pollingstationinformation');
			Route::get('pollingstationinformation_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pollingstationinformation')->name('pollingstationinformation_pdf');
			Route::get('pollingstationinformation_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pollingstationinformation')->name('pollingstationinformation_xls');

			Route::get('pcwisevoterturnout', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pcwisevoterturnout')->name('pcwisevoterturnout');
			Route::get('pcwisevoterturnout_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pcwisevoterturnout')->name('pcwisevoterturnout_pdf');
			Route::get('pcwisevoterturnout_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@pcwisevoterturnout')->name('pcwisevoterturnout_xls');


			Route::get('detailsofassemblysegmentofpc', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@detailsofassemblysegmentofpc')->name('pcwisevoterturnout');
			Route::get('detailsofassemblysegmentofpc_pdf', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@detailsofassemblysegmentofpc')->name('detailsofassemblysegmentofpc_pdf');
			Route::get('detailsofassemblysegmentofpc_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@detailsofassemblysegmentofpc')->name('detailsofassemblysegmentofpc_xls');


			Route::get('constituencywisedetailedresult','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');
			Route::get('constituencywisedetailedresult_pdf','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');
			Route::get('constituencywisedetailedresult_xls','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');

			#Jitendra Singh Code End


			#Praveen route Start

			// Assembly segment wise information of electors

			Route::get('AssemblySegmentWiseInformationElectors',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			Route::get('AssemblySegmentWiseInformationElectorsPDF',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			Route::get('AssemblySegmentWiseInformationElectorsXLS',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			// Winning Candidate analysis over total voters

			Route::get('winning-condidate-analysis-over-total-voters', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@winningcandidateoverseasevoters');
			Route::get('winning-condidate-analysis-over-total-voters-pdf', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@winningcandidateoverseasevoterspdf');
			Route::get('winning-condidate-analysis-over-total-voters-excel', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@winningcandidateoverseasevotersxls');

			// Political Party  Wise VDeposit forfeited

		    Route::any('Political_party_Wise_Deposits_Forfeited',
				'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

			Route::get('Political_party_Wise_Deposits_ForfeitedPDF',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

			Route::get('Political_party_Wise_Deposits_ForfeitedXLS',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

			// Party Wise Valid Votes and Seat Won

			Route::get('partywiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@partywiseseatwonvalidvotes');

			Route::get('downloadpartywiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadpartywiseseatwonvalidvotes');

			Route::get('downloadpartywiseseatwonvalidvotesXLS', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadpartywiseseatwonvalidvotes');

			// State Wise Valid Votes and Seat Won

			Route::get('statewiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewiseseatwonvalidvotes');
      		Route::get('downloadstatewiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewiseseatwonvalidvotes');
      		Route::get('downloadstatewiseseatwonvalidvotesXLS', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewiseseatwonvalidvotes');

      		//performance of unreognised party   

      		Route::get('performance-of-unrecognised-partys', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@performanceofunrecognisedparties');
   			Route::get('performance-of-unrecognised-partys-pdf', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@performanceofunrecognisedparties');
   			Route::get('performance-of-unrecognised-partys_xls', 'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@performanceofunrecognisedparties');


   		    // Participation of Women in State Party

   		    Route::get('ParticipationofWomenInStateParties','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@getParticipationofWomenInStateParties');
			Route::get('ParticipationofWomenInStatePartiesPDF','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@getParticipationofWomenInStateParties');
			Route::get('ParticipationofWomenInStatePartiesXls','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@getParticipationofWomenInStateParties');


			// No of candidates Per consitituency

			Route::get('noofcandidateperconsitituency','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@NoOffCandidatePCWISE');
			Route::get('No-of-candidate-per-consitituency-pdf','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@noofcandidateperconsitituencypdf');
			Route::get('No-of-candidate-per-consitituency-excel','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@noofcandidateperconsitituencyexcel');


			//Partitation of women candidate in poll

			Route::any('participationofwomencandidateinpoll',
				'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@participationofwomencandidateinpoll');

			Route::get('participationofwomencandidateinpollPDF',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@participationofwomencandidateinpoll');

			Route::get('participationofwomencandidateinpollXLS',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@participationofwomencandidateinpoll');







		      //Highlights report eci-index website

      		Route::get('highlights','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@highlights');
      		Route::get('highlights-pdf','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@highlights');
      		Route::get('highlights-excel','IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@highlights');

      		// Report 32 eci-index

      		Route::any('constituencyDataSummaryReport','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReport');

			Route::any('constituencyDataSummaryReportPDF','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReportPDF');

			Route::any('constituencyDataSummaryReportXLS','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
				->name('constituencyDataSummaryReportXls');

			// Report 32 eci-index ends here


			# Report 14 eci-index


			Route::get('PCWiseDistributionVotesPolled','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');
			Route::get('PCWiseDistributionVotesPolledPDF','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');
			Route::get('PCWiseDistributionVotesPolledXls','IndexCardReports\StatisticalReportPC\EciReportTwoController@getPCWiseDistributionVotesPolled');

			# Report 14 eci-index ends here



			Route::get('statewiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewiseseatwonvalidvotes');
      		Route::get('downloadstatewiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewiseseatwonvalidvotes');
      		Route::get('downloadstatewiseseatwonvalidvotesXLS', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewiseseatwonvalidvotes');


			#Praveen route Ends





});


Route::group(['prefix' => 'ropc', 'as' => 'ropc::', 'middleware' => ['auth:admin', 'auth']], function(){

	Route::get('indexcardpc', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcarddata')->name('getindexcarddata');
	Route::get('indexcardpcpdf', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcarddata')->name('getindexcarddata');
	Route::get('indexcardpcexcel', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcarddata')->name('getindexcarddata');

	Route::get('indexcardbriefed', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcardbriefed')->name('getindexcardbriefed');
	Route::get('indexcardbriefedpdf', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@getindexcardbriefed')->name('getindexcardbriefed');


	#Praveen route Start

         	Route::get('AssemblySegmentWiseInformationElectors',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			Route::get('AssemblySegmentWiseInformationElectorsPDF',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

			Route::get('AssemblySegmentWiseInformationElectorsXLS',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

	       Route::any('finaliserequest', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@finaliserequest')->name('finaliserequest');
	       Route::any('finalizerequestsubmit', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@finalizerequestsubmit')->name('finalizerequestsubmit');
	       Route::get('myrequestindexcard', 'IndexCardReports\IndexCardDataRoPC\IndexCardDataRoPCController@myrequestindexcard')->name('myrequestindexcard');

	#Praveen route Ends

});


Route::group(['prefix' => 'pcceo', 'as' => 'pcceo::', 'middleware' => ['auth:admin', 'auth']], function(){
	/************************Coded By Mohd Saquib****************************/

	Route::get('myrequestindexcard', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@myrequestindexcard')->name('myrequestindexcard');

	Route::get('AddIndexData', 'IndexCardReports\AddIndexcardDataController@add');

	Route::get('test',function(){echo "<pre>"; print_r(Session::get('admin_login_details')); die;});


	Route::get('indexcardpc', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@pcwisedata')->name('pcwisedata');
	Route::any('getindexcarddata', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcarddata')->name('getindexcarddata');
	Route::get('indexcardpcpdf/{pc}', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcarddatapdf')->name('getindexcarddatapdf');

	Route::get('indexcardpcexcel/{pc}', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcarddataexcel')->name('getindexcarddataexcel');

	Route::get('indexcardbriefed', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@pcwisebriefdata')->name('pcwisebriefdata');
	Route::any('getindexcardbriefdata', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcardbriefdata')->name('getindexcardbriefdata');
	Route::get('indexcardbriefedpdf/{pc}', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcardbriefdatapdf')->name('getindexcardbriefdatapdf');




	Route::any('changeRequest', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@feedbackForm')->name('changeRequest');
	Route::any('feedbackSubmit', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@feedbackSubmit')->name('feedbackSubmit');
	Route::any('myRequest', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@myRequest')->name('myRequest');

	Route::any('finaliserequest', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@finaliserequest')->name('finaliserequest');
	Route::any('finalizerequestsubmit', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@finalizerequestsubmit')->name('finalizerequestsubmit');



	Route::any('getindexcarddatapreview', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcarddatapreview')->name('getindexcarddatapreview');

	Route::post('updateCandiateAcWise', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@updateCandiateAcWise')->name('updateCandiateAcWise');

	Route::post('updatepcwisedata','IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@updatepcwisedata')->name('updatepcwisedata');

	Route::post('updateDataForElectionAcWise',
        'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@electiondataAcwise')->name('AcwiseElectionData');


	Route::get('IndexCardDataReport', 'IndexCardReports\IndexCardDataReport\IndexCardReportController@indexCardReport')->name('IndexCardDataReport');
	Route::get('IndexCardDataReportPDF', 'IndexCardReports\IndexCardDataReport\IndexCardReportController@indexCardReport')->name('IndexCardDataReportPDF');

	Route::any('Political_party_Wise_Deposits_Forfeited',
	'IndexCardReports\Political_party_Wise_Deposits_ForfeitedController@index');

	Route::get('StateWiseNoofElectorsView', 'IndexCardReports\StatisticatReport\StatisticatReportController@StateWiseNoofElectors')->name('StateWiseNoofElectorsView');
	Route::get('StateWiseNoofElectorsPDF', 'IndexCardReports\StatisticatReport\StatisticatReportController@StateWiseNoofElectors')->name('StateWiseNoofElectorsPDF');
	Route::get('StateWiseNoofElectorsCSV', 'IndexCardReports\StatisticatReport\StatisticatReportController@StateWiseNoofElectors')->name('StateWiseNoofElectorsCSV');

	Route::get('constituencyPCWiseView', 'IndexCardReports\StatisticatReport\StatisticatReportController@constituencyPCWise')->name('constituencyPCWiseView');
	Route::get('constituencyPCWisePDF', 'IndexCardReports\StatisticatReport\StatisticatReportController@constituencyPCWise')->name('constituencyPCWisePDF');
	Route::get('constituencyPCWiseCSV', 'IndexCardReports\StatisticatReport\StatisticatReportController@constituencyPCWise')->name('constituencyPCWiseCSV');

	Route::get('numberofcandidateperconstituency', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@numberofcandidateperconstituency')->name('numberofcandidateperconstituency');
    Route::get('numberofcandidateperconstituencyPDF', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@numberofcandidateperconstituencyPDF')->name('numberofcandidateperconstituencyPDF');

    Route::any('perRegUnPartyView', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@performanceRegisteredUnrecognisedParty')->name('perRegUnPartyView');
    Route::any('perRegUnPartyPdf', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@performanceRegisteredUnrecognisedParty')->name('perRegUnPartyPdf');
    Route::any('perRegUnPartyCsv', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@performanceRegisteredUnrecognisedParty')->name('perRegUnPartyCsv');

    Route::any('perWomenStatePartView', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@participationofwomeninstateparties')->name('perWomenStatePartView');
    Route::any('perWomenStatePartPdf', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@participationofwomeninstateparties')->name('perWomenStatePartPdf');
    Route::any('perWomenStatePartCsv', 'IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@participationofwomeninstateparties')->name('perWomenStatePartCsv');

	/************************Coded By Mohd Saquib Ends here****************************/
//Neha Sah routes start

//finalised pc report

Route::get('FinalisedPCReport','IndexCardReports\FinalisedReport\FinalisedReportPCController@getFinalisedPCReport')->name('FinalisedPCReport');
Route::get('FinalisedPCReportPDF','IndexCardReports\FinalisedReport\FinalisedReportPCController@getFinalisedPCReport');
Route::get('FinalisedPCReportXls','IndexCardReports\FinalisedReport\FinalisedReportPCController@getFinalisedPCReport');

//not finalised pc report

Route::get('NotFinalisedPCReport','IndexCardReports\FinalisedReport\FinalisedReportPCController@getNotFinalisedPCReport');
Route::get('NotFinalisedPCReportPDF','IndexCardReports\FinalisedReport\FinalisedReportPCController@getNotFinalisedPCReport');
Route::get('NotFinalisedPCReportXls','IndexCardReports\FinalisedReport\FinalisedReportPCController@getNotFinalisedPCReport');

//no of finalized pc report

Route::get('NoofFinalizedPC','IndexCardReports\FinalisedReport\FinalisedReportPCController@getNoofFinalizedPC');
Route::get('NoofFinalizedPCPDF','IndexCardReports\FinalisedReport\FinalisedReportPCController@getNoofFinalizedPC');
Route::get('NoofFinalizedPCXls','IndexCardReports\FinalisedReport\FinalisedReportPCController@getNoofFinalizedPC');

//party details report
Route::get('PartyDetailsReportVol2','IndexCardReports\StatisticalReports\StatisticalReportsVolumeTwoController@getPartyDetailsReportVol2');
Route::get('PartyDetailsReportPDFVol2','IndexCardReports\StatisticalReports\StatisticalReportsVolumeTwoController@getPartyDetailsReportVol2');
Route::get('PartyDetailsReportXlsVol2','IndexCardReports\StatisticalReports\StatisticalReportsVolumeTwoController@getPartyDetailsReportVol2');

//voter information

Route::get('voterInformation','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getVoterInformation');
Route::get('voterInformationPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getVoterInformation');
Route::get('voterInformationXls','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getVoterInformation');

// indexcard brief report
Route::get('indexCardBriefReport','IndexCardReports\IndexCardReportPC\IndexCardReportPCController@getIndexCardReportPC');
Route::get('indexCardBriefReportPDF','IndexCardReports\IndexCardReportPC\IndexCardReportPCController@getIndexCardReportPC');


//participation of Women in national parties

Route::get('ParticipationofWomenInNationalParties','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInNationalParties');
Route::get('ParticipationofWomenInNationalPartiesPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInNationalParties');
Route::get('ParticipationofWomenInNationalPartiesXls','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInNationalParties');

//participation of Women As independent candidate for PCCEO Login

Route::get('ParticipationofWomenAsIndependentCandidates','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenAsIndependentCandidates');
Route::get('ParticipationofWomenAsIndependentCandidatesPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenAsIndependentCandidates');
Route::get('ParticipationofWomenAsIndependentCandidatesXls','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenAsIndependentCandidates');

//participation of Women As independent candidate for PCCEO Login



//participation of Women in registered parties

Route::get('ParticipationofWomenInRegisteredParties','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInRegisteredParties');
Route::get('ParticipationofWomenInRegisteredPartiesPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getParticipationofWomenInRegisteredParties');

//Constituency Wise Detailed Result
Route::get('ConstituencyWiseDetailedResult','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');
Route::get('ConstituencyWiseDetailedResultPDF','IndexCardReports\StatisticalReports\StatisticalReportsVolumeOneController@getConstituencyWiseDetailedResult');

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

	Route::any('numberandtypesofconstituencies_xls', 'IndexCardReports\StatisticalReportPC\StatisticalReportPCController@numberandtypesofconstituencies_xls')->name('numberandtypesofconstituencies_xls');

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

	Route::get('statewisevoterturnout', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewisevoterturnout');
    Route::get('downloadstatewisevoterturnout', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadstatewisevoterturnout');
    Route::get('pollingstationinformation', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@pollingstationinformation');
    Route::get('downloadpollingstationinformation', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadpollingstationinformation');
	Route::get('pcwisevoterturnout', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@pcwisevoterturnout');
	Route::get('downloadpcwisevoterturnout', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadpcwisevoterturnout');
	Route::get('performanceofstateparties', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@performanceofstateparties');
    Route::get('downloadperformanceofstateparties', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadperformanceofstateparties');
	Route::get('statewisevoterturnoutexcel', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@statewisevoterturnoutexcel');
	Route::get('partywiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@partywiseseatwonvalidvotes');
	Route::get('downloadpartywiseseatwonvalidvotes', 'IndexCardReports\StatisticalReportPC\PCStatisticalreport@downloadpartywiseseatwonvalidvotes');

    #Jitendra Singh Code End



// Praveen Route Start Here


Route::any('Political_party_Wise_Deposits_Forfeited',
'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

Route::get('Political_party_Wise_Deposits_ForfeitedPDF',
		'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');

Route::get('Political_party_Wise_Deposits_ForfeitedXLS',
			'IndexCardReports\Political_party_Wise_Deposits_Forfeited\Political_party_Wise_Deposits_ForfeitedController@index');


Route::get('AssemblySegmentWiseInformationElectors',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

Route::get('AssemblySegmentWiseInformationElectorsPDF',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

Route::get('AssemblySegmentWiseInformationElectorsXLS',
			'IndexCardReports\Assembly_Segment_Wise_Information_Electors\Assemblr_Segment_Wise_Information_ElectorsController@index');

Route::any('StateWiseCandidateDataSummary',
	'IndexCardReports\State_Wise_Candidate_Data_Summry\State_Wise_Candidate_Data_SummryController@index');

Route::any('StateWiseCandidateDataSummaryPDF',
	'IndexCardReports\State_Wise_Candidate_Data_Summry\State_Wise_Candidate_Data_SummryController@index');


Route::any('Winning-candidate-analysis-over-total-valid-votes',
	'IndexCardReports\WinningCandidateAnalysisVotes\Winning_Candidate_Analysis_Over_Total_VotesController@index');

Route::any('Winning-candidate-analysis-over-total-valid-votesPDF',
	'IndexCardReports\WinningCandidateAnalysisVotes\Winning_Candidate_Analysis_Over_Total_VotesController@index');

Route::any('constituencyDataSummaryReport','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
	->name('constituencyDataSummaryReport');

	Route::any('constituencyDataSummaryReportPDF','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
	->name('constituencyDataSummaryReportPDF');

	Route::any('constituencyDataSummaryReportXLS','IndexCardReports\ConstituencyDataSummary\ConstituencyDataSummaryController@index')
	->name('constituencyDataSummaryReportXls');

	Route::any('State-Wise-Overseas-Electors-Voters',
	'IndexCardReports\StateWiseOverseasElectorsVoters\State_Wise_Electors_votersController@index');

Route::any('State-Wise-Overseas-Electors-VotersPDF',
	'IndexCardReports\StateWiseOverseasElectorsVoters\State_Wise_Electors_votersController@index');

// Praveen Route Ends Here
/// Amit's code start//
Route::get('indexcard-report-listing', 'IndexCardReports\Condidatedatasummary\Condidatedatasummary@indexcardreport');
Route::get('Statistical-report-listing', 'IndexCardReports\Condidatedatasummary\Condidatedatasummary@Statisticalreport');

Route::get('candidateDataSummary', 'IndexCardReports\Condidatedatasummary\Condidatedatasummary@getcandidateDataSummary');
   Route::get('candidateDataSummaryPDF', 'IndexCardReports\Condidatedatasummary\Condidatedatasummary@getcandidateDataSummaryPDF');
   Route::get('Candidate-DataSummary-Excel', 'IndexCardReports\Condidatedatasummary\Condidatedatasummary@getcandidateDataSummaryExcel');
    Route::get('Eci-Performance-national-parties', 'IndexCardReports\performancenationalparties\performancenationalparties@index');
    Route::get('Performance-national-parties', 'IndexCardReports\performancenationalparties\performancenationalparties@index');
    Route::get('Performance-of-national-parties-pdf', 'IndexCardReports\performancenationalparties\performancenationalparties@performanceofnatiionalpartiespdf');
    Route::get('Performance-state-parties', 'IndexCardReports\performancenationalparties\performancenationalparties@performanceSate');
    Route::get('Performance-state-parties-pdf', 'IndexCardReports\performancenationalparties\performancenationalparties@performanceSatepfd');
    Route::get('Performance-national-parties-pdf', 'IndexCardReports\performancenationalparties\performancenationalparties@indexpdf');
    Route::get('Performance-national', 'IndexCardReports\performancenationalparties\performancenationalparties@performance');
    Route::get('Eci-list-of-successfull-candidate', 'IndexCardReports\performancenationalparties\performancenationalparties@successfullcondidate');
    Route::get('List-of-successfull-candidate', 'IndexCardReports\performancenationalparties\performancenationalparties@successfullcondidate');
    Route::get('Eci-list-of-successfull-candidatepdf', 'IndexCardReports\performancenationalparties\performancenationalparties@successfullcondidatePDF');
    Route::get('list-of-successfull-candidatepdf', 'IndexCardReports\performancenationalparties\performancenationalparties@successfullcondidatePDF');
    Route::get('list-of-successfull-candidatexls', 'IndexCardReports\performancenationalparties\performancenationalparties@successfullcondidateExcel');
    Route::get('State-wise-overseas-electors-voters', 'IndexCardReports\performancenationalparties\performancenationalparties@statewiseoverseaselectorsvoters');
    Route::get('Eci-State-wise-overseas-electors-voters', 'IndexCardReports\performancenationalparties\performancenationalparties@statewiseoverseaselectorsvoters');
    Route::get('State-wise-overseas-electors-voters-pdf', 'IndexCardReports\performancenationalparties\performancenationalparties@statewiseoverseaselectorsvoterspdf');
    Route::get('Eci-state-wise-overseas-electors-voters-pdf', 'IndexCardReports\performancenationalparties\performancenationalparties@statewiseoverseaselectorsvoterspdf');
    Route::get('State-wise-overseas-electors-voters', 'IndexCardReports\performancenationalparties\performancenationalparties@allstatewiseoverseaselectorsvoter');
    Route::get('All-State-wise-overseas-electors-voters-pdf', 'IndexCardReports\performancenationalparties\performancenationalparties@allstatewiseoverseaselectorsvoterpdf');
   /* Route::get('Details-of-repoll-held', 'Detailsofrepollheld@index');
    Route::get('Details-of-repoll-held-pdf', 'Detailsofrepollheld@'); */
	Route::get('Eci-details-of-repoll-held', 'IndexCardReports\Detailsofrepollheld\Detailsofrepollheld@index');

	Route::get('Details-of-repoll-held', 'IndexCardReports\Detailsofrepollheld\Detailsofrepollheld@index');
	Route::get('Eci-details-of-repoll-held-pdf', 'IndexCardReports\Detailsofrepollheld\Detailsofrepollheld@Detailsofrepollheldpdf');
	Route::get('Details-of-repoll-held-pdf', 'IndexCardReports\Detailsofrepollheld\Detailsofrepollheld@Detailsofrepollheldpdf');
    Route::get('Eci-winning-candidate-analysis-over-total-electors', 'IndexCardReports\winningcondidateanalysisoverelectors\Winningcondidateanalysisoverelectors@index');
    Route::get('winning-candidate-analysis-over-total-electors', 'IndexCardReports\winningcondidateanalysisoverelectors\Winningcondidateanalysisoverelectors@index');
    Route::get('winning-condidate-analysisover-elector-pdf', 'IndexCardReports\winningcondidateanalysisoverelectors\Winningcondidateanalysisoverelectors@winningcpndidateanalysisoverelectorpdf');
    Route::get('Eci-winning-condidate-analysisover-elector-pdf', 'IndexCardReports\winningcondidateanalysisoverelectors\Winningcondidateanalysisoverelectors@winningcpndidateanalysisoverelectorpdf');
    Route::get('winning-condidate-analysisover-elector-pdf', 'IndexCardReports\winningcondidateanalysisoverelectors\Winningcondidateanalysisoverelectors@winningcpndidateanalysisoverelectorpdf');

	Route::get('winning-condidate-analysisover-elector-xls', 'IndexCardReports\winningcondidateanalysisoverelectors\Winningcondidateanalysisoverelectors@winningcpndidateanalysisoverelectorxls');
/// Amit's end code start//

	});
	############################## Start DEO ######################################	
	Route::group(['prefix' => 'pcdeo', 'as' => 'pcdeo::', 'middleware' => ['auth:admin', 'auth']], function(){
		
	 Route::get('indexcardpc', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@pcwisedata')->name('pcwisedata');
		Route::any('getindexcarddata', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcarddata')->name('getindexcarddata');
		Route::get('indexcardpcpdf/{pc}', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcarddatapdf')->name('getindexcarddatapdf');

		Route::get('indexcardpcexcel/{pc}', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcarddataexcel')->name('getindexcarddataexcel');

		Route::get('indexcardbriefed', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@pcwisebriefdata')->name('pcwisebriefdata');
		Route::any('getindexcardbriefdata', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcardbriefdata')->name('getindexcardbriefdata');
		Route::get('indexcardbriefedpdf/{pc}', 'IndexCardReports\IndexCardDataPC\IndexCardDataPCFormController@getindexcardbriefdatapdf')->name('getindexcardbriefdatapdf');
	  
	}); 
	############################## End DEO ######################################
?>
