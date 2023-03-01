<?php

namespace App\Http\Controllers\IndexCardReports\StatisticalReportPC;

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
		use MPDF;
		use App\commonModel;
		use App\adminmodel\CandidateModel;
		use App\adminmodel\PartyMaster;
		use App\adminmodel\CandidateNomination;
		use App\Helpers\SmsgatewayHelper;
		use App\Classes\xssClean;
		use App\adminmodel\SymbolMaster;
		use Illuminate\Support\Facades\URL;
		use Excel;
		
		ini_set("memory_limit","48000M");
        set_time_limit('6000');
        ini_set("pcre.backtrack_limit", "5000000000");

class StatisticalReportPCController extends Controller
{
	public function __construct(){
       $this->middleware(['auth:admin', 'auth']);
       $this->middleware(function (Request $request, $next) {
           if (!\Auth::check()) {
               return redirect('login')->with(Auth::logout());
           }

           $user = Auth::user();
           switch ($user->role_id) {
               case '7':
                   $this->middleware('eci');
                   break;
               case '4':
                   $this->middleware('ceo');
                   break;
               case '18':
                   $this->middleware('ro');
                   break;
			  case '27':
                   $this->middleware('eci_index');
                   break;   
				   
               default:
                   $this->middleware('eci');
           }
           return $next($request);
       });
 
        $this->middleware('adminsession');
        $this->commonModel = new commonModel();
       // $this->ceomodel = new ACCEOModel();
       // $this->acceoreportModel = new ACCEOReportModel();
        $this->xssClean = new xssClean;
   }


		   
		   
    public function numberandtypesofconstituencies(Request $request){
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		
		$dataSum = array();
		
		DB::enableQueryLog();
			
		$data = DB::table('m_pc')
		->select(
		'm_state.ST_NAME',
		DB::raw("SUM(CASE WHEN m_pc.PC_TYPE = 'GEN' THEN 1 ELSE 0 END) AS gen"),
		DB::raw("SUM(CASE WHEN m_pc.PC_TYPE = 'SC' THEN 1 ELSE 0 END) AS sc"),
		DB::raw("SUM(CASE WHEN m_pc.PC_TYPE = 'ST' THEN 1 ELSE 0 END) AS st"),
		DB::raw("COUNT('m_pc.PC_ID') AS total"),	
		DB::raw("(SELECT COUNT(DISTINCT(pc_no)) as not_completed FROM winning_leading_candidate as wlc WHERE wlc.status = '1' AND st_code = m_pc.ST_CODE GROUP by st_code) as completed")		
		)
		->join('m_state','m_pc.ST_CODE', '=', 'm_state.ST_CODE')	
		->groupBy('m_pc.ST_CODE','m_state.ST_NAME')
		->orderBy('m_state.ST_NAME','ASC')
		->get();
		
		
		
		$dataSum['gen'] = $data->sum('gen');
		$dataSum['sc'] = $data->sum('sc');
		$dataSum['st'] = $data->sum('st');
		$dataSum['total'] = $data->sum('total');
		$dataSum['completed'] = $data->sum('completed');
		
		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
		
		
		
		
		 if($request->path() == "$prefix/numberandtypesofconstituencies_pdf"){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.StatisticalPC.numberandtypesofconstituencies_pdf', compact('data','dataSum'));

			 if(verifyreport(5)){
        
                  $file_name = 'Number And Types Of Constituencies'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/5/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '5',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
				
			return $pdf->download('Number And Types Of Constituencies.pdf');
		}else if($request->path() == "$prefix/numberandtypesofconstituencies_xls"){
		
			$data = $data->toArray();
		
			array_push($data, array(
			   'ST_NAME' => 'Total',
				'gen'=> $dataSum['gen'],
				'sc'=> $dataSum['sc'],
				'st'=> $dataSum['st'],
				'total'=> $dataSum['total'],
				'completed'=> $dataSum['completed'],
		   ));
		
			$data = json_decode( json_encode($data), true);
				
		
			return Excel::create('Number And Types Of Constituencies', function($excel) use ($data) {
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
				   // $sheet->fromArray($data);
					
					$sheet->mergeCells('A1:F1');	
					$sheet->mergeCells('B2:E2');	
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('5 - Number And Types Of Constituencies');
						$cells->setAlignment('center');
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					

					$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('F3')->getAlignment()->setWrapText(true);
					$sheet->setSize('A3', 25,25);
					
					$sheet->setSize('F3', 30,30);
					$sheet->cell('A3', function($cell) {$cell->setValue('State/UT NAME'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('B3', function($cell) {$cell->setValue('GEN'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('C3', function($cell) {$cell->setValue('SC'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('D3', function($cell) {$cell->setValue('ST'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('E3', function($cell) {$cell->setValue('TOTAL'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					$sheet->cell('F3', function($cell) {$cell->setValue('NO. OF CONSTITUENCIES WHERE ELECTION COMPLETED'); $cell->setFont(array('name' => 'Times New Roman', 'size' => 12));  });
					if (!empty($data)) {
						foreach ($data as $key => $value) {
							$i= $key+4;
							$sheet->cell('A'.$i, $value['ST_NAME']); 
							$sheet->cell('B'.$i, ($value['gen'] > 0) ? $value['gen']:'=(0)' ); 
							$sheet->cell('C'.$i, ($value['sc'] > 0) ? $value['sc']:'=(0)' ); 
							$sheet->cell('D'.$i, ($value['st'] > 0) ? $value['st']:'=(0)' ); 
							$sheet->cell('E'.$i, ($value['total'] > 0) ? $value['total']:'=(0)' ); 
							$sheet->cell('F'.$i, ($value['completed'] > 0) ? $value['completed']:'=(0)' );
						}
					}


					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,30);



					$sheet->mergeCells("A$i:H$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
		
				});
			})->download('xls');
		
		
		}else{
				return view('IndexCardReports.StatisticalPC.numberandtypesofconstituencies', compact('data','dataSum','sched','election_detail','user_data'));
		}
					
	}
	
	
	
	public function listofpoliticalpartiesparticipated(Request $request){
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		
		
		

			
		$data = DB::table('m_party')
		->select('m_party.PARTYTYPE','m_party.PARTYABBRE',
		DB::raw('GROUP_CONCAT(DISTINCT(`m_symbol`.`SYMBOL_DES`)) as SYMBOL_DES'),
		'm_party.PARTYNAME')
		->join('candidate_nomination_detail as cnd','cnd.party_id', '=', 'm_party.CCODE')
		->join('m_symbol','cnd.symbol_id', '=', 'm_symbol.SYMBOL_NO')		
		->join('m_election_details as med',function($join){
			$join->on('med.st_code', '=', 'cnd.st_code')
			     ->on('med.CONST_NO', '=', 'cnd.pc_no');
		}
		)
		->where(array(
			'med.CONST_TYPE' => 'PC',
			'med.election_status' => '1',
			//'med.ELECTION_ID' => '1',
			'cnd.application_status' => '6',
			'cnd.finalaccepted' => '1'
		))
		->groupBy('m_party.CCODE')
		->orderBy('m_party.PARTYTYPE', 'ASC')
		->orderBy('m_party.PARTYABBRE', 'ASC')
		->get();
		
			
		//echo '<pre>'; print_r($data); die;

			
		$dataArray = array();
		foreach($data as $key){
			$dataArray[$key->PARTYTYPE][] = array(
				'PARTYABBRE' 	=> $key->PARTYABBRE,
				'SYMBOL_DES' 	=> $key->SYMBOL_DES,
				'PARTYNAME' 	=> $key->PARTYNAME
			);
		}
		
		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}


		if($request->path() == "$prefix/listofpoliticalpartiesparticipated_pdf"){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.StatisticalPC.listofpoliticalpartiesparticipated_pdf', compact('dataArray'));

			 if(verifyreport(3)){
        
                  $file_name = 'List Of Political Parties Participated'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/3/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '3',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('List Of Political Parties Participated.pdf');
		}else if($request->path() == "$prefix/listofpoliticalpartiesparticipated_xls"){
				
			$data = json_decode( json_encode($dataArray), true);
				

			return Excel::create('List Of Political Parties Participated', function($excel) use ($data) {
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
					
					$sheet->mergeCells('A1:D1');
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('5. LIST OF POLITICAL PARTIES PARTICIPATED');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					

					$sheet->getStyle('B2')->getAlignment()->setWrapText(true);
					$sheet->getStyle('C2')->getAlignment()->setWrapText(true);				
					$sheet->setSize('B2', 20,20);
					$sheet->setSize('C2', 20,20);
					
					
					$sheet->cell('A2', function($cells) {
						$cells->setValue('Sr.No.');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
		
					$sheet->cell('B2', function($cells) {
						$cells->setValue('PARTY TYPE');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
		
		
					$sheet->cell('C2', function($cells) {
						$cells->setValue('ABBREVIATION');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('D2', function($cells) {
						$cells->setValue('PARTY SYMBOL');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('E2', function($cells) {
						$cells->setValue('PARTY NAME');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
						$sn = 1;
					
						$i= 3;
					
					
					if (!empty($data)) {
						foreach ($data as $key => $row){														
															
							foreach ($row as $keys => $rowData){
								
								
								$sheet->getStyle('D'.$i)->getAlignment()->setWrapText(true);
								$sheet->getStyle('E'.$i)->getAlignment()->setWrapText(true);
								$sheet->setSize('D'.$i, 50,500);
								$sheet->setSize('E'.$i, 50,25);
								
								
								$sheet->cell('A'.$i, $sn); 
								$sheet->cell('B'.$i, $key); 
								$sheet->cell('C'.$i, $rowData['PARTYABBRE']); 								
								$sheet->cell('D'.$i, $rowData['SYMBOL_DES']); 
								$sheet->cell('E'.$i, $rowData['PARTYNAME']); 
								
								$sn++;
								$i++;
							}
						}
					
						$sheet->cell('A'.$i, $sn); 
						$sheet->cell('B'.$i, 'Z1'); 
						$sheet->cell('C'.$i, 'NOTA'); 
						$sheet->cell('D'.$i, 'NOTA'); 
						$sheet->cell('E'.$i, 'None of the Above');
					
					}

					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,30);



					$sheet->mergeCells("A$i:E$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
		
				});
			})->download('xls');	
			
		}else{
			return view('IndexCardReports.StatisticalPC.listofpoliticalpartiesparticipated', compact('dataArray','sched','election_detail','user_data'));
		}
	
	}
	
	
	public function statewisenumberelectors(Request $request){
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		
		
		DB::enableQueryLog();
		
		
		
		$data = DB::table('electors_cdac AS ec')
                          ->select(array(
						    'm_state.ST_NAME',
                            DB::raw('SUM(ec.gen_electors_male + ec.nri_male_electors) AS e_gen_m'),
                            DB::raw("SUM(ec.service_male_electors) AS e_ser_m"),
                            DB::raw("SUM(ec.nri_male_electors) AS e_nri_m"),

                            DB::raw("SUM(ec.gen_electors_female + ec.nri_female_electors) AS e_gen_f"),
                            DB::raw("SUM(ec.service_female_electors) AS e_ser_f"),
                            DB::raw("SUM(ec.nri_female_electors) AS e_nri_f"),

                            DB::raw("SUM(ec.gen_electors_other + ec.nri_third_electors) AS e_gen_o"),
                            DB::raw("SUM(ec.nri_third_electors) AS e_nri_o"),
                            DB::raw("SUM(ec.service_third_electors) AS e_ser_o"),

                            DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other + ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS e_gen_t"),
                            DB::raw("SUM(ec.service_male_electors+ec.service_female_electors+ec.service_third_electors) AS e_ser_t"),
                            DB::raw("SUM(ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS e_nri_t"),

                        ))
						->join('m_state','ec.st_code', '=', 'm_state.ST_CODE')
						->join('m_election_details as med',function($join){
							$join->on('ec.st_code', '=', 'med.ST_CODE')
							     ->on('ec.pc_no', '=', 'med.CONST_NO');
						})
                        ->where(array(
                            'med.CONST_TYPE'    	=> 'PC',
                            'med.election_status'   => '1',
                            //'med.ELECTION_ID'    	=> '1'
                        ))
                        ->orderBy('m_state.ST_NAME','asc')
                        ->groupBy('ec.st_code')

                        ->get()->toArray();
						
						if($user->designation == 'ROPC'){
							$prefix 	= 'ropc';
						}else if($user->designation == 'CEO'){	
							$prefix 	= 'pcceo';
						}else if($user->role_id == '27'){
							$prefix 	= 'eci-index';
						}else if($user->role_id == '7'){
							$prefix 	= 'eci';
						}				
						
						

		if($request->path() == "$prefix/statewisenumberelectors_pdf"){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.StatisticalPC.statewisenumberelectors_pdf', compact('data'));

			  if(verifyreport(9)){
        
                  $file_name = 'State Wise Number Of Electors'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/9/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '9',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('State Wise Number Of Electors.pdf');
		}else if($request->path() == "$prefix/statewisenumberelectors_xls"){
		
		
			$data = json_decode( json_encode($data), true);
				

			return Excel::create('State Wise Number Of Electors', function($excel) use ($data) {
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
					
					$sheet->mergeCells('A1:Q1');
					$sheet->mergeCells('C2:F2');
					$sheet->mergeCells('G2:I2');
					$sheet->mergeCells('J2:M2');
					$sheet->mergeCells('N2:Q2');
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('9 - State Wise Number Of Electors');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					

					
					$sheet->cell('A2', function($cells) {
						$cells->setValue('');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
		
					$sheet->cell('C2', function($cells) {
						$cells->setValue(' GENERAL(Including NRIs) ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					$sheet->cell('G2', function($cells) {
                        $cells->setValue(' SERVICE ');
                        $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });
					
					$sheet->cell('J2', function($cells) {
						$cells->setValue('GRAND');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					$sheet->cell('N2', function($cells) {
						$cells->setValue('NRIs');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					
					
					$sheet->cell('A3', function($cells) {
						$cells->setValue(' SL. NO.');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					$sheet->cell('B3', function($cells) {
						$cells->setValue(' STATE/UT ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
		
					$sheet->cell('C3', function($cells) {
						$cells->setValue(' MALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('D3', function($cells) {
						$cells->setValue(' FEMALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('E3', function($cells) {
						$cells->setValue(' THIRD GENDER ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('F3', function($cells) {
						$cells->setValue(' TOTAL ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					
					$sheet->cell('G3', function($cells) {
						$cells->setValue(' MALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('H3', function($cells) {
						$cells->setValue(' FEMALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					
					
					$sheet->cell('I3', function($cells) {
						$cells->setValue(' TOTAL ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('J3', function($cells) {
						$cells->setValue(' MALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('K3', function($cells) {
						$cells->setValue(' FEMALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('L3', function($cells) {
						$cells->setValue(' THIRD GENDER ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('M3', function($cells) {
						$cells->setValue(' TOTAL ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('N3', function($cells) {
						$cells->setValue(' MALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('O3', function($cells) {
						$cells->setValue(' FEMALE ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('P3', function($cells) {
						$cells->setValue(' THIRD GENDER ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
					$sheet->cell('Q3', function($cells) {
						$cells->setValue(' TOTAL ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
					});
					
						$sn = 1;
					
						$i= 4;
					
					
					$total_gen_m = $total_gen_f = $total_gen_o = $total_gen_t =$total_ser_m = $total_ser_f = $total_ser_o = $total_ser_t = $total_grand_m = $total_grand_f = $total_grand_o = $total_grand_t = $total_nri_m = $total_nri_f = $total_nri_o = $total_nri_t =0;
					
					
					if (!empty($data)) {
						foreach ($data as $key => $row){														
								
								
						$grand_m = $grand_f = $grand_o = $grand_t =0; 
					
					$grand_m = $row['e_gen_m'] + $row['e_ser_m']; 
					$grand_f = $row['e_gen_f'] + $row['e_ser_f']; 
					$grand_o = $row['e_gen_o'] + $row['e_ser_o']; 
					$grand_t = $row['e_gen_t'] + $row['e_ser_t']; 
					
					
					$total_gen_m += $row['e_gen_m']; 
					$total_gen_f += $row['e_gen_f']; 
					$total_gen_o += $row['e_gen_o']; 
					$total_gen_t += $row['e_gen_t']; 
					
					$total_ser_m += $row['e_ser_m'];
					$total_ser_f += $row['e_ser_f'];
					$total_ser_o += $row['e_ser_o'];
					$total_ser_t += $row['e_ser_t']; 
					
					
					$total_nri_m += $row['e_nri_m'];
					$total_nri_f += $row['e_nri_f'];
					$total_nri_o += $row['e_nri_o'];
					$total_nri_t += $row['e_nri_t']; 
										
					$total_grand_m += $grand_m;
					$total_grand_f += $grand_f;
					$total_grand_o += $grand_o;
					$total_grand_t += $grand_t;	
								
								
								
							
							$sheet->cell('A'.$i, $sn); 
							$sheet->cell('B'.$i, $row['ST_NAME']); 
							$sheet->cell('C'.$i, ($row['e_gen_m'] > 0) ? $row['e_gen_m']:'=(0)' ); 
							$sheet->cell('D'.$i, ($row['e_gen_f'] > 0) ? $row['e_gen_f']:'=(0)' ); 
							$sheet->cell('E'.$i, ($row['e_gen_o'] > 0) ? $row['e_gen_o']:'=(0)' ); 
							$sheet->cell('F'.$i, ($row['e_gen_t'] > 0) ? $row['e_gen_t']:'=(0)' ); 
							
							$sheet->cell('G'.$i, ($row['e_ser_m'] > 0) ? $row['e_ser_m']:'=(0)' ); 
							$sheet->cell('H'.$i, ($row['e_ser_f'] > 0) ? $row['e_ser_f']:'=(0)' ); 
							$sheet->cell('I'.$i, ($row['e_ser_t'] > 0) ? $row['e_ser_t']:'=(0)' ); 
							
							$sheet->cell('J'.$i, ($grand_m > 0) ? $grand_m:'=(0)' ); 
							$sheet->cell('K'.$i, ($grand_f > 0) ? $grand_f:'=(0)' ); 
							$sheet->cell('L'.$i, ($grand_o > 0) ? $grand_o:'=(0)' ); 
							$sheet->cell('M'.$i, ($grand_t > 0) ? $grand_t:'=(0)' ); 
							
							$sheet->cell('N'.$i, ($row['e_nri_m'] > 0) ? $row['e_nri_m']:'=(0)' ); 
							$sheet->cell('O'.$i, ($row['e_nri_f'] > 0) ? $row['e_nri_f']:'=(0)' ); 
							$sheet->cell('P'.$i, ($row['e_nri_o'] > 0) ? $row['e_nri_o']:'=(0)' ); 
							$sheet->cell('Q'.$i, ($row['e_nri_t'] > 0) ? $row['e_nri_t']:'=(0)' ); 
							
							$i++;
							$sn++;
						}
					
							
							$sheet->cell('B'.$i, function($cells) {
								$cells->setValue('TOTAL :');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							
							$sheet->cell('C'.$i, function($cells) use($total_gen_m) {
								$cells->setValue(($total_gen_m > 0) ? $total_gen_m:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('D'.$i, function($cells) use($total_gen_f) {
								$cells->setValue(($total_gen_f > 0) ? $total_gen_f:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('E'.$i, function($cells) use($total_gen_o) {
								$cells->setValue(($total_gen_o > 0) ? $total_gen_o:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('F'.$i, function($cells) use($total_gen_t) {
								$cells->setValue(($total_gen_t > 0) ? $total_gen_t:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
														
							$sheet->cell('G'.$i, function($cells) use($total_ser_m) {
								$cells->setValue(($total_ser_m > 0) ? $total_ser_m:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('H'.$i, function($cells) use($total_ser_f) {
								$cells->setValue(($total_ser_f > 0) ? $total_ser_f:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('I'.$i, function($cells) use($total_ser_t) {
								$cells->setValue(($total_ser_t > 0) ? $total_ser_t:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
													
	
						 
							$sheet->cell('J'.$i, function($cells) use($total_grand_m) {
								$cells->setValue(($total_grand_m > 0) ? $total_grand_m:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
														
							$sheet->cell('K'.$i, function($cells) use($total_grand_f) {
								$cells->setValue(($total_grand_f > 0) ? $total_grand_f:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('L'.$i, function($cells) use($total_grand_o) {
								$cells->setValue(($total_grand_o > 0) ? $total_grand_o:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('M'.$i, function($cells) use($total_grand_t) {
								$cells->setValue(($total_grand_t > 0) ? $total_grand_t:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							}); 
							
							
							$sheet->cell('N'.$i, function($cells) use($total_nri_m) {
								$cells->setValue(($total_nri_m > 0) ? $total_nri_m:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
														
							$sheet->cell('O'.$i, function($cells) use($total_nri_f) {
								$cells->setValue(($total_nri_f > 0) ? $total_nri_f:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('P'.$i, function($cells) use($total_nri_o) {
								$cells->setValue(($total_nri_o > 0) ? $total_nri_o:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});
							
							$sheet->cell('Q'.$i, function($cells) use($total_nri_t) {
								$cells->setValue(($total_nri_t > 0) ? $total_nri_t:'=(0)');
								$cells->setFont(array('name' => 'Times New Roman', 'bold' => true));
							});					
					}


					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,30);



					$sheet->mergeCells("A$i:K$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
		
				});
			})->download('xls');	
		
		
		}else{
			 return view('IndexCardReports.StatisticalPC.statewisenumberelectors', compact('data','sched','election_detail','user_data'));
		}
		
		
	}
	
	
		public function individualperformanceOfWownCandidates(Request $request){
		
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
          $session['election_detail']['st_code'] = $user->st_code;
          $session['election_detail']['st_name'] = $user->placename;
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		
		
		
		$stateData = DB::select("SELECT ST_CODE,ST_NAME from m_state order by ST_CODE asc");
		
		
		$dataArray = array();
		
		
		/* $data = DB::select("SELECT MP.pc_no, MP.PC_NAME, MP.PC_TYPE, A.`new_srno`, C.`cand_name`, P.PARTYABBRE, P.PARTYTYPE, D1.total_vote AS total_get_vote,
       (D2.gen_electors_male + D2.gen_electors_female + D2.gen_electors_other +D2.service_male_electors+D2.service_female_electors+D2.service_third_electors + D2.nri_male_electors + D2.nri_female_electors+ D2.nri_third_electors) as e_all_t,
       D1.total_vote as total_valid_votes,
       (case when D3.candidate_id is not null then '1' else '0' end ) as status
       FROM candidate_nomination_detail AS A
       JOIN candidate_personal_detail AS C ON A.`candidate_id` = C.`candidate_id`
       JOIN m_party AS P ON P.CCODE = A.party_id
       JOIN m_pc AS MP ON MP.ST_CODE = A.`st_code` AND MP.PC_NO = A.`pc_no`
       JOIN counting_pcmaster AS D1 ON D1.pc_no = A.pc_no AND D1.st_code = A.st_code
       JOIN electors_cdac AS D2 ON D2.pc_no = A.pc_no AND D2.st_code = A.st_code
       LEFT JOIN winning_leading_candidate AS D3 ON D3.candidate_id = A.candidate_id
       WHERE C.`cand_gender` = 'female' AND A.st_code = '$st_code' AND D2.year = '2019'
       GROUP BY A.pc_no, A.candidate_id ;"); */
				
		
			$data = DB::select("SELECT TEMP.*,
CASE WHEN wlc.candidate_id=TEMP.candidate_id then 'W'
WHEN (TEMP.total_get_vote/TEMP.total_valid_votes) < 0.1666 THEN 'DF' ELSE 'L' END AS 'FINAL_STATUS'
FROM
(
SELECT MS.ST_NAME, MP.pc_no, MP.PC_NAME, MP.PC_TYPE, A.`new_srno`,A.candidate_id,
C.`cand_name`, P.PARTYABBRE, P.PARTYTYPE,
(select sum(total_vote) from counting_pcmaster where counting_pcmaster.candidate_id = A.candidate_id) as total_get_vote,
(select sum(total_vote) from counting_pcmaster where counting_pcmaster.pc_no = A.pc_no AND counting_pcmaster.st_code = A.st_code and counting_pcmaster.party_id != '1180') as total_valid_votes,
(select sum(nri_male_electors + nri_female_electors + nri_third_electors + service_male_electors + service_female_electors + gen_electors_male + gen_electors_female + gen_electors_other + service_third_electors) from electors_cdac where electors_cdac.pc_no = A.pc_no AND electors_cdac.st_code = A.st_code) as e_all_t
FROM candidate_nomination_detail AS A
JOIN candidate_personal_detail AS C ON A.`candidate_id` = C.`candidate_id`
JOIN m_party AS P ON P.CCODE = A.party_id
JOIN m_pc AS MP ON MP.ST_CODE = A.`st_code` AND MP.PC_NO = A.`pc_no`
JOIN m_election_details AS med ON med.ST_CODE = A.`st_code` AND med.CONST_NO = A.`pc_no`
JOIN m_state AS MS ON MS.ST_CODE = A.`st_code`
WHERE C.`cand_gender` = 'female' AND med.CONST_TYPE = 'pc' and med.election_status = '1'
and A.application_status = '6'
AND A.finalaccepted = '1'
GROUP BY MS.ST_CODE, A.pc_no, A.candidate_id
ORDER BY MS.ST_CODE, A.pc_no, A.`new_srno` ASC
)TEMP left join winning_leading_candidate wlc
on wlc.candidate_id=TEMP.candidate_id");	
				
		//echo '<pre>'; print_r($data); die;	

		foreach($data as $key){
			
			$dataArray[$key->ST_NAME][$key->pc_no.' '.$key->PC_NAME.' ('.$key->PC_TYPE.')'][] = array(
				'PC_NO' 					=> $key->pc_no,
				'PC_NAME' 					=> $key->PC_NAME,
				'srno' 						=> $key->new_srno,
				'candidate_name' 			=> $key->cand_name,
				'party_abbre' 				=> $key->PARTYABBRE,
				'PARTYTYPE' 				=> $key->PARTYTYPE,
				'candidate_votes' 			=> $key->total_get_vote,
				'total_electors' 			=> $key->e_all_t,
				'total_votes' 				=> $key->total_valid_votes,				
				'status' 					=> $key->FINAL_STATUS				
			);
		}
				
		
		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}				
						
						

		if($request->path() == "$prefix/individualperformanceofwomencandidates_pdf"){
			
			
			$pdf = PDF::loadView('IndexCardReports.StatisticalPC.performanceOfWownCandidates_pdf', compact('dataArray','sched','election_detail','user_data'));

			 if(verifyreport(25)){
        
                  $file_name = 'Individual Performance Of Women Candidate'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/25/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '25',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('Individual Performance Of Women Candidate.pdf');
		}else if($request->path() == "$prefix/individualperformanceofwomencandidates_xls"){
			
			$data = json_decode( json_encode($dataArray), true);
				
				
			//echo '<pre>'; print_r($data); die;		
					
			
			return Excel::create('Individual Performance Of Women Candidate', function($excel) use ($data) {
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
					//$sheet->fromArray($data);
					
					
					$sheet->mergeCells('A1:I1');
					$sheet->mergeCells('G3:H3');
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('25 - INDIVIDUAL PERFORMANCE OF WOMEN CANDIDATES');
						$cells->setFont(array('name' => 'Times New Roman','size' => 15,'bold' => true));
                        $cells->setAlignment('center');
					});
						
					
					$sheet->mergeCells('G3:H3');
					$sheet->cell('G3', function($cells) {
						$cells->setValue('% OF VOTES SECURED');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
						
					$sheet->getStyle('A4')->getAlignment()->setWrapText(true);
					$sheet->setSize('A4', 25,50);
					
					$sheet->getStyle('B4')->getAlignment()->setWrapText(true);
					$sheet->setSize('B4', 25,50);
					
					$sheet->getStyle('C4')->getAlignment()->setWrapText(true);
					$sheet->setSize('C4', 50,50);
					
					$sheet->getStyle('D4')->getAlignment()->setWrapText(true);
					$sheet->setSize('D4', 15,50);
					
					$sheet->getStyle('E4')->getAlignment()->setWrapText(true);
					$sheet->setSize('E4', 15,50);
					
					$sheet->getStyle('F4')->getAlignment()->setWrapText(true);
					$sheet->setSize('F4', 20,50);
					
					$sheet->getStyle('G4')->getAlignment()->setWrapText(true);
					$sheet->setSize('G4', 25,50);
					
					$sheet->getStyle('H4')->getAlignment()->setWrapText(true);
					$sheet->setSize('H4', 25,50);
					
					$sheet->getStyle('I4')->getAlignment()->setWrapText(true);
					$sheet->setSize('I4', 15,50);
					
					$sheet->getStyle('J4')->getAlignment()->setWrapText(true);
					$sheet->setSize('J4', 20,50);
					
					
					$sheet->cell('A4', function($cells) {
						$cells->setValue('STATE NAME');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));					
					});
					
					$sheet->cell('B4', function($cells) {
						$cells->setValue('CONSTITUENCY NAME');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));					
					});
					
					
		
					$sheet->cell('C4', function($cells) {
						$cells->setValue('CANDIDATES NAME');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});
					
					
					$sheet->cell('D4', function($cells) {
						$cells->setValue('PARTY NAME');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});
					
					
					$sheet->cell('E4', function($cells) {
						$cells->setValue('PARTY TYPE');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});
					
					
					$sheet->cell('F4', function($cells) {
						$cells->setValue('VOTES SECURED');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});			
					
					
					$sheet->cell('G4', function($cells) {
						$cells->setValue('OVER TOTAL ELECTORS IN CONSTITUENCY');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});
					
					
					$sheet->cell('H4', function($cells) {
						$cells->setValue('OVER TOTAL VALID VOTES IN CONSTITUENCY');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});		
					
					$sheet->cell('I4', function($cells) {
						$cells->setValue('STATUS');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});
					$sheet->cell('J4', function($cells) {
						$cells->setValue('TOTAL VALID VOTES');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
					});
					
					
									
					$i= 5;
					$count= 1;
					
									
					if (!empty($data)) {
						foreach ($data as $keys => $rowdata){
							
							foreach ($rowdata as $key => $raw){
								 
								
								foreach ($raw as $keyk => $row){
								
								if($row['total_electors']){
									$over_electors = (($row['candidate_votes']*100)/$row['total_electors']);
								}else{
									$over_electors = 0;
								}
								
								if($row['total_votes']){
									$over_voters = (($row['candidate_votes']*100)/$row['total_votes']);
								}else{
									$over_voters = 0;
								}
								
								
								$sheet->cell('A'.$i, $keys ); 
								$sheet->cell('B'.$i, $row['PC_NAME'] ); 
								$sheet->cell('C'.$i, $row['candidate_name'] ); 
								$sheet->cell('D'.$i, $row['party_abbre'] ); 
								$sheet->cell('E'.$i, $row['PARTYTYPE'] ); 
								$sheet->cell('F'.$i, ($row['candidate_votes']) ? $row['candidate_votes']:'=(0)' ); 
								$sheet->cell('G'.$i, ($over_electors) ? $over_electors:'=(0)' ); 
								$sheet->cell('H'.$i, ($over_voters) ? $over_voters:'=(0)' ); 
								$sheet->cell('I'.$i, $row['status'] ); 
								$sheet->cell('J'.$i, ($row['total_votes']) ? $row['total_votes']:'=(0)' );
								
								$i++;						
								$count++;			
								}								
							}
							
							
						}
					}

					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,30);



					$sheet->mergeCells("A$i:E$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
		
				});
			})->download('xls');
		
		}else{
		 return view('IndexCardReports.StatisticalPC.performanceOfWownCandidates', compact('dataArray','arrSessionData','sched','election_detail','user_data'));
		}
	}
	
	

	public function participationofWomeneletorsinPoll(Request $request){
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
          //$session['election_detail']['st_code'] = $user->st_code;
          //$session['election_detail']['st_name'] = $user->placename;
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
					
					$data = DB::table('m_pc')
                          ->select(array(
						    'm_state.ST_NAME',
							DB::raw("(SELECT count(*) FROM `m_pc` join m_election_details as med on med.st_code = m_pc.ST_CODE and med.CONST_NO = m_pc.PC_NO where m_pc.ST_CODE= m_state.ST_CODE and med.CONST_TYPE = 'PC' and med.election_status = '1' GROUP By m_pc.ST_CODE) AS no_of_seats"),
                            DB::raw("SUM(ec.gen_electors_female + ec.service_female_electors + ec.nri_female_electors) AS electors_female"),
                            DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other +ec.service_male_electors+ec.service_female_electors+ec.service_third_electors + ec.nri_male_electors + ec.nri_female_electors+ ec.nri_third_electors) AS electors_total"),
							
                            DB::raw("(SELECT SUM(ecoi.general_female_voters + ecoi.nri_female_voters) AS voter_female FROM electors_cdac_other_information as ecoi where ST_CODE= m_state.ST_CODE GROUP By ST_CODE ) AS voter_female"),
                            DB::raw("(SELECT SUM(ecoi.general_male_voters + ecoi.general_female_voters + ecoi.general_other_voters + ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters + ecoi.service_postal_votes_under_section_8 + ecoi.service_postal_votes_gov) AS voter_total FROM electors_cdac_other_information as ecoi where ST_CODE= m_state.ST_CODE GROUP By ST_CODE) AS voter_total") 

                        ))
												
						->join('electors_cdac as ec', function($join){
									$join->on('m_pc.ST_CODE', '=', 'ec.st_code')
										->on('m_pc.PC_NO', '=', 'ec.pc_no');
								}		
							)
						->join('m_election_details as med',function($join){
							   $join->on('med.st_code', '=', 'm_pc.ST_CODE')
									->on('med.CONST_NO', '=', 'm_pc.PC_NO');
						   })
						->join('m_state','m_pc.ST_CODE', '=', 'm_state.ST_CODE')
						 ->where(array(
							   //'ec.election_id'    => '1',
							   'med.CONST_TYPE' => 'PC',
							   'med.election_status' => '1',
							   //'med.ELECTION_ID' => '1'
						   ))
                        ->groupBy('m_state.ST_CODE','m_state.ST_NAME')
                        ->get()->toArray();
		
		
		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}				
						
						

		if($request->path() == "$prefix/participationofWomeneletorsinPoll_pdf"){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.StatisticalPC.participationofWomeneletorsinPoll_pdf', compact('data','user_data'));

			 if(verifyreport(23)){
        
                  $file_name = 'Participation Of Women Electors In Polls'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/23/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '23',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('Participation Of Women Electors In Polls.pdf');
		}else if($request->path() == "$prefix/participationofWomeneletorsinPoll_xls"){
			
			$data = json_decode( json_encode($data), true);
				
		
			return Excel::create('Participation Of Women Electors In Polls', function($excel) use ($data) {
				$excel->sheet('mySheet', function($sheet) use ($data)
				{
				   // $sheet->fromArray($data);
					$sheet->mergeCells('D1:E1');

					$sheet->cell('D1', function($cells) {
						$cells->setValue('23 - Participation Of Women Electors In Polls');
					});
		
					$sheet->cells('D1:E1', function($cells) {
						$cells->setFont(array(
							'size'       => '15',
							'bold'       => true
						));
					});
		
					$sheet->getStyle('A2')->getAlignment()->setWrapText(true);
					$sheet->setSize('A2', 20,50);
					
					$sheet->getStyle('B2')->getAlignment()->setWrapText(true);
					$sheet->setSize('B2', 15,50);
					
					$sheet->getStyle('C2')->getAlignment()->setWrapText(true);
					$sheet->setSize('C2', 15,50);
					
					$sheet->getStyle('D2')->getAlignment()->setWrapText(true);
					$sheet->setSize('D2', 20,50);
					
					$sheet->getStyle('E2')->getAlignment()->setWrapText(true);
					$sheet->setSize('E2', 20,50);
					
					$sheet->getStyle('F2')->getAlignment()->setWrapText(true);
					$sheet->setSize('F2', 15,50);
					
					$sheet->getStyle('H2')->getAlignment()->setWrapText(true);
					$sheet->setSize('H2', 20,50);
					
					$sheet->getStyle('I2')->getAlignment()->setWrapText(true);
					$sheet->setSize('I2', 20,50);
					
					$sheet->getStyle('J2')->getAlignment()->setWrapText(true);
					$sheet->setSize('J2', 20,50);
					
					$sheet->cell('A2', function($cell) {$cell->setValue('STATE');   });
					$sheet->cell('B2', function($cell) {$cell->setValue('NO. OF SEATS');   });
					$sheet->cell('C2', function($cell) {$cell->setValue('TOTAL ELECTORS');   });
					$sheet->cell('D2', function($cell) {$cell->setValue('WOMEN ELECTORS');   });
					$sheet->cell('E2', function($cell) {$cell->setValue('% OF WOMEN ELECTORS OVER TOTAL ELECTORS');   });
					$sheet->cell('F2', function($cell) {$cell->setValue('Total Voters');   });
					$sheet->cell('G2', function($cell) {$cell->setValue('WOMEN Voters');   });
					$sheet->cell('H2', function($cell) {$cell->setValue('% OF WOMEN VOTERS OVER VOTERS');   });
					$sheet->cell('I2', function($cell) {$cell->setValue('% OF WOMEN VOTERS OVER WOMEN ELECTORS');   });
					$sheet->cell('J2', function($cell) {$cell->setValue('TOTAL POLL% IN THE STATE/UT');   });
					if (!empty($data)) {
						
						$totalNoOfSeats = $totalElectors = $totalWomenElectors = $totalWomenElectorsPer = $totalVoters = $totalWomenVoters = $totalWomenVotersPer = $totalWomenVotersOverElectorsPer = $totalVotersPer = 0;
						
						foreach ($data as $key => $value) {
							
							if ($value['electors_total'] > 0){
										$perWomenElectors = ($value['electors_female']*100)/$value['electors_total'];
										
									}else{
										$perWomenElectors = 0;
									}
									
									if ($value['voter_total'] > 0){
										$perWomenVoters = ($value['voter_female']*100)/$value['voter_total'];
										
									}else{
										$perWomenVoters = 0;
									}
									
									if ($value['electors_female'] > 0){
										$perWomenVotersOverElectors = ($value['voter_female']*100)/$value['electors_female'];
										
										
									}else{
										$perWomenVotersOverElectors = 0;
									}
									
									if ($value['electors_total'] > 0){
										$perTotalPoll = ($value['voter_total']*100)/$value['electors_total'];
										
									}else{
										$perTotalPoll = 0;
									}
									
									
									$totalNoOfSeats += $value['no_of_seats'];
									$totalElectors += $value['electors_total'];
									$totalWomenElectors += $value['electors_female'];								
									$totalVoters += $value['voter_total'];
									$totalWomenVoters += $value['voter_female'];
							
							
							
							
							
							$i= $key+3;
							$sheet->cell('A'.$i, $value['ST_NAME']); 
							$sheet->cell('B'.$i, ($value['no_of_seats'] > 0) ? $value['no_of_seats']:'=(0)' ); 
							$sheet->cell('C'.$i, ($value['electors_total'] > 0) ? $value['electors_total']:'=(0)' ); 
							$sheet->cell('D'.$i, ($value['electors_female'] > 0) ? $value['electors_female']:'=(0)' ); 
							$sheet->cell('E'.$i, ($perWomenElectors > 0) ? $perWomenElectors:'=(0)' ); 
							$sheet->cell('F'.$i, ($value['voter_total'] > 0) ? $value['voter_total']:'=(0)' ); 
							$sheet->cell('G'.$i, ($value['voter_female'] > 0) ? $value['voter_female']:'=(0)' ); 
							$sheet->cell('H'.$i, ($perWomenVoters > 0) ? $perWomenVoters:'=(0)' ); 
							$sheet->cell('I'.$i, ($perWomenVotersOverElectors > 0) ? $perWomenVotersOverElectors:'=(0)' ); 
							$sheet->cell('J'.$i, ($perTotalPoll > 0) ? $perTotalPoll:'=(0)' ); 
						}
						
							/* $i += 2;
							
												
							if ($totalElectors > 0){
									$totalWomenElectorsPer = round((($totalWomenElectors*100)/$totalElectors),2);
								}else{
									$totalWomenElectorsPer = 0;
								}
								
								if ($totalVoters > 0){
									$totalWomenVotersPer = round((($totalWomenVoters*100)/$totalVoters),2);
								}else{
									$totalWomenVotersPer = 0;
								}
								
								if ($totalElectors > 0){
									$totalWomenVotersOverElectorsPer = round((($totalWomenVoters*100)/$totalElectors),2);
									
								}else{
									$totalWomenVotersOverElectorsPer = 0;
								}
								
								if ($totalElectors > 0){
									$totalVotersPer = round((($totalVoters*100)/$totalElectors),2);
									
								}else{
									$totalVotersPer = 0;
								}
							
												
							$sheet->cell('A'.$i, 'Total'); 
							$sheet->cell('B'.$i, ($totalNoOfSeats > 0) ? $totalNoOfSeats:'=(0)' ); 
							$sheet->cell('C'.$i, ($totalElectors > 0) ? $totalElectors:'=(0)' ); 
							$sheet->cell('D'.$i, ($totalWomenElectors > 0) ? $totalWomenElectors:'=(0)' ); 
							$sheet->cell('E'.$i, ($totalWomenElectorsPer > 0) ? $totalWomenElectorsPer:'=(0)' ); 
							$sheet->cell('F'.$i, ($totalVoters > 0) ? $totalVoters:'=(0)' ); 
							$sheet->cell('G'.$i, ($totalWomenVoters > 0) ? $totalWomenVoters:'=(0)' ); 
							$sheet->cell('H'.$i, ($totalWomenVotersPer > 0) ? $totalWomenVotersPer:'=(0)' ); 
							$sheet->cell('I'.$i, ($totalWomenVotersOverElectorsPer > 0) ? $totalWomenVotersOverElectorsPer:'=(0)' ); 
							$sheet->cell('J'.$i, ($totalVotersPer > 0) ? $totalVotersPer:'=(0)' ); */
						
					}

					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,30);



					$sheet->mergeCells("A$i:G$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
		
				});
			})->download('xls');
			
			
		}else{
			return view('IndexCardReports.StatisticalPC.participationofWomeneletorsinPoll', compact('data','user_data'));
		}
	}
	
		
	public function scheduleloksabhahighlights(Request $request){
		
		DB::enableQueryLog();
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
          $session['election_detail']['st_code'] = $user->st_code;
          $session['election_detail']['st_name'] = $user->placename;
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		
		
		$data = DB::select("SELECT  m_schedule.SCHEDULEID, Count(DISTINCT(pd.ST_CODE)) AS no_state, count(pd.`ScheduleID`) AS no_pc, m_schedule.DATE_POLL 
		FROM m_election_details AS pd 
		join m_schedule on pd.ScheduleID = m_schedule.SCHEDULEID
		where  pd.CONST_TYPE = 'PC' and pd.election_status = '1'
		GROUP by m_schedule.DATE_POLL");
		
		$data2 = DB::select("SELECT   Count(DISTINCT(pd.`PHASE_NO`)) AS no_phase, m_state.ST_NAME 
		FROM m_election_details AS pd 
		join m_state on pd.`ST_CODE` = m_state.ST_CODE
		where pd.CONST_TYPE = 'PC' and pd.election_status = '1'
		GROUP by pd.ST_CODE
        ORDER BY m_state.ST_NAME asc");
		
		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}				
						
						

		if($request->path() == "$prefix/scheduleloksabhahighlights_pdf"){
			
			
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.StatisticalPC.scheduleloksabhahighlights_pdf', compact('data','data2','user_data'));

			 if(verifyreport(1)){
        
                  $file_name = 'THE SCHEDULE OF GE TO LOK SABHA'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/1/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '1',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('THE SCHEDULE OF GE TO LOK SABHA.pdf');
		}else if($request->path() == "$prefix/scheduleloksabhahighlights_xls"){

			$data = json_decode( json_encode($data), true);
			$data2 = json_decode( json_encode($data2), true);
				

			return Excel::create('THE SCHEDULE OF GE TO LOK SABHA', function($excel) use ($data,$data2) {
				$excel->sheet('mySheet', function($sheet) use ($data,$data2)
				{
	  
	  
					$sheet->mergeCells('A1:D1');
					
	  
					$sheet->cell('A1', function($cells) {
						$cells->setValue('1. THE SCHEDULE OF GE TO LOK SABHA');
						$cells->setFont(array('name' => 'Times New Roman','size' => 15,'bold' => true));
                        $cells->setAlignment('center');
					});
					


					$sheet->mergeCells('A2:D2');
	  
					$sheet->cell('A2', function($cells) {
						$cells->setValue('PHASE GENERAL ELECTIONS-'.getElectionYear().'');
						$cells->setFont(array('name' => 'Times New Roman','size' => 11,'bold' => true));
                        $cells->setAlignment('center');
					});

					
					$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('B3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('C3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('D3')->getAlignment()->setWrapText(true);
					$sheet->setSize('A3', 20,30);
					$sheet->setSize('B3', 25,30);
					$sheet->setSize('C3', 25,30);
					$sheet->setSize('D3', 20,30);
			
					$sheet->cell('A3', function($cells) {
						$cells->setValue('PHASE');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
		
					$sheet->cell('B3', function($cells) {
						$cells->setValue('Number of State & union Territories');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$sheet->cell('C3', function($cells) {
						$cells->setValue('Nmber Of Parliamentry Constituencies');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$sheet->cell('D3', function($cells) {
						$cells->setValue('Poll Dates');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$i= 4;
									
					if (!empty($data)) {
							
						foreach ($data as $key => $row){
														
							$sheet->cell('A'.$i, $row['SCHEDULEID']); 
							$sheet->cell('B'.$i, $row['no_state'] ); 
							$sheet->cell('C'.$i, $row['no_pc'] ); 
							$sheet->cell('D'.$i, function($cells) use($row) {
								$cells->setValue(date('d M Y', strtotime($row['DATE_POLL'])));
								$cells->setAlignment('right');
							});
							
							$i++;						
						}
					}
					
					$i++;
					$i++;
					
					$sheet->mergeCells("A$i:D$i");
	  
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('NUMBER OF PHASES IN STATES AND UNION TERRITORIES');
						$cells->setFont(array('name' => 'Times New Roman','size' => 13,'bold' => true));
                        $cells->setAlignment('center');
					});

					
					$i++;
			
			
					$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('B3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('C3')->getAlignment()->setWrapText(true);
					$sheet->setSize('A3', 20,30);
					$sheet->setSize('B3', 25,30);
					$sheet->setSize('C3', 30,30);
			
			
			
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Sr No');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
		
					$sheet->cell('B'.$i, function($cells) {
						$cells->setValue('NO OF PHASES');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					
					$sheet->cell('C'.$i, function($cells) {
						$cells->setValue('STATES AND UNION TERRITORIES');
						$cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                        $cells->setAlignment('center');
					});
					
					$i++;
					
					if (!empty($data2)) {
							
						foreach ($data2 as $key => $row){
														
							$sheet->cell('A'.$i, $key+1); 
							$sheet->cell('B'.$i, $row['no_phase'] ); 
							$sheet->cell('C'.$i, $row['ST_NAME'] );
							
							$i++;						
						}
					}

					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,30);



					$sheet->mergeCells("A$i:E$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
					
					
					
		
				});
			})->download('xls');	

		}else{
			return view('IndexCardReports.StatisticalPC.scheduleloksabhahighlights', compact('data','data2','user_data'));
		}		
	}
	
	
	
	public function statewisevoterturnout(Request $request){
		//DB::enableQueryLog();
		 	$session = $request->session()->all();
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		// $year = $session['election_detail']['YEAR'];
       // $scheduleID = $session['election_detail']['ScheduleID']; 
		
		$statewisevoterturnouts	=	DB::table("electors_cdac as ec")
		->select('ec.st_code','m_state.ST_NAME',
		DB::raw("SUM(ec.gen_electors_male + ec.gen_electors_female + ec.gen_electors_other + ec.nri_male_electors + ec.nri_female_electors + ec.nri_third_electors) AS e_gen_t"),
		DB::raw("SUM(ec.service_male_electors + ec.service_female_electors + ec.service_third_electors) AS e_ser_t")
		)
        ->join('m_state', 'm_state.st_code', '=', 'ec.st_code')
		->join('m_election_details as med',function($join){
           $join->on('med.st_code', '=', 'ec.st_code')
                ->on('med.CONST_NO', '=', 'ec.pc_no');
			}
		)
		->where(array(
		   //'ec.election_id'=>'1',
		   'med.CONST_TYPE' => 'PC',
		   'med.election_status' => '1',
		   //'med.ELECTION_ID' => '1'
		))
		->groupBy('ec.st_code')
		->orderBy('m_state.ST_NAME','ASC')
		->get()->toArray();
		
		
		
		//dd($statewisevoterturnouts);
		
		
		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
				
		if($request->path() == "$prefix/statewisevoterturnout_pdf"){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.StatisticalPC.statewisevoterturnout_pdf',compact('statewisevoterturnouts','year'));  


                 if(verifyreport(12)){
        
                  $file_name = 'State Wise Voters Turn Out'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/12/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '12',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('State Wise Voters Turn Out.pdf');
		}else if($request->path() == "$prefix/statewisevoterturnout_xls"){
			
			//$statewisevoterturnouts = json_decode( json_encode($statewisevoterturnouts), true);
			
			return Excel::create('State Wise Voters Turn Out', function($excel) use ($statewisevoterturnouts) {
            $excel->sheet('mySheet', function($sheet) use ($statewisevoterturnouts)
            {
                $sheet->mergeCells('A1:H1');
              
                $sheet->cells('A1', function($cells) {                  
                            $cells->setValue('12. State Wise Voters Turn Out');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 14,'bold' => true));
                            $cells->setAlignment('center');
                        });
                        
                        
                        
                $sheet->mergeCells('B3:C3');       
                $sheet->mergeCells('D3:E3');       		  
                   $sheet->cells('B3', function($cells) {                   
                            $cells->setValue('ELECTORS');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12));
                            $cells->setAlignment('center');
                            
                        });
                        
                   $sheet->cells('D3', function($cells) {
                            $cells->setValue('VOTERS');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12));
                            $cells->setAlignment('center');
                            
                        });
					
                        

                $sheet->cell('A4', function($cell) {$cell->setValue('NAME OF State/UT');});
                $sheet->cell('B4', function($cell) {$cell->setValue('GENERAL (INCLUDING NRIs)');});
                $sheet->cell('C4', function($cell) {$cell->setValue('SERVICE');});
                $sheet->cell('D4', function($cell) {$cell->setValue('EVM');});
                $sheet->cell('E4', function($cell) {$cell->setValue('POSTAL');});
                $sheet->cell('F4', function($cell) {$cell->setValue('VOTERS TOUROUT %');});

                $sheet->getStyle('F4')->getAlignment()->setWrapText(true);
               
                
                if (!empty($statewisevoterturnouts)) {

					$e_gen_t = $e_ser_t = $vt_all_t = $postal_valid_votes =0;
                            foreach ($statewisevoterturnouts as $key => $value) {
                                $i = $key + 5;

							$votes = \App\models\Admin\VoterModel::get_total([
								'group_by' => 'st_code',
								'st_code' => $value->st_code
							]);

							if(($value->e_gen_t+$value->e_ser_t) > 0){
								$avg = ((($votes['vt_all_t']+$votes['postal_valid_votes'])/($value->e_gen_t+$value->e_ser_t))*100);
							}else{
								$avg = 0;
							}

                                $sheet->cell('A' . $i, $value->ST_NAME);
                                $sheet->cell('B' . $i, $value->e_gen_t);
                                $sheet->cell('C' . $i, $value->e_ser_t);
                                $sheet->cell('D' . $i, $votes['vt_all_t']);
                                $sheet->cell('E' . $i, $votes['postal_valid_votes']);
                                $sheet->cell('F' . $i, $avg);
								
								$e_gen_t += $value->e_gen_t;
								$e_ser_t += $value->e_ser_t;
								$vt_all_t += $votes['vt_all_t'];
								$postal_valid_votes += $votes['postal_valid_votes'];
								
							$i++;                          
                            }							
								if(($e_gen_t+$e_ser_t) > 0){
								$avgtotal = round(((($vt_all_t+$postal_valid_votes)/($e_gen_t+$e_ser_t))*100),2);
							}else{
								$avg = 0;
							}
							
//								$sheet->mergeCells("A$i:B$i");
								
								$sheet->cells('A' . $i, function($cells) {
									$cells->setValue('Total');								
								});
								
								$sheet->cells('B' . $i, function($cells) use($e_gen_t) {
									$cells->setValue($e_gen_t);
								});
								
								$sheet->cells('C' . $i, function($cells) use($e_ser_t) {
									$cells->setValue($e_ser_t);									
								});
								
								$sheet->cells('D' . $i, function($cells) use($vt_all_t) {
									$cells->setValue($vt_all_t);
								});
								
								$sheet->cells('E' . $i, function($cells) use($postal_valid_votes) {
									$cells->setValue($postal_valid_votes);
								});
								
								$sheet->cells('F' . $i, function($cells) use($avgtotal) {
									$cells->setValue($avgtotal);	
								});
							
                        }

                        $i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,40);



					$sheet->mergeCells("A$i:G$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
                    });
                })->export();
		
		}else{
		return view('IndexCardReports.StatisticalPC.statewisevoterturnout',compact('statewisevoterturnouts','year','sched','election_detail','user_data'));
	    }
			
	}
	
	public function pollingstationinformation(Request $request){
		//DB::enableQueryLog();
		
		$session = $request->session()->all();
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
          $status=$this->commonModel->allstatus();
          if(isset($ele_details)) {  $i=0;
            foreach($ele_details as $ed) {
               $sched=$this->commonModel->getschedulebyid($ed->ScheduleID);
               $const_type=$ed->CONST_TYPE;
             }
          }
          $session['election_detail'] = array();
          $session['election_detail']['st_code'] = $user->st_code;
          $session['election_detail']['st_name'] = $user->placename;
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		
		
         
        $st_name = $session['election_detail']['st_name'];
		
 
		$pollingstations=DB::table('electors_cdac as ec')
					  ->select('ec.pc_no','ec.st_code','m_state.st_name','m_pc.pc_name','ecoi.total_polling_station_s_i_t_c as total_no_polling_station','ec.electors_male as e_gen_m','ec.electors_female as e_gen_f','ec.electors_other as e_gen_o','ec.service_male_electors as e_ser_m','ec.service_female_electors as e_ser_f','ec.service_third_electors as e_ser_o')
					   ->join('m_state', 'ec.st_code', '=', 'm_state.st_code')
						->join('m_pc', function($join){
								$join->on('ec.pc_no', '=', 'm_pc.pc_no')
									 ->on('ec.st_code', '=', 'm_pc.st_code');
								})
						->leftjoin('electors_cdac_other_information as ecoi', function($join){
								$join->on('ecoi.pc_no', '=', 'm_pc.pc_no')
									 ->on('ecoi.st_code', '=', 'm_pc.st_code');
								})
						//->where('ec.election_id','1')
						//->where('ecoi.year','2019')
					   ->groupBy('ec.st_code','ec.pc_no')
                       ->get()->toArray();
					   
		//echo "<pre>"; print_r($pollingstations); die;	


		if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
		
		
		
		
		if($request->path() == "$prefix/pollingstationinformation_pdf"){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.StatisticalPC.pollingstationinformation_pdf',['pollingstations' => $pollingstations]);  
			return $pdf->download('Polling Station Information.pdf');
		}else if($request->path() == "$prefix/pollingstationinformation_xls"){

			$pollingstations = json_decode( json_encode($pollingstations), true);
				

			return Excel::create('Polling Station Information', function($excel) use ($pollingstations) {
				$excel->sheet('mySheet', function($sheet) use ($pollingstations)
				{
					
					$sheet->mergeCells('A1:P1');
					$sheet->mergeCells('A2:C2');
					$sheet->mergeCells('E2:H2');
					$sheet->mergeCells('I2:L2');
					$sheet->mergeCells('M2:P2');
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('4 - Polling Station Information');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					

					
					$sheet->cell('D2', function($cells) {
						$cells->setValue('Polling Station');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
		
					$sheet->cell('E2', function($cells) {
						$cells->setValue('General Electors');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					$sheet->cell('I2', function($cells) {
						$cells->setValue('Service Electors');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
					$sheet->cell('M2', function($cells) {
						$cells->setValue('Grand Total');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
					
								
					
					$sheet->cell('A3', function($cells) {
						$cells->setValue('State/UT');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
					});
		
					$sheet->cell('B3', function($cells) {
						$cells->setValue('PC. No.');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('C3', function($cells) {
						$cells->setValue('PC Name');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('D3', function($cells) {
						$cells->setValue('Total(Regular+Auxilary)');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
				
					$sheet->cell('E3', function($cells) {
						$cells->setValue('Male');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('F3', function($cells) {
						$cells->setValue('Female');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('G3', function($cells) {
						$cells->setValue('Third Gender');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('H3', function($cells) {
						$cells->setValue('Total');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('I3', function($cells) {
						$cells->setValue('Male');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('J3', function($cells) {
						$cells->setValue('Female');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('K3', function($cells) {
						$cells->setValue('Third Gender');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('L3', function($cells) {
						$cells->setValue('Total');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('M3', function($cells) {
						$cells->setValue('Male');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('N3', function($cells) {
						$cells->setValue('Female');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('O3', function($cells) {
						$cells->setValue('Third Gender');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
					$sheet->cell('P3', function($cells) {
						$cells->setValue('Total');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 11, 'bold' => true));
					});
					
						$sn = 1;
					
						$i= 4;
					
				
					if (!empty($pollingstations)) {
						
						$gen_m_sum_tot  = $gen_f_sum_tot = $gen_o_sum_tot =  $ser_m_sum_tot = $ser_f_sum_tot =  $ser_o_sum_tot = $pollingregaux_tot = 0;
						$flag = $stcode = $gen_m_sum  = $gen_f_sum = $gen_o_sum =  $ser_m_sum = $ser_f_sum = $ser_o_sum = $pollingregaux = 0;
						
						
						foreach ($pollingstations as $key => $pollingstation){														
								
								
						$pollingregaux_tot +=$pollingstation['total_no_polling_station'];
						$gen_m_sum_tot += $pollingstation['e_gen_m'];
						$gen_f_sum_tot += $pollingstation['e_gen_f'];						
						$gen_o_sum_tot += $pollingstation['e_gen_o'];						
						$ser_m_sum_tot += $pollingstation['e_ser_m'];
						$ser_f_sum_tot += $pollingstation['e_ser_f'];
						$ser_o_sum_tot += $pollingstation['e_ser_o'];	
							
						if($stcode!=0 || $stcode!=$pollingstation['st_code']){
						
							//$sheet->mergeCells("A$i:C$i");
												
                            $sheet->cell('A'.$i, 'Total'); 
							$sheet->cell('D'.$i, ($pollingregaux > 0) ? $pollingregaux:'=(0)' ); 
							$sheet->cell('E'.$i, ($gen_m_sum > 0) ? $gen_m_sum:'=(0)' ); 
							$sheet->cell('F'.$i, ($gen_f_sum > 0) ? $gen_f_sum:'=(0)' ); 
							$sheet->cell('G'.$i, ($gen_o_sum > 0) ? $gen_o_sum:'=(0)' ); 							
							$sheet->cell('H'.$i, $gen_m_sum + $gen_f_sum + $gen_o_sum ); 
							$sheet->cell('I'.$i, ($ser_m_sum > 0) ? $ser_m_sum:'=(0)' ); 
							$sheet->cell('J'.$i, ($ser_f_sum > 0) ? $ser_f_sum:'=(0)' ); 
							$sheet->cell('K'.$i, ($ser_o_sum > 0) ? $ser_o_sum:'=(0)' ); 							
							$sheet->cell('L'.$i, $ser_m_sum + $ser_f_sum + $ser_o_sum );
							$sheet->cell('M'.$i, $gen_m_sum + $ser_m_sum ); 
							$sheet->cell('N'.$i, $ser_f_sum + $ser_f_sum ); 
							$sheet->cell('O'.$i, $gen_o_sum + $ser_o_sum ); 
							
							$sheet->cell('P'.$i, $gen_m_sum + $gen_f_sum + $ser_m_sum + $ser_f_sum + $gen_o_sum + $ser_o_sum);
							
							$i++;
							
							$gen_m_sum  = $gen_f_sum = $gen_o_sum = $ser_m_sum = $ser_f_sum = $ser_o_sum = $pollingregaux = 0;
						
						}

						$pollingregaux +=$pollingstation['total_no_polling_station'];
						$gen_m_sum += $pollingstation['e_gen_m'];
						$gen_f_sum += $pollingstation['e_gen_f'];						
						$gen_o_sum += $pollingstation['e_gen_o'];						
						$ser_m_sum += $pollingstation['e_ser_m'];
						$ser_f_sum += $pollingstation['e_ser_f'];
						$ser_o_sum += $pollingstation['e_ser_o'];
						
						if($stcode=='' || $stcode!=$pollingstation['st_code']){
							//$sheet->mergeCells('A'.$i:'P'.$i);
							$sheet->cell('A'.$i, $pollingstation['st_name']);
							$i++;
							$stcode = $pollingstation['st_code'];
						}

							
							$sheet->cell('A'.$i, ''); 
							$sheet->cell('B'.$i, $pollingstation['pc_no']); 
							$sheet->cell('C'.$i, $pollingstation['pc_name']); 
							$sheet->cell('D'.$i, ($pollingstation['total_no_polling_station'] > 0) ? $pollingstation['total_no_polling_station']:'=(0)' ); 
							$sheet->cell('E'.$i, ($pollingstation['e_gen_m'] > 0) ? $pollingstation['e_gen_m']:'=(0)' ); 
							$sheet->cell('F'.$i, ($pollingstation['e_gen_f'] > 0) ? $pollingstation['e_gen_f']:'=(0)' ); 
							$sheet->cell('G'.$i, ($pollingstation['e_gen_o'] > 0) ? $pollingstation['e_gen_o']:'=(0)' );
							$sheet->cell('H'.$i, $pollingstation['e_gen_m'] + $pollingstation['e_gen_f'] + $pollingstation['e_gen_o'] ); 
							$sheet->cell('I'.$i, ($pollingstation['e_ser_m'] > 0) ? $pollingstation['e_ser_m']:'=(0)' ); 
							$sheet->cell('J'.$i, ($pollingstation['e_ser_f'] > 0) ? $pollingstation['e_ser_f']:'=(0)' ); 
							$sheet->cell('K'.$i, ($pollingstation['e_ser_o'] > 0) ? $pollingstation['e_ser_o']:'=(0)' ); 							
							$sheet->cell('L'.$i, $pollingstation['e_ser_m'] + $pollingstation['e_ser_f'] + $pollingstation['e_ser_o'] );
							$sheet->cell('M'.$i, $pollingstation['e_gen_m'] + $pollingstation['e_ser_m'] ); 
							$sheet->cell('N'.$i, $pollingstation['e_gen_f'] + $pollingstation['e_ser_f'] ); 
							$sheet->cell('O'.$i, $pollingstation['e_gen_o'] + $pollingstation['e_ser_o'] ); 
							
							$sheet->cell('P'.$i, $pollingstation['e_gen_m'] + $pollingstation['e_gen_f'] + $pollingstation['e_gen_o'] + $pollingstation['e_ser_m'] + $pollingstation['e_ser_f'] + $pollingstation['e_ser_o']);
							
							$i++;
						}
					
							$i++;
					
							$sheet->cell('A'.$i, 'Grand Total'); 
							
							$sheet->cell('A'.$i, function($cells) {
								$cells->setValue('Grand Total');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
							});
							
							$sheet->cell('D'.$i, ($pollingregaux_tot > 0) ? $pollingregaux_tot:'=(0)' ); 
							$sheet->cell('E'.$i, ($gen_m_sum_tot > 0) ? $gen_m_sum_tot:'=(0)' ); 
							$sheet->cell('F'.$i, ($gen_f_sum_tot > 0) ? $gen_f_sum_tot:'=(0)' ); 
							$sheet->cell('G'.$i, ($gen_o_sum_tot > 0) ? $gen_o_sum_tot:'=(0)' ); 							
							$sheet->cell('H'.$i, $gen_m_sum_tot + $gen_f_sum_tot + $gen_o_sum_tot ); 
							$sheet->cell('I'.$i, ($ser_m_sum_tot > 0) ? $ser_m_sum_tot:'=(0)' ); 
							$sheet->cell('J'.$i, ($ser_f_sum_tot > 0) ? $ser_f_sum_tot:'=(0)' ); 
							$sheet->cell('K'.$i, ($ser_o_sum_tot > 0) ? $ser_o_sum_tot:'=(0)' ); 							
							$sheet->cell('L'.$i, $ser_m_sum_tot + $ser_f_sum_tot + $ser_o_sum_tot );
							$sheet->cell('M'.$i, $gen_m_sum_tot + $gen_m_sum_tot ); 
							$sheet->cell('N'.$i, $gen_f_sum_tot + $ser_f_sum_tot ); 
							$sheet->cell('O'.$i, $gen_o_sum_tot + $ser_o_sum_tot ); 
							
							$sheet->cell('P'.$i, $gen_m_sum_tot + $gen_f_sum_tot + $ser_m_sum_tot + $ser_f_sum_tot + $gen_o_sum_tot + $ser_o_sum_tot);
												
					}
		
				});
			})->download('xls');	

		}else{
		return view('IndexCardReports.StatisticalPC.pollingstationinformation', ['pollingstations' => $pollingstations,'sched' => $sched,'election_detail' => $election_detail, 'user_data'=> $user_data])->with('no', 1);
		//dd(DB::getQueryLog());
		}	
	}
		
	public function pcwisevoterturnout(Request $request){
		DB::enableQueryLog();
		
			$session = $request->session()->all();
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          
          $session['election_detail'] = array();
          //$session['election_detail']['st_code'] = $user->st_code;
          //$session['election_detail']['st_name'] = $user->placename;
       // echo "<pre>"; print_r($session['election_detail']); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
		 //$year = $session['election_detail']['YEAR'];
        //$scheduleID = $session['election_detail']['ScheduleID']; 
		
		
		
		$pcwisevoterturnouts	=	DB::table("m_pc")
		->select('m_state.ST_NAME','m_pc.PC_NAME','m_pc.PC_NO',
		DB::raw("SUM(ec.gen_electors_male + ec.nri_male_electors + ec.service_male_electors) AS electors_male"),
		DB::raw("SUM(ec.gen_electors_female + ec.nri_female_electors + ec.service_female_electors) AS electors_female"),
		DB::raw("SUM(ec.gen_electors_other + ec.nri_third_electors + ec.service_third_electors) AS electors_other"),
		DB::raw("SUM(ec.gen_electors_male + ec.nri_male_electors + ec.service_male_electors + ec.gen_electors_female + ec.nri_female_electors + ec.service_female_electors + ec.gen_electors_other + ec.nri_third_electors + ec.service_third_electors) AS electors_total"),
		DB::raw("(ecoi.general_male_voters + ecoi.nri_male_voters) AS voter_male"),
		DB::raw("(ecoi.general_female_voters + ecoi.nri_female_voters) AS voter_female"),
		DB::raw("(ecoi.general_other_voters + ecoi.nri_other_voters) AS voter_other"),
		DB::raw("(ecoi.nri_male_voters + ecoi.nri_female_voters + ecoi.nri_other_voters) AS voter_nri"),
		DB::raw("(ecoi.service_postal_votes_under_section_8 + ecoi.service_postal_votes_gov) AS postal_vote")
		)
		->join('m_state','m_state.st_code', '=', 'm_pc.ST_CODE')
		->leftjoin('electors_cdac as ec', function($join){ 
			$join->on('ec.st_code', '=', 'm_pc.ST_CODE')
				->on('ec.pc_no', '=', 'm_pc.PC_NO');			
		})
		->leftjoin('electors_cdac_other_information as ecoi', function($join){ 
			$join->on('ecoi.st_code', '=', 'm_pc.ST_CODE')
				->on('ecoi.pc_no', '=', 'm_pc.PC_NO');			
		})
		->join('m_election_details as med', function($join){ 
			$join->on('med.st_code', '=', 'm_pc.ST_CODE')
				->on('med.CONST_NO', '=', 'm_pc.PC_NO');			
		})
		->where(
			array(
				//'m_pc.ST_CODE' => 'S01',
				'med.CONST_TYPE' => 'PC',
				'med.election_status' => '1',
				//'med.ELECTION_ID' => '1',
			)
			)
		->groupBy('m_pc.ST_CODE','m_pc.PC_NO')
		->orderBy('m_state.ST_NAME','ASC')
		->orderBy('m_pc.PC_NO','ASC')
		->get();
		
		$arrData = array();
		foreach($pcwisevoterturnouts as $key => $data){
			
			$arrData[$data->ST_NAME][] = array(
				'PC_NAME'           		=> $data->PC_NAME,
				'PC_NO'           			=> $data->PC_NO,
				'electors_male'           	=> @$data->electors_male ? : 0,
				'electors_female'           => @$data->electors_female ? : 0,
				'electors_other'           	=> @$data->electors_other ? : 0,
				'electors_total'           	=> @$data->electors_total ? : 0,
				'voter_male'           		=> @$data->voter_male ? : 0,
				'voter_female'           	=> @$data->voter_female ? : 0,
				'voter_other'           	=> @$data->voter_other ? : 0,
				'voter_nri'           		=> @$data->voter_nri ? : 0,
				'voter_total'           	=> @$data->voter_male + $data->voter_female + $data->voter_other,
				'postal_vote'           	=> @$data->postal_vote ? : 0,
				'total_vote'           		=> @$data->voter_male + @$data->voter_female + @$data->voter_other + @$data->postal_vote
			);			
		}
		
		//echo '<pre>'; print_r($arrData);	die;
		
	    if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
		
		
		
		
		if($request->path() == "$prefix/pcwisevoterturnout_pdf"){
			//$pdf = \App::make('dompdf.wrapper');
			//$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf= PDF::loadView('IndexCardReports.StatisticalPC.pcwisevoterturnout_pdf',['pcwisevoterturnouts' => $arrData,'election_detail' => $election_detail, 'user_data'=> $user_data]);  

			  if(verifyreport(13)){
        
                  $file_name = 'PC Wise Voters Turn Out'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/13/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '13',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('PC Wise Voters Turn Out.pdf');
		}else if($request->path() == "$prefix/pcwisevoterturnout_xls"){
			
			$pcwisevoterturnouts = json_decode( json_encode($arrData), true);
				

			return Excel::create('PC Wise Voters Turn Out', function($excel) use ($pcwisevoterturnouts) {
				$excel->sheet('mySheet', function($sheet) use ($pcwisevoterturnouts)
				{
					
					$sheet->mergeCells('A1:O1');
					
					$sheet->cell('A1', function($cells) {
						$cells->setValue('13 - PC Wise Voters Turn Out');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});
					
					$sheet->mergeCells('E2:K2');
					$sheet->cell('E2', function($cells) {
						$cells->setValue(' VOTERS ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
					});
										
					$sheet->mergeCells('E3:I3');
					$sheet->mergeCells('M3:O3');
					
					$sheet->cell('E3', function($cells) {
						$cells->setValue(' EVM ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
					});
					
					$sheet->cell('J3', function($cells) {
						$cells->setValue(' POSTAL VOTES ');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
					});
					
					$sheet->cell('M3', function($cells) {
						$cells->setValue('Voters Turn Out(Excl. Postal)%');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
					});
					
					
							$sheet->cell("A4", function($cells) {
								$cells->setValue(' State Name ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("B4", function($cells) {
								$cells->setValue(' PC NO. ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("C4", function($cells) {
								$cells->setValue(' PC NAME ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
														
							$sheet->cell("D4", function($cells) {
								$cells->setValue(' ELECTORS ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("E4", function($cells) {
								$cells->setValue(' MALE ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("F4", function($cells) {
								$cells->setValue(' FEMALE ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("G4", function($cells) {
								$cells->setValue(' THIRD GENDER ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("H4", function($cells) {
								$cells->setValue(' TOTAL ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("I4", function($cells) {
								$cells->setValue(' NRI ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
					
					
							$sheet->cell("K4", function($cells) {
								$cells->setValue(' TOTAL VOTERS ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("L4", function($cells) {
								$cells->setValue(' VOTER TURN OUT (%) ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("M4", function($cells) {
								$cells->setValue(' MALE ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
					
							$sheet->cell("N4", function($cells) {
								$cells->setValue(' FEMALE ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
							
							$sheet->cell("O4", function($cells) {
								$cells->setValue(' THIRD GENDER ');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 11));
							});
					
					
					 
					$i = 5;
					 
					if ($pcwisevoterturnouts) {
						$sl_no = 1;
						
						$grand_total_electors = 0;
						$grand_total_voters_male = 0;
						$grand_total_voters_female = 0;
						$grand_total_voters_other = 0;
						$grand_total_voters_all = 0;
						$grand_total_postal_all = 0;
						$grand_total_voters_alltotal = 0;
						$grand_total_male_electors = 0;
						$grand_total_female_electors = 0;
						$grand_total_other_electors = 0;
						
						
						foreach ($pcwisevoterturnouts as $key => $row){
							
								$total_electors = 0;
								$total_voters_male = 0;
								$total_voters_female = 0;
								$total_voters_other = 0;
								$total_voters_all = 0;
								$total_postal_all = 0;
								$total_voters_alltotal = 0;
								$total_male_electors = 0;
								$total_female_electors = 0;
								$total_other_electors = 0;
							
								foreach($row as $no => $value){
									if($value['electors_total'] > 0)
									$voter_turn_all = (($value['total_vote']/$value['electors_total'])*100);
									else
									$voter_turn_all = 0;
								
								
								if($value['electors_male'] > 0)
									$voter_turn_male = (($value['voter_male']/$value['electors_male'])*100);
								else
									$voter_turn_male = 0;


								if($value['electors_female'] > 0)
									$voter_turn_female = (($value['voter_female']/$value['electors_female'])*100);
								else
									$voter_turn_female = 0;
								
								
								if($value['electors_other'] > 0)
									$voter_turn_other = (($value['voter_other']/$value['electors_other'])*100);
								else
									$voter_turn_other = 0;
								
								$total_electors += $value['electors_total'];
								$total_voters_male += $value['voter_male'];
								$total_voters_female += $value['voter_female'];
								$total_voters_other += $value['voter_other'];
								$total_voters_all += $value['voter_total'];
								$total_postal_all += $value['postal_vote'];
								$total_voters_alltotal += $value['total_vote'];
								$total_male_electors += $value['electors_male'];
								$total_female_electors += $value['electors_female'];
								$total_other_electors += $value['electors_other'];
																
								$grand_total_electors 			+= $value['electors_total'];
								$grand_total_voters_male 		+= $value['voter_male'];
								$grand_total_voters_female 		+= $value['voter_female'];
								$grand_total_voters_other 		+= $value['voter_other'];
								$grand_total_voters_all 		+= $value['voter_total'];
								$grand_total_postal_all 		+= $value['postal_vote'];
								$grand_total_voters_alltotal 	+= $value['total_vote'];
								$grand_total_male_electors 		+= $value['electors_male'];
								$grand_total_female_electors 	+= $value['electors_female'];
								$grand_total_other_electors 	+= $value['electors_other'];
									
									
									$sheet->cell('A'.$i, $key);
									$sheet->cell('B'.$i, $value['PC_NO'] );
									$sheet->cell('C'.$i, $value['PC_NAME'] ); 									 
									$sheet->cell('D'.$i, ($value['electors_total'] > 0) ? $value['electors_total']:'=(0)' ); 
									$sheet->cell('E'.$i, ($value['voter_male'] > 0) ? $value['voter_male']:'=(0)' );
									$sheet->cell('F'.$i, ($value['voter_female'] > 0) ? $value['voter_female']:'=(0)' ); 
									$sheet->cell('G'.$i, ($value['voter_other'] > 0) ? $value['voter_other']:'=(0)' ); 
									$sheet->cell('H'.$i, ($value['voter_total'] > 0) ? $value['voter_total']:'=(0)' ); 
									$sheet->cell('I'.$i, ($value['voter_nri'] > 0) ? $value['voter_nri']:'=(0)' ); 
									$sheet->cell('J'.$i, ($value['postal_vote'] > 0) ? $value['postal_vote']:'=(0)' );		$sheet->cell('K'.$i, ($value['total_vote'] > 0) ? $value['total_vote']:'=(0)' );
									$sheet->cell('L'.$i, ($voter_turn_all > 0) ? $voter_turn_all:'=(0)' ); 
									$sheet->cell('M'.$i, ($voter_turn_male > 0) ? $voter_turn_male:'=(0)' ); 
									$sheet->cell('N'.$i, ($voter_turn_female > 0) ? $voter_turn_female:'=(0)' ); 
									$sheet->cell('O'.$i, ($voter_turn_other  > 0) ? $voter_turn_other:'=(0)' ); 
									
									$sl_no++;
									$i++;
								}
								
						
						}		


	
						
					}

					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,30);



					$sheet->mergeCells("A$i:K$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
		
				});
			})->download('xls');
			
		

		}else{
		return view('IndexCardReports.StatisticalPC.pcwisevoterturnout', ['pcwisevoterturnouts' => $arrData,'election_detail' => $election_detail, 'user_data'=> $user_data])->with('no', 1);
		
		
		}
		
	}
	
	
	
	public function detailsofassemblysegmentofpc(Request $request){
		DB::enableQueryLog();
		
		$session = $request->session()->all();
		
		$user = Auth::user();
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          
          $session['election_detail'] = array();
       $election_detail = $session['election_detail'];
       $user_data = $d;
		
		
		
		$dataArray	=	DB::select("select F.ST_CODE, G.ST_NAME, F.PC_NO,F.PC_NAME,A.AC_NO,E.ac_name,(E.electors_total+E.electors_service) total_electors from m_ac A
		join electors_cdac E on A.ST_CODE=E.st_code and A.PC_NO=E.pc_no and A.AC_NO =E.ac_no
		join m_pc F  on A.ST_CODE=F.st_code and A.PC_NO=F.pc_no
		join m_election_details med  on A.ST_CODE=med.ST_CODE and A.PC_NO=med.CONST_NO
		join m_state G on A.ST_CODE=G.ST_CODE where med.CONST_TYPE = 'PC' and med.election_status='1' group by F.ST_CODE, F.PC_NO,A.AC_NO order by F.ST_CODE, F.PC_NO,A.AC_NO asc");
					
		$arrData = array();
		foreach($dataArray as $key => $data){		
			$arrData[$data->ST_CODE.' - '.$data->ST_NAME][$data->PC_NO.' - '.$data->PC_NAME][$data->AC_NO.' - '.$data->ac_name][] = array(
				'st_code'           			=> $data->ST_CODE,
				'pc_no'           				=> $data->PC_NO,
				'ac_no'           				=> $data->AC_NO,
				'ac_electors'           		=> $data->total_electors
			);			
		}
		
		//echo '<pre>'; print_r($arrData);	die;
		
	    if($user->designation == 'ROPC'){
			$prefix 	= 'ropc';
		}else if($user->designation == 'CEO'){	
			$prefix 	= 'pcceo';
		}else if($user->role_id == '27'){
			$prefix 	= 'eci-index';
		}else if($user->role_id == '7'){
			$prefix 	= 'eci';
		}
		
		
		
		
		if($request->path() == "$prefix/detailsofassemblysegmentofpc_pdf"){
			//$pdf = \App::make('dompdf.wrapper');
			//$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf = PDF::loadView('IndexCardReports.StatisticalPC.detailsofassemblysegmentofpc_pdf',['arrData' => $arrData,'election_detail' => $election_detail, 'user_data'=> $user_data]);  


                 if(verifyreport(34)){
        
                  $file_name = 'Details Of Assembly Segment Of PC'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/34/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '34',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('Details Of Assembly Segment Of PC.pdf');
		}else if($request->path() == "$prefix/detailsofassemblysegmentofpc_xls"){
			
			$arrData = array();
			foreach($dataArray as $key => $data){		
				$arrData[$data->PC_NO.' - '.$data->PC_NAME][] = array(
					'ST_CODE'           			=> $data->ST_CODE,
					'ST_NAME'           			=> $data->ST_NAME,
					'PC_NO'           				=> $data->PC_NO,
					'PC_NAME'           				=> $data->PC_NAME,
					'AC_NO'           				=> $data->AC_NO,
					'ac_name'           				=> $data->ac_name,
					'total_electors'           		=> $data->total_electors
				);			
			}
			
			
			
			
			$dataRaw = json_decode( json_encode($arrData), true);
				

			return Excel::create('Details Of Assembly Segment Of PC', function($excel) use ($dataRaw) {
				$excel->sheet('mySheet', function($sheet) use ($dataRaw)
				{
					
					$sheet->mergeCells('A1:K1');
					$sheet->cell('A1', function($cells) {
						$cells->setValue('34 - Details Of Assembly Segment Of PC');
						$cells->setAlignment('center');
                        $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
					});

					$sheet->getStyle('A3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('B3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('C3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('D3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('E3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('F3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('G3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('H3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('I3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('J3')->getAlignment()->setWrapText(true);
					$sheet->getStyle('K3')->getAlignment()->setWrapText(true);


					$sheet->cell("A3", function($cells) {
								$cells->setValue('State-UT Code & Name');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});


					$sheet->cell("B3", function($cells) {
								$cells->setValue('PC NO');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});
							
					$sheet->cell("C3", function($cells) {
								$cells->setValue('PC NAME');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});

					$sheet->cell("D3", function($cells) {
								$cells->setValue('AC NO');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});


					$sheet->cell("E3", function($cells) {
								$cells->setValue('AC NAME');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});
							
					$sheet->cell("F3", function($cells) {
								$cells->setValue('TOTAL ELECTORS');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});

					$sheet->cell("G3", function($cells) {
								$cells->setValue('TOTAL VOTES IN STATE');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});


					$sheet->cell("H3", function($cells) {
								$cells->setValue('NOTA VOTES EVM');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});

					$sheet->cell("I3", function($cells) {
								$cells->setValue('CANDIDATE NAME');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});


					$sheet->cell("J3", function($cells) {
								$cells->setValue('PARTY');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});
							
					$sheet->cell("K3", function($cells) {
								$cells->setValue('VOTES SECURED EVM');
								$cells->setAlignment('center');
								$cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
							});




					//echo '<pre>'; print_r($dataRaw); die;


					 
					$i = 4;
					 
					if ($dataRaw) {
					
						foreach($dataRaw as $key => $dataRaw2){	
						
							$ST_CODE = $ST_NAME = $PC_NO = $PC_NAME = '';
							$total_electors = $total_vote = $nota = 0;
							
						
							foreach($dataRaw2 as $key => $raw){	
						
								$datarawc = \App\models\Admin\VoterModel::get_candedates_votes_by_ac_no($raw['ST_CODE'],$raw['PC_NO'],$ac_no   = $raw['AC_NO']);
								
								$datastate = \App\models\Admin\VoterModel::get_total_valid_votes_by_st_code($raw['ST_CODE']);
								
								$datanota = \App\models\Admin\VoterModel::get_nota_votes_by_ac_no($raw['ST_CODE'],$raw['PC_NO'],$ac_no   = $raw['AC_NO']);
								
								
								foreach($datarawc as $key => $raw2){
							
									$sheet->cell('A'.$i, $raw['ST_NAME']); 
									$sheet->cell('B'.$i, $raw['PC_NO'] ); 
									$sheet->cell('C'.$i, $raw['PC_NAME'] );
									$sheet->cell('D'.$i, $raw['AC_NO'] );
									$sheet->cell('E'.$i, $raw['ac_name'] );
									$sheet->cell('F'.$i, $raw['total_electors'] );
									$sheet->cell('G'.$i, $datastate[0]->total_vote );
									$sheet->cell('H'.$i, $datanota[0]->total_vote );
									$sheet->cell('I'.$i, $raw2->candidate_name );
									$sheet->cell('J'.$i, $raw2->party_abbre );
									$sheet->cell('K'.$i, $raw2->total_vote );
									$i++;		
								}

								$ST_CODE = $raw['ST_CODE'];
								$ST_NAME = $raw['ST_NAME'];
								$PC_NO = $raw['PC_NO'];
								$PC_NAME = $raw['PC_NAME'];
								$total_electors = $raw['total_electors'];
								$total_vote = $datastate[0]->total_vote;
								$nota = $datanota[0]->total_vote;
								
							}
							
							 if($ST_CODE == 'S09' && in_array($PC_NO, [1,2,3]) ){
									
								if($ST_CODE == 'S09' && $PC_NO == 1) { 
									$AC_NO = 88;
								}else if($ST_CODE == 'S09' && $PC_NO == 2){
									$AC_NO = 89;
								}else if($ST_CODE == 'S09' && $PC_NO == 3){ 
									$AC_NO = 90;
								}

									
								$dataraw2 = \App\models\Admin\VoterModel::get_migrante_by_pc_no($ST_CODE,$PC_NO);
																								
								foreach($dataraw2 as $kk => $pdata){							
									$sheet->cell('A'.$i, $ST_NAME); 
									$sheet->cell('B'.$i, $PC_NO ); 
									$sheet->cell('C'.$i, $PC_NAME );
									$sheet->cell('D'.$i, $AC_NO );
									$sheet->cell('E'.$i, 'DelhiUdhampurJammu' );
									$sheet->cell('F'.$i, '=(0)' );
									$sheet->cell('G'.$i, $total_vote );
									$sheet->cell('H'.$i, $nota );
									$sheet->cell('I'.$i, $pdata->candidate_name );
									$sheet->cell('J'.$i, $pdata->party_abbre );
									$sheet->cell('K'.$i, ($pdata->migrate_votes > 0) ? $pdata->migrate_votes : '=(0)' );
									$i++;
								}							
							} 
													
						}
					
					
					}

					$i = $i+3;

					

					$sheet->mergeCells("A$i:B$i");
					$sheet->cell('A'.$i, function($cells) {
						$cells->setValue('Disclaimer');
						$cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
					});

					$i = $i+1;

					$sheet->getStyle('A'.$i)->getAlignment()->setWrapText(true);
					$sheet->setSize('A'.$i, 25,40);



					$sheet->mergeCells("A$i:G$i");
					$sheet->cell('A'.$i, function($cells) {
					$cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
					$cells->setFont(array('name' => 'Times New Roman','size' => 10));
					});
		
				});
			})->download('xls');
		

		}else{
		return view('IndexCardReports.StatisticalPC.detailsofassemblysegmentofpc', ['arrData' => $arrData, 'user_data'=> $user_data])->with('no', 1);
		
		
		}
		
	}	
		
}