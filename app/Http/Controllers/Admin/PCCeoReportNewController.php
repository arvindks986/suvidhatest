<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use \PDF;
use App\commonModel;
use App\adminmodel\CEOModel;
use App\adminmodel\PCCeoReportModel;


use App\Http\Controllers\Admin\Eci\Report\PolldayTurnoutController;
use App\Http\Controllers\Admin\Eci\Report\MissingTurnoutController;
use App\Http\Controllers\Admin\Eci\Report\PolldayCloseOfPollController;
use App\Http\Controllers\Admin\Eci\Report\PolldayEndOfPollController;


//INCLUDING CLASSES
use App\Classes\xssClean;
use App\Classes\secureCode;

use App\Exports\ExcelExport;
use App\Helpers\LogNotification;
use Maatwebsite\Excel\Facades\Excel;

//INCLUDING TRAIT FOR COMMON FUNCTIONS
use App\Http\Traits\CommonTraits;
use App\models\AC;
use App\models\Admin\EndOfPollFinaliseModel;
use App\models\Admin\PhaseModel;
use App\models\Admin\polling_station\PollingStationModel;
use App\models\Admin\turnout\TurnoutModel;
use App\models\PC;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

date_default_timezone_set('Asia/Kolkata');

class PCCeoReportNewController extends Controller
{

  //USING TRAIT FOR COMMON FUNCTIONS
  use CommonTraits;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    //date_default_timezone_set('Asia/Kolkata');    
    $this->middleware(['auth:admin', 'auth']);
    $this->middleware('ceo');
    $this->commonModel = new commonModel();
    $this->ceomodel = new CEOModel();
    $this->pcceoreportModel = new PCCeoReportModel();
    $this->PolldayTurnoutModel = new PolldayTurnoutController;
    $this->MissingTurnoutModel = new MissingTurnoutController;
    $this->CloseOfPollModel = new PolldayCloseOfPollController;
    $this->EndOfPollModel = new PolldayEndOfPollController;
  }
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */

  protected function guard()
  {
    return Auth::guard();
  }


  //PC CEO COUNTING RESULT DATA REPORT STARTS
  public function CountingStatus(Request $request)
  {
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code = $user_data->st_code;
        $PcCeoCountingSelectData = "SELECT w.st_name ,w.pc_name as wpc, p.PC_NO as pno , p.PC_NAME as npc ,
                                          IF(lead_cand_name!='null' and lead_cand_name!='' ,'STARTED','NOT STARTED') as counting ,IF(STATUS='1','DECLARED','NOT DECLARED') as res_declare FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO   WHERE p.ST_CODE='" . $st_code . "' order by p.PC_NO ";
        $CountingStatus = DB::select($PcCeoCountingSelectData);
        return view('admin.pc.ceo.ceo_counting.CountingStatus', ['user_data' => $user_data, 'CountingStatus' => $CountingStatus]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK ENDS

  }
  //PC CEO COUNTING RESULT DATA REPORT FUNCTION ENDS


  //PC CEO COUNTING RESULT DATA EXCEL REPORT STARTS
  public function CountingStatusExcel(Request $request)
  {
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;

        $user_data = $this->commonModel->getunewserbyuserid($uid);

        $cur_time    = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;

        $export_data[] = ['PC No', 'PC Name', 'Counting Status', 'Result Status'];
        $headings[] = [];

        $PcCeoCountingSelectData = "SELECT w.st_name ,w.pc_name as wpc, p.PC_NO as pno , p.PC_NAME as npc ,
              IF(lead_cand_name!='null' and lead_cand_name!='' ,'STARTED','NOT STARTED') as counting ,IF(STATUS='1','DECLARED','NOT DECLARED') as res_declare FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO   WHERE p.ST_CODE='" . $st_code . "' order by p.PC_NO ";

        $PcCeoCountingData = DB::select($PcCeoCountingSelectData);
        //dd($PcCeoCountingData);  

        $arr  = array();

        $user = Auth::user();
        foreach ($PcCeoCountingData as $CountingData) {

          $export_data[] = [
            $CountingData->pno,
            $CountingData->npc,
            $CountingData->counting,
            $CountingData->res_declare,
          ];
        }

        $name_excel = 'CountingStatus_' . trim($st_name) . '_' . $cur_time;
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');


        Excel::create('CountingStatus_' . trim($st_name) . '_' . $cur_time, function ($excel) use ($st_code) {
          $excel->sheet('Sheet1', function ($sheet) use ($st_code) {
            $PcCeoCountingSelectData = "SELECT w.st_name ,w.pc_name as wpc, p.PC_NO as pno , p.PC_NAME as npc ,
                                          IF(lead_cand_name!='null' and lead_cand_name!='' ,'STARTED','NOT STARTED') as counting ,IF(STATUS='1','DECLARED','NOT DECLARED') as res_declare FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO   WHERE p.ST_CODE='" . $st_code . "' order by p.PC_NO ";
            $PcCeoCountingData = DB::select($PcCeoCountingSelectData);
            $arr  = array();
            foreach ($PcCeoCountingData as $CountingData) {
              $data =  array(
                $CountingData->pno,
                $CountingData->npc,
                $CountingData->counting,
                $CountingData->res_declare,
              );
              array_push($arr, $data);
            }
            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(
              array(
                'PC No', 'PC Name', 'Counting Status', 'Result Status'
              )
            );
          });
        })->export('xls');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO COUNTING RESULT DATA EXCEL REPORT  TRY CATCH BLOCK ENDS
  }
  //PC CEO COUNTING RESULT DATA EXCEL REPORT FUNCTION ENDS


  //PC CEO COUNTING RESULT PDF DATA REPORT STARTS
  public function CountingStatusPdf(Request $request)
  {
    //PC CEO COUNTING RESULT DATA REPORT TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $cur_time    = Carbon::now();
        $st_code = $user_data->st_code;
        $st_name = $user_data->placename;
        $PcCeoCountingSelectData = "SELECT w.st_name ,w.pc_name as wpc, p.PC_NO as pno , p.PC_NAME as npc ,
                                          IF(lead_cand_name!='null' and lead_cand_name!='' ,'STARTED','NOT STARTED') as counting ,IF(STATUS='1','DECLARED','NOT DECLARED') as res_declare FROM winning_leading_candidate w JOIN m_pc p ON w.st_code=p.ST_CODE AND w.pc_no=p.PC_NO   WHERE p.ST_CODE='" . $st_code . "' order by p.PC_NO ";
        $CountingStatus = DB::select($PcCeoCountingSelectData);
        $pdf = PDF::loadView('admin.pc.ceo.ceo_counting.CountingStatusPdf', ['user_data' => $user_data, 'CountingStatus' => $CountingStatus]);
        return $pdf->download('CountingStatusPdf' . trim($st_name) . '_Today_' . $cur_time . '.pdf');
        return view('admin.pc.ceo.ceo_counting.CountingStatusPdf');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO COUNTING RESULT PDF DATA REPORT TRY CATCH BLOCK ENDS

  }
  //PC CEO COUNTING RESULT PDF DATA REPORT FUNCTION ENDS


  //PC CEO ELECTION SCHEDULE DATA REPORT STARTS
  public function CeoElectionSchedule(Request $request)
  {
    //PC CEO ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        //SETTING SCHEDULE LIST IN SESSION FOR FILTER STARTS
        $GetAllElectionSchedule = $this->GetAllElectionSchedule();
        Session::put('ScheduleList', $GetAllElectionSchedule);
        //SETTING SCHEDULE LIST IN SESSION FOR FILTER ENDS
        //dd($GetAllElectionSchedule);
        $ScheduleData =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                 p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                 s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                 s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                 FROM m_election_details e 
                                 RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                 RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                 WHERE e.CONST_TYPE = 'PC' AND p.ST_CODE='" . $st_code . "' 
                                 ORDER BY sid ,cno";
        $ScheduleSelectData = DB::select($ScheduleData);
        return view('admin.pc.ceo.CeoElectionSchedule', ['user_data' => $user_data, 'ScheduleSelectData' => $ScheduleSelectData]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ELECTION SCHEDULE DATA REPORT TRY CATCH BLOCK ENDS

  }
  //PC CEO ELECTION SCHEDULE DATA REPORT FUNCTION ENDS


  //PC CEO ELECTION SCHEDULE EXCEL DATA REPORT STARTS
  public function CeoElectionScheduleExcel(Request $request)
  {
    //PC CEO ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK STARTS
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;
        $ScheduleExcelData =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                     p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                     s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                     s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                     FROM m_election_details e 
                                     RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                     RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                     WHERE e.CONST_TYPE = 'PC' AND p.ST_CODE='" . $st_code . "' 
                                     ORDER BY sid ,cno";
        $ScheduleSelectExcelData = DB::select($ScheduleExcelData);
        $user = Auth::user();
        $export_data[] = ['Schedule No', 'PC Name', 'PC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'];
        $headings[] = [];
        foreach ($ScheduleSelectExcelData as $ScheduleData) {
          $export_data[] = [
            $ScheduleData->sid,
            $ScheduleData->npc,
            $ScheduleData->cno,
            GetReadableDate($ScheduleData->start_nomi_date),
            GetReadableDate($ScheduleData->last_nomi_date),
            GetReadableDate($ScheduleData->dt_nomi_scr),
            GetReadableDate($ScheduleData->last_wid_date),
            GetReadableDate($ScheduleData->poll_date),
          ];
        }
        $name_excel = 'CeoElectionScheduleExcelData_' . trim($st_name) . '_' . $cur_time;
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ELECTION SCHEDULE EXCEL DATA REPORT TRY CATCH BLOCK ENDS

  }
  //PC CEO ELECTION SCHEDULE EXCEL DATA REPORT FUNCTION ENDS


  //PC CEO ELECTION FILTER FUNCTION STARTS
  public function CeoCustomReportFilter(Request $request)
  {
    //PC CEO ELECTION FILTER TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $validator = Validator::make($request->all(), [
          'ScheduleList'   => 'nullable|numeric|regex:/^\S*$/u',
        ]);
        if ($validator->fails()) {
          return Redirect::back()
            ->withErrors($validator)
            ->withInput();
        }
        $xss = new xssClean;
        $ScheduleList        = $xss->clean_input($request['ScheduleList']);
        $uid = $user->id;
        return redirect('/pcceo/CeoCustomReportFilterGet/' . base64_encode($ScheduleList));
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ELECTION FILTER TRY CATCH ENDS HERE
  }
  //PC CEO ELECTION FILTER FUNCTION ENDS


  //PC CEO ELECTION FILTER FUNCTION STARTS
  public function CeoCustomReportFilterGet(Request $request, $ScheduleList = null)
  {
    //PC CEO ELECTION FILTER TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $ScheduleList      = base64_decode($ScheduleList);
        //CHECKING URL VARIABLES FOR VALUES STARTS
        if (!$ScheduleList) {
          $ScheduleList = "";
        } else {
          $ScheduleList = $ScheduleList;
        }
        //CHECKING URL VARIABLES FOR VALUES ENDS
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $FilterData =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                             p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                             s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                             s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                             FROM m_election_details e 
                             RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                             RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                             WHERE e.CONST_TYPE = 'PC' AND p.ST_CODE='" . $st_code . "' 
                             AND e.ScheduleID='" . $ScheduleList . "'
                             ORDER BY sid ,cno";

        $FilterSelectData = DB::select($FilterData);
        return view('admin.pc.ceo.CeoCustomReportFilterGet', ['user_data' => $user_data, 'FilterSelectData' => $FilterSelectData, 'ScheduleList' => $ScheduleList]);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ELECTION FILTER TRY CATCH ENDS HERE
  }
  //PC CEO ELECTION FILTER FUNCTION ENDS


  //PC CEO ELECTION FILTER FUNCTION STARTS
  public function CeoCustomReportFilterGetExcel(Request $request, $ScheduleList = null)
  {
    //PC CEO ELECTION FILTER TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      if (session()->has('admin_login')) {
        $ScheduleList      = base64_decode($ScheduleList);
        //CHECKING URL VARIABLES FOR VALUES STARTS
        if (!$ScheduleList) {
          $ScheduleList = "";
        } else {
          $ScheduleList = $ScheduleList;
        }
        //CHECKING URL VARIABLES FOR VALUES ENDS
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $cur_time    = Carbon::now();
        $st_code     = $user_data->st_code;
        $st_name     = $user_data->placename;
        $ScheduleList = Session::put('ScheduleList', $ScheduleList);
        Excel::create('CeoElectionScheduleFilterExcelData_' . trim($st_name) . '_' . $cur_time, function ($excel) use ($st_code) {
          $excel->sheet('Sheet1', function ($sheet) use ($st_code) {
            $ScheduleList = Session::get('ScheduleList');
            $FilterDataExcel =   "SELECT e.ScheduleID AS sid, e.CONST_NO AS cno, e.CONST_TYPE AS ctype,
                                       p.PC_NO AS pcno , p.PC_NAME AS npc , s.DT_ISS_NOM AS start_nomi_date,
                                       s.LDT_IS_NOM AS last_nomi_date, s.DT_SCR_NOM AS dt_nomi_scr, 
                                       s.LDT_WD_CAN AS last_wid_date, s.DATE_POLL AS poll_date
                                       FROM m_election_details e 
                                       RIGHT JOIN m_pc p ON e.st_code=p.ST_CODE AND e.CONST_NO=p.PC_NO
                                       RIGHT JOIN m_schedule s ON e.ScheduleID=s.SCHEDULEID  
                                       WHERE e.CONST_TYPE = 'PC' AND p.ST_CODE='" . $st_code . "' 
                                       AND e.ScheduleID='" . $ScheduleList . "'
                                       ORDER BY sid ,cno";

            $ScheduleSelectExcelData = DB::select($FilterDataExcel);
            $arr  = array();
            foreach ($ScheduleSelectExcelData as $ScheduleData) {
              $data =  array(
                $ScheduleData->sid,
                $ScheduleData->npc,
                $ScheduleData->cno,
                GetReadableDate($ScheduleData->start_nomi_date),
                GetReadableDate($ScheduleData->last_nomi_date),
                GetReadableDate($ScheduleData->dt_nomi_scr),
                GetReadableDate($ScheduleData->last_wid_date),
                GetReadableDate($ScheduleData->poll_date),
              );
              array_push($arr, $data);
            }
            $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(
              array(
                'Phase No', 'PC Name', 'PC No', 'Issue of Notification', 'Last Date For Filing Nominations', 'Scrutiny Date', 'Last Date For Withdrawl', 'Date Of Poll'
              )
            );
          });
        })->export('xls');
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ELECTION FILTER TRY CATCH ENDS HERE
  }
  //PC CEO ELECTION FILTER FUNCTION ENDS


  //PC CEO ESTIMATE POLL TRUNOUT PC WISE REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutPc(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT PC WISE REPORT TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $st_code     = $user_data->st_code;
      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);
      $PercentPc = $this->PolldayTurnoutModel->report_pc($request);
      $PercentPc['buttons']    = [];
      $PercentPc['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutPcExcel') . '?' . $PercentPc['filter'],
        'target' => false
      ];
      $PercentPc['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutPcPdf') . '?' . $PercentPc['filter'],
        'target' => false
      ];

      $PercentPc['buttons'][]  = [
        'name' => 'AC Wise Report',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
        'target' => false
      ];

      /* $PercentPc['buttons'][]  = [
          'name' => 'Missed ACs',
          'href' =>  url('pcceo/PcCeoEstimatePollTurnoutMissedAc'),
          'target' => false
        ];*/
      $PercentPc['action']         = url('pcceo/PcCeoEstimatePollTurnoutPc');
      $results = [];
      foreach ($PercentPc['results'] as $key => $result) {
        $individual_filter    = implode('&', [
          'pc_no' => 'pc_no=' . $result['pc_no'],
          'state' => 'state=' . base64_encode($result['st_code']),
          'phase' => 'phase=' . $PercentPc['phase']
        ]);
        $results[] = [
          'label'                 => $result['label'],
          'pc_no'                 => $result['pc_no'],
          'pc_name'               => $result['pc_name'],
          'filter'                => $individual_filter,
          "est_total_round1"      => $result['est_total_round1'],
          "est_total_round2"      => $result['est_total_round2'],
          "est_total_round3"      => $result['est_total_round3'],
          "est_total_round4"      => $result['est_total_round4'],
          "est_total_round5"      => $result['est_total_round5'],
          "close_of_poll"         => $result['close_of_poll'],
          "est_total"             => $result['est_total'],
          "total_record"          => $result['total_record'],
          "total_percentage"      => $result['total_percentage'],
          "st_code"               => $result['st_code'],
          "href"                  => url('pcceo/PcCeoEstimatePollTurnoutAc') . "?" . $individual_filter
        ];
      }
      $PercentPc['results'] = $results;
      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoEstimatePollTurnoutPc', $PercentPc);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT PC WISE REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT PC WISE REPORT FUNCTION ENDS



  //PC CEO ESTIMATE POLL TRUNOUT PC WISE Excel REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutPcExcel(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT PC WISE Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->PolldayTurnoutModel->export_excel_report_pc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT PC WISE Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT PC WISE Excel REPORT FUNCTION ENDS


  //PC CEO ESTIMATE POLL TRUNOUT PC WISE PDF REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutPcPdf(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT PC WISE PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->PolldayTurnoutModel->export_pdf_report_pc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT PC WISE PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT PC WISE PDF REPORT FUNCTION ENDS



  //PC CEO ESTIMATE POLL TRUNOUT AC WISE REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutAc(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT AC WISE REPORT TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $st_code     = $user_data->st_code;
      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);
      $PercentAc = $this->PolldayTurnoutModel->report_ac($request);
      //buttons
      $PercentAc['buttons']    = [];
      $PercentAc['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAcExcel') . '?' . $PercentAc['filter'],
        'target' => false
      ];
      $PercentAc['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAcPdf') . '?' . $PercentAc['filter'],
        'target' => false
      ];

      $PercentAc['buttons'][]  = [
        'name' => 'AC Wise Report',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
        'target' => false
      ];

      /*$PercentAc['buttons'][]  = [
          'name' => 'Missed ACs',
          'href' =>  url('pcceo/PcCeoEstimatePollTurnoutMissedAc'),
          'target' => false
        ];*/

      $PercentAc['action']         = url('pcceo/PcCeoEstimatePollTurnoutAc');
      $results = [];
      foreach ($PercentAc['results'] as $key => $result) {
        $individual_filter    = implode('&', [
          'pc_no' => 'pc_no=' . $result['pc_no'],
          'state' => 'state=' . base64_encode($result['st_code']),
          'phase' => 'phase=' . $PercentAc['phase']
        ]);
        $results[] = [
          'label'                 => $result['label'],
          'pc_no'                 => $result['pc_no'],
          'pc_name'               => $result['pc_name'],
          'ac_no'                 => $result['ac_no'],
          'ac_name'               => $result['ac_name'],
          'filter'                => $individual_filter,
          "est_total_round1"      => $result['est_total_round1'],
          "est_total_round2"      => $result['est_total_round2'],
          "est_total_round3"      => $result['est_total_round3'],
          "est_total_round4"      => $result['est_total_round4'],
          "est_total_round5"      => $result['est_total_round5'],
          "close_of_poll"         => $result['close_of_poll'],
          "est_total"             => $result['est_total'],
          "total_record"          => $result['total_record'],
          "total_percentage"      => $result['total_percentage'],
          "st_code"               => $result['st_code'],
          "href"                  => url('pcceo/PcCeoEstimatePollTurnoutAc') . "?" . $individual_filter
        ];
      }
      $PercentAc['results'] = $results;
      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoEstimatePollTurnoutAc', $PercentAc);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT AC WISE REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT AC WISE REPORT FUNCTION ENDS


  //PC CEO ESTIMATE POLL TRUNOUT AC WISE Excel REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutAcExcel(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT AC WISE Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->PolldayTurnoutModel->export_excel_report_ac($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT AC WISE Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT AC WISE Excel REPORT FUNCTION ENDS


  //PC CEO ESTIMATE POLL TRUNOUT AC WISE PDF REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutAcPdf(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT AC WISE PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->PolldayTurnoutModel->export_pdf_report_ac($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT AC WISE PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT AC WISE PDF REPORT FUNCTION ENDS



  //PC CEO ESTIMATE POLL TRUNOUT MISSED AC REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutMissedAc(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT MISSED AC  REPORT TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $st_code     = $user_data->st_code;
      $request->merge([
        'is_missed' => 1,
        'state' => base64_encode($st_code)
      ]);
      $MissedAc = $this->PolldayTurnoutModel->get_missed_ac($request);
      //buttons
      $MissedAc['buttons']    = [];
      $MissedAc['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutMissedAcExcel') . '?' . $MissedAc['filter'],
        'target' => false
      ];
      $MissedAc['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutMissedAcPdf') . '?' . $MissedAc['filter'],
        'target' => false
      ];

      $MissedAc['buttons'][]  = [
        'name' => 'AC Wise Report',
        'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
        'target' => false
      ];
      $results = [];
      foreach ($MissedAc['results'] as $key => $result) {
        $individual_filter    = implode('&', [
          'pc_no' => 'pc_no=' . $result['pc_no'],
          'state' => 'state=' . base64_encode($result['st_code']),
          'phase' => 'phase=' . $MissedAc['phase']
        ]);
        $results[] = [
          'label'                 => $result['label'],
          'pc_no'                 => $result['pc_no'],
          'pc_name'               => $result['pc_name'],
          'ac_no'                 => $result['ac_no'],
          'ac_name'               => $result['ac_name'],
          'filter'                => $individual_filter,
          "est_total_round1"      => $result['est_total_round1'],
          "est_total_round2"      => $result['est_total_round2'],
          "est_total_round3"      => $result['est_total_round3'],
          "est_total_round4"      => $result['est_total_round4'],
          "est_total_round5"      => $result['est_total_round5'],
          "close_of_poll"         => $result['close_of_poll'],
          "est_total"             => $result['est_total'],
          "total_record"          => $result['total_record'],
          "total_percentage"      => $result['total_percentage'],
          "st_code"               => $result['st_code'],
          "href"                  => url('pcceo/PcCeoEstimatePollTurnoutMissedAc') . "?" . $individual_filter
        ];
      }
      $MissedAc['results'] = $results;
      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoEstimatePollTurnoutMissedAc', $MissedAc);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT MISSED AC REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT MISSED AC REPORT FUNCTION ENDS


  //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC WISE Excel REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutMissedAcExcel(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC WISE Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->PolldayTurnoutModel->export_excel_report_ac_missed($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC WISE Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC WISE Excel REPORT FUNCTION ENDS



  //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC PDF REPORT  FUNCTION STARTS
  public function PcCeoEstimatePollTurnoutMissedAcPdf(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->PolldayTurnoutModel->export_pdf_report_ac_missed($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT MISSED AC PDF REPORT FUNCTION ENDS


  //PC CEO PC POLL TRUNOUT COMPARISON WISE REPORT  FUNCTION STARTS
  public function PcCeoPollComparison(Request $request)
  {
    //PC CEO PC POLL TRUNOUT COMPARISON WISE REPORT TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $st_code     = $user_data->st_code;
      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);
      $data = $this->PolldayTurnoutModel->report_pc($request);

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoPollComparisonExcel') . '?' . $data['filter'],
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoPollComparisonPdf') . '?' . $data['filter'],
        'target' => false
      ];

      /*$PercentPc['buttons'][]  = [
          'name' => 'All ACs',
          'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
          'target' => false
        ];


        $PercentPc['buttons'][]  = [
          'name' => 'Missed ACs',
          'href' =>  url('pcceo/PcCeoEstimatePollTurnoutMissedAc'),
          'target' => false
        ];*/

      $data['action']         = url('pcceo/PcCeoPollComparison');
      $results = [];
      foreach ($data['results'] as $key => $result) {
        $individual_filter    = implode('&', [
          'pc_no' => 'pc_no=' . $result['pc_no'],
          'state' => 'state=' . base64_encode($result['st_code']),
          'phase' => 'phase=' . $data['phase']
        ]);
        $results[] = [
          'label'                 => $result['label'],
          'pc_no'                 => $result['pc_no'],
          'pc_name'               => $result['pc_name'],
          'filter'                => $individual_filter,
          "est_total_round1"      => $result['est_total_round1'],
          "est_total_round2"      => $result['est_total_round2'],
          "est_total_round3"      => $result['est_total_round3'],
          "est_total_round4"      => $result['est_total_round4'],
          "est_total_round5"      => $result['est_total_round5'],
          "close_of_poll"         => $result['close_of_poll'],
          "est_total"             => $result['est_total'],
          "total_record"          => $result['total_record'],
          "total_percentage"      => $result['total_percentage'],
          "st_code"               => $result['st_code'],
          "href"                  => url('pcceo/PcCeoPollComparisonAc') . "?" . $individual_filter
        ];
      }
      $data['results'] = $results;
      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoPollComparison', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO PC POLL TRUNOUT COMPARISON REPORT TRY CATCH ENDS HERE
  }
  //PC CEO PC POLL TRUNOUT COMPARISON REPORT FUNCTION ENDS


  //PC CEOPC POLL TRUNOUT COMPARISON Excel REPORT  FUNCTION STARTS
  public function PcCeoPollComparisonExcel(Request $request)
  {
    //PC CEO PC POLL TRUNOUT COMPARISON Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->PolldayTurnoutModel->export_excel_report_ac_missed($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO PC POLL TRUNOUT COMPARISON Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO PC POLL TRUNOUT COMPARISON Excel REPORT FUNCTION ENDS



  //PC CEO PC POLL TRUNOUT COMPARISON PDF REPORT  FUNCTION STARTS
  public function PcCeoPollComparisonPdf(Request $request)
  {
    //PC CEO PC POLL TRUNOUT COMPARISON PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);

        return $this->PolldayTurnoutModel->export_pdf_report_ac_missed($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO PC POLL TRUNOUT COMPARISON PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEO PC POLL TRUNOUT COMPARISON PDF REPORT FUNCTION ENDS


  // Rocky Code Start Here


  public function PcCeoMissedModifyAc(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT MISSED AC  REPORT NEW TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $st_code     = $user_data->st_code;
      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);
      $data = $this->MissingTurnoutModel->get_enable_acs_for_update($request);
      $data['phase'] = ($request->has('phase')) ? $request->phase : 1;
      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoMissedAcExcel') . '?' . $data['filter'],
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoMissedAcPdf') . '?' . $data['filter'],
        'target' => false
      ];

      /*$data['buttons'][]  = [
          'name' => 'AC Wise Report',
          'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
          'target' => false
        ];*/

      $data['action']         = url('pcceo/PcCeoMissedModifyAc');

      $results = [];
      foreach ($data['results'] as $key => $result) {
        $results[] = [
          'label'                 => $result['label'],
          'pc_no'                 => $result['pc_no'],
          'pc_name'               => $result['pc_name'],
          'ac_no'                 => $result['ac_no'],
          'ac_name'               => $result['ac_name'],
          'name'                  => $result['name'],
          'Phone_no'              => $result['Phone_no'],
          "est_turnout_round1"        => $result['est_turnout_round1'],
          "est_turnout_round2"        => $result['est_turnout_round2'],
          "est_turnout_round3"        => $result['est_turnout_round3'],
          "est_turnout_round4"        => $result['est_turnout_round4'],
          "est_turnout_round5"        => $result['est_turnout_round5'],
          "est_turnout_round6"        => $result['est_turnout_round6'],
          "missed_status_round1"      => $result['missed_status_round1'],
          "missed_status_round2"      => $result['missed_status_round2'],
          "missed_status_round3"      => $result['missed_status_round3'],
          "missed_status_round4"      => $result['missed_status_round4'],
          "missed_status_round5"      => $result['missed_status_round5'],
          "missed_status_round6"      => $result['missed_status_round6'],
          "modification_status_round1"  => $result['modification_status_round1'],
          "modification_status_round2"  => $result['modification_status_round2'],
          "modification_status_round3"  => $result['modification_status_round3'],
          "modification_status_round4"  => $result['modification_status_round4'],
          "modification_status_round5"  => $result['modification_status_round5'],
          "modification_status_round6"  => $result['modification_status_round6'],
          'href'                  => 'javascript:void(0)',
        ];
      }
      $data['results'] = $results;
      $estimated_time = getschedulebyid($data['phase']);
      $data['estimated_time'] = $estimated_time;
      if (session()->has('admin_login')) {
        $xss = new xssClean;
        return view('admin.pc.ceo.pollday.PcCeoMissedModifyAc', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT MISSED AC NEW REPORT TRY CATCH ENDS HERE
  }


  public function enbale_modified_acs(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $state_code = $request->input('st_code');
        $phase_no = $request->input('phase_no');
        $round_no = $request->input('round_no');
        $ac_no = $request->input('ac_no');
        $data_option = $request->input('data_option');
        if ($data_option == 'on') {
          $flagval = 1;
          $message = 'enabled';
        } else {
          $message = 'disabled';
          $flagval = 0;
        }
        if (!empty($phase_no) && !empty($round_no) && !empty($ac_no)) {
          $missed_flag = 'modification_status_round' . $round_no;
          DB::table('pd_scheduledetail')->where('st_code', $state_code)->where('ac_no', $ac_no)->update([$missed_flag => $flagval]);
          Session::flash('success_mes', 'Option ' . $message . ' successfully.');
          return Redirect::back();
        } else {
          Session::flash('error_mes', 'Please try again');
          return Redirect::back();
        }
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }


  public function enableAcs(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $state_code = $user->st_code;
        $phase_no = $request->input('phase_no');
        $round_no = $request->input('round_no');
        $ac_no = $request->input('ac_no');
        $data_option = $request->input('data_option');
        if ($data_option == 'on') {
          $flagval = 1;
          $message = 'enabled';
        } else {
          $message = 'disabled';
          $flagval = 0;
        }
        if (!empty($phase_no) && !empty($round_no) && !empty($ac_no)) {
          $missed_flag = 'missed_status_round' . $round_no;
          DB::table('pd_scheduledetail')->where('st_code', $state_code)->where('ac_no', $ac_no)->update([$missed_flag => $flagval]);
          Session::flash('success_mes', 'Option ' . $message . ' successfully.');
          return Redirect::back();
        } else {
          Session::flash('error_mes', 'Please try again');
          return Redirect::back();
        }
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  //PC CEO ESTIMATE POLL TRUNOUT MISSED AC REPORT NEW  FUNCTION STARTS
  public function PcCeoMissedAc(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT MISSED AC  REPORT NEW TRY CATCH STARTS HERE
    try {
      $user = Auth::user();
      $uid = $user->id;
      $user_data = $this->commonModel->getunewserbyuserid($uid);
      $st_code     = $user_data->st_code;
      $request->merge([
        'is_excel' => 1,
        'state' => base64_encode($st_code)
      ]);
      $data = $this->MissingTurnoutModel->get_missed($request);

      //buttons
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoMissedAcExcel') . '?' . $data['filter'],
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoMissedAcPdf') . '?' . $data['filter'],
        'target' => false
      ];

      /*$data['buttons'][]  = [
          'name' => 'AC Wise Report',
          'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
          'target' => false
        ];*/

      $data['action']         = url('pcceo/PcCeoMissedAc');

      $results = [];
      foreach ($data['results'] as $key => $result) {
        $results[] = [
          'label'                 => $result['label'],
          'pc_no'                 => $result['pc_no'],
          'pc_name'               => $result['pc_name'],
          'ac_no'                 => $result['ac_no'],
          'ac_name'               => $result['ac_name'],
          'name'                  => $result['name'],
          'Phone_no'              => $result['Phone_no'],
          'href'                  => 'javascript:void(0)',
        ];
      }
      $data['results'] = $results;
      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoMissedAc', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT MISSED AC NEW REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT MISSED AC NEW REPORT FUNCTION ENDS


  //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT  FUNCTION STARTS
  public function PcCeoMissedAcExcel(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->MissingTurnoutModel->export_excel_report_ac_missed($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW WISE Excel REPORT FUNCTION ENDS

  //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW PDF REPORT  FUNCTION STARTS
  public function PcCeoMissedAcPdf(Request $request)
  {
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $st_code     = $user_data->st_code;
        $request->merge([
          'state' => base64_encode($st_code)
        ]);
        return $this->MissingTurnoutModel->export_pdf_report_ac_missed($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO ESTIMATE POLL TRUNOUT  MISSED AC NEW PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEO ESTIMATE POLL TRUNOUT MISSED AC NEW PDF REPORT FUNCTION ENDS



  //PC CEO CLOSE OF POLL REPORT  FUNCTION STARTS
  public function PcCeoCloseOfPoll(Request $request)
  {
    try {
      $request->merge([
        'is_excel' => 1,
      ]);
      $data = $this->CloseOfPollModel->pc($request);
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoCloseOfPollExcel') . '?' . $data['filter'],
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoCloseOfPollPdf') . '?' . $data['filter'],
        'target' => false
      ];

      /*$data['buttons'][]  = [
            'name' => 'AC Wise Report',
            'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
            'target' => false
          ];*/
      $data['action']         = url('pcceo/PcCeoCloseOfPoll');
      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoCloseOfPoll', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO CLOSE OF POLL  REPORT TRY CATCH ENDS HERE
  }
  //PC CEO CLOSE OF POLL   REPORT FUNCTION ENDS


  //PC CEO CLOSE OF POLL  Excel REPORT  FUNCTION STARTS
  public function PcCeoCloseOfPollExcel(Request $request)
  {
    //PC CEO CLOSE OF POLL  Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        return $this->CloseOfPollModel->export_excel_pc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO CLOSE OF POLL  Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO CLOSE OF POLL  Excel REPORT FUNCTION ENDS


  //PC CEO CLOSE OF POLL PDF REPORT  FUNCTION STARTS
  public function PcCeoCloseOfPollPdf(Request $request)
  {
    //PC CEO CLOSE OF POLL PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        return $this->CloseOfPollModel->export_pdf_pc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO CLOSE OF POLL PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEO CLOSE OF POLL PDF REPORT FUNCTION ENDS


  //PC CEO END OF POLL REPORT FUNCTION STARTS
  public function PcCeoEndOfPoll(Request $request)
  {
    try {
      $request->merge([
        'is_excel' => 1,
      ]);
      $data = $this->EndOfPollModel->report_pc($request);
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoEndOfPollExcel') . '?' . $data['filter'],
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoEndOfPollPdf') . '?' . $data['filter'],
        'target' => false
      ];

      /*$data['buttons'][]  = [
            'name' => 'AC Wise Report',
            'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
            'target' => false
          ];*/
      $results = [];
      foreach ($data['results'] as $key => $result) {
        $individual_filter    = implode('&', [
          'pc_no' => 'pc_no=' . $result['pc_no'],
          'state' => 'state=' . base64_encode($result['st_code']),
          'phase' => 'phase=' . $data['phase']
        ]);
        $results[] = [
          'label'               => $result['label'],
          'filter'              => $individual_filter,
          "pc_no"               => $result['pc_no'],
          "pc_name"             => $result['pc_name'],
          "st_code"             => $result['st_code'],
          "ac_no"               => $result['ac_no'],
          "old_total_male"      => $result['old_total_male'],
          "old_total_female"    => $result['old_total_female'],
          "old_total_other"     => $result['old_total_other'],
          "old_total"           => $result['old_total'],
          "total_male"          => $result['total_male'],
          "total_female"        => $result['total_female'],
          "total_other"         => $result['total_other'],
          "total"               => $result['total'],
          "total_percentage"    => $result['total_percentage'],
          "href"                => url('pcceo/PcCeoEndOfPollAc') . "?" . $individual_filter
        ];
      }
      $data['results'] = $results;
      $data['action']         = url('pcceo/PcCeoEndOfPoll');
      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoEndOfPoll', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO END OF POLL REPORT TRY CATCH ENDS HERE
  }
  //PC CEO END OF POLL REPORT FUNCTION ENDS


  //PC CEO END OF POLL Excel REPORT  FUNCTION STARTS
  public function PcCeoEndOfPollExcel(Request $request)
  {
    //PC CEO END OF POLL Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        return $this->EndOfPollModel->export_excel_report_pc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO  END OF POLL Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO  END OF POLL Excel REPORT FUNCTION ENDS


  //PC CEO END OF POLL  PDF REPORT  FUNCTION STARTS
  public function PcCeoEndOfPollPdf(Request $request)
  {
    //PC CEO END OF POLL  PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        return $this->EndOfPollModel->export_pdf_report_pc($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO END OF POLL  PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEOEND OF POLL  PDF REPORT FUNCTION ENDS


  //PC CEO END OF POLL AC REPORT FUNCTION STARTS
  public function PcCeoEndOfPollAc(Request $request)
  {
    try {
      $request->merge([
        'is_excel' => 1,
      ]);
      $data = $this->EndOfPollModel->report_ac($request);
      $data['buttons']    = [];
      $data['buttons'][]  = [
        'name' => 'Export Excel',
        'href' =>  url('pcceo/PcCeoEndOfPollAcExcel') . '?' . $data['filter'],
        'target' => false
      ];
      $data['buttons'][]  = [
        'name' => 'Export Pdf',
        'href' =>  url('pcceo/PcCeoEndOfPollAcPdf') . '?' . $data['filter'],
        'target' => false
      ];
      /*$data['buttons'][]  = [
            'name' => 'AC Wise Report',
            'href' =>  url('pcceo/PcCeoEstimatePollTurnoutAc'),
            'target' => false
          ];*/
      $data['action']         = url('pcceo/PcCeoEndOfPollAc');

      if (session()->has('admin_login')) {
        return view('admin.pc.ceo.pollday.PcCeoEndOfPollAc', $data);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO END OF POLL AC REPORT TRY CATCH ENDS HERE
  }
  //PC CEO END OF POLL AC REPORT FUNCTION ENDS


  //PC CEO END OF POLL AC Excel REPORT  FUNCTION STARTS
  public function PcCeoEndOfPollAcExcel(Request $request)
  {
    //PC CEO END OF POLL AC Excel REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        return $this->EndOfPollModel->export_excel_report_ac($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO  END OF POLL Excel REPORT TRY CATCH ENDS HERE
  }
  //PC CEO  END OF POLL Excel REPORT FUNCTION ENDS


  //PC CEO END OF POLL  AC PDF REPORT  FUNCTION STARTS
  public function PcCeoEndOfPollAcPdf(Request $request)
  {
    //PC CEO END OF POLL  AC PDF REPORT TRY CATCH STARTS HERE
    try {
      if (session()->has('admin_login')) {
        return $this->EndOfPollModel->export_pdf_report_ac($request);
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {

      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
    //PC CEO END OF POLL  AC PDF REPORT TRY CATCH ENDS HERE
  }
  //PC CEOEND OF POLL  PDF AC REPORT FUNCTION ENDS

  public function PcCeoPSElectoralDefinalzied(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $data = [];
        $data['user_data'] = $user_data;
        $data['phases'] = PhaseModel::get_phases();
        $data['phase'] = ($request->has('phase')) ? $request->phase : 1;
        $data['pcs'] = PC::getPcForPhase($data['phase'], $user_data->st_code);
        $data['pc'] = ($request->has('pc')) ? $request->pc : '';

        $acsForSelectedPhase = EndOfPollFinaliseModel::where('schedule_id',  $data['phase'])->where('st_code', $user_data->st_code)->pluck('ac_no');
        $data['results'] = AC::with(['pc'  => function ($query) use ($user_data) {
          $query->where('ST_CODE', $user_data->st_code);
        }])->whereIn('AC_NO', $acsForSelectedPhase)->select(["AC_NO", "AC_NAME", "PC_NO"])->where('ST_CODE', $user_data->st_code)->where(function ($q) use ($request) {
          if ($request->has('pc') && $request->input('pc') != '' && $request->input('pc') != 'all') {
            $q->where('PC_NO', $request->input('pc'));
          }
        })->orderBy('AC_NO')->get()->map(function ($item, $key) use ($request, $user_data) {
          $total_ps = PollingStationModel::getAcPollingStationCount($user_data->st_code, $item->AC_NO, $item->PC_NO);
          $total_ps_finalized = PollingStationModel::getAcPollingStationFinalizedCount($user_data->st_code, $item->AC_NO, $item->PC_NO);
          $temp = $item;
          if ($request->has('excel') && $request->input('excel') == 'download') {
            $temp = [];
            $temp['PC_NO'] = $item['PC_NO'];
            $temp['PC_NAME'] = $item['pc']['PC_NAME'];
            $temp['AC_NO'] = $item['AC_NO'];
            $temp['AC_NAME'] = $item['AC_NAME'];
            $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 'Finalized' : 'Not Yet Finalize';
          } else {
            $temp['ps_finalized'] = (($total_ps != 0 && $total_ps_finalized != 0) && $total_ps == $total_ps_finalized) ? 1 : 0;
          }
          return $temp;
        });
        $filter = [
          'st_code'       => $user_data->st_code,
          'election_id'   => $user->election_id,
          'pc_no'         => '',
        ];
        if ($data['phase'] != 1) {
          $filter['phase_no'] = $data['phase'];
        }
        $estimated_time = TurnoutModel::get_scheduletime($filter);
        $data['showDefinalizeBtn'] = (date('Y-m-d') < $estimated_time->poll_date) ? true : false;
        $data['heading_title'] = 'AC List with Polling Station Electorals Finalized Status';
        if ($request->has('excel') && $request->input('excel') == 'download') {
          $name_excel = strtolower(str_replace([',', ': ', ' '], ['_', '-', '_'], "Polling_Station_Electoral_Finalize_" . $user_data->st_code . "_Report"));
          $headings = [
            "PC No",
            "PC Name",
            "AC No",
            "AC Name",
            "Status",
          ];
          if (config("public_config.vt_log")) {
            $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
            $ErrorMessage['applicationType'] = 'WebApp';
            $ErrorMessage['Module'] = 'ENCORE';
            $ErrorMessage['TransectionType'] = 'VoterTurnout';
            $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report Imports';
            $ErrorMessage['TransectionStatus'] = 'Success';
            $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report Imports done for state " . $user_data->st_code;
            LogNotification::LogInfo($ErrorMessage);
          }
          return Excel::download(new ExcelExport($headings, $data['results']), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
        } else {
          return view('admin.pc.ceo.polling_station.PcCeoPSElectoralDefinalzied', $data);
        }
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral finalized report';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral finalized report failed ";
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }

  public function PcCeoPSElectoralDefinalziedUpdate(Request $request)
  {
    try {
      if (session()->has('admin_login')) {
        $validator = Validator::make($request->all(), [
          'ac_no'   => 'required',
          'pc_no'   => 'required',
        ]);

        if ($validator->fails()) {
          return Redirect::back()
            ->withErrors($validator)
            ->withInput();
        }
        $user = Auth::user();
        $uid = $user->id;
        $user_data = $this->commonModel->getunewserbyuserid($uid);
        $data['user_data'] = $user_data;
        $update = [
          'electors_finalize_by_ro' => 0,
          'electors_finalize_by_ro_date' => date('Y-m-d H:i:s', time())
        ];
        PollingStationModel::where('ST_CODE', $user_data->st_code)->where('AC_NO', $request->ac_no)->update($update);
        if (config("public_config.vt_log")) {
          $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
          $ErrorMessage['applicationType'] = 'WebApp';
          $ErrorMessage['Module'] = 'ENCORE';
          $ErrorMessage['TransectionType'] = 'VoterTurnout';
          $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized';
          $ErrorMessage['TransectionStatus'] = 'Success';
          $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized successfully for state " . $user_data->st_code . " AC " . $request->ac_no;
          LogNotification::LogInfo($ErrorMessage);
        }
        return redirect()->back()->with("success", "Polling Station Electorals is definalized");
      } else {
        return redirect('/admin-login');
      }
    } catch (Exception $ex) {
      if (config("public_config.vt_log")) {
        $ErrorMessage['MobNo'] = Auth::user()->officername ?? '';
        $ErrorMessage['applicationType'] = 'WebApp';
        $ErrorMessage['Module'] = 'ENCORE';
        $ErrorMessage['TransectionType'] = 'VoterTurnout';
        $ErrorMessage['TransectionAction'] = 'Polling Station AC wise electoral definalized';
        $ErrorMessage['TransectionStatus'] = 'Failed';
        $ErrorMessage['LogDescription'] = "Polling Station AC wise electoral definalized failed for AC " . $request->ac_no;
        LogNotification::LogInfo($ErrorMessage);
      }
      return Redirect('/internalerror')->with('error', 'Internal Server Error');
    }
  }
}  // end class