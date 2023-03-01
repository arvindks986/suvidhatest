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

		$dfdata = DB::select("SELECT
           TEMP1.ST_CODE,TEMP1.ST_NAME,TEMP1.CATEGORY,TEMP1.total_pc,TEMP1.fdmale AS fdmale,
           TEMP1.fdfemale AS fdfemale,TEMP1.fdthird AS fdthird,TEMP1.FD AS fd
           FROM
           (
           SELECT TEMP.*,
           (SELECT COUNT(PC_TYPE) AS CC FROM m_pc MM  WHERE MM.ST_CODE=TEMP.ST_CODE AND MM.PC_TYPE=TEMP.category and (MM.st_code,MM.pc_no) <> ('S22','8')
           GROUP BY TEMP.ST_CODE,TEMP.category LIMIT 1) AS total_pc
           FROM (
           SELECT cp.st_code,M.PC_TYPE as category,MP.ST_NAME,C.cand_gender,cp.pc_no,
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'male' and (cp1.st_code,cp1.pc_no) <> ('S22','8')
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fdmale,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'female' and (cp1.st_code,cp1.pc_no) <> ('S22','8')
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fdfemale,
            
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'third' and (cp1.st_code,cp1.pc_no) <> ('S22','8')
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fdthird,
            
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.total_vote) as pctotalvotes
           FROM counting_pcmaster as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and (cp1.st_code,cp1.pc_no) <> ('S22','8')
           GROUP BY cp1.pc_no ),4) < .1666 THEN 1 ELSE 0 END) as fd
            
           FROM  counting_pcmaster cp ,m_state  MP ,m_pc M,candidate_personal_detail  C
           WHERE cp.candidate_id not in(select candidate_id from winning_leading_candidate as w1
           where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code and (w1.st_code,w1.pc_no) <> ('S22','8'))
           AND cp.candidate_id != 4319
           AND cp.pc_no=M.PC_NO
           AND MP.ST_CODE = cp.st_code
           AND M.ST_CODE=MP.ST_CODE
           AND C.cand_gender IN ('male','female','third')
           and C.candidate_id = cp.candidate_id
           AND M.PC_NO=cp.pc_no and (cp.st_code,cp.pc_no) <> ('S22','8')
           GROUP By MP.ST_CODE,M.PC_TYPE
           )TEMP
           )TEMP1;");
		$dfdata = json_decode( json_encode($dfdata), true);

		//echo '<pre>'; print_r($candatawise); echo '</pre>';
		//echo '<pre>'; print_r($dfdata); die;


		$arrCompare = array();

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
			$pdf = PDF::loadView('IndexCardReports/StatisticalReports.Vol1.candidate-data-summary-pdf', compact('dataArray', 'user_data'));
			return $pdf->download('State Wise Candidate data Summary.pdf');
		}else if($request->path() == "$prefix/statewisecandidatedatasummary_xls"){


			$data = json_decode( json_encode($dataArray), true);

			return Excel::create('State Wise Candidate data Summary', function($excel) use ($data) {
                    $excel->sheet('mySheet', function($sheet) use ($data) {
                        $sheet->mergeCells('A1:V1');
                        $sheet->mergeCells('A5:B5');
                        $sheet->mergeCells('C5:F5');
                        $sheet->mergeCells('G5:J5');
                        $sheet->mergeCells('K5:N5');
                        $sheet->mergeCells('O5:R5');
                        $sheet->mergeCells('S5:V5');


                        $sheet->cells('A1', function($cells) {

                            $cells->setValue('6 - State Wise Candidate data Summary');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('C5', function($cells) {
                            $cells->setValue('Nominations Filed');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('G5', function($cells) {
                            $cells->setValue('Nominations Rejected');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('K5', function($cells) {
                            $cells->setValue('Contesting Candidates');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('O5', function($cells) {
                            $cells->setValue('Nominations Withdrawn');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('S5', function($cells) {
                            $cells->setValue('Deposit Forfeited');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
                            $cells->setAlignment('center');
                        });


						            $sheet->mergeCells('A6:B6');

                        $sheet->cells('A6', function($cells) {
                            $cells->setValue('State/UT');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('C6', function($cells) {
                            $cells->setValue('Male');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('D6', function($cells) {
                            $cells->setValue('Women');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('E6', function($cells) {
                            $cells->setValue('Others');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('F6', function($cells) {
                            $cells->setValue('Total');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('G6', function($cells) {
                            $cells->setValue('Male');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('H6', function($cells) {
                            $cells->setValue('Women');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('I6', function($cells) {
                            $cells->setValue('Others');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('J6', function($cells) {
                            $cells->setValue('Total');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('K6', function($cells) {
                            $cells->setValue('Male');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('L6', function($cells) {
                            $cells->setValue('Women');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('M6', function($cells) {
                            $cells->setValue('Others');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('N6', function($cells) {
                            $cells->setValue('Total');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('O6', function($cells) {
                            $cells->setValue('Male');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('P6', function($cells) {
                            $cells->setValue('Women');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('Q6', function($cells) {
                            $cells->setValue('Others');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('R6', function($cells) {
                            $cells->setValue('Total');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                       $sheet->cells('S6', function($cells) {
                            $cells->setValue('Male');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('T6', function($cells) {
                            $cells->setValue('Women');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('U6', function($cells) {
                            $cells->setValue('Others');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('V6', function($cells) {
                            $cells->setValue('Total');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setAlignment('center');
                        });



						/* $sheet->mergeCells('A7:V7');
                        $sheet->cells('A7', function($cells) {
							$cells->setValue('Constituency Type/No. Of Seats');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                        }); */
						$i = 7;

						if (!empty($data)) {
							
							
							
						$allcnomfdtotal = $allcnomfdother = $allcnomfdfemale = $allcnomfdmale = $allcnomcototal = $allcnomcother = $allcnomcofemale = $allcnomcomale = $allcnomwtotal = $allcnomwother = $allcnomwfemale = $allcnomwmale = $allcnomrall = $allcnomrother = 
                            $allcnomrfemale = $allcnomrmale = $allCandNomall = $allCandNomOther = $allCandNomFemale = $allcandNomMale = $alltotSeat = 0; 	
							

						foreach($data as $key => $dataArr){

					$sheet->mergeCells("A$i:V$i");
					$i++;

						$sheet->cells("A$i", function($cells) use ($key){
							$cells->setValue($key);
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                        });

					$i++;
					
					
					$sheet->cells("A$i", function($cells){
							$cells->setValue('Constituency Type');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                        });
					$sheet->cells("B$i", function($cells){
							$cells->setValue('No. of Seats');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                        });

					$i++;
					
					
					
                            $cnomfdtotal = $cnomfdother = $cnomfdfemale = $cnomfdmale = $cnomcototal = $cnomcother = $cnomcofemale = $cnomcomale = $cnomwtotal = $cnomwother = $cnomwfemale = $cnomwmale = $cnomrall = $cnomrother = 
                            $cnomrfemale = $cnomrmale = $CandNomall = $CandNomOther = $CandNomFemale = $candNomMale = $totSeat = 0; 



                            //$last_key = end(array_keys($data));

                            foreach ($dataArr as $key1 => $value) {


                                $sheet->cell('A' . $i, $value['category']);
                                $sheet->cell('B' . $i, ($value['total_pc'] > 0) ? $value['total_pc']:'=(0)');

                                $sheet->cell('C' . $i, ($value['nom_male'] > 0) ? $value['nom_male']:'=(0)');
                                $sheet->cell('D' . $i, ($value['nom_female'] > 0) ? $value['nom_female']:'=(0)');
                                $sheet->cell('E' . $i, ($value['nom_third'] > 0) ? $value['nom_third']:'=(0)');
                                $sheet->cell('F' . $i, ($value['nom_total'] > 0) ? $value['nom_total']:'=(0)');

                                $sheet->cell('G' . $i, ($value['rej_male'] > 0) ? $value['rej_male']:'=(0)');
                                $sheet->cell('H' . $i, ($value['rej_female'] > 0) ? $value['rej_female']:'=(0)');
                                $sheet->cell('I' . $i, ($value['rej_third'] > 0) ? $value['rej_third']:'=(0)');
                                $sheet->cell('J' . $i, ($value['rej_total'] > 0) ? $value['rej_total']:'=(0)');

                                $sheet->cell('K' . $i, ($value['with_male'] > 0) ? $value['with_male']:'=(0)');
                                $sheet->cell('L' . $i, ($value['with_female'] > 0) ? $value['with_female']:'=(0)');
                                $sheet->cell('M' . $i, ($value['with_third'] > 0) ? $value['with_third']:'=(0)');
                                $sheet->cell('N' . $i, ($value['with_total'] > 0) ? $value['with_total']:'=(0)');

                                $sheet->cell('O' . $i, ($value['cont_male'] > 0) ? $value['cont_male']:'=(0)');
                                $sheet->cell('P' . $i, ($value['cont_female'] > 0) ? $value['cont_female']:'=(0)');
                                $sheet->cell('Q' . $i, ($value['cont_third'] > 0) ? $value['cont_third']:'=(0)');
                                $sheet->cell('R' . $i, ($value['cont_total'] > 0) ? $value['cont_total']:'=(0)');

								$sheet->cell('S' . $i, ($value['fdmale'] > 0) ? $value['fdmale']:'=(0)');
                                $sheet->cell('T' . $i, ($value['fdfemale'] > 0) ? $value['fdfemale']:'=(0)');
                                $sheet->cell('U' . $i, ($value['fdthird'] > 0) ? $value['fdthird']:'=(0)');
                                $sheet->cell('V' . $i, ($value['fd'] > 0) ? $value['fd']:'=(0)');

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
														
							
								$sheet->cells("A$i", function($cells){
									$cells->setValue('Total');
									$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
								});
								$sheet->cell('B' . $i, ($totSeat > 0) ? $totSeat:'=(0)');								
								$sheet->cell('C' . $i, ($candNomMale > 0) ? $candNomMale:'=(0)');
								$sheet->cell('D' . $i, ($CandNomFemale > 0) ? $CandNomFemale:'=(0)');
								$sheet->cell('E' . $i, ($CandNomOther > 0) ? $CandNomOther:'=(0)');
								$sheet->cell('F' . $i, ($CandNomall > 0) ? $CandNomall:'=(0)');
								$sheet->cell('G' . $i, ($cnomrmale > 0) ? $cnomrmale:'=(0)');
								$sheet->cell('H' . $i, ($cnomrfemale > 0) ? $cnomrfemale:'=(0)');
								$sheet->cell('I' . $i, ($cnomrother > 0) ? $cnomrother:'=(0)');
								$sheet->cell('J' . $i, ($cnomrall > 0) ? $cnomrall:'=(0)');								
								$sheet->cell('K' . $i, ($cnomwmale > 0) ? $cnomwmale:'=(0)');
								$sheet->cell('L' . $i, ($cnomwfemale > 0) ? $cnomwfemale:'=(0)');
								$sheet->cell('M' . $i, ($cnomwother > 0) ? $cnomwother:'=(0)');
								$sheet->cell('N' . $i, ($cnomwtotal > 0) ? $cnomwtotal:'=(0)');					
								$sheet->cell('O' . $i, ($cnomcomale > 0) ? $cnomcomale:'=(0)');
								$sheet->cell('P' . $i, ($cnomcofemale > 0) ? $cnomcofemale:'=(0)');
								$sheet->cell('Q' . $i, ($cnomcother > 0) ? $cnomcother:'=(0)');
								$sheet->cell('R' . $i, ($cnomcototal > 0) ? $cnomcototal:'=(0)');
							
								$sheet->cell('S' . $i, ($cnomfdmale > 0) ? $cnomfdmale:'=(0)');
								$sheet->cell('T' . $i, ($cnomfdfemale > 0) ? $cnomfdfemale:'=(0)');
								$sheet->cell('U' . $i, ($cnomfdother > 0) ? $cnomfdother:'=(0)');
								$sheet->cell('V' . $i, ($cnomfdtotal > 0) ? $cnomfdtotal:'=(0)');
							
							
							
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
							
							
							
							
								$i += 2;
							
						}
							$i += 2;

								$sheet->cells("A$i", function($cells){
									$cells->setValue('Grand Total');
									$cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
								});
								$sheet->cell('B' . $i, ($alltotSeat > 0) ? $alltotSeat:'=(0)');								
								$sheet->cell('C' . $i, ($allcandNomMale > 0) ? $allcandNomMale:'=(0)');
								$sheet->cell('D' . $i, ($allCandNomFemale > 0) ? $allCandNomFemale:'=(0)');
								$sheet->cell('E' . $i, ($allCandNomOther > 0) ? $allCandNomOther:'=(0)');
								$sheet->cell('F' . $i, ($allCandNomall > 0) ? $allCandNomall:'=(0)');
								$sheet->cell('G' . $i, ($allcnomrmale > 0) ? $allcnomrmale:'=(0)');
								$sheet->cell('H' . $i, ($allcnomrfemale > 0) ? $allcnomrfemale:'=(0)');
								$sheet->cell('I' . $i, ($allcnomrother > 0) ? $allcnomrother:'=(0)');
								$sheet->cell('J' . $i, ($allcnomrall > 0) ? $allcnomrall:'=(0)');								
								$sheet->cell('K' . $i, ($allcnomwmale > 0) ? $allcnomwmale:'=(0)');
								$sheet->cell('L' . $i, ($allcnomwfemale > 0) ? $allcnomwfemale:'=(0)');
								$sheet->cell('M' . $i, ($allcnomwother > 0) ? $allcnomwother:'=(0)');
								$sheet->cell('N' . $i, ($allcnomwtotal > 0) ? $allcnomwtotal:'=(0)');					
								$sheet->cell('O' . $i, ($allcnomcomale > 0) ? $allcnomcomale:'=(0)');
								$sheet->cell('P' . $i, ($allcnomcofemale > 0) ? $allcnomcofemale:'=(0)');
								$sheet->cell('Q' . $i, ($allcnomcother > 0) ? $allcnomcother:'=(0)');
								$sheet->cell('R' . $i, ($allcnomcototal > 0) ? $allcnomcototal:'=(0)');
							
								$sheet->cell('S' . $i, ($allcnomfdmale > 0) ? $allcnomfdmale:'=(0)');
								$sheet->cell('T' . $i, ($allcnomfdfemale > 0) ? $allcnomfdfemale:'=(0)');
								$sheet->cell('U' . $i, ($allcnomfdother > 0) ? $allcnomfdother:'=(0)');
								$sheet->cell('V' . $i, ($allcnomfdtotal > 0) ? $allcnomfdtotal:'=(0)');



                           
                        }
                    });
                })->export();


		}else{
        return view('IndexCardReports/StatisticalReports.Vol1.candidate-data-summary')->with(['dataArray' => $dataArray,'user_data' => $user_data]);
	}

    }


}
