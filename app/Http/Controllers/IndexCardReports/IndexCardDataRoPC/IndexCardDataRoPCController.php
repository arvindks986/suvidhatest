<?php

namespace App\Http\Controllers\IndexCardReports\IndexCardDataRoPC;

		use Illuminate\Http\Request;
		use App\Http\Controllers\Controller;
		use Session;
		 
		use Illuminate\Support\Facades\Auth;
		use Illuminate\Support\Facades\Input;
		use Illuminate\Support\Facades\Redirect;
		use Carbon\Carbon;
		use DB;
		use Illuminate\Support\Facades\Hash;
		use Validator;
		use Config;
		use App;
		use \PDF;
		use Excel;
		use MPDF;
		use App\commonModel;
		use App\adminmodel\CandidateModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\adminmodel\ROPCModel;
		use App\adminmodel\ROPCReportModel;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;

class IndexCardDataRoPCController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('adminsession');
			$this->middleware(['auth:admin','auth']);
			$this->commonModel = new commonModel();
			$this->CandidateModel = new CandidateModel();
			$this->romodel = new ROPCModel();
			$this->ropcreportmodel = new ROPCReportModel();
			$this->xssClean = new xssClean;
			$this->sym = new SymbolMaster();
    }
   
    public function getindexcarddata(Request $request){
        
    	$session = $request->session()->all();
      //dd($session);
        $user = Auth::user();

        //changes waseem
   $data = \App\Http\Controllers\Admin\Indexcard\ComplainController::get_help_data($request);
   if($request->has('pc_no')){
		$data['pc_no']       = $request->pc_no;
	  } 

	  if($request->has('st_code')){
		$data['st_code']       = $request->st_code;
	  }     

	  if(\Auth::user()->role_id == '18'){
		$data['pc_no']          = \Auth::user()->pc_no;
		$data['st_code']        = \Auth::user()->st_code;
		$filter['pc_no']        = $data['pc_no'];
		$filter['st_code']      = $data['st_code'];
	  }

	  if(\Auth::user()->role_id == '4'){
		$data['st_code']        = \Auth::user()->st_code;
		$filter['st_code']      = $data['st_code'];
	  }
	  $data['states'] = [];
	  $states = App\models\Admin\StateModel::get_states($filter);
	  foreach ($states as $key => $iterate_state) {
	   $data['states'][] = [
		 'st_code' => $iterate_state->ST_CODE,
		 'st_name' => $iterate_state->ST_NAME,
	   ];
	 }
	 
	 if(Auth::user()->role_id == '4' && $request->has('complain')){
	  if($request->has('pc_no')){
		$user->pc_no = $request->pc_no;
	  }else{
		$data['results']    = [];
		$data['user_data']  =   Auth::user();
		return view('admin.indexcard.indexcard_error',$data);
	  }
	}
		//end changes waseem
		
		
		
     

           $uid   = $user->id;
     $d     = $user;


		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		    if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
           }

			$seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);

           /* $sched=''; $search='';
           $status=$this->commonModel->allstatus();
           if(isset($ele_details)) {  $i=0;
             foreach($ele_details as $ed) {
				 
				 //echo '<pre>'; print_r($ed); die;
				 
               // $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               // $const_type=$ed->CONST_TYPE;
              }
           }
		   
		   $sched=$ele_details->ScheduleID;
		   $const_type=$ele_details->CONST_TYPE;
		   
           $session['election_detail'] = (array)$ele_details; */
           $session['election_detail']['st_code'] = $user->st_code;
           $session['election_detail']['st_name'] = $user->placename;
    	// echo "<pre>"; print_r($session); die;
    	$st_code = $user->st_code;
    	$pc = $user->pc_no;
    	$election_detail = $session['election_detail'];
        $user_data = $d;
    	$getIndexCardDataCandidatesVotesACWise = $this->getIndexCardDataCandidatesVotesACWise($user->st_code, $user->pc_no);
    	 



    	$getIndexCardDataPCWise = $this->getIndexCardDataPCWise($user->st_code, $user->pc_no, $ele_details->ScheduleID);
    	
	//	echo "<pre>"; print_r($getIndexCardDataPCWise); die;
		
		
    	$getelectorsacwise = $this->getelectorsacwise($user->st_code, $user->pc_no);
		
		
if($request->path() == 'ropc/indexcardpcpdf'){
$pdf=PDF::loadView
('IndexCardReports.IndexCardDataPCWise.indexcardreportpcpdf',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','getelectorsacwise','user_data','ele_details','cand_finalize_ro'));
return $pdf->download('IndexCardReport.pdf');
	
}else if($request->path() == 'ropc/indexcardpcexcel'){
	
	return Excel::create('IndexCard Report', function($excel) use($getIndexCardDataCandidatesVotesACWise,$getIndexCardDataPCWise,$getelectorsacwise,$st_code,$pc ){
             $excel->sheet('mySheet', function($sheet) use($getIndexCardDataCandidatesVotesACWise,$getIndexCardDataPCWise,$getelectorsacwise,$st_code,$pc){

               $year = getElectionYear();
					     $st=getstatebystatecode($st_code);

               $sheet->mergeCells('A1:J1');
               $sheet->mergeCells('A2:J2');
               $sheet->mergeCells('A4:J4');
               $sheet->mergeCells('A5:J5');

               $sheet->cells('A1', function($cells) {
                       $cells->setValue('Election Commistion of India');
                       $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                       $cells->setAlignment('center');
                       });

              $sheet->cells('A2', function($cells) use ($year) {
                      $cells->setValue('Bye Election - '.$year);
					            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					            $cells->setAlignment('center');
                     });

              $sheet->cells('A4', function($cells) use($st) {
                      $cells->setValue('Parliamentary Constituency of '.$st->ST_NAME);
						          $cells->setFont(array('name' => 'Times New Roman','size' => 12));
						          $cells->setAlignment('center');
                       });

              $sheet->cells('A5', function($cells) use($getIndexCardDataPCWise){
                      $cells->setValue('No. and Name of Parliamentary Constituancy '.$getIndexCardDataPCWise['pcType']->PC_NO.'- '. $getIndexCardDataPCWise['pcType']->PC_NAME);
         						  $cells->setFont(array('name' => 'Times New Roman','size' => 12));
         						  $cells->setAlignment('center');
                      });
              // Header Ends for Excel
              //Body Start from here

              $sheet->cells('A7', function($cells){
                      $cells->setValue('I');;
						          $cells->setFont(array('bold' => true));
                      });

					$sheet->cells('B7', function($cells){
                  $cells->setValue('CANDIDATES');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('C7', function($cells){
                  $cells->setValue('MALE');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('D7', function($cells){
                  $cells->setValue('FEMALE');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('E7', function($cells){
                  $cells->setValue('THIRD GENDER');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('F7', function($cells){
                  $cells->setValue('TOTAL');
						      $cells->setFont(array('bold' => true));
                  });

          $sheet->cells('A8', function($cells){
                  $cells->setValue('1');
                  });

					$sheet->cells('B8', function($cells){
                  $cells->setValue('Nominated');
                  });

					$sheet->cells('C8', function($cells) use($getIndexCardDataPCWise){
                  $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_m_t);
                  });

					$sheet->cells('D8', function($cells) use($getIndexCardDataPCWise){
                  $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_f_t);
                  });

					$sheet->cells('E8', function($cells) use($getIndexCardDataPCWise){
                  $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_o_t);
                  });

					$sheet->cells('F8', function($cells) use($getIndexCardDataPCWise){
                  $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_a_t);
                  });

          $sheet->cells('A9', function($cells){
                $cells->setValue('2');
               });

					$sheet->cells('B9', function($cells){
                        $cells->setValue('Nominations  Rejected');
                       });

					$sheet->cells('C9', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_r_m);
                       });

					$sheet->cells('D9', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_r_f);
                       });

					$sheet->cells('E9', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_r_o);
                       });

					$sheet->cells('F9', function($cells) use($getIndexCardDataPCWise){
                      $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_r_a);
                       });

         $sheet->cells('A10', function($cells){
                       $cells->setValue('3');
                      });

  				$sheet->cells('B10', function($cells){
                       $cells->setValue('Withdrawn');
                      });

 					$sheet->cells('C10', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_w_m);
                        });

 					$sheet->cells('D10', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_w_f);
                        });

 					$sheet->cells('E10', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_w_o);
                        });

 					$sheet->cells('F10', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_w_t);
                        });

          $sheet->cells('A11', function($cells){
                        $cells->setValue('4');
                       });

					$sheet->cells('B11', function($cells){
                        $cells->setValue('Contested');
                       });

					$sheet->cells('C11', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_co_m);
                       });

					$sheet->cells('D11', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_co_f);
                       });

					$sheet->cells('E11', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_co_o);
                       });

					$sheet->cells('F11', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_co_t);
                       });


					$sheet->cells('A12', function($cells){
                        $cells->setValue('5');
                       });

					$sheet->cells('B12', function($cells){
                        $cells->setValue('Deposit Forfeited');
                       });

					$sheet->cells('C12', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_m?:'=(0)');
                       });

					$sheet->cells('D12', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_f?:'=(0)');
                       });

					$sheet->cells('E12', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_o?:'=(0)');
                       });

					$sheet->cells('F12', function($cells) use($getIndexCardDataPCWise){
                        $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->c_nom_fd_t?:'=(0)');
                       });

          $sheet->cells('A14', function($cells){
                         $cells->setValue('II');;
  						           $cells->setFont(array('bold' => true));
                        });

  					$sheet->cells('B14', function($cells){
                         $cells->setValue('ELECTORS');
  						           $cells->setFont(array('bold' => true));
                        });

  					$sheet->mergeCells('C14:D14');

  					$sheet->cells('C14', function($cells){
                    $cells->setValue('GENERAL');
  						      $cells->setFont(array('bold' => true));
                    });



  					$sheet->cells('E14', function($cells){
                         $cells->setValue('SERVICE');
  						$cells->setFont(array('bold' => true));
                        });

  					$sheet->cells('F14', function($cells){
                         $cells->setValue('TOTAL');
  						$cells->setFont(array('bold' => true));
                        });



  					$sheet->cells('C15', function($cells){
                         $cells->setValue('Other than NRIs');
  						$cells->setFont(array('bold' => true));
                        });

  					$sheet->cells('D15', function($cells){
                         $cells->setValue('NRIs');
  						$cells->setFont(array('bold' => true));
                        });




  					$sheet->cells('A16', function($cells){
                         $cells->setValue('1');
                        });

  					$sheet->cells('B16', function($cells){
                         $cells->setValue('Male');
                        });

  					$sheet->cells('C16', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_gen_m?:'=(0)');
                        });

  					$sheet->cells('D16', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_nri_m?:'=(0)');
                        });

  					$sheet->cells('E16', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_ser_m?:'=(0)');
                        });

  					$sheet->cells('F16', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_all_t_m?:'=(0)');
                        });

  					$sheet->cells('A17', function($cells){
                         $cells->setValue('2');
                        });

  					$sheet->cells('B17', function($cells){
                         $cells->setValue('Female');
                        });

  					$sheet->cells('C17', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue( $getIndexCardDataPCWise['t_pc_ic']->e_gen_f?:'=(0)');
                        });

  					$sheet->cells('D17', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_nri_f?:'=(0)');
                        });

  					$sheet->cells('E17', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_ser_f?:'=(0)');
                        });

  					$sheet->cells('F17', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_all_t_f?:'=(0)');
                        });


  					$sheet->cells('A18', function($cells){
                         $cells->setValue('3');
                        });

  					$sheet->cells('B18', function($cells){
                         $cells->setValue('Third Gender(Not applicable to Service electors)');
                        });

  					$sheet->cells('C18', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_gen_o?:'=(0)');
                        });

  					$sheet->cells('D18', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_nri_o?:'=(0)');
                        });

  					$sheet->cells('E18', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_ser_o?:'=(0)');
                        });

  					$sheet->cells('F18', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_all_t_o?:'=(0)');
                        });

  					$sheet->cells('A19', function($cells){
                         $cells->setValue('4');
                        });

  					$sheet->cells('B19', function($cells){
                         $cells->setValue('Total');
                        });

  					$sheet->cells('C19', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_gen_t?:'=(0)');
                        });

  					$sheet->cells('D19', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_nri_t?:'=(0)');
                        });

  					$sheet->cells('E19', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_ser_t?:'=(0)');
                        });

  					$sheet->cells('F19', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->e_all_t?:'=(0)');
                        });



  					// No 3

  					$sheet->cells('A21', function($cells){
                         $cells->setValue('III');;
  						$cells->setFont(array('bold' => true));
                        });

  					$sheet->cells('B21', function($cells){
                         $cells->setValue('VOTERS TURNED UP FOR VOTING');
  						$cells->setFont(array('bold' => true));
                        });

  					$sheet->mergeCells('C21:D21');

  					$sheet->cells('C21', function($cells){
                         $cells->setValue('GENERAL');
  						$cells->setFont(array('bold' => true));
                        });

  					$sheet->mergeCells('E21:F21');

  					$sheet->cells('E21', function($cells){
                         $cells->setValue('TOTAL');
  						$cells->setFont(array('bold' => true));
                        });



  					$sheet->cells('C22', function($cells){
                         $cells->setValue('Other than NRIs');
  						$cells->setFont(array('bold' => true));
                        });

  					$sheet->cells('D22', function($cells){
                         $cells->setValue('NRIs');
  						$cells->setFont(array('bold' => true));
                        });

  					$sheet->mergeCells('E22:F22');


  					$sheet->cells('A23', function($cells){
                         $cells->setValue('1');
                        });

  					$sheet->cells('B23', function($cells){
                         $cells->setValue('Male');
                        });

  					$sheet->cells('C23', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_m?:'=(0)');
                        });

  					$sheet->cells('D23', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_nri_m?:'=(0)');
                        });

  					$sheet->mergeCells('E23:F23');

  					$sheet->cells('E23', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_m +$getIndexCardDataPCWise['t_pc_ic']->vt_nri_m?:'=(0)');
  						$cells->setAlignment('right');
                        });



  					$sheet->cells('A24', function($cells){
                         $cells->setValue('2');
                        });

  					$sheet->cells('B24', function($cells){
                         $cells->setValue('Female');
                        });

  					$sheet->cells('C24', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_f?:'=(0)');
                        });

  					$sheet->cells('D24', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_nri_f?:'=(0)');
                        });

  					$sheet->mergeCells('E24:F24');

  					$sheet->cells('E24', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_f + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_f?:'=(0)');
  						$cells->setAlignment('right');
                        });

  					$sheet->cells('A25', function($cells){
                         $cells->setValue('3');
                        });

  					$sheet->cells('B25', function($cells){
                         $cells->setValue('Third Gender');
                        });

  					$sheet->cells('C25', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_o?:'=(0)');
                        });

  					$sheet->cells('D25', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_nri_o?:'=(0)');
                        });


  					$sheet->mergeCells('E25:F25');
  					$sheet->cells('E25', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_o + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_o?:'=(0)');
  						$cells->setAlignment('right');
                        });


  					$sheet->cells('A26', function($cells){
                         $cells->setValue('4');
                        });

  					$sheet->cells('B26', function($cells){
                         $cells->setValue('Total(Male + Female + Third Gender)');
                        });

  					$sheet->cells('C26', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_t?:'=(0)');
                        });

  					$sheet->cells('D26', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_nri_t?:'=(0)');
                        });

  					$sheet->mergeCells('E26:F26');
  					$sheet->cells('E26', function($cells) use($getIndexCardDataPCWise){
                         $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->vt_gen_t + $getIndexCardDataPCWise['t_pc_ic']->vt_nri_t?:'=(0)');
  						$cells->setAlignment('right');
                        });


                        // No 4

     					$sheet->cells('A28', function($cells){
                      $cells->setValue('IV');;
     						      $cells->setFont(array('bold' => true));
                      });

     					$sheet->mergeCells('B28:F28');

     					$sheet->cells('B28', function($cells){
                      $cells->setValue('DETAILS OF VOTES POLLED ON EVM');
     						      $cells->setFont(array('bold' => true));
                      });

     					$sheet->cells('A29', function($cells){
                      $cells->setValue('1');
                      });

     					$sheet->mergeCells('B29:E29');
     					$sheet->cells('B29', function($cells){
                      $cells->setValue('Total votes polled on EVM');
                      });

     					$sheet->cells('F29', function($cells) use($getIndexCardDataPCWise){
                      $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->t_votes_evm?:'=(0)');
                      });

     					$sheet->cells('A30', function($cells){
                      $cells->setValue('2');
                      });

     					$sheet->mergeCells('B30:E30');
     					$sheet->cells('B30', function($cells){
                             $cells->setValue('Test votes under Rule 49 MA');
                            });

     					$sheet->cells('F30', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->mock_poll_evm?:'=(0)');
                            });


     					$sheet->cells('A31', function($cells){
                             $cells->setValue('3A');
                            });

     					$sheet->mergeCells('B31:B31');
     					$sheet->cells('B31', function($cells){
                             $cells->setValue('Votes Counted From CU Of EVM');
                            });

     					$sheet->cells('C31', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->votes_counted_from_evm?:'=(0)');
                            });
							
							
						$sheet->cells('D31', function($cells){
                             $cells->setValue('3B');
                            });

     					$sheet->mergeCells('E31:E31');
     					$sheet->cells('E31', function($cells){
                             $cells->setValue('Votes Counted From VVPAT (Whenever Votes Not Retrieved From CU)');
                            });

     					$sheet->cells('F31', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->votes_counted_from_vvpat?:'=(0)');
                            });

     					$sheet->cells('A32', function($cells){
                             $cells->setValue('4');
                            });

     					$sheet->mergeCells('B32:E32');
     					$sheet->cells('B32', function($cells){
                             $cells->setValue('Rejected votes (due to other reasons)');
                            });

     					$sheet->cells('F32', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->r_votes_evm?:'=(0)');
                            });


     					$sheet->cells('A33', function($cells){
                             $cells->setValue('5');
                            });

     					$sheet->mergeCells('B33:E33');
     					$sheet->cells('B33', function($cells){
                             $cells->setValue("Votes polled for 'NOTA' on EVM");
                            });

     					$sheet->cells('F33', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue(($getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota)?:'=(0)');
                            });


     					$sheet->cells('A34', function($cells){
                             $cells->setValue('6');
                            });

     					$sheet->mergeCells('B34:E34');
     					$sheet->cells('B34', function($cells){
                             $cells->setValue("Total of test votes + votes rejected (due to other reasons) + 'NOTA'[2+4+5]");
                            });

     					$sheet->cells('F34', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->v_r_evm_all?:'=(0)');
                            });

     					$sheet->cells('A35', function($cells){
                             $cells->setValue('7');
                            });

     					$sheet->mergeCells('B35:E35');
     					$sheet->cells('B35', function($cells){
                             $cells->setValue("Total valid votes counted from EVM [1-6]");
                            });

     					$sheet->cells('F35', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->v_votes_evm_all?:'=(0)');
                            });

     					// No 5

     					$sheet->cells('A37', function($cells){
                             $cells->setValue('V');;
     						$cells->setFont(array('bold' => true));
                            });

     					$sheet->mergeCells('B37:F37');

     					$sheet->cells('B37', function($cells){
                             $cells->setValue('DETAILS OF POSTAL VOTES');
     						$cells->setFont(array('bold' => true));
                            });

     					$sheet->cells('A38', function($cells){
                             $cells->setValue('1');
                            });

     					$sheet->mergeCells('B38:E38');
     					$sheet->cells('B38', function($cells){
                             $cells->setValue('Postal votes counted for service voters under sub-section (8) of Section 20 of R.P.Act, 1950');
                            });

     					$sheet->cells('F38', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_u?:'=(0)');
                            });

     					$sheet->cells('A39', function($cells){
                             $cells->setValue('2');
                            });

     					$sheet->mergeCells('B39:E39');
     					$sheet->cells('B39', function($cells){
                             $cells->setValue('Postal votes counted for Govt. servants on election duty (including all police personnel, driver, conductors, cleaner');
                            });

     					$sheet->cells('F39', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_vote_ser_o?:'=(0)');
                            });


     					$sheet->cells('A40', function($cells){
                             $cells->setValue('3');
                            });

     					$sheet->mergeCells('B40:E40');
     					$sheet->cells('B40', function($cells){
                             $cells->setValue('Postal votes rejected');
                            });

     					$sheet->cells('F40', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_vote_rejected?:'=(0)');
                            });

     					$sheet->cells('A41', function($cells){
                             $cells->setValue('4');
                            });

     					$sheet->mergeCells('B41:E41');
     					$sheet->cells('B41', function($cells){
                             $cells->setValue("Postal votes polled for 'NOTA'");
                            });

     					$sheet->cells('F41', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota?:'=(0)');
                            });


     					$sheet->cells('A42', function($cells){
                             $cells->setValue('5');
                            });

     					$sheet->mergeCells('B42:E42');
     					$sheet->cells('B42', function($cells){
                             $cells->setValue("Total of postal votes rejected + NOTA [3+4]");
                            });

     					$sheet->cells('F42', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_vote_r_nota?:'=(0)');
                            });


     					$sheet->cells('A43', function($cells){
                             $cells->setValue('6');
                            });

     					$sheet->mergeCells('B43:E43');
     					$sheet->cells('B43', function($cells){
                             $cells->setValue("Total valid postal votes [1+2-5]");
                            });

     					$sheet->cells('F43', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_valid_votes?:'=(0)');
                            });


     					// No 6

     					$sheet->cells('A45', function($cells){
                             $cells->setValue('VI');;
     						$cells->setFont(array('bold' => true));
                            });

     					$sheet->mergeCells('B45:F45');

     					$sheet->cells('B45', function($cells){
                             $cells->setValue('COMBINED DETAILS OF EVM & POSTAL VOTES');
     						$cells->setFont(array('bold' => true));
                            });

     					$sheet->cells('A46', function($cells){
                             $cells->setValue('1');
                            });

     					$sheet->mergeCells('B46:E46');
     					$sheet->cells('B46', function($cells){
                             $cells->setValue('Total votes polled [IV(1) + V(1+2)]');
                            });

     					$sheet->cells('F46', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->total_votes_polled?:'=(0)');
                            });

     					$sheet->cells('A47', function($cells){
                             $cells->setValue('2');
                            });

     					$sheet->mergeCells('B47:E47');
     					$sheet->cells('B47', function($cells){
                             $cells->setValue("Total of test votes + votes rejected + 'NOTA'[IV(6) + V(5)]");
                            });

     					$sheet->cells('F47', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->total_not_count_votes?:'=(0)');
                            });


     					$sheet->cells('A48', function($cells){
                             $cells->setValue('3');
                            });

     					$sheet->mergeCells('B48:E48');
     					$sheet->cells('B48', function($cells){
                             $cells->setValue('Total valid votes [IV(7)+ V(6)]');
                            });

     					$sheet->cells('F48', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->total_valid_votes?:'=(0)');
                            });

     					$sheet->cells('A49', function($cells){
                             $cells->setValue('4');
                            });

     					$sheet->mergeCells('B49:E49');
     					$sheet->cells('B49', function($cells){
                             $cells->setValue("Total votes polled for 'NOTA'[IV(5) + V(4)]");
                            });

     					$sheet->cells('F49', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->total_votes_nota?:'=(0)');
                            });


     					// No 7

     					$sheet->cells('A51', function($cells){
                             $cells->setValue('VII');;
     						$cells->setFont(array('bold' => true));
                            });

     					$sheet->mergeCells('B51:F51');

     					$sheet->cells('B51', function($cells){
                             $cells->setValue('MISCELLANEOUS');
     						$cells->setFont(array('bold' => true));
                            });

     					$sheet->cells('A52', function($cells){
                             $cells->setValue('1');
                            });

     					$sheet->mergeCells('B52:E52');
     					$sheet->cells('B52', function($cells){
                             $cells->setValue('Proxy votes');
                            });

     					$sheet->cells('F52', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->proxy_votes?:'=(0)');
                            });

     					$sheet->cells('A53', function($cells){
                             $cells->setValue('2');
                            });

     					$sheet->mergeCells('B53:E53');
     					$sheet->cells('B53', function($cells){
                             $cells->setValue("Tendered votes");
                            });

     					$sheet->cells('F53', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->tendered_votes?:'=(0)');
                            });


     					$sheet->cells('A54', function($cells){
                             $cells->setValue('3');
                            });

     					$sheet->mergeCells('B54:E54');
     					$sheet->cells('B54', function($cells){
                             $cells->setValue('Total number of polling station set up in a Constituency');
                            });

     					$sheet->cells('F54', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->total_no_polling_station?:'=(0)');
                            });

     					$sheet->cells('A55', function($cells){
                             $cells->setValue('4');
                            });

     					$sheet->mergeCells('B55:E55');
     					$sheet->cells('B55', function($cells){
                             $cells->setValue("Averages number of Electors assigned to a polling station");
                            });

     					$sheet->cells('F55', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->avg_elec_polling_stn?:'=(0)');
                            });



     					$sheet->cells('A56', function($cells){
                             $cells->setValue('5');
                            });

     					$sheet->mergeCells('B56:E56');
     					$sheet->cells('B56', function($cells){
                             $cells->setValue('Date(s) Of Poll');
                            });

     					$sheet->cells('F56', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue(date(date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_poll))));
                            });

     					$sheet->cells('A57', function($cells){
                             $cells->setValue('6');
                            });

     					$sheet->mergeCells('B57:E57');
     					$sheet->cells('B57', function($cells){
                             $cells->setValue("Date(s) Of Re-Poll,(if any)");
                            });


     					if (trim($getIndexCardDataPCWise['t_pc_ic']->dt_repoll) != 0 && $getIndexCardDataPCWise['t_pc_ic']->dt_repoll){

     						$repoll_dates   = explode(',',$getIndexCardDataPCWise['t_pc_ic']->dt_repoll);
     						$dates_array    = [];
     						foreach($repoll_dates as $res_repoll){
     							$dates_array[] = date('d-m-Y', strtotime(trim($res_repoll)));
     							}
     						$sheet->cells('F57', function($cells) use($dates_array){
     							$cells->setValue( implode(', ', $dates_array) );
     						   });

     					}else{
     						$sheet->cells('F57', function($cells) {
     							$cells->setValue('NA');
     						   });
     					}

     					$sheet->cells('A58', function($cells){
                             $cells->setValue('7');
                            });

     					$sheet->mergeCells('B58:E58');
     					$sheet->cells('B58', function($cells){
                             $cells->setValue('Number of Polling stations where Re-poll was ordered(mention date of order also)');
                            });

     					$sheet->cells('F58', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->re_poll_station?:'NA');
                            });

     					$sheet->cells('A59', function($cells){
                             $cells->setValue('8');
                            });

     					$sheet->mergeCells('B59:E59');
     					$sheet->cells('B59', function($cells){
                             $cells->setValue('Date(s) Of counting');
                            });

     					$sheet->cells('F59', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue(date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_counting)));
                            });

     					$sheet->cells('A60', function($cells){
                             $cells->setValue('9');
                            });

     					$sheet->mergeCells('B60:E60');
     					$sheet->cells('B60', function($cells){
                             $cells->setValue('Date Of Declaration Of result');
                            });

     					$sheet->cells('F60', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue(date('d-m-Y', strtotime($getIndexCardDataPCWise['t_pc_ic']->dt_declare)));
                            });


     					$sheet->cells('A61', function($cells){
                             $cells->setValue('10');
                            });

     					$sheet->mergeCells('B61:E61');
     					$sheet->cells('B61', function($cells){
                             $cells->setValue('Whether this is Bye election or Countermanded election?');
                            });

     					$sheet->cells('F61', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue(($getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter == 1)? 'Yes':'No');
                            });

     					$sheet->cells('A62', function($cells){
                             $cells->setValue('11');
                            });

     					$sheet->mergeCells('B62:E62');
     					$sheet->cells('B62', function($cells){
                             $cells->setValue('If yes, reason thereof');
                            });

     					$sheet->cells('F62', function($cells) use($getIndexCardDataPCWise){
                             $cells->setValue($getIndexCardDataPCWise['t_pc_ic']->flag_bye_counter_reason?:'NA');
                            });


// No 8  index card 2nd form start

					$sheet->cells('A64', function($cells){
                  $cells->setValue('VIII');
			            $cells->setFont(array('bold' => true));
                  });

					$sheet->mergeCells('B64:J64');

					$sheet->cells('B64', function($cells){
                 $cells->setValue(' DETAILS OF VOTES POLLED BY EACH CANDIDATE');
						     $cells->setFont(array('bold' => true));
                 });

					$sheet->cells('A65', function($cells){
                  $cells->setValue('SL. No.');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('B65', function($cells){
                  $cells->setValue('Name of the Contesting Candidates(in Block Letters)');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('C65', function($cells){
                  $cells->setValue('Sex(Male/Female/Third Gender)');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('D65', function($cells){
                  $cells->setValue('Age(Years)');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('E65', function($cells){
                  $cells->setValue('Category(GEN/SC/ST)');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('F65', function($cells){
                  $cells->setValue('Full name of the Party');
						      $cells->setFont(array('bold' => true));
                  });

					$sheet->cells('G65', function($cells){
                  $cells->setValue('Election Symbol Alloted');
						      $cells->setFont(array('bold' => true));
                  });


			$chr = chr(71+count($getIndexCardDataCandidatesVotesACWise['allACList']));
			$sheet->mergeCells('H65:'.$chr.'65');

			for( $a = 1; $a <= count($getIndexCardDataCandidatesVotesACWise['allACList']); $a++ ){
			$t = 71+$a;
			$chr = chr(71+$a);

				$sheet->cells('H65', function($cells){
                $cells->setValue('Valid Votes counted From Electronic Voting Machines');
					$cells->setFont(array('bold' => true));
					$cells->setAlignment('center');
                });
			}
			

			if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){
				
				$chr = chr($t+1);
				$t++;
				
				$sheet->cells($chr.'65', function($cells){
					$cells->setValue('Migrant Votes');
					$cells->setFont(array('bold' => true));
				});
			 }
			
			
			$chr = chr($t+1);

			$sheet->cells($chr.'65', function($cells){
                $cells->setValue('Valid Postal Votes');
				$cells->setFont(array('bold' => true));
            });
			$t++;
			$chr = chr($t+1);
					$sheet->cells($chr.'65', function($cells){
                  $cells->setValue('Total Valid Votes');
						      $cells->setFont(array('bold' => true));
                  });


$a = 0;
                  foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){

               $t = 71+$a;

               $chr = chr($t+1);

               $sheet->cells($chr.'66', function($cells) use($allACListsKey,$allACListsValue){
                       $cells->setValue($allACListsKey.' : '.$allACListsValue);
                       $cells->setFont(array('bold' => true));
                       });
                  $a++;
                }



					$count=1; $i = 67;
                    $total_votes = $total_postel_votes = 0;
					
					 
					$dataSum  = array();
					
					$total_valid_postel_votes = 0;
					$total_valid_migrate_votes = 0;
					$total_valid_votes = 0;
					
					//$i=0;
					
                    foreach($getIndexCardDataCandidatesVotesACWise['candidatedataarray'] as $key1 => $candpcdata){
                      foreach($candpcdata as  $key2 => $canddata){
						  
						  
						$sheet->cells('A'.$i, function($cells) use($count){
							     $cells->setValue($count);
					   });
							$sheet->cells('B'.$i, function($cells) use($canddata){
									  $cells->setValue($canddata['cand_name']);
						});
							$sheet->cells('C'.$i, function($cells) use($canddata){
									 $cells->setValue(strtoupper($canddata['cand_gender']));
					   });
							$sheet->cells('D'.$i, function($cells) use($canddata){
									 $cells->setValue($canddata['cand_age']);
					   });
							$sheet->cells('E'.$i, function($cells) use($canddata){
									 $cells->setValue(strtoupper($canddata['cand_category']));
					   });
							$sheet->cells('F'.$i, function($cells) use($canddata){
									  $cells->setValue($canddata['partyname']);
						});
							$sheet->cells('G'.$i, function($cells) use($canddata){
									  $cells->setValue($canddata['party_symbol']);
						});
						$a=0;
					
					
						$sum = 0;
						foreach ($canddata['acdata'] as $key3 => $values) {
							
							$sum = $values;
												
							if (isset($dataSum[$key3]))
							{
								$dataSum[$key3] += $sum;
							}
							else
							{
								$dataSum[$key3] = $sum;
							}	
												
							$t = 71+$a;

							$chr = chr($t+1);
							$sheet->cells($chr.$i, function($cells) use($values){
								$cells->setValue($values);
							});
							$a++;
						}
						
							$migrate_votes = 0;
							
							if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){ 
								$migrate_votes = $canddata['migrate_votes'];
							
								$chr = chr($t+2);
								$t++;
								$sheet->cells($chr.$i, function($cells) use($canddata){
									$cells->setValue($canddata['migrate_votes']);
								});
																	
							}
							
						
						
							$chr = chr($t+2);
							$sheet->cells($chr.$i, function($cells) use($canddata){
									$cells->setValue($canddata['valid_postal_votes']);
							});
							$chr = chr($t+3);
							$sheet->cells($chr.$i, function($cells) use($canddata){
								$cells->setValue($canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $canddata['migrate_votes']);
							});

						}
						
						$total_valid_postel_votes += $canddata['valid_postal_votes'];
								$total_valid_migrate_votes += $canddata['migrate_votes'];
							$total_valid_votes += $canddata['valid_postal_votes']+$canddata['total_valid_vote'] + $migrate_votes;
						$i++; $count++;
					}



						$migrate_vote_nota = 0;
						$postal_vote_nota = 0;
						$total_nota = 0;
						
						
						if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){

							
							
							$sheet->cells('A'.$i, function($cells) use($count){
								$cells->setValue($count);
							});
							
							$sheet->cells('B'.$i, function($cells) use($count){
								$cells->setValue('Nota');
							});
							
							$sheet->cells('C'.$i, function($cells) use($count){
								$cells->setValue('-');
							});
							$sheet->cells('D'.$i, function($cells) use($count){
								$cells->setValue('-');
							});
							$sheet->cells('E'.$i, function($cells) use($count){
								$cells->setValue('-');
							});
							$sheet->cells('F'.$i, function($cells) use($count){
								$cells->setValue('-');
							});
							$sheet->cells('G'.$i, function($cells) use($count){
								$cells->setValue('-');
							});
							


							$a =0;
							foreach($getIndexCardDataPCWise['migrate_nota'] as $key4 => $dataValue){
							
							$t = 71+$a;

							$chr = chr($t+1);
							$sheet->cells($chr.$i, function($cells) use($dataValue){
								$cells->setValue($dataValue['total_vote']);
							});
							$a++;
							
							
								if (isset($dataSum[$key4]))
								{
									$dataSum[$key4] += $dataValue['total_vote'];
								}
								else
								{
									$dataSum[$key4] = $dataValue['total_vote'];
								}							
					
							
							}
							

						$chr = chr($t+2);
						$t++;
						$sheet->cells($chr.$i, function($cells) use($getIndexCardDataPCWise){
							$cells->setValue($getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota);
						});

					

						$chr = chr($t+2);
						$sheet->cells($chr.$i, function($cells) use($getIndexCardDataPCWise){
							$cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota);
						});
						$chr = chr($t+3);
						$sheet->cells($chr.$i, function($cells) use($getIndexCardDataPCWise){
							$cells->setValue($getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota);
						});
						$i++;

						$migrate_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;
						$postal_vote_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota;
						$total_nota = $getIndexCardDataPCWise['t_pc_ic']->postal_vote_nota+$getIndexCardDataPCWise['t_pc_ic']->nota_vote_evm + $getIndexCardDataPCWise['t_pc_ic']->migrate_vote_nota;


						}




        $sheet->mergeCells('A'.$i.':G'.$i);

		$sheet->cells('A'.$i, function($cells){
			$cells->setValue('TOTAL');
			$cells->setAlignment('right');
			$cells->setFont(array('bold' => true));
		});

			$a =0;

			foreach($dataSum as $dataValue){
				
			$t = 71+$a;

				$chr = chr($t+1);
				$sheet->cells($chr.$i, function($cells) use($dataValue){
					$cells->setValue($dataValue);
					$cells->setFont(array('bold' => true));
				});
				$a++;	
	
			}
			
			
			if(($st_code == 'S09') && ($getIndexCardDataPCWise['pcType']->PC_NO == '1' || $getIndexCardDataPCWise['pcType']->PC_NO == '2' || $getIndexCardDataPCWise['pcType']->PC_NO == '3') ){
				
				$chr = chr($t+2);
				$t++;
				$sheet->cells($chr.$i, function($cells) use($total_valid_migrate_votes,$migrate_vote_nota){
					$cells->setValue($total_valid_migrate_votes + $migrate_vote_nota);
					$cells->setFont(array('bold' => true));
				});
							
			}
			

			$chr = chr($t+2);
			$sheet->cells($chr.$i, function($cells) use($total_valid_postel_votes,$postal_vote_nota){
				$cells->setValue($total_valid_postel_votes + $postal_vote_nota);
				$cells->setFont(array('bold' => true));
			});
			$chr = chr($t+3);
			$sheet->cells($chr.$i, function($cells) use($total_valid_votes,$total_nota){
				$cells->setValue($total_valid_votes + $total_nota);
				$cells->setFont(array('bold' => true));
			});
            $i++;
            // No 8  index card 2nd form Ends


            // No 9 index card 3rd Form Start

            $sheet->cells('A'.$i, function($cells){
                    $cells->setValue('IX');
  			            $cells->setFont(array('bold' => true));
                    });

  					$sheet->mergeCells('B'.$i.':J'.$i);

  					$sheet->cells('B'.$i, function($cells){
                   $cells->setValue(' Data For Election AC Wise');
  						     $cells->setFont(array('bold' => true));
                   });

                  $i++;

			$sheet->mergeCells('B'.$i.':C'.$i);

          $sheet->cells('B'.$i, function($cells){
                  $cells->setValue('Total Electors');
         					$cells->setFont(array('bold' => true));
                  });

  
			$sheet->mergeCells('B'.($i+1).':B'.($i+4));
			$sheet->cells('B'.($i+1), function($cells){
                $cells->setValue('1. General [Other than NRIs]');
				$cells->setFont(array('bold' => true));
				$cells->setValignment('center');

                });
  
  
        $sheet->cells('C'.($i+1), function($cells){
                $cells->setValue('Male');
				$cells->setFont(array('bold' => true));


                });
        $sheet->cells('C'.($i+2), function($cells){
                $cells->setValue('Female');
				$cells->setFont(array('bold' => true));


                });
        $sheet->cells('C'.($i+3), function($cells){
                $cells->setValue('Third Gender');
				$cells->setFont(array('bold' => true));


                });
        $sheet->cells('C'.($i+4), function($cells){
                $cells->setValue('Total');
                $cells->setFont(array('bold' => true));

                });


		$sheet->mergeCells('B'.($i+6).':B'.($i+9));
			$sheet->cells('B'.($i+6), function($cells){
                $cells->setValue('2. General [NRIs]');
				$cells->setFont(array('bold' => true));
				$cells->setValignment('center');

                });

        $sheet->cells('C'.($i+6), function($cells){
                $cells->setValue('Male');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+7), function($cells){
                $cells->setValue('Female');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+8), function($cells){
                $cells->setValue('Third Gender');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+9), function($cells){
                $cells->setValue('Total');
                $cells->setFont(array('bold' => true));

                });

		$sheet->mergeCells('B'.($i+11).':B'.($i+14));
			$sheet->cells('B'.($i+11), function($cells){
                $cells->setValue('3. Service');
				$cells->setFont(array('bold' => true));
				$cells->setValignment('center');

                });


        $sheet->cells('C'.($i+11), function($cells){
                $cells->setValue('Male');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+12), function($cells){
                $cells->setValue('Female');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+13), function($cells){
                $cells->setValue('Third Gender');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+14), function($cells){
                $cells->setValue('Total');
                $cells->setFont(array('bold' => true));

                });
				
				
		$sheet->mergeCells('B'.($i+16).':B'.($i+19));
			$sheet->cells('B'.($i+16), function($cells){
                $cells->setValue('4. Total');
				$cells->setFont(array('bold' => true));
				$cells->setValignment('center');

                });		

        $sheet->cells('C'.($i+16), function($cells){
                $cells->setValue('Male');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+17), function($cells){
                $cells->setValue('Female');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+18), function($cells){
                $cells->setValue('Third Gender');
				$cells->setFont(array('bold' => true));

                });
        $sheet->cells('C'.($i+19), function($cells){
                $cells->setValue('Total');
                $cells->setFont(array('bold' => true));

                });
              $a = 0 ;
          foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
            $t = 67+$a;

            $chr = chr($t+1);
            $sheet->cells($chr.$i, function($cells) use($allACListsKey,$allACListsValue){
                  $cells->setValue($allACListsKey.". ".$allACListsValue);
                  $cells->setFont(array('bold' => true));
                  });
            $a++;


          }
          $chr = chr($t+2);
          $sheet->cells($chr.$i, function($cells){
                  $cells->setValue('Total');
         					$cells->setFont(array('bold' => true));
                  });


          $a = 0 ; $total = 0;
      foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
        $t = 67+$a;

        $chr = chr($t+1);
        $sheet->cells($chr.($i+1), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
              $cells->setValue($getelectorsacwise[$allACListsKey]['gen_m']);


              });


        $a++;

        $total += $getelectorsacwise[$allACListsKey]['gen_m'];


              }

              $chr = chr($t+2);
              $sheet->cells($chr.($i+1), function($cells) use($total){
                      $cells->setValue($total);
					  $cells->setFont(array('bold' => true));
                      });


            // $t = 66;
            //
            // $chr = chr($t+2);
            // $sheet->cells($chr.($i+1), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise, $total){
            //       $cells->setValue($total);
            //
            //       });





              $a = 0 ; $total = 0;
          foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
            $t = 67+$a;

            $chr = chr($t+1);
            $sheet->cells($chr.($i+2), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                  $cells->setValue($getelectorsacwise[$allACListsKey]['gen_f']);

                  });
            $a++;
            $total += $getelectorsacwise[$allACListsKey]['gen_f'];

                  }

                  $chr = chr($t+2);
                  $sheet->cells($chr.($i+2), function($cells) use($total){
                          $cells->setValue($total);
						  $cells->setFont(array('bold' => true));
                          });


                  $a = 0 ; $total = 0;
              foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                $t = 67+$a;

                $chr = chr($t+1);
                $sheet->cells($chr.($i+3), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                      $cells->setValue($getelectorsacwise[$allACListsKey]['gen_o']);

                      });
                $a++;

                $total += $getelectorsacwise[$allACListsKey]['gen_o'];
                      }


                      $chr = chr($t+2);
                      $sheet->cells($chr.($i+3), function($cells) use($total){
                              $cells->setValue($total);
							  $cells->setFont(array('bold' => true));
                              });
                      $a = 0 ; $total = 0;
                  foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                    $t = 67+$a;

                    $chr = chr($t+1);
                    $sheet->cells($chr.($i+4), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                          $cells->setValue($getelectorsacwise[$allACListsKey]['gen_t']);
						  $cells->setFont(array('bold' => true));
                          });
                    $a++;

                      $total += $getelectorsacwise[$allACListsKey]['gen_t'];
                          }

                          $chr = chr($t+2);
                          $sheet->cells($chr.($i+4), function($cells) use($total){
                                  $cells->setValue($total);
									$cells->setFont(array('bold' => true));
                                  });


                    $a = 0 ; $total = 0;
                foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                  $t = 67+$a;

                  $chr = chr($t+1);
                  $sheet->cells($chr.($i+6), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                        $cells->setValue($getelectorsacwise[$allACListsKey]['nri_m']);

                        });
                  $a++;

                  $total += $getelectorsacwise[$allACListsKey]['nri_m'];
                        }


                        $chr = chr($t+2);
                        $sheet->cells($chr.($i+6), function($cells) use($total){
                                $cells->setValue($total);
								$cells->setFont(array('bold' => true));
                                });

                        $a = 0 ; $total =0;
                    foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                      $t = 67+$a;

                      $chr = chr($t+1);
                      $sheet->cells($chr.($i+7), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                            $cells->setValue($getelectorsacwise[$allACListsKey]['nri_f']);

                            });
                      $a++;

                      $total += $getelectorsacwise[$allACListsKey]['nri_f'];
                            }

                            $chr = chr($t+2);
                            $sheet->cells($chr.($i+7), function($cells) use($total){
                                    $cells->setValue($total);
									$cells->setFont(array('bold' => true));
                                    });

                        $a = 0 ; $total = 0;
                        foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                          $t = 67+$a;

                          $chr = chr($t+1);
                          $sheet->cells($chr.($i+8), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                                $cells->setValue($getelectorsacwise[$allACListsKey]['nri_o']);

                                });
                          $a++;

                          $total += $getelectorsacwise[$allACListsKey]['nri_o'];


                                }

                                $chr = chr($t+2);
                                $sheet->cells($chr.($i+8), function($cells) use($total){
                                        $cells->setValue($total);
										$cells->setFont(array('bold' => true));
                                        });

                            $a = 0 ; $total = 0;
                              foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                              $t = 67+$a;

                            $chr = chr($t+1);
                            $sheet->cells($chr.($i+9), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                                    $cells->setValue($getelectorsacwise[$allACListsKey]['nri_t']);
									$cells->setFont(array('bold' => true));
                                    });
                            $a++;
                            $total += $getelectorsacwise[$allACListsKey]['nri_t'];
                          }

                          $chr = chr($t+2);
                          $sheet->cells($chr.($i+9), function($cells) use($total){
                                  $cells->setValue($total);
									$cells->setFont(array('bold' => true));
                                  });

                          $a = 0 ; $total =0;
                            foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                            $t = 67+$a;

                          $chr = chr($t+1);
                          $sheet->cells($chr.($i+11), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                                  $cells->setValue($getelectorsacwise[$allACListsKey]['ser_m']);

                                  });
                          $a++;
                          $total += $getelectorsacwise[$allACListsKey]['ser_m'];
                        }

                        $chr = chr($t+2);
                        $sheet->cells($chr.($i+11), function($cells) use($total){
                                $cells->setValue($total);
								$cells->setFont(array('bold' => true));
                                });

                        $a = 0 ; $total = 0;
                          foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                          $t = 67+$a;

                        $chr = chr($t+1);
                        $sheet->cells($chr.($i+12), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                                $cells->setValue($getelectorsacwise[$allACListsKey]['ser_f']);

                                });
                        $a++;
                        $total += $getelectorsacwise[$allACListsKey]['ser_f'];
                      }

                      $chr = chr($t+2);
                      $sheet->cells($chr.($i+12), function($cells) use($total){
                              $cells->setValue($total);
							 $cells->setFont(array('bold' => true));
                              });

                      $a = 0 ; $total =0 ;
                        foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                        $t = 67+$a;

                      $chr = chr($t+1);
                      $sheet->cells($chr.($i+13), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                              $cells->setValue($getelectorsacwise[$allACListsKey]['ser_o']);

                              });
                      $a++;

                      $total += $getelectorsacwise[$allACListsKey]['ser_o'];
                    }

                    $chr = chr($t+2);
                    $sheet->cells($chr.($i+13), function($cells) use($total){
                            $cells->setValue($total);
							$cells->setFont(array('bold' => true));
                            });

                    $a = 0 ; $total =0 ;
                      foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                      $t = 67+$a;

                    $chr = chr($t+1);
                    $sheet->cells($chr.($i+14), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                            $cells->setValue($getelectorsacwise[$allACListsKey]['ser_t']);
							$cells->setFont(array('bold' => true));

                            });
                    $a++;

                    $total += $getelectorsacwise[$allACListsKey]['ser_t'];
                  }

                  $chr = chr($t+2);
                  $sheet->cells($chr.($i+14), function($cells) use($total){
                          $cells->setValue($total);
							$cells->setFont(array('bold' => true));
                          });


                  $a = 0 ; $total = 0;
                    foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                    $t = 67+$a;

                  $chr = chr($t+1);
                  $sheet->cells($chr.($i+16), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                          $cells->setValue($getelectorsacwise[$allACListsKey]['tot_m']);

                          });
                  $a++;
                  $total += $getelectorsacwise[$allACListsKey]['tot_m'];
                }

                $chr = chr($t+2);
                $sheet->cells($chr.($i+16), function($cells) use($total){
                        $cells->setValue($total);
						$cells->setFont(array('bold' => true));
                        });

                $a = 0 ; $total = 0;
                  foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                  $t = 67+$a;

                $chr = chr($t+1);
                $sheet->cells($chr.($i+17), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                        $cells->setValue($getelectorsacwise[$allACListsKey]['tot_f']);

                        });
                $a++;
                $total += $getelectorsacwise[$allACListsKey]['tot_f'];
              }

              $chr = chr($t+2);
              $sheet->cells($chr.($i+17), function($cells) use($total){
                      $cells->setValue($total);
					  $cells->setFont(array('bold' => true));
                      });

              $a = 0 ; $total =0;
                foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
                $t = 67+$a;

              $chr = chr($t+1);
              $sheet->cells($chr.($i+18), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                      $cells->setValue($getelectorsacwise[$allACListsKey]['tot_o']);

                      });
              $a++;
              $total += $getelectorsacwise[$allACListsKey]['tot_o'];
            }

            $chr = chr($t+2);
            $sheet->cells($chr.($i+18), function($cells) use($total){
                    $cells->setValue($total);
					$cells->setFont(array('bold' => true));
                    });

            $a = 0 ; $total =0;
              foreach($getIndexCardDataCandidatesVotesACWise['allACList'] as $allACListsKey => $allACListsValue){
              $t = 67+$a;

            $chr = chr($t+1);
            $sheet->cells($chr.($i+19), function($cells) use($allACListsKey,$allACListsValue,$getelectorsacwise){
                    $cells->setValue($getelectorsacwise[$allACListsKey]['tot_all']);
					$cells->setFont(array('bold' => true));
                    });
            $a++;
            $total+= $getelectorsacwise[$allACListsKey]['tot_all'];
          }

          $chr = chr($t+2);
          $sheet->cells($chr.($i+19), function($cells) use($total){
                  $cells->setValue($total);
					$cells->setFont(array('bold' => true));
                  });

            // No 9 index card 3rd Form Ends


			$i += 21;
						
						$sheet->mergeCells('A'.$i.':J'.$i);
			
						$sheet->cells('A'.$i, function($cells){
							$cells->setValue('Disclaimer');
							$cells->setFont(array('bold' => true));
                        });
			
						$i++;
						
						$sheet->mergeCells('A'.$i.':J'.$i);
			
						$sheet->cells('A'.$i, function($cells){
							$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                        });


             });
            })->download('xls');

}else{
  //changes waseem 2019-07-22
  //changes waseem 2019-07-22
          if($request->has('complain')){
            $request->merge(array_merge(['is_excel' => 1],$filter));
            $candidate_object = new App\Http\Controllers\Admin\Eci\Report\CandidateController();
            $cadidates_list   = $candidate_object->get_candidates($request);
            if(isset($cadidates_list['results'])){
              $cadidates_list = json_encode($cadidates_list['results']);
            }else{
              $cadidates_list = json_encode([]);
            }
            $data['session'] = $session;
            $data['getIndexCardDataCandidatesVotesACWise'] = $getIndexCardDataCandidatesVotesACWise;
            $data['getIndexCardDataPCWise'] = $getIndexCardDataPCWise;
            $data['pc']                     = $data['pc_no'];
            $data['getelectorsacwise']      = $getelectorsacwise;

            $data['ele_details']            = $ele_details;
            $data['cand_finalize_ro']       = $cand_finalize_ro;
            $data['cadidates_list']         = $cadidates_list;
            $data['user_data']              = Auth::user();
            
            return view('admin/indexcard/complain-index-card',$data);
            
          }else{
            return view('IndexCardReports/IndexCardDataPCWise/indexcardreportpc',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','getelectorsacwise','user_data','ele_details','cand_finalize_ro'));
          }
}
		
		
	
    }



   public function getindexcardbriefed(Request $request){
        
    	$session = $request->session()->all();
      //dd($session);
        $user = Auth::user();
		
		//echo '<pre>'; print_r($user); die;
		
            $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
		    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,'PC');

$check_finalize=candidate_finalizebyro($ele_details->ST_CODE,$ele_details->CONST_NO,$ele_details->CONST_TYPE);
		    if($check_finalize=='') {$cand_finalize_ceo=0; $cand_finalize_ro=0;} else {
           	$cand_finalize_ceo=$check_finalize->finalize_by_ceo; $cand_finalize_ro=$check_finalize->finalized_ac;
           }

			$seched=getschedulebyid($ele_details->ScheduleID);
            $sechdul=checkscheduledetails($seched);

          
           $session['election_detail']['st_code'] = $user->st_code;
           $session['election_detail']['st_name'] = $user->placename;
    	// echo "<pre>"; print_r($session); die;
    	$st_code = $user->st_code;
    	$pc = $user->pc_no;
    	$election_detail = $session['election_detail'];
        $user_data = $d;
    	$getIndexCardDataCandidatesVotesACWise = $this->getIndexCardDataCandidatesVotesACWise($user->st_code, $user->pc_no);
    	 
//echo "<pre>"; print_r($getIndexCardDataCandidatesVotesACWise); die;


    	$getIndexCardDataPCWise = $this->getIndexCardDataPCWise($user->st_code, $user->pc_no, $ele_details->ScheduleID);
    	
	//	echo "<pre>"; print_r($getIndexCardDataPCWise); die;
		
		
    	//$getelectorsacwise = $this->getelectorsacwise($user->st_code, $user->pc_no);
		
		
if($request->path() == 'ropc/indexcardbriefedpdf'){
$pdf=PDF::loadView
('IndexCardReports.IndexCardDataPCWise.indexcardbriefedreportpdf',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','user_data','ele_details','cand_finalize_ro'));
return $pdf->download('IndexCardBriefedReport.pdf');
}else{
	return view('IndexCardReports/IndexCardDataPCWise/indexcardbriefedreport',compact('getIndexCardDataCandidatesVotesACWise','session','getIndexCardDataPCWise','st_code','pc','user_data','ele_details','cand_finalize_ro'));
}
		
		
	
    }




    /***************************By Praveen***********************************/
    public function getelectorsacwise($st_code,$pc){
        //dd($pc);
        $data_pc_wise = array();
        $data_pc_wise_new = array();
        
        $ac_no = DB::table('m_ac')->select('m_ac.ac_no')
                ->where(['m_ac.st_code' => $st_code, 'm_ac.pc_no' =>$pc])
                ->get();
				
        $data = DB::table('m_ac AS mac')
                   ->select('*','ed.electors_male','ed.electors_female','ed.electors_other','ed.electors_service','ed.electors_total','ed.gen_electors_male','ed.gen_electors_female','ed.gen_electors_other','ed.nri_male_electors','ed.nri_female_electors','ed.nri_third_electors','ed.service_male_electors','ed.service_female_electors','ed.service_third_electors'
                     )

                   ->leftJoin('electors_cdac AS ed',function($query){
                          $query->on('mac.AC_NO','ed.ac_no')
                                  ->on('mac.ST_CODE','ed.st_code')
                                  ->on('mac.PC_NO','ed.pc_no');
                      })
                   ->where('mac.st_code', $st_code)
				   ->where('mac.PC_NO', $pc)
                   //->where('ed.year', getElectionYear())
                     // ->where('ed.scheduledid', 1)

                   ->get()->toArray();
					
      
        foreach ($data as $key) {
                   $data_pc_wise[$key->ac_no] = array(
                   'st_code'    => $st_code,
                   'pc_no'      => $pc,
                   'ac_no'      => $key->ac_no,
                   'ac_name'    => $key->ac_name,
                   'gen_m'      => $key->gen_electors_male,
                   'gen_f'      => $key->gen_electors_female,
                   'gen_o'      => $key->gen_electors_other,

                   'gen_t'      => $key->gen_electors_male + $key->gen_electors_female + $key->gen_electors_other,

                   'ser_m'      => $key->service_male_electors,
                   'ser_f'      => $key->service_female_electors,
                   'ser_o'      => $key->service_third_electors,

                   'ser_t'      => $key->service_male_electors + $key->service_female_electors + $key->service_third_electors,

                   'tot_m'      => $key->gen_electors_male+$key->service_male_electors+$key->nri_male_electors,
                   'tot_f'      => $key->gen_electors_female+$key->service_female_electors+$key->nri_female_electors,
                   'tot_o'      => $key->gen_electors_other+$key->service_third_electors+$key->nri_third_electors,


                   'tot_all'    => $key->gen_electors_male+$key->service_male_electors+$key->nri_male_electors + $key->gen_electors_female+$key->service_female_electors+$key->nri_female_electors + $key->gen_electors_other+$key->service_third_electors+$key->nri_third_electors,

                   'nri_m'      => $key->nri_male_electors,
                   'nri_f'      => $key->nri_female_electors,
                   'nri_o'      => $key->nri_third_electors,
                   'nri_t'      => $key->nri_male_electors +  $key->nri_female_electors + $key->nri_third_electors
               );
               }

                return $data_pc_wise;
 
    }
    

public function getIndexCardDataCandidatesVotesACWise($st_code, $pc){

    	$sWhere = array(
    		'mac.PC_NO' 			=> $pc,
    		'mac.ST_CODE' 			=> $st_code
    	);


    	$responseFromIC = DB::table("counting_master_".strtolower($st_code)." AS  master")
				->select('mac.PC_NO as pc_no','B.candidate_id','B.new_srno','A.cand_name','A.cand_gender','A.cand_age','A.cand_category','C.PARTYNAME','C.PARTYABBRE','D.SYMBOL_DES','CP.migrate_votes','CP.postal_vote as postal_vote_count','CP.evm_vote as total_valid_vote','mac.AC_NO','master.total_vote as vote_count','mac.AC_NAME')
                ->join('m_ac as mac', function($query) {
                  $query->on('mac.PC_NO','master.pc_no')
                        ->on('mac.AC_NO','master.ac_no');
                       })
                   ->join('candidate_personal_detail AS A', 'A.candidate_id', 'master.candidate_id')
                   ->join('counting_pcmaster AS CP','CP.candidate_id','A.candidate_id')
                    ->join('candidate_nomination_detail AS B','A.candidate_id','B.candidate_id')
                    ->join('m_party AS C','B.party_id','C.CCODE')
                    ->join('m_symbol AS D','B.symbol_id','D.symbol_no')
    						->where($sWhere)
    						->where('B.application_status', '6')
							->where('B.finalaccepted', '1')
							->where('master.party_id','!=','1180')
    						->orderBy('mac.AC_NO','asc')
							->orderBy('B.new_srno','asc')
    						->get()->toArray();
    //  $queue = DB::getQueryLog();
 

          $candidatedataarray = array();
          $aclistforpc = array();
          $actotalvote = $pctotalvotes = $totalvalidpostal_votes = 0;

      foreach ($responseFromIC as $dataArraycandidatewise) {
          //echo "<pre>"; print_r($dataArraycandidatewise); die;
          $actotalvote += 0;
          $pctotalvotes += 0;
          $totalvalidpostal_votes += 0;

            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['new_srno'] = $dataArraycandidatewise->new_srno;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_name'] = $dataArraycandidatewise->cand_name;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_gender'] = $dataArraycandidatewise->cand_gender;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_age'] = $dataArraycandidatewise->cand_age;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['cand_category'] = $dataArraycandidatewise->cand_category;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['partyname'] = $dataArraycandidatewise->PARTYNAME;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['PARTYABBRE'] = $dataArraycandidatewise->PARTYABBRE;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['party_symbol'] = $dataArraycandidatewise->SYMBOL_DES;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['valid_postal_votes'] = $dataArraycandidatewise->postal_vote_count;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['migrate_votes'] = $dataArraycandidatewise->migrate_votes;
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['total_valid_vote'] = $dataArraycandidatewise->total_valid_vote;
			
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['total_valid_votes'] = $dataArraycandidatewise->total_valid_vote + $dataArraycandidatewise->postal_vote_count + $dataArraycandidatewise->migrate_votes;
 
            $candidatedataarray[$dataArraycandidatewise->candidate_id][$dataArraycandidatewise->pc_no]['acdata'][$dataArraycandidatewise->AC_NO] = $dataArraycandidatewise->vote_count;

            $aclistforpc[$dataArraycandidatewise->AC_NO] = $dataArraycandidatewise->AC_NAME;

      }

//echo "<pre>"; print_r($candidatedataarray); die;


	  $cnt=0;
	  foreach ($candidatedataarray as $key => $row)
		{
			foreach ($row as $key1 => $row1){
				$cnt++;
				$pc_array_name[$cnt][$key1] = $row1['total_valid_votes'];
			}
		}
	  array_multisort($pc_array_name, SORT_DESC, $candidatedataarray);

    	//echo "<pre>"; print_r($candidatedataarray); die;



    	return $data = [
    		'candidatedataarray' 	=> $candidatedataarray,
    		'allACList'				=> $aclistforpc
    	];
    }
	
	
        public function getIndexCardDataPCWise($st_code, $pc, $election_detail){



	   		$st_code = $st_code;
        $pc = $pc;


		// $st_code = 'U07';
       //  $pc = 1;

        $dataarraypc = array(
            'st_code' => $st_code,
             'pc' => $pc,
            // 'ScheduleID'=> $pcrow->ScheduleID,
        );



//dd($dataarraypc);
        //$election_detail['ScheduleID'] = $pcrow->ScheduleID;


        $fWhere = array(
                        'st_code'   => $st_code,
                        'pc_no'     => $pc,
                        'ac_no'     => null
                    );

        DB::enableQueryLog();



        $electorData = DB::table('electors_cdac AS ec')
                          ->select(array(
								  'ovi.general_male_voters AS male_voter',
                            	  'ovi.general_female_voters AS female_voter',
                            	  'ovi.general_other_voters AS other_voter',
                            	  'ovi.nri_male_voters AS nri_male_voters',
                            	  'ovi.nri_female_voters AS nri_female_voters',
                            	  'ovi.nri_other_voters AS nri_other_voters',
                            	  'ovi.test_votes_49_ma AS test_votes_49_ma',
                                'ovi.votes_not_retreived_from_evm AS votes_not_retreived_from_evm',
                                'ovi.votes_counted_from_evm AS votes_counted_from_evm',
                                'ovi.votes_counted_from_vvpat AS votes_counted_from_vvpat',
                                'ovi.rejected_votes_due_2_other_reason AS rejected_votes_due_2_other_reason',
                                'ovi.service_postal_votes_under_section_8 AS service_postal_votes_under_section_8',

                                'ovi.service_postal_votes_gov AS service_postal_votes_gov',
                                'ovi.proxy_votes AS proxy_votes',
                                'ovi.total_polling_station_s_i_t_c AS total_polling_station_s_i_t_c',
                                'ovi.date_of_repoll AS date_of_repoll',
                                'ovi.no_poll_station_where_repoll AS no_poll_station_where_repoll',
                                'ovi.is_by_or_countermanded_election AS is_by_or_countermanded_election',
                                'ovi.reasons_for_by_or_countermanded_election AS reasons_for_by_or_countermanded_election',
                                'ovi.finalize_by_ceo AS finalize_by_ceo',
                                'ovi.finalize AS finalize_by_ro',
                                'ovi.finalize_by_eci AS finalize_by_eci',
                                'ovi.date_of_finalize_by_ro AS finalize_by_ro_date',
                                'ovi.date_of_finalize_by_ceo AS finalize_by_ceo_date',

                            DB::raw('SUM(ec.gen_electors_male) AS gen_m'),
                            DB::raw("SUM(ec.service_male_electors) AS ser_m"),
                            DB::raw("SUM(ec.nri_male_electors) AS nri_m"),

                            DB::raw("SUM(ec.gen_electors_female) AS gen_f"),
                            DB::raw("SUM(ec.service_female_electors) AS ser_f"),
                            DB::raw("SUM(ec.nri_female_electors) AS nri_f"),

                            DB::raw("SUM(ec.gen_electors_other) AS gen_o"),
                            DB::raw("SUM(ec.nri_third_electors) AS nri_o"),
                            DB::raw("SUM(ec.service_third_electors) AS ser_o"),

                            DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other) AS gen_t"),
                            DB::raw("SUM(ec.service_male_electors+ec.service_female_electors+ec.service_third_electors) AS ser_t"),
                            DB::raw("SUM(ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS total_o"),

                            DB::raw("SUM(ec.electors_male + ec.service_male_electors) AS total_m"),
                            DB::raw("SUM(ec.electors_female + ec.service_female_electors) AS total_f"),
                            DB::raw("SUM(ec.electors_other + ec.service_third_electors) AS total_other_electors"),

                            DB::raw("SUM(ec.electors_total + ec.electors_service) AS total_all")

                        ))

                         ->leftJoin('electors_cdac_other_information as ovi',function($query){
                           $query->on('ovi.st_code','ec.st_code')
                                   ->on('ovi.pc_no','ec.pc_no');

                       })

                        ->where(array(
                            'ec.st_code' => $st_code,
                            'ec.pc_no'   => $pc,
                            //'ec.year'    => '2019'
                        ))

                        ->groupBy('ec.st_code','ec.pc_no')

                        ->first();

                       //echo "<pre>"; print_r($electorData); die;

                          $queue = DB::getQueryLog();



                          // echo "<pre>"; print_r($queue); die;


                  /*  $voterdata = DB::table('pd_scheduledetail')
                                ->select(
                                    DB::raw("sum(total_male) AS male_voter"),
                                    DB::raw("sum(total_female) AS female_voter"),
                                    DB::raw("sum(total_other) AS other_voter"),
                                    DB::raw("sum(total) AS totel_voter")
                                )

                                ->where(array(
                                 'pd_scheduledetail.st_code' => $st_code,
                                 'pd_scheduledetail.pc_no'   => $pc


                                ))



                        ->first(); */



                                           $dataarraypc['e_gen_m']  = @$electorData->gen_m;

                                           $dataarraypc['e_nri_m']  = @$electorData->nri_m;
                                           $dataarraypc['e_ser_m']  = @$electorData->ser_m;
                                           $dataarraypc['e_all_t_m'] =@$electorData->gen_m+ @$electorData->nri_m+ @$electorData->ser_m;
                                           $dataarraypc['e_gen_f'] = @$electorData->gen_f;
                                           $dataarraypc['e_nri_f'] = @$electorData->nri_f;
                                           $dataarraypc['e_ser_f'] = @$electorData->ser_f;
                                           $dataarraypc['e_all_t_f'] =@$electorData->gen_f+ @$electorData->nri_f+@ $electorData->ser_f;
                                           $dataarraypc['e_gen_o'] = @$electorData->gen_o;
                                           $dataarraypc['e_nri_o'] = @$electorData->nri_o;
                                           $dataarraypc['e_ser_o'] = @$electorData->ser_o;
                                            $dataarraypc['e_all_t_o'] =@$electorData->gen_o+ @$electorData->nri_o;
                                           $dataarraypc['e_gen_t'] = @$electorData->gen_m+@$electorData->gen_f+@$electorData->gen_o;
                                           $dataarraypc['e_ser_t'] = @$electorData->ser_t;
                                           $dataarraypc['e_nri_t'] =  @$electorData->nri_m+ @$electorData->nri_f+@$electorData->nri_o;

										   $dataarraypc['total_t']  = $dataarraypc['e_gen_t'] + $dataarraypc['e_ser_t'] + $dataarraypc['e_nri_t'];
                                          $dataarraypc['total_t_ws']  = $dataarraypc['e_gen_t'] +  $dataarraypc['e_nri_t']  + $dataarraypc['e_ser_t'];
                                           $dataarraypc['proxy_votes'] =@$electorData->proxy_votes;
                                           $dataarraypc['test_votes_49_ma'] =@$electorData->test_votes_49_ma;
                                           //voter

                                           $dataarraypc[ 'voter_male'] =@$electorData->male_voter ? :0;
                                           $dataarraypc[ 'voter_female'] =@$electorData->female_voter ? :0;
                                           $dataarraypc[ 'voter_other'] =@$electorData->other_voter ? :0;
                                           $dataarraypc[ 'voters_service'] =@$electorData->voters_service;
                                           
                                           $dataarraypc[ 'nri_male_votes'] =@$electorData->nri_male_voters ? :0;
                                           $dataarraypc[ 'nri_female_votes'] =@$electorData->nri_female_voters ? :0;
                                           $dataarraypc[ 'nri_third_votes'] =@$electorData->nri_other_voters ? :0;
										   
										   $dataarraypc[ 'voter_total'] =@$electorData->male_voter + @$electorData->female_voter + @$electorData->other_voter + @$electorData->nri_male_voters + @$electorData->nri_female_voters + @$electorData->nri_other_voters;
										   
										   
										   
                                           $dataarraypc[ 'service_postal_votes'] =@$electorData->service_postal_votes_under_section_8 ? :0;
                                           $dataarraypc[ 'votes_not_retrieved_on_evm'] =@$electorData->votes_not_retreived_from_evm ? :0;
                                           $dataarraypc[ 'votes_counted_from_evm'] =@$electorData->votes_counted_from_evm ? :0;
                                           $dataarraypc[ 'votes_counted_from_vvpat'] =@$electorData->votes_counted_from_vvpat ? :0;
                                           $dataarraypc[ 'govt_servent_postal_votes'] =@$electorData->service_postal_votes_gov ? :0;
                                           $dataarraypc[ 'rejected_votes_evm'] =@$electorData->rejected_votes_due_2_other_reason ? :0;

                                           $dataarraypc[ 'date_of_repoll'] =@$electorData->date_of_repoll;
                                           $dataarraypc[ 'no_poll_station_where_repoll'] =@$electorData->no_poll_station_where_repoll;
                                           $dataarraypc[ 'is_by_countermanded_election'] =@$electorData->is_by_countermanded_election;
                                           $dataarraypc[ 'reasons_thereof'] =@$electorData->reasons_thereof;
                                           $dataarraypc[ 'total_polling_station_s_i_t_c'] =@$electorData->total_polling_station_s_i_t_c ? :0;





         /////////////////////////dataarray/////////////////////////////////////



        $evmvotesfromcp = DB::table('counting_pcmaster')
                            ->select(array(
                            DB::raw('SUM(evm_vote) AS evm_votes'),
                            DB::raw("SUM(postal_vote) AS postal_votes"),
                            DB::raw("SUM(total_vote) AS total_votes"),
                            DB::raw("rejectedvote AS rej_votes_postal"),
                            DB::raw("tended_votes AS tended_votes")
                            ))
                            ->where(array(
                            'st_code' => $st_code,
                            'pc_no'   => $pc
                            ))
                            ->groupBy('st_code')
                            ->first();




        //$evmvotesfromcp = json_decode(json_encode($evmvotesfromcp));
       // echo "<pre>"; print_r($evmvotesfromcp); die;

        $dataarraypc = array_merge($dataarraypc,array(

                                //'v_votes_evm_all'=> @$evmvotesfromcp->evm_votes,
                                'v_votes_evm_all'=> $dataarraypc[ 'voter_total'],
                                'postal_valid_votes'=> @$evmvotesfromcp->postal_votes,
                                'total_valid_votes'=> @$evmvotesfromcp->total_votes,
                                'r_votes_evm'=> 0,
                                'r_votes_postal'=> @$evmvotesfromcp->rej_votes_postal,
                                'tendered_votes'=> @$evmvotesfromcp->tended_votes,

                                ));



    // echo "<pre>"; print_r($dataarraypc); die;

        DB::enableQueryLog();

		/* $indexCardDatas1 = DB::select("SELECT
		SUM(male) as male, SUM(female) as female,SUM(third) as third
		FROM (SELECT
		IF (B.cand_gender = 'male' ,COUNT(DISTINCT A.candidate_id),'')  AS male,
		IF (B.cand_gender = 'female' ,COUNT(DISTINCT A.candidate_id),'')  AS female,
		IF (B.cand_gender = 'third' ,COUNT(DISTINCT A.candidate_id),'')  AS third
		FROM `candidate_nomination_detail` AS `A` INNER JOIN `candidate_personal_detail` AS `B` ON `A`.`candidate_id` = `B`.`candidate_id`  WHERE `A`.`st_code` = '$st_code' AND `A`.`pc_no` = $pc AND `A`.`candidate_id` NOT IN (4319) GROUP BY  A.candidate_id)X limit 1"); */


        /* $indexCardDatas = DB::select("select (P.wdmale+P.rejmale+P.acpmale) as male , (P.wdfemale+P.rejfemale+P.acpfemale) as female, (P.wdthird+P.rejthird+P.acpthird) as third, P.* from (select         SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS wdmale,         SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS wdfemale,         SUM(CASE WHEN A.application_status = '5' AND B.cand_gender = 'third' THEN 1 ELSE 0 END) AS wdthird,         SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS rejmale,         SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS rejfemale,         SUM(CASE WHEN A.application_status = '4' AND B.cand_gender = 'third' THEN 1 ELSE 0 END) AS rejthird ,         SUM(CASE WHEN A.application_status = '6' and A.finalaccepted='1' AND B.cand_gender = 'male' THEN 1 ELSE 0 END) AS acpmale,         SUM(CASE WHEN A.application_status = '6' and A.finalaccepted='1' AND B.cand_gender = 'female' THEN 1 ELSE 0 END) AS acpfemale, SUM(CASE WHEN A.application_status = '6' and A.finalaccepted='1' AND B.cand_gender = 'third' THEN 1 ELSE 0 END)  AS acpthird from (select Y.candidate_id,Y.finalaccepted,Y.application_status from candidate_nomination_detail Y INNER JOIN( select candidate_id ,max(application_status) as application_status from candidate_nomination_detail  where `st_code` = '$st_code' AND `pc_no` = '$pc' and application_status<> '11'  and `candidate_id` NOT IN (4319) group by candidate_id) X on X.candidate_id=Y.candidate_id and X.application_status=Y.application_status group by Y.candidate_id,Y.finalaccepted,Y.application_status) A INNER JOIN candidate_personal_detail B on  B.candidate_id=A.candidate_id) P"); */


			$indexCardDatas = App\models\Admin\CandidateModel::get_count_nominated($st_code,$pc);

//echo '<pre>'; print_r($dataaa); die;



                $datanotapcwise = DB::table('counting_pcmaster')
                                ->select('postal_vote','evm_vote','migrate_votes')
                                ->where(array(
                                        'st_code' => $st_code,
                                        'pc_no'    => $pc,
                                        'party_id'=>1180
                                 ))
                                 ->first();


            $dataarraypc = array_merge($dataarraypc,array(

                                'c_nom_m_t'=> $indexCardDatas['nom_male'],
                                'c_nom_f_t'=> $indexCardDatas['nom_female'],
                                'c_nom_o_t'=> $indexCardDatas['nom_third'],
                                'c_nom_all_t'=> $indexCardDatas['nom_male'] + $indexCardDatas['nom_female'] + $indexCardDatas['nom_third'],

                                'c_wd_m_t'=> $indexCardDatas['with_male'],
                                'c_wd_f_t'=> $indexCardDatas['with_female'],
                                'c_wd_o_t'=> $indexCardDatas['with_third'],

                                 'c_rej_m_t'=> $indexCardDatas['rej_male'],
                                'c_rej_f_t'=> $indexCardDatas['rej_female'],
                                'c_rej_o_t'=> $indexCardDatas['rej_third'],

                                 'c_acp_m_t'=> $indexCardDatas['cont_male'],
                                'c_acp_f_t'=> $indexCardDatas['cont_female'],
                                'c_acp_o_t'=> $indexCardDatas['cont_third'],



                                ));









   //echo "<pre>"; print_r($dataarraypc); die;
            $indexCardDatasDf = DB::select("SELECT cp.st_code,cp.pc_no,
             SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdmale,

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdfemale,

            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdthird,


            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
            where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd

            FROM `counting_pcmaster` as cp
            join candidate_personal_detail as C on C.candidate_id = cp.candidate_id
            WHERE cp.candidate_id != (select candidate_id from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code)
            AND cp.party_id != 1180 AND cp.st_code = '$st_code' and cp.pc_no = $pc");


		//	echo '<pre>'; print_r($indexCardDatasDf); die;



            $dataarraypc = array_merge($dataarraypc,array(

                                'c_fd_m_t'=> $indexCardDatasDf[0]->fdmale,
                                'c_fd_f_t'=> $indexCardDatasDf[0]->fdfemale,
                                'c_fd_o_t'=> $indexCardDatasDf[0]->fdthird,
                                'c_fd_t'=> $indexCardDatasDf[0]->fd,


                                ));

            $pollDateInfoPcwise = DB::table('m_schedule as ms')
                                ->select('ms.DATE_POLL','ms.DATE_COUNT','ms.DT_PRESS_ANNC','ms.DT_ISS_NOM','wlc.result_declared_date')
                                ->join('m_election_details as mss','mss.ScheduleID','ms.SCHEDULEID')
                                ->join('winning_leading_candidate as wlc', function($join){
                                    $join->on('wlc.st_code', 'mss.st_code')
                                            ->on('wlc.pc_no', 'mss.CONST_NO');
                                })
                                ->where(array(
                                        'mss.ST_CODE' => $st_code,
										'mss.CONST_NO'   => $pc,
										'mss.CONST_TYPE'   => 'PC',
										//'mss.YEAR'   => '2019'														
                                 ))
                                ->first();

		   if($dataarraypc['total_polling_station_s_i_t_c'] > 0){
			   $avg = round(($dataarraypc['total_t_ws'])/$dataarraypc['total_polling_station_s_i_t_c']);
		   }else{
			  $avg = 0;
		   }


 $data=array(

             'st_code'                        =>  $dataarraypc['st_code'],
             'pc_no'                          => $dataarraypc['pc'],
            // 'schedule_id'                    => $dataarraypc['ScheduleID'],
             'e_nri_m'                        => $dataarraypc['e_nri_m'],
             'e_nri_f'                        => $dataarraypc['e_nri_f'],
             'e_nri_o'                        => $dataarraypc['e_nri_o'],
             'e_nri_t'                        => $dataarraypc['e_nri_m'] + $dataarraypc['e_nri_f'] + $dataarraypc['e_nri_o'],
             'e_gen_m'                        => $dataarraypc['e_gen_m'],
             'e_gen_f'                        => $dataarraypc['e_gen_f'],
             'e_gen_o'                        => $dataarraypc['e_gen_o'],
             'e_gen_t'                        => $dataarraypc['e_gen_m'] +  $dataarraypc['e_gen_f'] + $dataarraypc['e_gen_o'],
             'e_ser_m'                        => $dataarraypc['e_ser_m'],
             'e_ser_f'                        => $dataarraypc['e_ser_f'],
              "e_ser_o"                        => $dataarraypc['e_ser_o'],
              'e_ser_t'                        => $dataarraypc['e_ser_t'],
             'e_all_t_m'                     =>   $dataarraypc['e_nri_m'] + $dataarraypc['e_gen_m'] + $dataarraypc['e_ser_m'],
             'e_all_t_f'                     =>   $dataarraypc['e_nri_f'] + $dataarraypc['e_gen_f'] + $dataarraypc['e_ser_f'],
             'e_all_t_o'                     =>  $dataarraypc['e_nri_o'] + $dataarraypc['e_gen_o'],
              "e_all_t"                      =>  $dataarraypc['total_t'],

             'proxy_votes'                   => $dataarraypc['proxy_votes'],
             'tendered_votes'                 => $dataarraypc['tendered_votes'],

           "total_no_polling_station"         => $dataarraypc['total_polling_station_s_i_t_c'],

            "avg_elec_polling_stn"             => $avg,
               'dt_poll'                         => @$pollDateInfoPcwise->DATE_POLL,
               "dt_counting"                     => @$pollDateInfoPcwise->DATE_COUNT,
               "dt_declare"                     =>  @$pollDateInfoPcwise->result_declared_date,
             "DT_ISS_NOM"                 		=> @$pollDateInfoPcwise->DT_ISS_NOM,
             "DT_PRESS_ANNC"         			=> @$pollDateInfoPcwise->DT_PRESS_ANNC,

    "c_nom_m_t"                     =>$dataarraypc['c_nom_m_t'],
    "c_nom_f_t"                     => $dataarraypc['c_nom_f_t'],
    "c_nom_o_t"                     =>$dataarraypc['c_nom_o_t'],
	"c_nom_a_t"                     =>$dataarraypc['c_nom_all_t'],

    "c_nom_w_m"                     =>$dataarraypc['c_wd_m_t'],
    "c_nom_w_f"                     =>$dataarraypc['c_wd_f_t'],
    "c_nom_w_o"                     =>$dataarraypc['c_wd_o_t'],
    "c_nom_w_t"                     =>$dataarraypc['c_wd_m_t']  + $dataarraypc['c_wd_f_t'] +$dataarraypc['c_wd_o_t'],

    "c_nom_r_m"                     =>$dataarraypc['c_rej_m_t'],
    "c_nom_r_f"                     =>$dataarraypc['c_rej_f_t'],
    "c_nom_r_o"                     =>$dataarraypc['c_rej_o_t'],
    "c_nom_r_a"                     =>$dataarraypc['c_rej_m_t'] + $dataarraypc['c_rej_f_t'] + $dataarraypc['c_rej_o_t'],

    "c_nom_co_m"                     =>$dataarraypc['c_acp_m_t'],
    "c_nom_co_f"                     =>$dataarraypc['c_acp_f_t'],
    "c_nom_co_o"                     =>$dataarraypc['c_acp_o_t'],
     'c_nom_co_t'                      =>$dataarraypc['c_acp_m_t'] + $dataarraypc['c_acp_f_t'] +$dataarraypc['c_acp_o_t'],


    "c_nom_fd_m"                     =>$dataarraypc['c_fd_m_t'],
    "c_nom_fd_f"                     =>$dataarraypc['c_fd_f_t'],
    "c_nom_fd_o"                     =>$dataarraypc['c_fd_o_t'],
    "c_nom_fd_t"                     =>$dataarraypc['c_fd_t'],


    "vt_gen_m"                     =>$dataarraypc['voter_male'],
    "vt_gen_f"                     =>$dataarraypc['voter_female'],
    "vt_gen_o"                     =>$dataarraypc['voter_other'],
    "vt_gen_t"                     =>$dataarraypc['voter_male'] + $dataarraypc['voter_female'] +$dataarraypc['voter_other'],

    //                           =>$dataarraypc['voter_total'],
    "vt_nri_m"                     =>$dataarraypc['nri_male_votes'],
    "vt_nri_f"                     =>$dataarraypc['nri_female_votes'],
    "vt_nri_o"                     =>$dataarraypc['nri_third_votes'],
    "vt_nri_t"                     =>$dataarraypc['nri_male_votes'] + $dataarraypc['nri_female_votes'] + $dataarraypc['nri_third_votes'],

    "vt_m_t"                     =>$dataarraypc['voter_male']+$dataarraypc['nri_male_votes'],
    "vt_f_t"                     =>$dataarraypc['voter_female']+$dataarraypc['nri_female_votes'],
    "vt_o_t"                     =>$dataarraypc['voter_other']+$dataarraypc['nri_third_votes'],
    "vt_all_t"                     =>$dataarraypc['voter_male']+$dataarraypc['nri_male_votes']+$dataarraypc['voter_female']+$dataarraypc['nri_female_votes']+$dataarraypc['voter_other']+$dataarraypc['nri_third_votes'],



    "t_votes_evm"               	=> 	$dataarraypc['v_votes_evm_all'] + @$dataarraypc['test_votes_49_ma'],
    "mock_poll_evm"       			=>  @$dataarraypc['test_votes_49_ma'],
    "not_retrieved_vote_evm"        =>	$dataarraypc['votes_not_retrieved_on_evm'],
    "votes_counted_from_evm"        =>	$dataarraypc['votes_counted_from_evm'],
    "votes_counted_from_vvpat"        =>	$dataarraypc['votes_counted_from_vvpat'],
    "r_votes_evm"                   => 	$dataarraypc[ 'rejected_votes_evm'],
    "nota_vote_evm"       			=>  @$datanotapcwise->evm_vote,
    "v_r_evm_all"       			=>  $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote+@$datanotapcwise->migrate_votes,
	
    "v_votes_evm_all"              	=> 	($dataarraypc['v_votes_evm_all'] + @$dataarraypc['test_votes_49_ma'] ) - ($dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote +@$datanotapcwise->migrate_votes),

    "postal_vote_ser_u"             =>	$dataarraypc['service_postal_votes'],
    "postal_vote_ser_o"             =>	$dataarraypc['govt_servent_postal_votes'],
    "postal_vote_rejected"          =>	$dataarraypc['r_votes_postal'],
    "postal_vote_nota"       		=>  @$datanotapcwise->postal_vote,
    "migrate_vote_nota"       		=>  @$datanotapcwise->migrate_votes,
    "postal_vote_r_nota"  			=>  @$datanotapcwise->postal_vote+@$dataarraypc['r_votes_postal'],
    "postal_valid_votes"            =>	$dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes']-(@$datanotapcwise->postal_vote+@$dataarraypc['r_votes_postal']),

    "total_votes_polled"            =>	$dataarraypc['v_votes_evm_all']  + @$dataarraypc['test_votes_49_ma'] + $dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes'],
                     
	"total_not_count_votes"       	=>  $dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + 	$dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote + @$datanotapcwise->postal_vote + @$datanotapcwise->migrate_votes +@$evmvotesfromcp->rej_votes_postal ,

    "total_valid_votes"            => 	($dataarraypc['v_votes_evm_all'] + @$dataarraypc['test_votes_49_ma']) - ($dataarraypc['test_votes_49_ma'] + $dataarraypc['votes_not_retrieved_on_evm'] + $dataarraypc[ 'rejected_votes_evm'] +@$datanotapcwise->evm_vote + @$datanotapcwise->migrate_votes) + $dataarraypc['service_postal_votes']+$dataarraypc['govt_servent_postal_votes']-(@$datanotapcwise->postal_vote+@$evmvotesfromcp->rej_votes_postal),
	
     "total_votes_nota"       		=>   @$datanotapcwise->evm_vote + @$datanotapcwise->migrate_votes + @$datanotapcwise->postal_vote,

     "dt_repoll"               		=>   @$electorData->date_of_repoll,
     "re_poll_station"         		=>   @$electorData->no_poll_station_where_repoll,
     "flag_bye_counter"        		=>   @$electorData->is_by_or_countermanded_election ? : 0,
     "flag_bye_counter_reason" 		=>   @$electorData->reasons_for_by_or_countermanded_election,
	 "finalize_by_ceo" 		   		=>   @$electorData->finalize_by_ceo ? : 0,
     "finalize_by_ro" 		   		=>   @$electorData->finalize_by_ro ? : 0,
     "finalize_by_eci" 		   		=>   @$electorData->finalize_by_eci ? : 0,
     "finalize_by_ro_date" 	   		=>   @$electorData->finalize_by_ro_date,
     "finalize_by_ceo_date"    		=>   @$electorData->finalize_by_ceo_date

 );






	   $indexCardData[0] = (object)array(
							'status'=> 'nominated',
							'male'=> $indexCardDatas['nom_male'],
							'female'=> $indexCardDatas['nom_female'],
							'third'=> $indexCardDatas['nom_third'],
							'total'=> $indexCardDatas['nom_male'] + $indexCardDatas['nom_female'] + $indexCardDatas['nom_third']
							);


		$indexCardData[1] = (object)array(
								'status'=> 'rejected',
                                'male'=> $indexCardDatas['rej_male'],
                                'female'=> $indexCardDatas['rej_female'],
                                'third'=> $indexCardDatas['rej_third'],
                                'total'=> $indexCardDatas['rej_male'] + $indexCardDatas['rej_female'] + $indexCardDatas['rej_third'],
							);

		$indexCardData[2] = (object)array(
								'status'=> 'withdrawn',
                                'male'=> $indexCardDatas['with_male'],
                                'female'=> $indexCardDatas['with_female'],
                                'third'=> $indexCardDatas['with_third'],
                                'total'=> $indexCardDatas['with_male'] + $indexCardDatas['with_female'] + $indexCardDatas['with_third'],
							);

		$indexCardData[3] = (object)array(
								'status'=> 'accepted',
                                'male'=> $indexCardDatas['cont_male'],
                                'female'=> $indexCardDatas['cont_female'],
                                'third'=> $indexCardDatas['cont_third'],
                                'total'=> $indexCardDatas['cont_male'] + $indexCardDatas['cont_female'] + $indexCardDatas['cont_third'],
                            );


		$indexCardData[4] = (object)array(
								'status'=> 'forfieted',
                                'male'=> $indexCardDatasDf[0]->fdmale,
                                'female'=> $indexCardDatasDf[0]->fdfemale,
                                'third'=> $indexCardDatasDf[0]->fdthird,
                                'total'=> $indexCardDatasDf[0]->fd,
                                );




        #distict_name
        $distictData = DB::select("SELECT GROUP_CONCAT(DISTINCT DIST_NAME) as distict_name FROM `m_district` join m_ac on m_ac.DIST_NO_HDQTR = m_district.DIST_NO and m_ac.ST_CODE = m_district.ST_CODE where 
m_ac.ST_CODE = '$st_code' and m_ac.PC_NO = $pc ORDER BY m_district.DIST_NAME ASC");


        #PC_TYPE
        $pcType = DB::table('m_pc')
                    ->select([
                        'PC_NO','PC_NAME','PC_TYPE'
                    ])
                    ->where(array(
                        'ST_CODE' => $st_code,
                        'PC_NO'   => $pc
                    ))
                    ->first();



         // echo "<pre>"; print_r($election_detail); die;


        #Schedule ID
        // $electionSession = $request->session()->all();

        /* $ScheduleID = DB::table('m_schedule')
                    ->select([
                        'DATE_POLL',
                        'DATE_COUNT',
                        'DT_ISS_NOM',
                        'DT_PRESS_ANNC'
                    ])
					->join('m_election_details','m_schedule.SCHEDULEID','m_election_details.ScheduleID' )
                    ->where(array(
                        'm_election_details.ST_CODE' => $st_code,
                        'm_election_details.CONST_NO'   => $pc,
                        'm_election_details.CONST_TYPE'   => 'PC',
                        'm_election_details.YEAR'   => '2019'
                    ))
                    ->first(); */
        // echo "<pre>"; print_r($election_detail['ScheduleID']); die;
		
		
		$migrate_notadb = array();
		$migrate_nota = array();
		
		if($st_code == 'S09'){
			$migrate_notadb = DB::select("SELECT ac_no, total_vote FROM `counting_master_s09` where pc_no = $pc and party_id = '1180' ORDER BY ac_no ASC");
			
			foreach($migrate_notadb as $datam){
			$migrate_nota[$datam->ac_no] = array(
				'total_vote' =>$datam->total_vote
				);
			}
			
		}
				
		//echo '<pre>'; print_r($migrate_nota); die;
		
        return $data = [
            'indexCardData'         =>    json_decode(json_encode($indexCardData)),
            'distict_name'          =>    $distictData[0]->distict_name,
            'pcType' 				=>    $pcType,
            't_pc_ic'               =>    (object)$data,
            'migrate_nota'          =>    $migrate_nota,
			
        ];
    }

    function callAPI($method, $url, $data)
    {

        // echo '<pre>'; print_r($data); die;

        $curl = curl_init();
        switch ($method)
            {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;

        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;

        default:
            if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
            }

        // OPTIONS:

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:multipart/form-data',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // EXECUTE:

        $result = curl_exec($curl);
        if (!$result)
            {
            die("Connection Failure");
            }

        curl_close($curl);
        return $result;
        }


public function finaliserequest(Request $request){
      $user = Auth::user();
      $uid=$user->id;
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $d=$this->commonModel->getunewserbyuserid($uid);
      $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

      $sched=''; $search='';
      $status=$this->commonModel->allstatus();
      if(isset($ele_details)) {  $i=0;
       foreach($ele_details as $ed) {
         // $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
          //$const_type=$ed->CONST_TYPE;
        }
      }
 
      $user_data = $d;
    
    $resultPCs = DB::table('m_pc')
      ->select('PC_NO','pc_name')
      ->where('st_code',$user->st_code)
      ->where('pc_no',$user->pc_no)
      ->get()->toArray();
    
    
    //echo '<pre>';
    //print_r($data);die;
    
                    
      return view('IndexCardReports.IndexCardDataPCWise.finaliserequest', compact('user_data','resultPCs'));
    }



public function finalizerequestsubmit(Request $request){
    
    
    
      $validator = Validator::make($request->all(), [ 
                'file_upload' => 'required|mimes:pdf'
                
            ]);


            if ($validator->fails()) {
               return Redirect::back()
               ->withErrors($validator)
               ->withInput();          
            }
    
    
    
    
    $user = Auth::user();
     
      
    $photo = $request->file('file_upload')->getClientOriginalName();
    $photo =   time().'-'.$photo;
    
    $destination = base_path() . '/public/indexcard';
    
    $request->file('file_upload')->move($destination, $photo);
   
    
          $insertData[] = array(
            'st_code'                   => $user->st_code,
            'pc_no'           => $request->pcno,
            'file_name'           => $photo,
            'submitted_by'            => $user->officername,
            'submitted_at'          => date('Y-m-d H:i:s')
          );
 
      foreach ($insertData as $key => $value) {
      $insertId = DB::table('finalize_request_ic')->insertGetId($value);
      }
     
    $user_data = $user;
                    
      return Redirect::to('ropc/myrequestindexcard');
    }



public function myrequestindexcard(Request $request){
  //dd("Hello");
  $user_data = Auth::user();
     
    $data = DB::table('finalize_request_ic')
          ->select('finalize_request_ic.*','m_pc.PC_NAME')
        ->join('m_pc',function($join){
          $join->on('finalize_request_ic.st_code','=','m_pc.st_code')
            ->on('finalize_request_ic.pc_no','=','m_pc.pc_no');
        })
        ->where('submitted_by',$user_data->officername)
        ->where('finalize_request_ic.st_code',$user_data->st_code)
        ->where('finalize_request_ic.pc_no',$user_data->pc_no)
        ->orderBy('finalize_request_ic.id','DESC')
        ->get();
        
    //echo '<pre>';
    //print_r($data); die;

      return view('IndexCardReports.IndexCardDataPCWise.myrequestindexcard', compact('user_data','data'));
    }
   	
}

