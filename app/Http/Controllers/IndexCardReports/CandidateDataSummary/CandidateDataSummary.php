<?php

namespace App\Http\Controllers\IndexCardReports\CandidateDataSummary;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\commonModel;
use Auth;
use Session;
use PDF;
use App;
use Excel;
class CandidateDataSummary extends Controller {

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
            //$this->xssClean = new xssClean;
       }

    public function indexcardreport(){
        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

        $sched = '';
        $search = '';
        $status = $this->commonModel->allstatus();

        $session['election_detail'] = array();
        $user_data = $d;

        return view('IndexCardReports/indexcard-report-listing',compact('user_data'));
    }

    public function Statisticalreport(){
       $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

        $sched = '';
        $search = '';
        $status = $this->commonModel->allstatus();
        if (isset($ele_details)) {
            $i = 0;
            foreach ($ele_details as $ed) {
                $sched = $this->commonModel->getschedulebyid($ed->ScheduleID);
                $const_type = $ed->CONST_TYPE;
            }
        }
        $session['election_detail'] = array();
        $user_data = $d;

        if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
          }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
          }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
          }else if($user->role_id == '7'){
                  $prefix     = 'eci';
        }


        return view('IndexCardReports/statistical-report-listing',compact('user_data'));
    }
	
	  public function verifyreportcheckbox(Request $request){

      //dd($request);

      if($request->is_verified != 0){

        //dd('1');

        $user = Auth::user();

        $user_name = $user->name;
        $report_no = $request->report_no;
        $date = date('Y-m-d H:i:s');

        $updateData = [
          'is_verified' => '1',
          'verifiat_date' => $date,
          'report_no' => $report_no,
      ];

       $insertData = [
          'is_verified' => '1',
          'verifiat_date' => $date,
          'report_no' => $report_no,
      ];

        $query = DB::table('statical_report_verification_details')
                ->where('report_no', $report_no)
                ->update($updateData);

        


        




        if($query){
          $msg = 'Success';
          $queryinsert = DB::table('statical_report_verification_details_logs')
                
                ->insert($insertData);
        }else{
          $msg = 'Fail';
        }

        return response()->json(array('msg'=> $msg), 200);

      }elseif($request->is_verified == 0){

          //dd('2');
      //  dd($request);
        $user = Auth::user();

        $user_name = $user->name;
        $report_no = $request->report_number;
        $date = date('Y-m-d H:i:s');

        $updateData = [
          'is_verified' => '0',
          'verifiat_date' => $date,
          'report_no' => $report_no,
      ];

      $insertData = [
          'is_verified' => '0',
          'verifiat_date' => $date,
          'report_no' => $report_no,
      ];

//dd($updateData);

         $query = DB::table('statical_report_verification_details')
                    ->where('report_no', $report_no)
                    ->update($updateData);



        if($query){
          $msg = 'Success';
          $queryinsert = DB::table('statical_report_verification_details_logs')
                
                ->insert($insertData);
        }else{
          $msg = 'Fail';
        }

        return response()->json(array('msg'=> $msg), 200);

      }



    }


     public function verifyallreport(Request $request){
      $report_no = $request->report_number;
      $date = date('Y-m-d H:i:s');
    $number = 'ST'.rand(1000,9999);
      $updateData = [
       'is_verified' => '1',
       'verifiat_date' => $date,
   'report_sequence' => $number,
    ];
  $insertData = [
       'report_no' => '777',
       'download_time' => $date,
   'file_name' => $number,
    ];
     $query = DB::table('statical_report_verification_details')
           ->where('report_no', $report_no)
           ->update($updateData);
     if($query){
    DB::table('statical_report_download_logs')->insert($insertData);
       $msg = 'Success';
     }else{
       $msg = 'Fail';
     }
     return response()->json(array('msg'=> $msg), 200);
   }



    public function getcandidateDataSummary(Request $request) {

        $user = Auth::user();
        $uid = $user->id;
        $d = $this->commonModel->getunewserbyuserid($user->id);
        $d = $this->commonModel->getunewserbyuserid($uid);
        $ele_details = $this->commonModel->election_details($d->st_code, $d->ac_no, $d->pc_no, $d->id, $d->officerlevel);

        $sched = '';
        $search = '';
        $status = $this->commonModel->allstatus();

        $session['election_detail'] = array();
        $user_data = $d;

        $candatawise = App\models\Admin\CandidateModel::get_count_by_status_category();

       // echo "<pre>"; print_r($candatawise); die;

		       $dfdata = DB::select("SELECT
           TEMP1.ST_CODE,TEMP1.ST_NAME,TEMP1.CATEGORY,TEMP1.total_pc,TEMP1.fdmale AS fdmale,
           TEMP1.fdfemale AS fdfemale,TEMP1.fdthird AS fdthird,TEMP1.FD AS fd
           FROM
           (
           SELECT TEMP.*,
           (SELECT COUNT(PC_TYPE) AS CC FROM m_pc MM join m_election_details as med on med.ST_CODE = MM.ST_CODE AND med.CONST_NO = MM.PC_NO WHERE MM.ST_CODE=TEMP.ST_CODE AND MM.PC_TYPE=TEMP.category AND med.CONST_TYPE = 'PC' AND med.election_status = '1'
           GROUP BY TEMP.ST_CODE,TEMP.category LIMIT 1) AS total_pc
           FROM (
           SELECT cp.st_code,M.PC_TYPE as category,MP.ST_NAME,C.cand_gender,cp.pc_no,
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'male'
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fdmale,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'female'
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fdfemale,
            
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and C.cand_gender = 'third'
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fdthird,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes
           FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fd
            
           FROM  counting_pcmaster cp ,m_state  MP ,m_pc M,candidate_personal_detail C, m_election_details med
           WHERE cp.candidate_id not in(select candidate_id from winning_leading_candidate as w1
           where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code)
           AND cp.party_id != 1180
           AND cp.pc_no=M.PC_NO
           AND MP.ST_CODE = cp.st_code
           AND M.ST_CODE=MP.ST_CODE
           AND C.cand_gender IN ('male','female','third')
           and C.candidate_id = cp.candidate_id
           AND M.PC_NO=cp.pc_no 
		   AND med.ST_CODE = cp.st_code AND med.CONST_NO = cp.pc_no
		   AND med.CONST_TYPE = 'PC'
		   AND med.election_status = '1'
           GROUP By MP.ST_CODE,M.PC_TYPE
           )TEMP
           )TEMP1;");
		$dfdata = json_decode( json_encode($dfdata), true);

		//echo '<pre>'; print_r($candatawise); echo '</pre>';
		//echo '<pre>'; print_r($dfdata); die;


		$arrCompare = $arrFinal = array();

		foreach($dfdata as $key=>$val){
			$arrCompare[$key] = $val['st_code'].'::'.$val['category'];
		}

		foreach($candatawise as $data){

			$checkVal = $data['st_code'].'::'.$data['category'];

			if(in_array($checkVal, $arrCompare)){

				$keyVal = array_search($checkVal, $arrCompare);
				$arrFinal[] = array_merge($data, $dfdata[$keyVal]);

			}else{
				$arrFinal[] = $data;
			}


		}

		//echo '<pre>';
		//print_r($arrFinal);

		//die;


		$dataArray = array();
		foreach($arrFinal as $raw){
				$dataArray[$raw['ST_NAME']][] = array(
				'category' 		=> $raw['category'],
				'total_pc' 		=> $raw['total_pc'],
				'nom_male' 		=> $raw['nom_male'],
				'nom_female' 	=> $raw['nom_female'],
				'nom_third' 	=> $raw['nom_third'],
				'nom_total' 	=> $raw['nom_total'],
				'rej_male' 		=> $raw['rej_male'],
				'rej_female' 	=> $raw['rej_female'],
				'rej_third' 	=> $raw['rej_third'],
				'rej_total' 	=> $raw['rej_total'],
				'with_male' 	=> $raw['with_male'],
				'with_female' 	=> $raw['with_female'],
				'with_third' 	=> $raw['with_third'],
				'with_total' 	=> $raw['with_total'],
				'cont_male' 	=> $raw['cont_male'],
				'cont_female' 	=> $raw['cont_female'],
				'cont_third' 	=> $raw['cont_third'],
				'cont_total' 	=> $raw['cont_total'],
				'fdmale' 		=> $raw['fdmale'] ? : 0,
				'fdfemale' 		=> $raw['fdfemale'] ? : 0,
				'fdthird' 		=> $raw['fdthird'] ? : 0,
				'fd' 			=> $raw['fd'] ? : 0
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

		 if($request->path() == "$prefix/statewisecandidatedatasummary_pdf"){
			$pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			//$pdf->set_paper(DEFAULT_PDF_PAPER_SIZE, 'landscape');
			$pdf->loadView('IndexCardReports/StatisticalReports.Vol1.candidate-data-summary-pdf', compact('dataArray', 'user_data'));
        if(verifyreport(6)){
        
                  $file_name = 'State Wise Candidate data Summary'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/6/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '6',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
			return $pdf->download('State Wise Candidate data Summary.pdf');
		}else if($request->path() == "$prefix/statewisecandidatedatasummary_xls"){


			$data = json_decode( json_encode($dataArray), true);

			return Excel::create('State Wise Candidate data Summary', function($excel) use ($data) {
                    $excel->sheet('mySheet', function($sheet) use ($data) {
                        $sheet->mergeCells('A1:AA1');

                        $sheet->mergeCells('D5:G5');
                        $sheet->mergeCells('I5:L5');
                        $sheet->mergeCells('N5:Q5');
                        $sheet->mergeCells('S5:V5');
                        $sheet->mergeCells('X5:AA5');


                        $sheet->cells('A1', function($cells) {

                            $cells->setValue('6 - State Wise Candidate data Summary');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                            $cells->setAlignment('center');
                        });

						$sheet->cells('A5', function($cells) {
                            $cells->setValue(' STATE ');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
						
						$sheet->cells('B5', function($cells) {
                            $cells->setValue(' CATEGORY ');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('D5', function($cells) {
                            $cells->setValue(' NOMINATIONS FILED');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
						
                        $sheet->cells('I5', function($cells) {
                            $cells->setValue(' NOMINATIONS REJECTED');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
						$sheet->cells('N5', function($cells) {
                            $cells->setValue(' NOMINATIONS WITHDRAWN');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
						
                        $sheet->cells('S5', function($cells) {
                            $cells->setValue('CONTESTING CANDIDATES');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        
                        $sheet->cells('X5', function($cells) {
                            $cells->setValue(' DEPOSIT FORFIETED');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });


						$sheet->cells('B6', function($cells) {
                            $cells->setValue(' CATEGORY ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
						
						$sheet->cells('C6', function($cells) {
                            $cells->setValue(' NO. OF SEATS ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
						
                        $sheet->cells('D6', function($cells) {
                            $cells->setValue(' MALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('E6', function($cells) {
                            $cells->setValue(' FEMALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('F6', function($cells) {
                            $cells->setValue(' THIRD GENDER ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('G6', function($cells) {
                            $cells->setValue(' TOTAL ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('I6', function($cells) {
                            $cells->setValue(' MALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('J6', function($cells) {
                            $cells->setValue(' FEMALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('K6', function($cells) {
                            $cells->setValue(' THIRD GENDER ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('L6', function($cells) {
                            $cells->setValue(' TOTAL ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('N6', function($cells) {
                            $cells->setValue(' MALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('O6', function($cells) {
                            $cells->setValue(' FEMALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('P6', function($cells) {
                            $cells->setValue(' THIRD GENDER ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('Q6', function($cells) {
                            $cells->setValue(' TOTAL ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('S6', function($cells) {
                            $cells->setValue(' MALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('T6', function($cells) {
                            $cells->setValue(' FEMALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('U6', function($cells) {
                            $cells->setValue(' THIRD GENDER ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('V6', function($cells) {
                            $cells->setValue(' TOTAL ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                       $sheet->cells('X6', function($cells) {
                            $cells->setValue(' MALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('Y6', function($cells) {
                            $cells->setValue(' FEMALE ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('Z6', function($cells) {
                            $cells->setValue(' THIRD GENDER ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('AA6', function($cells) {
                            $cells->setValue(' TOTAL ');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                            $cells->setAlignment('center');
                        });


						$i = 7;

						if (!empty($data)) {
							
							
							
						$allcnomfdtotal = $allcnomfdother = $allcnomfdfemale = $allcnomfdmale = $allcnomcototal = $allcnomcother = $allcnomcofemale = $allcnomcomale = $allcnomwtotal = $allcnomwother = $allcnomwfemale = $allcnomwmale = $allcnomrall = $allcnomrother = 
                            $allcnomrfemale = $allcnomrmale = $allCandNomall = $allCandNomOther = $allCandNomFemale = $allcandNomMale = $alltotSeat = 0; 	
							

						foreach($data as $key => $dataArr){

					
                            $cnomfdtotal = $cnomfdother = $cnomfdfemale = $cnomfdmale = $cnomcototal = $cnomcother = $cnomcofemale = $cnomcomale = $cnomwtotal = $cnomwother = $cnomwfemale = $cnomwmale = $cnomrall = $cnomrother = 
                            $cnomrfemale = $cnomrmale = $CandNomall = $CandNomOther = $CandNomFemale = $candNomMale = $totSeat = 0; 



                            //$last_key = end(array_keys($data));

                            foreach ($dataArr as $key1 => $value) {


                                $sheet->cell('A' . $i, $key);
                                $sheet->cell('B' . $i, $value['category']);
                                $sheet->cell('C' . $i, ($value['total_pc'] > 0) ? $value['total_pc']:'=(0)');

                                $sheet->cell('D' . $i, ($value['nom_male'] > 0) ? $value['nom_male']:'=(0)');
                                $sheet->cell('E' . $i, ($value['nom_female'] > 0) ? $value['nom_female']:'=(0)');
                                $sheet->cell('F' . $i, ($value['nom_third'] > 0) ? $value['nom_third']:'=(0)');
                                $sheet->cell('G' . $i, ($value['nom_total'] > 0) ? $value['nom_total']:'=(0)');

                                $sheet->cell('I' . $i, ($value['rej_male'] > 0) ? $value['rej_male']:'=(0)');
                                $sheet->cell('J' . $i, ($value['rej_female'] > 0) ? $value['rej_female']:'=(0)');
                                $sheet->cell('K' . $i, ($value['rej_third'] > 0) ? $value['rej_third']:'=(0)');
                                $sheet->cell('L' . $i, ($value['rej_total'] > 0) ? $value['rej_total']:'=(0)');

                                $sheet->cell('N' . $i, ($value['with_male'] > 0) ? $value['with_male']:'=(0)');
                                $sheet->cell('O' . $i, ($value['with_female'] > 0) ? $value['with_female']:'=(0)');
                                $sheet->cell('P' . $i, ($value['with_third'] > 0) ? $value['with_third']:'=(0)');
                                $sheet->cell('Q' . $i, ($value['with_total'] > 0) ? $value['with_total']:'=(0)');

                                $sheet->cell('S' . $i, ($value['cont_male'] > 0) ? $value['cont_male']:'=(0)');
                                $sheet->cell('T' . $i, ($value['cont_female'] > 0) ? $value['cont_female']:'=(0)');
                                $sheet->cell('U' . $i, ($value['cont_third'] > 0) ? $value['cont_third']:'=(0)');
                                $sheet->cell('V' . $i, ($value['cont_total'] > 0) ? $value['cont_total']:'=(0)');

								$sheet->cell('X' . $i, ($value['fdmale'] > 0) ? $value['fdmale']:'=(0)');
                                $sheet->cell('Y' . $i, ($value['fdfemale'] > 0) ? $value['fdfemale']:'=(0)');
                                $sheet->cell('Z' . $i, ($value['fdthird'] > 0) ? $value['fdthird']:'=(0)');
                                $sheet->cell('AA' . $i, ($value['fd'] > 0) ? $value['fd']:'=(0)');

								$i++;

                                $totSeat 			+= $value['total_pc'];
								$candNomMale 		+= $value['nom_male'];
								$CandNomFemale 		+= $value['nom_female'];
								$CandNomOther  		+= $value['nom_third'];
								$CandNomall   		+= $value['nom_total'];
								$cnomrmale 			+= $value['rej_male'];
								$cnomrfemale 		+= $value['rej_female'];
								$cnomrother  		+= $value['rej_third'];
								$cnomrall   		+= $value['rej_total'];
								$cnomwmale  		+= $value['with_male'];
								$cnomwfemale 		+= $value['with_female'];
								$cnomwother 		+= $value['with_third'];
								$cnomwtotal 		+= $value['with_total'];
								$cnomcomale 		+= $value['cont_male'];
								$cnomcofemale 		+= $value['cont_female'];
								$cnomcother 		+= $value['cont_third'];
								$cnomcototal 		+= $value['cont_total'];
								$cnomfdmale			+= $value['fdmale'];
								$cnomfdfemale 		+= $value['fdfemale'];
								$cnomfdother 		+= $value['fdthird'];
								$cnomfdtotal 		+= $value['fd'];
								
									
                            }
														
							
								
								$sheet->cell('A' . $i, $key);								
								$sheet->cell('B' . $i, 'TOTAL');								
								$sheet->cell('C' . $i, ($totSeat > 0) ? $totSeat:'=(0)');								
								$sheet->cell('D' . $i, ($candNomMale > 0) ? $candNomMale:'=(0)');
								$sheet->cell('E' . $i, ($CandNomFemale > 0) ? $CandNomFemale:'=(0)');
								$sheet->cell('F' . $i, ($CandNomOther > 0) ? $CandNomOther:'=(0)');
								$sheet->cell('G' . $i, ($CandNomall > 0) ? $CandNomall:'=(0)');
								$sheet->cell('I' . $i, ($cnomrmale > 0) ? $cnomrmale:'=(0)');
								$sheet->cell('J' . $i, ($cnomrfemale > 0) ? $cnomrfemale:'=(0)');
								$sheet->cell('K' . $i, ($cnomrother > 0) ? $cnomrother:'=(0)');
								$sheet->cell('L' . $i, ($cnomrall > 0) ? $cnomrall:'=(0)');								
								$sheet->cell('N' . $i, ($cnomwmale > 0) ? $cnomwmale:'=(0)');
								$sheet->cell('O' . $i, ($cnomwfemale > 0) ? $cnomwfemale:'=(0)');
								$sheet->cell('P' . $i, ($cnomwother > 0) ? $cnomwother:'=(0)');
								$sheet->cell('Q' . $i, ($cnomwtotal > 0) ? $cnomwtotal:'=(0)');					
								$sheet->cell('S' . $i, ($cnomcomale > 0) ? $cnomcomale:'=(0)');
								$sheet->cell('T' . $i, ($cnomcofemale > 0) ? $cnomcofemale:'=(0)');
								$sheet->cell('U' . $i, ($cnomcother > 0) ? $cnomcother:'=(0)');
								$sheet->cell('V' . $i, ($cnomcototal > 0) ? $cnomcototal:'=(0)');
							
								$sheet->cell('X' . $i, ($cnomfdmale > 0) ? $cnomfdmale:'=(0)');
								$sheet->cell('Y' . $i, ($cnomfdfemale > 0) ? $cnomfdfemale:'=(0)');
								$sheet->cell('Z' . $i, ($cnomfdother > 0) ? $cnomfdother:'=(0)');
								$sheet->cell('AA' . $i, ($cnomfdtotal > 0) ? $cnomfdtotal:'=(0)');
							
							
							
								$alltotSeat 			+= $totSeat;
								$allcandNomMale 		+= $candNomMale;
								$allCandNomFemale 		+= $CandNomFemale;
								$allCandNomOther  		+= $CandNomOther;
								$allCandNomall   		+= $CandNomall;
								$allcnomrmale 			+= $cnomrmale;
								$allcnomrfemale 		+= $cnomrfemale;
								$allcnomrother  		+= $cnomrother;
								$allcnomrall   			+= $cnomrall;
								$allcnomwmale  			+= $cnomwmale;
								$allcnomwfemale 		+= $cnomwfemale;
								$allcnomwother 			+= $cnomwother;
								$allcnomwtotal 			+= $cnomwtotal;
								$allcnomcomale 			+= $cnomcomale;
								$allcnomcofemale 		+= $cnomcofemale;
								$allcnomcother 			+= $cnomcother;
								$allcnomcototal 		+= $cnomcototal;
								$allcnomfdmale			+= $cnomfdmale;
								$allcnomfdfemale 		+= $cnomfdfemale;
								$allcnomfdother 		+= $cnomfdother;
								$allcnomfdtotal 		+= $cnomfdtotal;
										
								$i++;							
							}
								$sheet->cell('B' . $i, 'G. Total');								
								$sheet->cell('C' . $i, ($alltotSeat > 0) ? $alltotSeat:'=(0)');								
								$sheet->cell('D' . $i, ($allcandNomMale > 0) ? $allcandNomMale:'=(0)');
								$sheet->cell('E' . $i, ($allCandNomFemale > 0) ? $allCandNomFemale:'=(0)');
								$sheet->cell('F' . $i, ($allCandNomOther > 0) ? $allCandNomOther:'=(0)');
								$sheet->cell('G' . $i, ($allCandNomall > 0) ? $allCandNomall:'=(0)');
								
								$sheet->cell('I' . $i, ($allcnomrmale > 0) ? $allcnomrmale:'=(0)');
								$sheet->cell('J' . $i, ($allcnomrfemale > 0) ? $allcnomrfemale:'=(0)');
								$sheet->cell('K' . $i, ($allcnomrother > 0) ? $allcnomrother:'=(0)');
								$sheet->cell('L' . $i, ($allcnomrall > 0) ? $allcnomrall:'=(0)');								
								$sheet->cell('N' . $i, ($allcnomwmale > 0) ? $allcnomwmale:'=(0)');
								$sheet->cell('O' . $i, ($allcnomwfemale > 0) ? $allcnomwfemale:'=(0)');
								$sheet->cell('P' . $i, ($allcnomwother > 0) ? $allcnomwother:'=(0)');
								$sheet->cell('Q' . $i, ($allcnomwtotal > 0) ? $allcnomwtotal:'=(0)');
								
								$sheet->cell('S' . $i, ($allcnomcomale > 0) ? $allcnomcomale:'=(0)');
								$sheet->cell('T' . $i, ($allcnomcofemale > 0) ? $allcnomcofemale:'=(0)');
								$sheet->cell('U' . $i, ($allcnomcother > 0) ? $allcnomcother:'=(0)');
								$sheet->cell('V' . $i, ($allcnomcototal > 0) ? $allcnomcototal:'=(0)');
							
								$sheet->cell('X' . $i, ($allcnomfdmale > 0) ? $allcnomfdmale:'=(0)');
								$sheet->cell('Y' . $i, ($allcnomfdfemale > 0) ? $allcnomfdfemale:'=(0)');
								$sheet->cell('Z' . $i, ($allcnomfdother > 0) ? $allcnomfdother:'=(0)');
								$sheet->cell('AA' . $i, ($allcnomfdtotal > 0) ? $allcnomfdtotal:'=(0)');  
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



          $sheet->mergeCells("A$i:J$i");
          $sheet->cell('A'.$i, function($cells) {
          $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
          $cells->setFont(array('name' => 'Times New Roman','size' => 10));
          });
                    });
                })->export();


		}else{
        return view('IndexCardReports/StatisticalReports.Vol1.candidate-data-summary')->with(['dataArray' => $dataArray,'user_data' => $user_data]);
	}

    }


}
