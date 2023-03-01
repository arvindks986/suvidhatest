<?php

namespace App\Http\Controllers\IndexCardReports\StatisticatReport;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Illuminate\Support\Facades\Auth AS Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Config;
use \PDF;
use MPDF;
use App\commonModel;  
use App\adminmodel\CEOModel;
use App\adminmodel\MELECMaster;
use App\adminmodel\ElectiondetailsMaster;
use App\adminmodel\Electioncurrentelection;
use App\Helpers\SmsgatewayHelper;
use App\adminmodel\CEOPCModel;
use App\adminmodel\PCCeoReportModel;
use App\Classes\xssClean;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;


class StatisticatReportController extends Controller
{
    public function __construct(){
       $this->middleware('adminsession');
       $this->middleware(['auth:admin','auth']);
       $this->middleware('ceo');
       $this->commonModel = new commonModel();
    }
    public function StateWiseNoofElectors(Request $request){
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
    	/*SELECT * FROM T_PC_IC AS A
		JOIN m_state AS C ON A.st_code = C.`st_code`
		JOIN (SELECT MAX(created_at) AS created_at FROM t_pc_ic GROUP BY st_code ORDER BY created_at DESC) AS B ON A.created_at = B.created_at*/
    	$sSelect = array(
            'C.st_name',
            DB::raw('Sum(e_gen_m) AS gen_male'),
            DB::raw('Sum(e_gen_f) AS gen_female'),
            DB::raw('Sum(e_gen_o) AS gen_other'),
            DB::raw('Sum(e_gen_o+e_gen_f+e_gen_m) AS gen_total'),
            DB::raw('Sum(e_nri_m) AS nri_male'),
            DB::raw('Sum(e_nri_f) AS nri_female'),
            DB::raw('Sum(e_nri_o) AS nri_other'),
            DB::raw('Sum(e_nri_m+e_nri_f+e_nri_o) AS nri_total'),
            DB::raw('Sum(e_ser_m) AS ser_male'),
            DB::raw('Sum(e_ser_f) AS ser_female'),
            DB::raw('Sum(e_ser_o) AS ser_other'),
            DB::raw('Sum(e_ser_m+e_ser_f+e_ser_o) AS ser_total'),
            DB::raw('Sum(e_all_t_m) AS total_male'),
            DB::raw('Sum(e_all_t_f) AS total_female'),
            DB::raw('Sum(e_all_t_m+e_all_t_f) AS total_male_female'),
    	);
        $sGroup = array('A.st_code');
    	$sResult = DB::table('t_pc_ic AS A')
                    ->select($sSelect)
    				->join(DB::raw("(SELECT MAX(created_at) AS created_at FROM t_pc_ic GROUP BY st_code ORDER BY created_at DESC) AS B"),'A.created_at','B.created_at')
    				->join('m_state AS C','C.st_code','A.st_code')
    				->groupBy($sGroup)
    				->get()->toArray();
    	// echo "<pre>"; print_r($sResult); die;
        if($request->path() == 'pcceo/StateWiseNoofElectorsView'){
            return view('IndexCardReports.StatisticalReports.Vol1.stateWiseElectors',compact('sResult','user_data','sched'));
        }elseif($request->path() == 'pcceo/StateWiseNoofElectorsPDF'){
            $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.stateWiseElectorsPDF',[
            'sResult'=>$sResult
        ]);
            // return view('IndexCardReports.StatisticalReports.Vol1.stateWiseElectorsPDF',compact('sResult')); 
        return $pdf->download('stateWiseElectors.pdf');
            
        }else{
            die('No Data Found');
        }
    }
    public function constituencyPCWise(Request $request){
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
        /*SELECT * FROM m_ac AS A 
        JOIN m_pc AS B ON B.PC_NO = A.PC_NO AND B.ST_CODE = A.ST_CODE
        JOIN m_state AS C ON A.ST_CODE = C.ST_CODE
        JOIN t_pc_ic AS D ON A.ST_CODE = D.st_code AND A.PC_NO = D.pc_no
        GROUP BY C.ST_CODE, B.PC_NO*/
        $sSelect = array(
            'C.ST_NAME',
            'B.PC_NAME',
            'B.PC_NO',
            DB::raw('COUNT(A.AC_NO) AS totalAC'),
            'D.total_no_polling_station',
            'D.e_all_t',
            DB::raw('ROUND((D.e_all_t/D.total_no_polling_station)) AS AvgElectors'),
            'D.c_nom_a_t',
            'D.c_nom_co_t',
            'D.vt_all_t',
            DB::raw('ROUND(((D.vt_all_t/D.e_all_t)*100),2) AS votersTurnOut')
        );
        $sGroup = array(
            'C.ST_CODE',
            'B.PC_NO'
        );
        $sResult = DB::table('m_ac AS A')
                        ->select($sSelect)
                        ->join('m_pc AS B', function($sJoin){
                            $sJoin->on('B.PC_NO','A.PC_NO')
                                ->on('B.ST_CODE','A.ST_CODE');
                        })
                        ->join('m_state AS C','A.ST_CODE','C.ST_CODE')
                        ->join('t_pc_ic AS D',function($mJoin){
                            $mJoin->on('A.ST_CODE','D.st_code')
                                ->on('A.PC_NO','D.pc_no');
                        })
                        ->groupBy($sGroup)
                        ->get()->toArray();
        // echo "<pre>"; print_r($sResult);       
        foreach ($sResult as $value) {
            $aaData[$value->ST_NAME][$value->PC_NAME] = array(
                'PC_NO' => $value->PC_NO,
                'totalAC' => $value->totalAC,
                'total_no_polling_station' => $value->total_no_polling_station,
                'e_all_t' => $value->e_all_t,
                'AvgElectors' => $value->AvgElectors,
                'c_nom_a_t' => $value->c_nom_a_t,
                'c_nom_co_t' => $value->c_nom_co_t,
                'vt_all_t' => $value->vt_all_t,
                'votersTurnOut' => $value->votersTurnOut
            );
        }
        $aaData = json_decode(json_encode($aaData));
        if($request->path() == 'pcceo/constituencyPCWiseView'){
            // die('deee');
            return view('IndexCardReports.StatisticalReports.Vol1.constituencyPCWise',compact('aaData','user_data','sched'));
        }elseif($request->path() == 'pcceo/constituencyPCWisePDF'){
            $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.constituencyPCWisePDF',[
            'aaData'=>$aaData
        ]);
            // return view('IndexCardReports.StatisticalReports.Vol1.constituencyPCWisePDF',compact('sResult')); 
        return $pdf->download('constituencyPCWise.pdf');
            
        }else{
            die('No Data Found');
        }
    }
}
