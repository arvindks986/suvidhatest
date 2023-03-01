<?php
namespace App\Http\Controllers\IndexCardReports\WinningCandidateAnalysisVotes;
use Auth;
use Session;
use PDF;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\commonModel;

class Winning_Candidate_Analysis_Over_Total_VotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response

     */

     public function __construct(){
        $this->middleware('adminsession');
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();


    }

    public function index(Request $request)
    {
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
        $datanew = array();

        $data = DB::table('m_pc as mpc')
                ->select('mpc.ST_CODE','B.st_name','mpc.PC_NO','mpc.ST_CODE',
                DB::raw('group_concat(winn.lead_total_vote) as lead_total_votes'),
                DB::raw('group_concat(ic.total_valid_votes) as total_valid_votes'),
                DB::raw('COUNT(mpc.PC_NO) AS TotalSeats'),
                'ic.st_code', 'ic.pc_no', 'ic.ac_no'
            )
                ->join('m_state AS B', 'B.ST_CODE', 'mpc.st_code')
                ->leftJoin('winning_leading_candidate AS winn', function($join){
                               $join->on('mpc.PC_NO','winn.pc_no')
                                   ->on('mpc.ST_CODE','winn.st_code');
                           })

                ->leftJoin('t_pc_ic AS ic', function($join){
                               $join->on('mpc.PC_NO','ic.pc_no')
                                   ->on('mpc.ST_CODE','ic.st_code');
                           })
                ->join(DB::raw("(SELECT id,MAX(created_at) AS created_at FROM t_pc_ic group by st_code, pc_no) AS icn"), 'ic.id','icn.id')
                 ->groupby ('mpc.ST_CODE')
                  ->get()->toArray();

         // dd($data);   (SELECT id,MAX(created_at) AS created_at FROM t_pc_ic GROUP BY st_code, pc_no) AS B ON A.id = B.id
        //DB::raw("(Select MAX(created_at) As created from t_pc_ic where st_code ='$stcode' GROUP by st_code, pc_no limit 1) AS C


foreach ($data as  $dataaa) {

 $datanew[$dataaa->ST_CODE]['ST_CODE'] = $dataaa->ST_CODE;
 $datanew[$dataaa->ST_CODE]['ST_NAME'] = $dataaa->st_name;
 $datanew[$dataaa->ST_CODE]['TotalSeats'] = $dataaa->TotalSeats;
            $datanew[$dataaa->ST_CODE]['lead_total_votes'] = explode(',', $dataaa->lead_total_votes)    ;
            $datanew[$dataaa->ST_CODE]['total_valid_votes'] = array_sum(explode(',', $dataaa->total_valid_votes));
             $b = array_sum(explode(',', $dataaa->total_valid_votes));
             // $datanew[$dataaa->ST_CODE]['total_valid_votesparv'] = explode(',', $dataaa->total_valid_votes);
            // dd($datanew[$dataaa->ST_CODE]['total_valid_votes']);
             if($b)
                 $datanew[$dataaa->ST_CODE]['total_valid_votes']= array_map(function($a) use($b){
                    return round(($b/$a)*100,2);
                },explode(',',$dataaa->lead_total_votes));



         }

         // $datanew = json_decode(json_encode($datanew));
         $countArray = array(
            '10' => 0,
            '20' => 0,
            '30' => 0,
            '40' => 0,
            '50' => 0,
            '60' => 0,
            '70' => 0,
            '80' => 0
         );
         $countArray80 = $countArray70 = $countArray60 = $countArray50 = $countArray40 = $countArray30 = $countArray20 = $countArray10 = 0;

         // echo "<pre>"; print_r(var_dump($countArray)); die;
         //dd($datanew);
         foreach ($datanew as $key) {

            if($key['total_valid_votes'] != 0){
                foreach($key['total_valid_votes'] as  $value) {
                   if($value<=10){
                    $countArray10 += 1;
                   }
                   if(($value>=11 && $value<=20)){
                    $countArray20 += 1;
                   }
                   if(($value>=21 && $value <=30)){
                    $countArray30 += 1;
                   }
                    if(($value>=31 && $value <=40)){
                    $countArray40 += 1;
                   }
                    if(($value>=41 && $value <=50)){
                    $countArray50 += 1;
                   }
                    if(($value>=51 && $value <=60)){
                    $countArray60 += 1;
                   }
                    if(($value>=61 && $value <=70)){
                    $countArray70 += 1;
                   }
                    if(($value>=71 && $value <=100)){
                    $countArray80 += 1;
                   }
                }
            $datanew[$key['ST_CODE']]['count'] = array(
            '10' =>  $countArray10,
            '20' =>  $countArray20,
            '30' =>  $countArray30,
            '40' =>  $countArray40,
            '50' =>  $countArray50,
            '60' =>  $countArray60,
            '70' =>  $countArray70,
            '80' =>  $countArray80
         );
            }else{
                $countArray = 0;
                $datanew[$key['ST_CODE']]['count'] = $countArray;
            }
         }
         // echo "<pre>"; print_r($datanew); die;

        // dd($datanew);

        if($request->path() == 'pcceo/Winning-candidate-analysis-over-total-valid-votes'){
        return view('IndexCardReports.Winning_Cand_Analysis_Over_Total_Valid_Votes.winning_camd_analysis_over_total_valid_votes', compact('datanew','user_data'));
        }elseif($request->path() == 'pcceo/Winning-candidate-analysis-over-total-valid-votesPDF'){
        $pdf=PDF::loadView
        ('IndexCardReports.Winning_Cand_Analysis_Over_Total_Valid_Votes.winning_camd_analysis_over_total_valid_votesPDF',[

            'datanew'=>$datanew
        ]);
        return $pdf->download('Winning_Cand_Analysis_Over_Total_Valid_VotesReport.pdf');
        }
    }

}
