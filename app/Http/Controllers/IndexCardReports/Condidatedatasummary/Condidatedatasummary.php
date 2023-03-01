<?php

namespace App\Http\Controllers\IndexCardReports\Condidatedatasummary;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\commonModel;
use Auth;
use Session;
use PDF;
use Excel;
class Condidatedatasummary extends Controller {

    public function __construct() {
        $this->middleware('adminsession');
        $this->middleware(['auth:admin', 'auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
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
        if (isset($ele_details)) {
            $i = 0;
            foreach ($ele_details as $ed) {
                $sched = $this->commonModel->getschedulebyid($ed->ScheduleID);
                $const_type = $ed->CONST_TYPE;
            }
        }
        $session['election_detail'] = (array) $ele_details[0];
        $session['election_detail']['st_code'] = $user->st_code;
        $session['election_detail']['st_name'] = $user->placename;
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
        $session['election_detail'] = (array) $ele_details[0];
        $session['election_detail']['st_code'] = $user->st_code;
        $session['election_detail']['st_name'] = $user->placename;
        $user_data = $d;

        
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
        if (isset($ele_details)) {
            $i = 0;
            foreach ($ele_details as $ed) {
                $sched = $this->commonModel->getschedulebyid($ed->ScheduleID);
                $const_type = $ed->CONST_TYPE;
            }
        }
        $session['election_detail'] = (array) $ele_details[0];
        $session['election_detail']['st_code'] = $user->st_code;
        $session['election_detail']['st_name'] = $user->placename;
        $user_data = $d;

        $candatapcwise = array();

//       $indexCardData = DB::table('t_pc_ic AS A')
//                       ->join(DB::raw('(SELECT MAX(created_at) AS created_at FROM t_pc_ic GROUP BY st_code, pc_no ORDER BY created_at DESC) AS B'),'A.created_at','B.created_at')
//                       ->where('st_code',$session['election_detail']['st_code'])
//                       ->get()->toArray();
        ///dd($indexCardData);

        $pcList = DB::table('m_pc')
                ->select(['PC_NO', 'PC_NAME'])
                ->where('ST_CODE', $session['election_detail']['st_code'])
                ->get();
        //->toArray();

        return view('IndexCardReports/StatisticalReports.Vol1.candidate-data-summary')->with(['pcdetails' => $pcList, 'stcode' => $session['election_detail']['st_code'], 'user_data' => $user_data]);
///Amit Rajak
    }

///condidate-data-summary-pdf
    public function getcandidateDataSummaryPDF(Request $request) {

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
        $session['election_detail'] = (array) $ele_details[0];
        $session['election_detail']['st_code'] = $user->st_code;
        $session['election_detail']['st_name'] = $user->placename;
        $user_data = $d;

$st_code= $session['election_detail']['st_code'];
        $candatapcwise = array();


        $pcdetails = DB::table('m_pc')
                ->select(['PC_NO', 'PC_NAME'])
                ->where('ST_CODE', $session['election_detail']['st_code'])
                ->get();
        //->toArray();


        $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports.Vol1.candidate-data-summary-pdf', compact('pcdetails', 'st_code'));
        return $pdf->download('candidate-data-summary.pdf');
    }

    ///condidate-data-summary-pdf
/// condidate-data-summary-xls
    public function getcandidateDataSummaryExcel(Request $request) {
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
        $session['election_detail'] = (array) $ele_details[0];
        $session['election_detail']['st_code'] = $user->st_code;
        $session['election_detail']['st_name'] = $user->placename;
        $user_data = $d;

        $candatapcwise = array();
        $st_code = $session['election_detail']['st_code'];
        $data = DB::table('m_pc')
                ->select('m_pc.PC_NO', 'm_pc.PC_TYPE', 'm_pc.PC_NAME', DB::raw("SUM(t_pc_ic.c_nom_m_t) as cnom_m_t"), DB::raw("SUM(t_pc_ic.c_nom_f_t) as cnom_f_t"), DB::raw("SUM(t_pc_ic.c_nom_o_t) as cnom_o_t"), DB::raw("SUM(t_pc_ic.c_nom_r_m) as cnom_r_m"), DB::raw("SUM(t_pc_ic.c_nom_r_f) as cnom_r_f"), DB::raw("SUM(t_pc_ic.c_nom_r_o) as cnom_r_o"), DB::raw("SUM(t_pc_ic.c_nom_w_m) as cnom_w_m"), DB::raw("SUM(t_pc_ic.c_nom_w_f) as cnom_w_f"), DB::raw("SUM(t_pc_ic.c_nom_w_o) as cnom_w_o"), DB::raw("SUM(t_pc_ic.c_nom_co_m) as c_nom_co_m"), DB::raw("SUM(t_pc_ic.c_nom_co_f) as c_nom_co_f"), DB::raw("SUM(t_pc_ic.c_nom_co_t) as c_nom_co_t"), DB::raw("SUM(t_pc_ic.c_nom_fd_m) as c_nom_fd_m"), DB::raw("SUM(t_pc_ic.c_nom_fd_f) as c_nom_fd_f"), DB::raw("SUM(t_pc_ic.c_nom_fd_t) as c_nom_fd_t"))
                ->join('t_pc_ic', 't_pc_ic.pc_no', '=', 'm_pc.PC_NO')
                ->where('m_pc.ST_CODE', $session['election_detail']['st_code'])
                ->GroupBy('m_pc.PC_TYPE', 'm_pc.ST_CODE')
                ->get();

        return Excel::create('laravelcode', function($excel) use ($data) {
                    $excel->sheet('mySheet', function($sheet) use ($data) {
                        $sheet->mergeCells('A1:R1');
                        $sheet->mergeCells('D5:F5');
                        $sheet->mergeCells('G5:I5');
                        $sheet->mergeCells('J5:L5');
                        $sheet->mergeCells('M5:O5');
                        $sheet->mergeCells('P5:R5');
                        $sheet->cells('A1', function($cells) {

                            $cells->setValue('Candidate Data Summary On Nominations , Rejections,Withdrawals And Deposits Forfeited');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('D5', function($cells) {
                            $cells->setValue('Nominations Filed');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('G5', function($cells) {
                            $cells->setValue('Nominations Rejected');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('J5', function($cells) {
                            $cells->setValue('Contesting Candidates');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('M5', function($cells) {
                            $cells->setValue('Nominations Withdrawn');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('P5', function($cells) {
                            $cells->setValue('Forfeited Contesting Candidates');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('A6:R6', function($cells) {
                            $cells->setFont(array(
                                'name' => 'Times New Roman',
                                'size' => 10,
                                'bold' => true
                            ));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });


                        $last_key = 0;
                        $last = $last_key + 10;
                        $col = 'B' . $last . ':' . 'R' . $last;

                        $sheet->cells($col, function($cells) {
                            $cells->setFont(array(
                                'name' => 'Times New Roman',
                                'size' => 12,
                                'bold' => true
                            ));

                            $cells->setAlignment('center');
                        });


                        $sheet->cell('A6', function($cell) {
                            $cell->setValue('State/UT');
                        });
                        $sheet->cell('B6', function($cell) {
                            $cell->setValue('PC NO');
                        });
                        $sheet->cell('C6', function($cell) {
                            $cell->setValue('PC NAME');
                        });

                        $sheet->cell('D6', function($cell) {
                            $cell->setValue('Men');
                        });
                        $sheet->cell('E6', function($cell) {
                            $cell->setValue('Women');
                        });
                        $sheet->cell('F6', function($cell) {
                            $cell->setValue('Total');
                        });

                        $sheet->cell('G6', function($cell) {
                            $cell->setValue('Men');
                        });
                        $sheet->cell('H6', function($cell) {
                            $cell->setValue('Women');
                        });
                        $sheet->cell('I6', function($cell) {
                            $cell->setValue('Total');
                        });

                        $sheet->cell('J6', function($cell) {
                            $cell->setValue('Men');
                        });
                        $sheet->cell('K6', function($cell) {
                            $cell->setValue('Women');
                        });
                        $sheet->cell('L6', function($cell) {
                            $cell->setValue('Total');
                        });

                        $sheet->cell('M6', function($cell) {
                            $cell->setValue('Men');
                        });
                        $sheet->cell('N6', function($cell) {
                            $cell->setValue('Women');
                        });
                        $sheet->cell('O6', function($cell) {
                            $cell->setValue('Total');
                        });

                        $sheet->cell('P6', function($cell) {
                            $cell->setValue('Men');
                        });
                        $sheet->cell('Q6', function($cell) {
                            $cell->setValue('Women');
                        });
                        $sheet->cell('R6', function($cell) {
                            $cell->setValue('Total');
                        });

                        $sheet->cell('B' . $last, function($cell) {
                            $cell->setValue('Grand Total');
                        });




                        if (!empty($data)) {

                            $Cnom_m_t = 0;
                            $Cnom_f_t = 0;
                            $Cnom_o_t = 0;

                            $Cnom_r_m = 0;
                            $Cnom_r_f = 0;
                            $Cnom_r_o = 0;

                            $Cnom_w_m = 0;
                            $Cnom_w_f = 0;
                            $Cnom_w_o = 0;

                            $Cnom_co_m = 0;
                            $Cnom_co_f = 0;
                            $Cnom_co_t = 0;

                            $Cnom_fd_m = 0;
                            $Cnom_fd_f = 0;
                            $Cnom_fd_t = 0;



                            //$last_key = end(array_keys($data));

                            foreach ($data as $key => $value) {
                                $i = $key + 7;

                                $sheet->cell('A' . $i, $value->PC_TYPE);
                                $sheet->cell('B' . $i, $value->PC_NO);
                                $sheet->cell('C' . $i, $value->PC_NAME);

                                $sheet->cell('D' . $i, $value->cnom_m_t);
                                $sheet->cell('E' . $i, $value->cnom_f_t);
                                $sheet->cell('F' . $i, $value->cnom_o_t);

                                $sheet->cell('G' . $i, $value->cnom_r_m);
                                $sheet->cell('H' . $i, $value->cnom_r_f);
                                $sheet->cell('I' . $i, $value->cnom_r_o);

                                $sheet->cell('J' . $i, $value->cnom_w_m);
                                $sheet->cell('K' . $i, $value->cnom_w_f);
                                $sheet->cell('L' . $i, $value->cnom_w_o);

                                $sheet->cell('M' . $i, $value->c_nom_co_m);
                                $sheet->cell('N' . $i, $value->c_nom_co_f);
                                $sheet->cell('O' . $i, $value->c_nom_co_t);

                                $sheet->cell('P' . $i, $value->c_nom_fd_m);
                                $sheet->cell('Q' . $i, $value->c_nom_fd_f);
                                $sheet->cell('R' . $i, $value->c_nom_fd_t);

                                $Cnom_m_t +=$value->cnom_m_t;
                                $Cnom_f_t +=$value->cnom_f_t;
                                $Cnom_o_t +=$value->cnom_o_t;

                                $Cnom_r_m +=$value->cnom_r_m;
                                $Cnom_r_f +=$value->cnom_r_f;
                                $Cnom_r_o +=$value->cnom_r_o;

                                $Cnom_w_m +=$value->cnom_w_m;
                                $Cnom_w_f +=$value->cnom_w_f;
                                $Cnom_w_o +=$value->cnom_w_o;


                                $Cnom_co_m +=$value->c_nom_co_m;
                                $Cnom_co_f +=$value->c_nom_co_f;
                                $Cnom_co_t +=$value->c_nom_co_t;

                                $Cnom_fd_m +=$value->c_nom_fd_m;
                                $Cnom_fd_f +=$value->c_nom_fd_f;
                                $Cnom_fd_t +=$value->c_nom_fd_t;

                                if ($value === end($data)) {
                                    $last_key = $value;
                                }
                            }

                            $sheet->cell('D' . $last, $Cnom_m_t);
                            $sheet->cell('E' . $last, $Cnom_f_t);
                            $sheet->cell('F' . $last, $Cnom_o_t);

                            $sheet->cell('G' . $last, $Cnom_r_m);
                            $sheet->cell('H' . $last, $Cnom_r_f);
                            $sheet->cell('I' . $last, $Cnom_r_o);

                            $sheet->cell('J' . $last, $Cnom_w_m);
                            $sheet->cell('K' . $last, $Cnom_w_f);
                            $sheet->cell('L' . $last, $Cnom_w_o);

                            $sheet->cell('M' . $last, $Cnom_co_m);
                            $sheet->cell('N' . $last, $Cnom_co_f);
                            $sheet->cell('O' . $last, $Cnom_co_t);

                            $sheet->cell('P' . $last, $Cnom_fd_m);
                            $sheet->cell('Q' . $last, $Cnom_fd_f);
                            $sheet->cell('R' . $last, $Cnom_fd_t);
                        }
                    });
                })->export();
    }

//// end condidate-data-summary-xls
}
