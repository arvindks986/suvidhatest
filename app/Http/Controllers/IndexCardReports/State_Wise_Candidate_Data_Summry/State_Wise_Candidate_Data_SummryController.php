<?php
namespace App\Http\Controllers\IndexCardReports\State_Wise_Candidate_Data_Summry;
use DB;
use Auth;
use Session;
use PDF;
use App\Http\Controllers\Controller;
use App\commonModel;
use Illuminate\Http\Request;

class State_Wise_Candidate_Data_SummryController extends Controller
{
    

  public function __construct(){
     $this->middleware('adminsession');
     $this->middleware(['auth:admin','auth']);
     $this->middleware('ceo');
     $this->commonModel = new commonModel();


  }

    public function index(Request $request){
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



    $session = $request->session()->all();
  //  dd($session);
   $datanew = array();
    $st_code = $session['admin_login_details']['st_code'];

    $st_name = $session['admin_login_details']['placename'];


    $session = $request->session()->all();
    $statewisedata = array();
    //dd($session);
    //$stcode=$session['election_detail']['st_code'];
    //$stname=$session['election_detail']['st_name'];

    $stcode = DB::table('m_state')->select('m_state.st_code')
                ->get();

     $vSelect=array('B.pc_type','A.ST_NAME',
       DB::raw("SUM(CASE WHEN B.`PC_TYPE` = 'GEN' THEN 1 ELSE 0 END) AS GENSEATS"),
       DB::raw("SUM(CASE WHEN B.`PC_TYPE` = 'SC' THEN 1 ELSE 0 END) AS SCSEATS"),
       DB::raw("SUM(CASE WHEN B.`PC_TYPE` = 'ST' THEN 1 ELSE 0 END) AS STSEATS"),

       DB::raw("DISTINCT(count(candidate_id)) as d"),
      


     );

      //DB::enableQueryLog();c_nom_a_t
     $voterquery = DB::table('m_state AS A')
                  ->select($vSelect)
                  ->JOIN ('m_pc AS B', 'A.ST_CODE', 'B.ST_CODE')
                  ->join('candidate_nomination_detail AS C', function($join){
                               $join->on('A.st_code','C.ST_CODE')
                                   ->on('B.pc_no','C.PC_NO');
                           })
               
                  ->groupby ('A.ST_CODE', 'B.PC_TYPE')
                  ->get()->toArray();
      echo "<pre>"; print_r($voterquery); die;


      //dd($voterquery);
      foreach ($voterquery as  $value) {
        # code...
        $statewisedata[$value->ST_NAME][] = array(
            'pc_type' => $value->pc_type,
            'GENSEATS'=> $value->GENSEATS,
            'SCSEATS' => $value->SCSEATS,
            'STSEATS' => $value->STSEATS,
            'CandNommale'=>$value->cmaletotal,
            'CandNomFemale'=>$value->cfemaletotal,
            'CandNomOther'=>$value->cothertotal,
            'CandNomall'=>$value->cnomalltotal,
            'cnomrmale'=>$value->cnomrmale,
            'cnomrfemale'=>$value->cnomrfemale,
            'cnomrother'=>$value->cnomrother,
            'cnomrall' => $value->cnomrall,
            'cnomwmale' =>$value->cnomwmale,
            'cnomwfemale' =>$value->cnomwfemale,
            'cnomwother'=> $value->cnomwother,
            'cnomwtotal' => $value->cnomwtotal,
            'cnomcomale' =>$value->cnomcomale,
            'cnomcofemale' =>$value->cnomcofemale,
            'cnomcother' =>$value->cnomcother,
            'cnomcototal' =>$value->cnomcototal,
            'cnomfdmale' =>$value->cnomfdmale,
            'cnomfdfemale' =>$value->cnomfdfemale,
            'cnomfdother' =>$value->cnomfdother,
            'cnomfdtotal' =>$value->cnomfdtotal

        );

        // $statewisedata[$value->ST_NAME][] =array(
        //     'totmale' => $value->totmale
        // );

      }
       // echo "<pre>"; print_r($statewisedata); die;

      if($request->path() == 'pcceo/StateWiseCandidateDataSummary'){
        return view('IndexCardReports.StateWiseCandidateDataSummry.StateWiseCandidateDataSummryPDF', compact('statewisedata','user_data'));
        }elseif($request->path() == 'pcceo/StateWiseCandidateDataSummaryPDF'){
        $pdf=PDF::loadView('IndexCardReports.StateWiseCandidateDataSummry.StateWiseCandidateDataSummryReportPDF',[
            'session'=>$session,
            'statewisedata'=>$statewisedata
        ]);
        return $pdf->download('StateWiseCandidateDataSummryReport.pdf');
        }

    }

}
