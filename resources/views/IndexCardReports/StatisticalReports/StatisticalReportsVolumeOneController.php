<?php

namespace App\Http\Controllers\IndexCardReports\StatisticalReports;

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
use Excel;
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



		ini_set("memory_limit","850M");
       set_time_limit('180');
       ini_set("pcre.backtrack_limit", "10000000");

class StatisticalReportsVolumeOneController extends Controller
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
            //$this->xssClean = new xssClean;
       }
    public function candidateDataNominationSummary(Request $request){
    //CANDIDATE DATA SUMMARY ON NOMINATIONS
    	$session = $request->session()->all();
    	/*SELECT B.cand_category,D.status,B.cand_gender, COUNT(A.nom_id) AS gend_total, A.pc_no, E.PC_NAME
		FROM candidate_nomination_detail AS A 
		INNER JOIN Candidate_personal_detail AS B ON A.candidate_id = B.candidate_id 
		INNER JOIN counting_pcmaster AS C ON A.candidate_id = C.candidate_id 
		INNER JOIN m_status AS D ON D.id = A.application_status
		INNER JOIN M_PC AS E ON A.pc_no = E.PC_NO
		WHERE (A.st_code = 'S25') 
		GROUP BY A.pc_no,B.cand_category,B.cand_gender, A.application_status*/
		$sSelect = array(
			'A.pc_no',
			'E.PC_NAME',
			'B.cand_category',
			'D.status',
			'B.cand_gender',
			DB::raw("COUNT(A.nom_id) AS gend_total")
		);
		$sTable = 'candidate_nomination_detail AS A';
		$sGroupBy = array(
			'B.cand_category',
			'B.cand_gender',
			'A.application_status'
		);
		$sWhere = array(
			'A.st_code' => $session['election_detail']['st_code']
		);
		$sGroupBy = array(
			'A.pc_no',
			'E.PC_NAME',
			'B.cand_category',
			'B.cand_gender',
			'A.application_status',
			'D.status'
		);
		$candidateDataNominationSummary = DB::table($sTable)
											->select($sSelect)
											->join('Candidate_personal_detail AS B','A.candidate_id','B.candidate_id')
											->join('counting_pcmaster AS C', 'A.candidate_id', 'C.candidate_id')
											->join('m_status AS D', 'D.id', 'A.application_status')
											->join('M_PC AS E', 'A.pc_no', 'E.PC_NO')
											->where($sWhere)
											->groupBy($sGroupBy)
											->get()->toArray();
		$data = array();
		foreach ($candidateDataNominationSummary as $cdns) {
			$data[$cdns->pc_no."~".$cdns->PC_NAME][$cdns->cand_category?'Other':$cdns->cand_category][] = array(
				'cand_category' => $cdns->cand_category?'Other':$cdns->cand_category,
				'status' => $cdns->status,
				'cand_gender' => $cdns->cand_gender,
				'gend_total' => $cdns->gend_total
			);
		}
		/*$data = "123_String";    
		$whatIWant = substr($data, strpos($data, "_") + 1);    
		echo $whatIWant;*/
    	// echo "<pre>"; print_r($data); die;
    	return view('StatisticalReports.Vol1.candidate-nomination-summary', compact('session','data'));
    }
    public function numberofcandidateperconstituency(Request $request){
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
    	/*
			SELECT A.ST_CODE, B.ST_NAME, COUNT(A.ST_CODE) 
			FROM M_PC AS A
			JOIN M_STATE AS B ON A.ST_CODE = B.ST_CODE
			GROUP BY A.ST_CODE
    	*/
		$sSelect = array(
			'A.ST_CODE',
			'B.ST_NAME',
			DB::raw('COUNT(A.ST_CODE) AS TotalSeats')
		);
		$totalSeats = DB::table('m_pc AS A')
						->select($sSelect)
						->join('m_state AS B', 'A.ST_CODE', 'B.ST_CODE')
						->groupBy('A.ST_CODE','B.ST_NAME')
						->get()->toArray();

		/*
			SELECT A.ST_CODE, COUNT(A.ST_CODE) 
			FROM M_PC AS A
			JOIN candidate_nomination_detail AS C ON A.`st_code` = C.`st_code`
			GROUP BY A.ST_CODE
		*/
		$totalNominationStateWise = DB::table('m_pc AS A')
										->select(['A.ST_CODE', DB::raw('COUNT(A.ST_CODE) as TotalCandidatesStateWise')])
										->join('candidate_nomination_detail AS C', 'A.st_code', 'C.st_code')
										->groupBy('A.ST_CODE')
										->get()->toArray();
			$totalNominationStateWiseArray = array();
			foreach ($totalNominationStateWise as $key) {
				$totalNominationStateWiseArray[$key->ST_CODE] = $key->TotalCandidatesStateWise;
			}
			// echo "<pre>"; print_r($totalNominationStateWiseArray); die;
    	/*
			SELECT A.ST_CODE,A.PC_NO,COUNT(A.NOM_ID)
			FROM candidate_nomination_detail AS A
			JOIN M_PC AS B ON A.ST_CODE = B.ST_CODE AND A.PC_NO = B.PC_NO
			GROUP BY B.ST_CODE, B.PC_NO
    	*/
		// echo "<pre>"; print_r($totalSeats);
		$cSelect = array(
			'A.ST_CODE',
			'A.PC_NO',
			DB::raw('COUNT(A.NOM_ID) AS TotalCandidates')
		);
		$candidatesPcWise = DB::table('candidate_nomination_detail AS A')
							->select($cSelect)
							->join('m_pc AS B', function($joining){
								$joining->on('A.ST_CODE', 'B.ST_CODE')
										->on('A.PC_NO','B.PC_NO');
							})
							->groupBy('A.ST_CODE', 'A.PC_NO')
							->get()->toArray();
    	$data = array();
    	$a=$b=$c=$d=$e=$f=$g=$h=$i=$j=$kk=0;
    	$count1 = $count16 = $count32 = $count48 = $count64 = $count65 = 0;
    	foreach ($totalSeats as $key => $value) {
    		$response = $this->getCountPerSeat($value, $candidatesPcWise);
    		// echo "<pre>"; print_r($response); die;
    		if($response->TotalCandidates == 1){
    			$count1++;
    		}
    		elseif($response->TotalCandidates > 1 && $response->TotalCandidates <= 16){
    			$count16++;
    		}elseif ($response->TotalCandidates > 16 && $response->TotalCandidates <= 32) {
    			$count32++;
    		}elseif($response->TotalCandidates > 32 && $response->TotalCandidates <= 48){
    			$count48++;
    		}elseif ($response->TotalCandidates > 48 && $response->TotalCandidates <= 64) {
    			$count64++;
    		}else{
    			$count65++;
    		}

    		$data[$value->ST_CODE] = array(
    			'ST_NAME' 		=> $value->ST_NAME,
    			'TotalSeats' 	=> $value->TotalSeats,
    			'count1'		=> $count1,
    			'count16'		=> $count16,
    			'count32'		=> $count32,
    			'count48'		=> $count48,
    			'count64'		=> $count64,
    			'count65'		=> $count65,
    			'min'			=> $response->min,
    			'max'			=> $response->max,
    			'totalCandidate'=>0,
    			'avg'			=>0,
    		);
    		
    		foreach ($totalNominationStateWiseArray as $k => $v) {
    			if($value->ST_CODE == $k){
    				$data[$value->ST_CODE]['totalCandidate'] = (int)$v;
    				$data[$value->ST_CODE]['avg'] = (int)($v/$data[$value->ST_CODE]['TotalSeats']);
    			}
    		}
    			$a+=$value->TotalSeats;
    			$b+=$count1;
    			$c+=$count16;
    			$d+=$count32;
    			$e+=$count48;
    			$f+=$count64;
    			$g+=$count65;
    			$h+=$response->min;
    			$i+=$response->max;
    			$j+=$data[$value->ST_CODE]['totalCandidate'];
    		// echo "<pre>"; var_dump($data[$value->ST_CODE]['avg']); die;
    			$kk+=$data[$value->ST_CODE]['avg'];
    	}
    		$grandData = array(
    			'TotalSeats' 	=> $a,
    			'count1'		=> $b,
    			'count16'		=> $c,
    			'count32'		=> $d,
    			'count48'		=> $e,
    			'count64'		=> $f,
    			'count65'		=> $g,
    			'min'			=> $h,
    			'max'			=> $i,
    			'totalCandidate'=> $j,
    			'avg'			=> $kk
    		);
    	// echo "<pre>"; print_r($grandData); die;
    	return view('IndexCardReports.StatisticalReports.Vol1.number-of-candidate-per-constituency',compact('session','data','grandData','user_data'));
    }
    public function getCountPerSeat($value,$candidatesPcWise){
    	$response = (object)array(
    		'TotalCandidates' => 0,
    		'min' => 0,
    		'max' => 0
    	);
    	foreach ($candidatesPcWise as $key) {
    	// echo "<pre>"; print_r($key); //die;
    		if($value->ST_CODE == $key->ST_CODE){
    			$response->TotalCandidates = $key->TotalCandidates;
    		/***********************MIN******************************/
    		if ($response->min == '') {
    			$response->min = $key->TotalCandidates;
    		}else{
	    		if($key->TotalCandidates < $response->min){
	    			$response->min = $key->TotalCandidates;
	    		}    			
    		}
    		/***********************MIN******************************/
    		/***********************MAX******************************/
    		if($response->max == ''){
    			$response->max = $key->TotalCandidates;
    		}else{
    			if($key->TotalCandidates > $response->max){
	    			$response->max = $key->TotalCandidates;
	    		}
    		}
    		/***********************MAX******************************/
    		}
    	} 
    	// echo "<pre>"; print_r($response); die;
    	return $response;
    }
    public function numberofcandidateperconstituencyPDF(Request $request){
    	$session = $request->session()->all();
    	/*
			SELECT A.ST_CODE, B.ST_NAME, COUNT(A.ST_CODE) 
			FROM M_PC AS A
			JOIN M_STATE AS B ON A.ST_CODE = B.ST_CODE
			GROUP BY A.ST_CODE
    	*/
		$sSelect = array(
			'A.ST_CODE',
			'B.ST_NAME',
			DB::raw('COUNT(A.ST_CODE) AS TotalSeats')
		);
		$totalSeats = DB::table('m_pc AS A')
						->select($sSelect)
						->join('m_state AS B', 'A.ST_CODE', 'B.ST_CODE')
						->groupBy('A.ST_CODE','B.ST_NAME')
						->get()->toArray();

		/*
			SELECT A.ST_CODE, COUNT(A.ST_CODE) 
			FROM M_PC AS A
			JOIN candidate_nomination_detail AS C ON A.`st_code` = C.`st_code`
			GROUP BY A.ST_CODE
		*/
		$totalNominationStateWise = DB::table('m_pc AS A')
										->select(['A.ST_CODE', DB::raw('COUNT(A.ST_CODE) as TotalCandidatesStateWise')])
										->join('candidate_nomination_detail AS C', 'A.st_code', 'C.st_code')
										->groupBy('A.ST_CODE')
										->get()->toArray();
			$totalNominationStateWiseArray = array();
			foreach ($totalNominationStateWise as $key) {
				$totalNominationStateWiseArray[$key->ST_CODE] = $key->TotalCandidatesStateWise;
			}
			// echo "<pre>"; print_r($totalNominationStateWiseArray); die;
    	/*
			SELECT A.ST_CODE,A.PC_NO,COUNT(A.NOM_ID)
			FROM candidate_nomination_detail AS A
			JOIN M_PC AS B ON A.ST_CODE = B.ST_CODE AND A.PC_NO = B.PC_NO
			GROUP BY B.ST_CODE, B.PC_NO
    	*/
		// echo "<pre>"; print_r($totalSeats);
		$cSelect = array(
			'A.ST_CODE',
			'A.PC_NO',
			DB::raw('COUNT(A.NOM_ID) AS TotalCandidates')
		);
		$candidatesPcWise = DB::table('candidate_nomination_detail AS A')
							->select($cSelect)
							->join('m_pc AS B', function($joining){
								$joining->on('A.ST_CODE', 'B.ST_CODE')
										->on('A.PC_NO','B.PC_NO');
							})
							->groupBy('A.ST_CODE', 'A.PC_NO')
							->get()->toArray();
    	$data = array();
    	$a=$b=$c=$d=$e=$f=$g=$h=$i=$j=$kk=0;
    	$count1 = $count16 = $count32 = $count48 = $count64 = $count65 = 0;
    	foreach ($totalSeats as $key => $value) {
    		$response = $this->getCountPerSeat($value, $candidatesPcWise);
    		// echo "<pre>"; print_r($response); die;
    		if($response->TotalCandidates == 1){
    			$count1++;
    		}
    		elseif($response->TotalCandidates > 1 && $response->TotalCandidates <= 16){
    			$count16++;
    		}elseif ($response->TotalCandidates > 16 && $response->TotalCandidates <= 32) {
    			$count32++;
    		}elseif($response->TotalCandidates > 32 && $response->TotalCandidates <= 48){
    			$count48++;
    		}elseif ($response->TotalCandidates > 48 && $response->TotalCandidates <= 64) {
    			$count64++;
    		}else{
    			$count65++;
    		}

    		$data[$value->ST_CODE] = array(
    			'ST_NAME' 		=> $value->ST_NAME,
    			'TotalSeats' 	=> $value->TotalSeats,
    			'count1'		=> $count1,
    			'count16'		=> $count16,
    			'count32'		=> $count32,
    			'count48'		=> $count48,
    			'count64'		=> $count64,
    			'count65'		=> $count65,
    			'min'			=> $response->min,
    			'max'			=> $response->max,
    			'totalCandidate'=>0,
    			'avg'			=>0,
    		);
    		
    		foreach ($totalNominationStateWiseArray as $k => $v) {
    			if($value->ST_CODE == $k){
    				$data[$value->ST_CODE]['totalCandidate'] = (int)$v;
    				$data[$value->ST_CODE]['avg'] = (int)($v/$data[$value->ST_CODE]['TotalSeats']);
    			}
    		}
    			$a+=$value->TotalSeats;
    			$b+=$count1;
    			$c+=$count16;
    			$d+=$count32;
    			$e+=$count48;
    			$f+=$count64;
    			$g+=$count65;
    			$h+=$response->min;
    			$i+=$response->max;
    			$j+=$data[$value->ST_CODE]['totalCandidate'];
    		// echo "<pre>"; var_dump($data[$value->ST_CODE]['avg']); die;
    			$kk+=$data[$value->ST_CODE]['avg'];
    	}
    		$grandData = array(
    			'TotalSeats' 	=> $a,
    			'count1'		=> $b,
    			'count16'		=> $c,
    			'count32'		=> $d,
    			'count48'		=> $e,
    			'count64'		=> $f,
    			'count65'		=> $g,
    			'min'			=> $h,
    			'max'			=> $i,
    			'totalCandidate'=> $j,
    			'avg'			=> $kk
    		);
    	/*return view('StatisticalReports.Vol1.candidate-nomination-summarypdf',compact('session','data','grandData'));*/
    	$pdf=PDF::loadView('StatisticalReports.Vol1.candidate-nomination-summarypdf',[
    		'session'=>$session,
    		'data'=>$data,
    		'grandData'=>$grandData
    	]);
		return $pdf->download('number-of-candidate-per-constituency.pdf');
    }
	
	
	//neha code start dated 12-4-2019
    public function constituencyDataSummaryReport(Request $request)
    {
       
	   
	   
                $session = $request->session()->all();
                $st_code=$session['election_detail']['st_code'];
                DB::enableQueryLog();
                $candatapcwise = array();

                $pcList = DB::table('m_pc')
                    ->select(['PC_NO','PC_NAME'])
                    ->where('ST_CODE',$session['election_detail']['st_code'])
                    ->get()->toArray();
 
            
                $indexCardData = DB::table('t_pc_ic AS A')
                        ->join(DB::raw('(SELECT MAX(created_at) AS created_at FROM t_pc_ic GROUP BY st_code, pc_no ORDER BY created_at DESC) AS B'),'A.created_at','B.created_at')
                        ->join('m_pc AS C',function($query){
                            $query->on('A.st_code','C.st_code')
                                    ->on('A.pc_no','C.pc_no');
                        })
                        ->where(['A.st_code' => $session['election_detail']['st_code'], 'ac_no' => null])
                        ->get()->toArray();


                //dd($indexCardData);
                        // $candidateData=DB::select(DB::raw($indexCardData));
                        $queue = DB::getQueryLog();

                        $candatapcwise[] = $indexCardData;
                 
                    
                    $electorData = DB::table('elector_details AS A')
                        ->select(array(
                            DB::raw('SUM(A.gen_m) AS gen_m'), 
                            DB::raw("SUM(A.gen_f) AS gen_f"),
                            DB::raw("SUM(A.gen_o) AS gen_o"),
                            DB::raw("SUM(A.gen_t) AS gen_t")
                        ))
                        ->groupBy(array(
                            'gen_m',
                            'gen_f',
                            'gen_o',
                            'gen_t'
                            
                        ))
                        ->get()->toArray();

                // return view('StatisticalReports.Vol1.constituency-data-summary-report', compact('candatapcwise','electorData','indexCardData'));


                if($request->path() == 'constituencyDataSummaryReport'){
                return view('StatisticalReports.Vol1.constituency-data-summary-report',compact('session','indexCardData','candatapcwise','electorData'));
                }elseif($request->path() == 'constituencyDataSummaryReportPDF'){
                $pdf=PDF::loadView('constituencyDataSummaryReport.constituencyDataSummaryReportPDF',[
                    'session'=>$session,
                    'indexCardData'=>$indexCardData
                ]);
                return $pdf->download('Constituency_data_summery_report.pdf');
                }else{
                die('No Data Found');
                }
            }

    public function getListofPoliticalPartiesParticipated(Request $request)
    {
    return view('StatisticalReports.Vol1.listof-political-parties-participated');
    }

    public function getListofPoliticalPartiesParticipatedPDF(Request $request)
    {
        $pdf=PDF::loadView('StatisticalReports.Vol1.listof-political-parties-participated-pdf');
        return $pdf->download('listof-political-parties-participated-report.pdf');
    }

//candidate data summary reports start 






//Amit rajak
    public function getcandidateDataSummary(Request $request)
    {
         $session = $request->session()->all();
       $candatapcwise = array();
       
//       $indexCardData = DB::table('t_pc_ic AS A')
//                       ->join(DB::raw('(SELECT MAX(created_at) AS created_at FROM t_pc_ic GROUP BY st_code, pc_no ORDER BY created_at DESC) AS B'),'A.created_at','B.created_at')
//                       ->where('st_code',$session['election_detail']['st_code'])
//                       ->get()->toArray();
       
       ///dd($indexCardData);
       
     
       $st_code=$session['election_detail']['st_code'];
             
       $pcList = DB::table('m_pc')
                   ->select(['PC_NO','PC_NAME'])
                   ->where('ST_CODE',$session['election_detail']['st_code'])
                   ->get();
               //->toArray();
       
           
       
        return view('StatisticalReports.Vol1.candidate-data-summary')->with(['pcdetails'=>$pcList,'stcode'=>$st_code]);
///Amit Rajak

    }

///condidate-data-summary-pdf
     public function getcandidateDataSummaryPDF(Request $request) {


        $session = $request->session()->all();
        $candatapcwise = array();
        $st_code = $session['election_detail']['st_code'];

        $pcdetails = DB::table('m_pc')
                ->select(['PC_NO', 'PC_NAME'])
                ->where('ST_CODE', $session['election_detail']['st_code'])
                ->get();
        //->toArray();


        $pdf = PDF::loadView('StatisticalReports.Vol1.candidate-data-summary-pdf', compact('pcdetails', 'st_code'));
        return $pdf->download('candidate-data-summary.pdf');
    }
    ///condidate-data-summary-pdf




/// condidate-data-summary-xls
 public function getcandidateDataSummaryExcel(Request $request)
    {
    	//$data = User::get()->toArray();
         $session = $request->session()->all();
        $candatapcwise = array();
        $st_code = $session['election_detail']['st_code'];
         $data = DB::table('m_pc')
                                  
                 ->select('m_pc.PC_NO', 'm_pc.PC_TYPE', 'm_pc.PC_NAME', 
                         
                         DB::raw("SUM(t_pc_ic.c_nom_m_t) as cnom_m_t"),
                         DB::raw("SUM(t_pc_ic.c_nom_f_t) as cnom_f_t"), 
                         DB::raw("SUM(t_pc_ic.c_nom_o_t) as cnom_o_t"), 
                         DB::raw("SUM(t_pc_ic.c_nom_r_m) as cnom_r_m"), 
                         DB::raw("SUM(t_pc_ic.c_nom_r_f) as cnom_r_f"), 
                         DB::raw("SUM(t_pc_ic.c_nom_r_o) as cnom_r_o"), 
                         DB::raw("SUM(t_pc_ic.c_nom_w_m) as cnom_w_m"), 
                         DB::raw("SUM(t_pc_ic.c_nom_w_f) as cnom_w_f"), 
                         DB::raw("SUM(t_pc_ic.c_nom_w_o) as cnom_w_o"),
                         DB::raw("SUM(t_pc_ic.c_nom_co_m) as c_nom_co_m"),
                         DB::raw("SUM(t_pc_ic.c_nom_co_f) as c_nom_co_f"),
                         DB::raw("SUM(t_pc_ic.c_nom_co_t) as c_nom_co_t"),
                         DB::raw("SUM(t_pc_ic.c_nom_fd_m) as c_nom_fd_m"),
                         DB::raw("SUM(t_pc_ic.c_nom_fd_f) as c_nom_fd_f"),
                         DB::raw("SUM(t_pc_ic.c_nom_fd_t) as c_nom_fd_t"))
                     ->join('t_pc_ic', 't_pc_ic.pc_no', '=', 'm_pc.PC_NO')
                ->where('m_pc.ST_CODE', $session['election_detail']['st_code'])

                ->GroupBy('m_pc.PC_TYPE', 'm_pc.ST_CODE')
                ->get();
      
        return Excel::create('laravelcode', function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $sheet->mergeCells('A1:R1');
                $sheet->mergeCells('D5:F5');
                $sheet->mergeCells('G5:I5');
                $sheet->mergeCells('J5:L5');
                $sheet->mergeCells('M5:O5');
                $sheet->mergeCells('P5:R5');
               $sheet->cells('A1', function($cells) {
                   
                            $cells->setValue('Candidate Data Summary On Nominations , Rejections,Withdrawals And Deposits Forfeited');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        
                        $sheet->cells('D5', function($cells) {
                            $cells->setValue('Nominations Filed');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        $sheet->cells('G5', function($cells) {
                            $cells->setValue('Nominations Rejected');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        $sheet->cells('J5', function($cells) {
                            $cells->setValue('Contesting Candidates');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        $sheet->cells('M5', function($cells) {
                            $cells->setValue('Nominations Withdrawn');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                            
                        });
                        
                        $sheet->cells('P5', function($cells) {
                            $cells->setValue('Forfeited Contesting Candidates');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 10,'bold' => true));
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
                    $col= 'B'.$last.':'.'R'.$last;
                    
                    $sheet->cells($col, function($cells) {
                            $cells->setFont(array(
                            'name' => 'Times New Roman',
                             'size' => 12,
                            'bold' => true
                        ));
                            
                            $cells->setAlignment('center');
                            
                        });
                    
              
                $sheet->cell('A6', function($cell) {$cell->setValue('State/UT');});
                $sheet->cell('B6', function($cell) {$cell->setValue('PC NO');});
                $sheet->cell('C6', function($cell) {$cell->setValue('PC NAME');});
                
                $sheet->cell('D6', function($cell) {$cell->setValue('Men');});
                $sheet->cell('E6', function($cell) {$cell->setValue('Women');});
                $sheet->cell('F6', function($cell) {$cell->setValue('Total');});
                
                $sheet->cell('G6', function($cell) {$cell->setValue('Men');});
                $sheet->cell('H6', function($cell) {$cell->setValue('Women');});
                $sheet->cell('I6', function($cell) {$cell->setValue('Total');});
                
                $sheet->cell('J6', function($cell) {$cell->setValue('Men');});
                $sheet->cell('K6', function($cell) {$cell->setValue('Women');});
                $sheet->cell('L6', function($cell) {$cell->setValue('Total');});
                
                $sheet->cell('M6', function($cell) {$cell->setValue('Men');});
                $sheet->cell('N6', function($cell) {$cell->setValue('Women');});
                $sheet->cell('O6', function($cell) {$cell->setValue('Total');});
                
                $sheet->cell('P6', function($cell) {$cell->setValue('Men');});
                $sheet->cell('Q6', function($cell) {$cell->setValue('Women');});
                $sheet->cell('R6', function($cell) {$cell->setValue('Total');});
                
                $sheet->cell('B'.$last, function($cell) {$cell->setValue('Grand Total');});

                
                
                
                if (!empty($data)) {
                    
                    $Cnom_m_t = 0;
                    $Cnom_f_t = 0;
                    $Cnom_o_t = 0;
                    
                    $Cnom_r_m = 0;
                    $Cnom_r_f = 0;
                    $Cnom_r_o = 0;
                    
                    $Cnom_w_m = 0;
                    $Cnom_w_f= 0;
                    $Cnom_w_o= 0;
                    
                    $Cnom_co_m= 0;
                    $Cnom_co_f= 0;
                    $Cnom_co_t= 0;
                    
                    $Cnom_fd_m= 0;
                    $Cnom_fd_f= 0;
                    $Cnom_fd_t= 0;
                                        

                    
                    //$last_key = end(array_keys($data));
                   
                            foreach ($data as $key => $value) {
                                $i = $key+7;

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
                                
                                if ($value === end($data)){
                              $last_key = $value ;
                                }  
                            }
                            
                            $sheet->cell('D'.$last , $Cnom_m_t);
                            $sheet->cell('E'.$last , $Cnom_f_t);
                            $sheet->cell('F'.$last , $Cnom_o_t);
                            
                            $sheet->cell('G'.$last , $Cnom_r_m);
                            $sheet->cell('H'.$last , $Cnom_r_f);
                            $sheet->cell('I'.$last , $Cnom_r_o);
                            
                            $sheet->cell('J'.$last , $Cnom_w_m);
                            $sheet->cell('K'.$last , $Cnom_w_f);
                            $sheet->cell('L'.$last , $Cnom_w_o);
                            
                            $sheet->cell('M'.$last , $Cnom_co_m);
                            $sheet->cell('N'.$last , $Cnom_co_f);
                            $sheet->cell('O'.$last , $Cnom_co_t);
                            
                            $sheet->cell('P'.$last , $Cnom_fd_m);
                            $sheet->cell('Q'.$last , $Cnom_fd_f);
                            $sheet->cell('R'.$last , $Cnom_fd_t);
                        }
                    });
                })->export();
    }
   

//// end condidate-data-summary-xls


//list of successful candidate report start
     public function getListofSuccessfulCandidate(Request $request)
    {
        return view('StatisticalReports.Vol1.listof-successful-candidate');

    }

     public function getListofSuccessfulCandidatePDF(Request $request)
    {
        $pdf=PDF::loadView('StatisticalReports.Vol1.listof-successful-candidate-pdf');
        return $pdf->download('listof-successful-candidate.pdf');
    }

//list of successful candidate report end

//Constituency Wise Detailed Result 

     public function getConstituencyWiseDetailedResult(Request $request)
    {

        $user = Auth::user();
		
		//echo '<pre>'; print_r($user); die;
		
		
		
           $uid=$user->id;
           $d=$this->commonModel->getunewserbyuserid($user->id);
           $d=$this->commonModel->getunewserbyuserid($uid);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);

          $sched=''; $search='';
         
         
          $session['election_detail'] = array();
          //$session['election_detail']['st_code'] = $user->st_code;
          //$session['election_detail']['st_name'] = $user->placename;
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
        DB::enableQueryLog();
           // $year=$session['election_detail']['YEAR'];
           // $state=$session['election_detail']['st_name'];


          //  foreach ($data as $key) {
                // echo '<pre>'; print_r($key);die;
                $sWhere = array(
                    'cci.st_code'       => 'S01',
                    'cci.pc_no'         => '1'
                );

                $candidateData = DB::select("SELECT TEMP.*,

(SELECT SUM(ccil.general_male_voters + ccil.general_female_voters + ccil.general_other_voters + ccil.nri_male_voters + ccil.nri_female_voters + ccil.nri_other_voters + ccil.service_postal_votes_under_section_8 + ccil.service_postal_votes_gov) as total_vote FROM electors_cdac_other_information ccil
where ccil.st_code=TEMP.ST_CODE AND ccil.pc_no=TEMP.pc_no AND ccil.year='2019' and ccil.election_id = '1' group by TEMP.pc_no )as total_votes,

(SELECT SUM(gen_electors_male + gen_electors_female + gen_electors_other + nri_male_electors + nri_female_electors + nri_third_electors + service_male_electors + service_female_electors + service_third_electors) as TOTAL_ELECT_VOTE  from electors_cdac cdac
WHERE TEMP.PC_NO=cdac.pc_no and TEMP.ST_CODE=cdac.st_code and cdac.year='2019' and cdac.election_id = '1' group by TEMP.PC_NO )AS total_electors

FROM
(
select mm.ST_CODE,mm.ST_NAME as st_name,mp.PC_NO,mp.PC_NAME,cpd.cand_name,cpd.cand_gender,
cpd.cand_age, cpd.cand_category, cci.party_abbre,
ms.SYMBOL_DES,cci.evm_vote as general,
cci.postal_vote as postal,
cci.total_vote as cand_total_vote
from counting_pcmaster cci, candidate_personal_detail cpd,
candidate_nomination_detail cnd left join m_symbol ms on cnd.symbol_id = ms.SYMBOL_NO,
 m_pc mp,m_state mm
where cci.candidate_id = cpd.candidate_id
and cnd.candidate_id = cci.candidate_id
and cci.st_code = mp.ST_CODE
and cci.pc_no = mp.PC_NO
and mm.ST_CODE =  cci.st_code
and cci.election_id = '1'
group by mp.ST_CODE,mp.PC_NO, cci.candidate_id
ORDER BY mp.ST_CODE,mp.PC_NO ASC
)TEMP");


$all_india_Data = DB::select("SELECT sum(evm_vote) as all_india_evm,sum(`postal_vote`) as all_india_postal,sum(`total_vote`) as all_india_total FROM `counting_pcmaster` WHERE election_id = 1");


				$dataArr = array();


				foreach($candidateData as $raw){
					
					$dataArr[$raw->st_name][$raw->PC_NO.' . '.$raw->PC_NAME.'&emsp;&emsp;&emsp;&emsp; &emsp;&emsp;<b>(Total Electors &emsp;&emsp;</b>'.$raw->total_electors.')'][] = array(
					
						'cand_name' => $raw->cand_name,
						'cand_gender' => $raw->cand_gender,
						'cand_age' => $raw->cand_age,
						'cand_category' => $raw->cand_category,
						'party_abbre' => $raw->party_abbre,
						'SYMBOL_DES' => $raw->SYMBOL_DES,
						'general_vote' => $raw->general,
						'postal_vote' => $raw->postal,
						'cand_total_vote' => $raw->cand_total_vote,
						'total_votes' => $raw->total_votes,
						'total_electors' => $raw->total_electors
					
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
			   
			   
                //echo '<pre>'; print_r($dataArr);die;
	
            //}
			
			if($request->path() == "$prefix/constituencywisedetailedresult_pdf"){
				
				 $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.constituency-wise-detailed-result-pdf',compact('dataArr','user_data','all_india_Data'));
				return $pdf->download('Constituency Wise Detailed Result.pdf');
				
			}elseif($request->path() == "$prefix/constituencywisedetailedresult_xls"){
				
				
				
			$dataArr = json_decode( json_encode($dataArr), true);            

            return Excel::create('Constituency Wise Detailed Result', function($excel) use ($dataArr,$all_india_Data) {
             $excel->sheet('mySheet', function($sheet) use ($dataArr,$all_india_Data)
             {
            $sheet->mergeCells('A1:L1');
           
           $sheet->cell('A1', function($cells) {
                $cells->setValue('33 - Constituency Wise Detailed Result');
				$cells->setAlignment('center');
			    $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
            });


			$i = 3;

			if (!empty($dataArr)) {

			foreach($dataArr as $key => $data){

				$sheet->mergeCells("A$i:L$i");
				$sheet->cell("A$i", function($cells) use ($key) {
					$cells->setValue($key);
					$cells->setAlignment('center');
					$cells->setFont(array('name' => 'Times New Roman', 'size' => 14, 'bold' => true));
				});
			
				$i++;
				foreach($data as $key1 => $raw){

					$sheet->mergeCells("A$i:L$i");
					$sheet->cell("A$i", function($cells) use ($key1) {
						$cells->setValue(strip_tags(html_entity_decode($key1)));
						$cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
					});
				
					$i++;

					$sheet->cell("A$i", function($cells) {
						$cells->setValue('Sl. No.');
					});

				   $sheet->cell("B$i", function($cells) {
						$cells->setValue('Candidates');
					});
					
					$sheet->cell("C$i", function($cells) {
						$cells->setValue('Gender');
					});

					$sheet->cell("D$i", function($cells) {
						$cells->setValue('Age');
					});
		 
					$sheet->cell("E$i", function($cells) {
						$cells->setValue('Category');
					});
					$sheet->cell("F$i", function($cells) {
						$cells->setValue('Party');
					});
					$sheet->cell("G$i", function($cells) {
						$cells->setValue('Symbol');
					});
					 $sheet->cell("H$i", function($cells) {
						$cells->setValue('General Votes');
					});
					$sheet->cell("I$i", function($cells) {
						$cells->setValue('Postal Votes');
					});
					$sheet->cell("J$i", function($cells) {
						$cells->setValue('Total');
					});

					$sheet->cell("K$i", function($cells) {
						$cells->setValue('Over Total Electors in Const.');
					});
					$sheet->cell("L$i", function($cells) {
						$cells->setValue('Over Total Voters Polled in Const.');
					});
					
					$i++;
					
					
					$count=1;$totalgeneral_vote=0;$totalpostal_vote=0;$grandtotal=0; $totalelectorspercent =0; $grandelector=0; $grandpolled=0;
					
					foreach($raw as $row){
						  
						$electors = $row['total_electors'];
						$totalvotespolled = $row['total_votes'];
				  
						$totalelectorPercent = ($electors!=0)?((($row['general_vote']+$row['postal_vote'])/$electors)*100):0;
						$grandelector+=$totalelectorPercent;


						$totalvotespolled=($totalvotespolled!=0)?((($row['general_vote']+$row['postal_vote'])/$totalvotespolled)*100):0;
						$grandpolled+=$totalvotespolled;
                        

						
                        $sheet->cell('A'.$i ,$count); 
                        $sheet->cell('B'.$i, $row['cand_name']); 
                        $sheet->cell('C'.$i, $row['cand_gender']);
                        $sheet->cell('D'.$i, $row['cand_age']); 
                        $sheet->cell('E'.$i, $row['cand_category']); 
                        $sheet->cell('F'.$i, $row['party_abbre']); 
                        $sheet->cell('G'.$i, $row['SYMBOL_DES']); 
                        $sheet->cell('H'.$i, ($row['general_vote']) ? $row['general_vote'] : '=(0)'); 
                        $sheet->cell('I'.$i, ($row['postal_vote']) ? $row['postal_vote'] : '=(0)'); 
                        $sheet->cell('J'.$i, ($row['cand_total_vote']) ? $row['cand_total_vote'] : '=(0)'); 
                        $sheet->cell('K'.$i, round($totalelectorPercent,2));  
                        $sheet->cell('L'.$i, round($totalvotespolled,2));
						
						$totalgeneral_vote+=$row['general_vote'];
						$totalpostal_vote+=$row['postal_vote'];
						$grandtotal+=$row['general_vote']+$row['postal_vote'];
						$count++;
                          $i++; 
                      } 
					  
					  $i++; 
						$sheet->mergeCells("A$i:G$i");
						$sheet->cell("A$i", function($cells) {
							$cells->setValue('TOTAL');
							$cells->setAlignment('center');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
						}); 
						
                        $sheet->cell('H'.$i, ($totalgeneral_vote > 0) ? $totalgeneral_vote:'=(0)' ); 
                        $sheet->cell('I'.$i, ($totalpostal_vote > 0) ? $totalpostal_vote:'=(0)' ); 
                        $sheet->cell('J'.$i, ($grandtotal > 0) ? $grandtotal:'=(0)' ); 
                        $sheet->cell('K'.$i, round($grandelector,2) ); 
                        $sheet->cell('L'.$i, round($grandpolled,2) );
						$i++;

					}
					
						$i++; 
						$i++; 
						$sheet->mergeCells("A$i:G$i");
						$sheet->cell("A$i", function($cells) {
							$cells->setValue('INDIA TOTAL');
							$cells->setAlignment('center');
							$cells->setFont(array('name' => 'Times New Roman', 'size' => 13, 'bold' => true));
						}); 
						
                        $sheet->cell('H'.$i, ($all_india_Data[0]->all_india_evm > 0) ? $all_india_Data[0]->all_india_evm:'=(0)' ); 
                        $sheet->cell('I'.$i, ($all_india_Data[0]->all_india_postal > 0) ? $all_india_Data[0]->all_india_postal:'=(0)' ); 
                        $sheet->cell('J'.$i, ($all_india_Data[0]->all_india_total > 0) ? $all_india_Data[0]->all_india_total:'=(0)' ); 
					
										 				
					}     


					



					
				}
                });
             })->download('xls');	
				
			}else{
				return view('IndexCardReports.StatisticalReports.Vol1.constituency-wise-detailed-result',compact('dataArr','user_data','all_india_Data'));
			}

    }

    

//State wise number of electors

     public function getStateWiseNumberElectors(Request $request)
    {
        return view('StatisticalReports.Vol1.statewise-number-electors');

    }

     public function getStateWiseNumberElectorsPDF(Request $request)
    {
        $pdf=PDF::loadView('StatisticalReports.Vol1.statewise-number-electors-pdf');
        return $pdf->download('statewise-number-electors.pdf');
    }

	//Voters Information
     public function getVoterInformation(Request $request)
    {
		// session data
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
        //echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
	   //end session data
      $stcode=$session['election_detail']['st_code'];
      $stname=$session['election_detail']['st_name'];
      $vSelect=array(
        'A.PC_TYPE',

        DB::raw("SUM(case when A.pc_type = 'GEN' THEN '1' else '0' end) AS GENSEATS"),
        DB::raw("SUM(case when A.pc_type = 'SC' THEN 1 else 0 end) AS SCSEATS"),
        DB::raw("SUM(case when A.pc_type = 'ST' THEN 1 else 0 end) AS STSEATS"),
        DB::raw('SUM(B.e_gen_m) as emale'),
        DB::raw('SUM(B.e_gen_f)as efemale'),
        DB::raw('SUM(B.e_gen_o) as eother'),
        DB::raw('SUM(B.e_gen_t)as etotal'),
        DB::raw('SUM(B.e_nri_t) as nri'),
        DB::raw('SUM(B.e_ser_t)as ser'),
        DB::raw('SUM(B.vt_gen_m)as votermale'),
        DB::raw('SUM(B.vt_gen_f)as voterfemale'),
        DB::raw('SUM(B.vt_gen_o)as voterother'),
        DB::raw('SUM(B.postal_valid_votes)as totalpostal'),
        DB::raw('SUM(B.total_votes_polled)as totalvotes'),
        DB::raw('SUM(B.vt_nri_t)as voternri'),
        DB::raw('SUM(B.r_votes_evm)as rejectedvotes'),
        DB::raw('SUM(B.not_retrieved_vote_evm)as notretrivedfromevm'),
        DB::raw('SUM(B.nota_vote_evm)as notavotes'),
        DB::raw('SUM(B.total_valid_votes)as totalvalidvote'),
        DB::raw('SUM(B.tendered_votes)as tenderedvotes')
    );

    $vWhere=array(
    'A.st_code'  => $session['election_detail']['st_code']
    );

     DB::enableQueryLog();

      $voterquery = DB::table('m_pc AS A')
                        ->select($vSelect)
                        ->join('t_pc_ic AS B', function($join){
                                $join->on('B.st_code','A.ST_CODE')
                                    ->on('B.pc_no','A.PC_NO');
                            })
                       // ->join(DB::raw("(Select MAX(created_at) As created from t_pc_ic where st_code ='$stcode' GROUP by st_code, pc_no limit 1) AS C"),'B.created_at','C.created')
                        ->where($vWhere)
                        ->groupby ('A.pc_type', 'A.ST_CODE')
                        ->get()->toArray();
                        
                  $queue = DB::getQueryLog();
                  //echo'<pre>'; print_r($queue);die;
                    if($request->path() == 'pcceo/voterInformation'){
            return view('IndexCardReports.StatisticalReports.Vol1.voter-information',compact('voterquery','stcode','stname','user_data','sched'));
        }elseif($request->path() == 'pcceo/voterInformationPDF'){
            $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.voter-information-pdf',compact('voterquery','stcode','stname','user_data','sched'));
             return $pdf->download('voter-information-report.pdf');
        }elseif($request->path() == 'pcceo/voterInformationXls'){
            $voterquery = json_decode( json_encode($voterquery), true);            

            return Excel::create('voterInformation', function($excel) use ($voterquery) {
             $excel->sheet('mySheet', function($sheet) use ($voterquery)
             {
            $sheet->mergeCells('A1:U1');
            $sheet->cells('A1:U1', function($cells) {
                $cells->setFont(array(
                    'size'       => '15',
                    'bold'       => true
                ));
            });
           $sheet->cell('A1', function($cells) {
                $cells->setValue('Voters Information');
            });


            $sheet->cell('A2', function($cells) {
                $cells->setValue('State/UT');
            });

           $sheet->cell('B2', function($cells) {
                $cells->setValue('Constituency Type');
            });
            
            $sheet->cell('C2', function($cells) {
                $cells->setValue('Seats');
            });

            $sheet->mergeCells('D2:I2');

            $sheet->cell('D2', function($cells) {
                $cells->setValue('Electors');
            });
            $sheet->mergeCells('J2:P2');

            $sheet->cell('J2', function($cells) {
                $cells->setValue('Voters');
            });
            $sheet->cell('Q2', function($cells) {
                $cells->setValue('Rejected Votes');
            });
            $sheet->cell('R2', function($cells) {
                $cells->setValue('Votes Not Retrived From EVM');
            });
             $sheet->cell('S2', function($cells) {
                $cells->setValue('NOTA Votes');
            });
            $sheet->cell('T2', function($cells) {
                $cells->setValue('Valid Votes Polled');
            });
            $sheet->cell('U2', function($cells) {
                $cells->setValue('Tendered Votes');
            });

            $sheet->cell('D3', function($cells) {
                $cells->setValue('Male');
            });
            $sheet->cell('E3', function($cells) {
                $cells->setValue('Female');
            });
            $sheet->cell('F3', function($cells) {
                $cells->setValue('Other');
            });
            $sheet->cell('G3', function($cells) {
                $cells->setValue('Total');
            });
            $sheet->cell('H3', function($cells) {
                $cells->setValue('NRI');
            });
            $sheet->cell('I3', function($cells) {
                $cells->setValue('Service');
            });


            $sheet->cell('J3', function($cells) {
                $cells->setValue('Male');
            });
            $sheet->cell('K3', function($cells) {
                $cells->setValue('Female');
            });
            $sheet->cell('L3', function($cells) {
                $cells->setValue('Other');
            });
            $sheet->cell('M3', function($cells) {
                $cells->setValue('Postal');
            });
            $sheet->cell('N3', function($cells) {
                $cells->setValue('Total');
            });
            $sheet->cell('O3', function($cells) {
                $cells->setValue('NRI');
            });
             $sheet->cell('P3', function($cells) {
                $cells->setValue('Poll %');
            });
           
           

             if (!empty($voterquery)) {
                 $i= 4;
                  $totalseats =  $totalemale = $totalefemale = $totaleother = $totaletotal = $totalenri = $totaleser = $totalvmale = $totalvfemale = $totalvother = $totalvpostal = $totalvtotal = $totalvnri = $totalpollpercent = $totalrejectedvote = $totalnotretrived = $totalnota= $totalvalidpollvotes= $totaltenderedvotes = $totalpoll = 0;
                 $tpollpercent=0;$telectors=0;$tvoters=0;
                 //$sn=1;
             // echo '<pre>';print_r($voterquery);die;
                  //$stname=$session['election_detail']['st_name'];
                foreach ($voterquery as $row) {   
                    $seat=0; 
                    ($row['PC_TYPE']=='GEN')?$seat=$row['GENSEATS']:0;
                    ($row['PC_TYPE']=='SC')?$seat=$row['SCSEATS']:0;
                    ($row['PC_TYPE']=='ST')?$seat=$row['STSEATS']:0;
                    $totalseats+=$row['GENSEATS']+$row['SCSEATS']+$row['STSEATS'];
                                        
                       // $sheet->cell('A'.$i ,$stname); 
                        $sheet->cell('B'.$i, $row['PC_TYPE']); 
                        $sheet->cell('C'.$i, $seat);
                        $sheet->cell('D'.$i, ($row['emale']) ? $row['emale'] : '=(0)'); 
                        $sheet->cell('E'.$i, ($row['efemale']) ? $row['efemale'] : '=(0)'); 
                        $sheet->cell('F'.$i, ($row['eother']) ? $row['eother'] : '=(0)'); 
                        $sheet->cell('G'.$i, ($row['etotal']) ? $row['etotal'] : '=(0)'); 
                        $sheet->cell('H'.$i, ($row['nri']) ? $row['nri'] : '=(0)'); 
                        $sheet->cell('I'.$i, ($row['ser']) ? $row['ser'] : '=(0)'); 

                        $sheet->cell('J'.$i, ($row['votermale']) ? $row['votermale'] : '=(0)'); 
                        $sheet->cell('K'.$i, ($row['voterfemale']) ? $row['voterfemale'] : '=(0)');  
                        $sheet->cell('L'.$i, ($row['voterother']) ? $row['voterother'] : '=(0)'); 
                        $sheet->cell('M'.$i, ($row['totalpostal']) ? $row['totalpostal'] : '=(0)'); 
                        $sheet->cell('N'.$i, ($row['totalvotes']) ? $row['totalvotes'] : '=(0)'); 
                        $sheet->cell('O'.$i, ($row['voternri']) ? $row['voternri'] : '=(0)');  
                        $sheet->cell('P'.$i, ($tpollpercent) ? $tpollpercent : '=(0)'); 

                         $sheet->cell('Q'.$i, ($row['rejectedvotes']) ? $row['rejectedvotes'] : '=(0)'); 
                         $sheet->cell('R'.$i, ($row['notretrivedfromevm']) ? $row['notretrivedfromevm'] : '=(0)'); 
                         $sheet->cell('S'.$i, ($row['notavotes']) ? $row['notavotes'] : '=(0)'); 
                         $sheet->cell('T'.$i, ($row['totalvalidvote']) ? $row['totalvalidvote'] : '=(0)'); 
                         $sheet->cell('U'.$i, ($row['tenderedvotes']) ? $row['tenderedvotes'] : '=(0)'); 
                          $i++;  
                      }   $i +=1;

                          //$totalseats+=$row->seats;
                          $totalemale+=$row['emale'];
                          $totalefemale+=$row['efemale'];
                          $totaleother+=$row['eother'];
                          $totaletotal+=$row['etotal'];
                          //$totalseats+=$row[]seats;
                          $totalenri+=$row['nri'];
                          $totaleser+=$row['ser'];
                          $totalvmale+=$row['votermale'];
                          $totalvfemale+=$row['voterfemale'];
                          $totalvother+=$row['voterother'];
                          $totalvpostal+=$row['totalpostal'];
                          $totalvtotal+=$row['totalvotes'];
                          $totalvnri+=$row['voternri'];
                          $totalpoll+=$tpollpercent;

                          $totalrejectedvote+=$row['rejectedvotes'];
                          $totalnotretrived+=$row['notretrivedfromevm'];
                          $totalnota+=$row['notavotes'];
                          $totalvalidpollvotes+=$row['totalvalidvote'];
                          $totaltenderedvotes+=$row['tenderedvotes'];

                        $sheet->cell('A'.$i, 'Grand Total'); 
                        $sheet->cell('C'.$i, ($totalseats > 0) ? $totalseats:'=(0)' ); 
                        $sheet->cell('D'.$i, ($totalemale > 0) ? $totalemale:'=(0)' ); 
                        $sheet->cell('E'.$i, ($totalefemale > 0) ? $totalefemale:'=(0)' ); 
                        $sheet->cell('F'.$i, ($totaleother > 0) ? $totaleother:'=(0)' ); 
                        $sheet->cell('G'.$i, ($totaletotal > 0) ? $totaletotal:'=(0)' ); 

                        $sheet->cell('H'.$i, ($totalenri > 0) ? $totalenri:'=(0)' ); 
                        $sheet->cell('I'.$i, ($totaleser > 0) ? $totaleser:'=(0)' ); 
                        $sheet->cell('J'.$i, ($totalvmale > 0) ? $totalvmale:'=(0)' ); 
                        $sheet->cell('K'.$i, ($totalvfemale > 0) ? $totalvfemale:'=(0)' ); 
                        $sheet->cell('L'.$i, ($totalvother > 0) ? $totalvother:'=(0)' );
                        $sheet->cell('M'.$i, ($totalvpostal > 0) ? $totalvpostal:'=(0)' );
                        $sheet->cell('N'.$i, ($totalvtotal > 0) ? $totalvtotal:'=(0)' );
                        $sheet->cell('O'.$i, ($totalvnri > 0) ? $totalvnri:'=(0)' );

                        $sheet->cell('P'.$i, ($totalpoll > 0) ? $totalpoll:'=(0)' );
                        $sheet->cell('Q'.$i, ($totalrejectedvote > 0) ? $totalrejectedvote:'=(0)' );
                        $sheet->cell('R'.$i, ($totalnotretrived > 0) ? $totalnotretrived:'=(0)' );
                        $sheet->cell('S'.$i, ($totalnota > 0) ? $totalnota:'=(0)' );
                        $sheet->cell('T'.$i, ($totalvalidpollvotes > 0) ? $totalvalidpollvotes:'=(0)' );
                        $sheet->cell('U'.$i, ($totaltenderedvotes > 0) ? $totaltenderedvotes:'=(0)' );
                       
                
                    }
                });
             })->download('xls');

        }
	}
    public function performanceRegisteredUnrecognisedParty(Request $request){
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
        /*SELECT D.st_code,A.CCODE,A.PARTYNAME, A.PARTYTYPE,
		SUM(D.v_votes_evm_all) AS v_votes_evm_all, SUM(C.total_valid_vote) AS totalvotesparty, 
		SUM(D.e_all_t) AS TotalElectorsState, SUM(D.vt_all_t) AS TotalValidVotesState 
		FROM m_party AS A
		INNER JOIN candidate_nomination_detail AS B ON A.CCODE = B.party_id 
		INNER JOIN candidate_count_ic AS C ON C.con_cand_id = B.candidate_id 
		INNER JOIN t_pc_ic AS D ON D.st_code = B.st_code AND D.pc_no = B.pc_no 
		INNER JOIN (SELECT MAX(id) AS id, MAX(created_at) AS created_at FROM t_pc_ic GROUP BY  st_code ORDER BY created_at DESC) AS DD ON D.id = DD.id
		WHERE (B.cand_party_type = 'U' AND B.application_status = 6) 
		GROUP BY A.CCODE, D.st_code*/
        // DB::enableQueryLog();
            $sSelect = array(
                'D.st_code',
                'A.CCODE',
                'A.PARTYNAME',
                'A.PARTYTYPE',
                DB::raw('SUM(D.v_votes_evm_all) AS v_votes_evm_all'),
                DB::raw('SUM(C.total_valid_vote) AS totalvotesparty'),
                DB::raw('SUM(D.e_all_t) AS TotalElectorsState'),
                DB::raw('SUM(D.vt_all_t) AS TotalValidVotesState')
            );
            $sTable = 'm_party AS A';
            $sWhere = array(
                'B.cand_party_type' => 'U',
                'B.application_status' => 6,
            );
            $sGroup = array(
                'A.CCODE',
                'D.st_code'
            );
            $result1 = DB::table($sTable)
                        ->select($sSelect)
                        ->join('candidate_nomination_detail AS B','A.CCODE','B.party_id')
                        ->join('candidate_count_ic AS C','C.con_cand_id','B.candidate_id')
                        ->join('t_pc_ic AS D', function($join){
                            $join->on('D.st_code', 'B.st_code')
                                ->on('D.pc_no', 'B.pc_no');
                        })
                        ->join(DB::raw('(SELECT MAX(id) as id, MAX(created_at) as created_at FROM t_pc_ic GROUP BY  st_code ORDER BY created_at DESC) AS DD'), 'D.id', 'DD.id')
                        ->where($sWhere)
                        ->groupBy($sGroup)
                        ->get()->toArray();
            /*SELECT G.st_code, H.CCODE, H.PARTYNAME, Counted  FROM candidate_nomination_detail AS G
			JOIN m_party AS H ON G.party_id = H.CCODE
			join (SELECT party_id,st_code, Count(Nom_id) AS Counted FROM candidate_nomination_detail GROUP by party_id, st_code) AS GG on G.party_id = GG.party_id AND G.st_code = GG.st_code
			GROUP BY H.CCODE*/
			$result2 = DB::table('candidate_nomination_detail AS G')
						->select(array('G.st_code', 'H.CCODE', 'H.PARTYNAME', 'Counted'))
						->join('m_party AS H', 'G.party_id', 'H.CCODE')
						->join(DB::raw('(SELECT party_id,st_code, Count(Nom_id) AS Counted FROM candidate_nomination_detail GROUP by party_id, st_code) AS GG'), function($gJoin){
							$gJoin->on('G.party_id','GG.party_id')
									->on('G.st_code','GG.st_code');
						})
						->where(array(
							'G.cand_party_type' => 'U',
                			'G.application_status' => 6,
						))
						->groupby('H.ccode')
						->get()->toArray();

        //     $queue = DB::getQueryLog();
        // echo "<pre>"; print_r($result1);
        // echo "<pre>"; print_r($result2); die;
        foreach ($result1 as $value1) {
        	foreach ($result2 as $value2) {
        		if(($value1->st_code == $value2->st_code) && ($value1->CCODE == $value2->CCODE)){
	        		$result[] = array(
	        			'PARTYNAME' => $value1->PARTYNAME,
	        			'c_nom_co_t' => $value2->Counted,
	        			'totalvotesparty' => $value1->totalvotesparty,
	        			'TotalElectorsState' => $value1->TotalElectorsState,
	        			'v_votes_evm_all' => $value1->v_votes_evm_all
	        		);
        		}
        	}
        }
        $result = json_decode(json_encode($result));
        // echo "<pre>"; print_r($result); die;
            if($request->path() == 'pcceo/perRegUnPartyView'){
                return view('IndexCardReports.StatisticalReports.Vol1.performance_of_registered_unrecognised_parties_view', compact('session','result','user_data','sched'));
            }elseif($request->path() == 'pcceo/perRegUnPartyPdf'){
                $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.performance_of_registered_unrecognised_parties_pdf',[
                'session'=>$session,
                'result'=>$result
            ]);
            return $pdf->download('Performance_of_registered_unrecognised_parties.pdf');
                
            }else{
                die('No Data Found');
            }
    }
    public function participationofwomenelectorsinpoll(Request $request){
        $session = $request->session()->all();
        /*SELECT B.ST_NAME, COUNT(A.pc_no) AS TotalSeats FROM m_pc AS A
        JOIN m_state AS B ON B.ST_CODE = A.st_code
        GROUP BY A.st_code


        SELECT B.st_name, SUM(e_all_t) AS totElec, SUM(C.e_all_t_f) AS totFemaleElec, 
        ROUND(((SUM(C.e_all_t_f)/SUM(e_all_t))*100),2) AS pertotelec, 
        SUM(C.vt_all_t) AS totVoters, SUM(C.vt_f_t) AS femVoters,
        ROUND(((SUM(C.vt_all_t)/SUM(C.vt_f_t))*100),2) AS pertotvoter,
        ROUND(((SUM(C.vt_all_t)/SUM(e_all_t))*100),2) AS pollper
        FROM m_pc AS A
        JOIN m_state AS B ON B.ST_CODE = A.st_code
        JOIN t_pc_ic AS C ON A.st_code = C.st_code AND A.pc_no = C.pc_no
        GROUP BY C.st_code*/
        $sSelect = array(
            'B.ST_NAME',
            DB::raw('COUNT(A.PC_NO) AS TotalSeats')
        );
        $sTable = 'm_pc AS A';
        $sGroup = array(
            'A.st_code'
        );
        $countSeats = DB::table($sTable)
                        ->select($sSelect)
                        ->join('m_state AS B','B.ST_CODE','A.st_code')
                        ->groupBy($sGroup)
                        ->get()->toArray();
        // echo "<pre>"; print_r($countSeats);

        $mSelect = array(
            'B.st_name',
            DB::raw('SUM(e_all_t) AS totElec, SUM(C.e_all_t_f) AS totFemaleElec'),
            DB::raw('ROUND(((SUM(C.e_all_t_f)/SUM(e_all_t))*100),2) AS pertotelec'),
            DB::raw('SUM(C.vt_all_t) AS totVoters, SUM(C.vt_f_t) AS femVoters'),
            DB::raw('ROUND(((SUM(C.vt_f_t)/SUM(C.e_all_t_f))*100),2) AS femVoterByFemElec'),
            DB::raw('ROUND(((SUM(C.vt_all_t)/SUM(C.vt_f_t))*100),2) AS pertotvoter'),
            DB::raw('ROUND(((SUM(C.vt_all_t)/SUM(e_all_t))*100),2) AS pollper')
        );
        $mTable = 'm_pc AS A';
        $mGroup = array(
            'C.st_code'
        );
        $perWomen = DB::table($mTable)
                        ->select($mSelect)
                        ->join('m_state AS B','B.ST_CODE','A.st_code')
                        ->join('t_pc_ic AS C', function($join){
                            $join->on('A.st_code','C.st_code')
                                    ->on('A.pc_no','C.pc_no');
                        })
                        ->groupBy($mGroup)
                        ->get()->toArray();
        // echo "<pre>"; print_r($perWomen); die;
        $aaData = array();
        foreach ($countSeats as $value) {
            foreach ($perWomen as $key) {
                if($value->ST_NAME == $key->st_name){
                    $aaData[$value->ST_NAME] = (object)array(
                        'st_name'           => $key->st_name,
                        'totalSeats'        => $value->TotalSeats,
                        'totElec'           => $key->totElec,
                        'totFemaleElec'     => $key->totFemaleElec,
                        'pertotelec'        => $key->pertotelec,
                        'totVoters'         => $key->totVoters,
                        'femVoters'         => $key->femVoters,
                        'femVoterByFemElec' => $key->femVoterByFemElec,
                        'pertotvoter'       => $key->pertotvoter,
                        'pollper'           => $key->pollper
                    );                    
                }
            }
        }
        $aaData = (object)$aaData;
        // echo "<pre>"; print_r($aaData); die;
        if($request->path() == 'perWomenPartView'){
                return view('StatisticalReports.Vol1.performance_of_women_electors_in_poll_view', compact('session','aaData'));
            }elseif($request->path() == 'perWomenPartPdf'){
                $pdf=PDF::loadView('StatisticalReports.Vol1.performance_of_women_electors_in_poll_pdf',[
                'session'=>$session,
                'aaData'=>$aaData
            ]);
            return $pdf->download('Performance_of_women_electors_in_poll.pdf');
                
            }else{
                die('No Data Found');
            }
    }
	
	public function getParticipationofWomenInNationalParties(Request $request)
    {
		// session data
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
        //echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
	   //end session data
	  
		DB::enableQueryLog();
		
		//$year=$session['election_detail']['year'];
		//$stcode=$session['admin_login_details']['ST_CODE'];
		//$stname=$session['election_detail']['st_name'];
		/*SELECT A.PARTYNAME, A.PARTYTYPE,SUM(C.total_valid_vote) as totalvote,count(B.candidate_id) as totalcontested, 
		ROUND(((SUM(F.vt_f_t)/SUM(F.e_all_t_f))*100),2) AS overtotalelectors,
		ROUND(((SUM(C.total_valid_vote)/SUM(F.total_valid_votes))*100),2) AS overtotalvalidvotes
		FROM `m_party` AS A 
		join candidate_nomination_detail AS B on A.CCODE=B.party_id
		join candidate_count_ic AS C on C.con_cand_id=B.candidate_id
		join candidate_personal_detail AS E on E.candidate_id=B.candidate_id 
		JOIN t_pc_ic AS F on C.st_code=F.st_code and C.pc_no=F.pc_no
		WHERE A.PARTYTYPE='N' and B.application_status=6  and E.cand_gender='female'GROUP BY A.PARTYNAME*/
		$rows = array(
			'A.PARTYNAME',
			'A.PARTYTYPE',                
			DB::raw('SUM(C.total_valid_vote) as totalvote'),
            DB::raw('count(B.candidate_id) as totalcontested'),
			DB::raw('count(D.candidate_id) as totalwon'),
			DB::raw('ROUND(((SUM(F.vt_f_t)/SUM(F.e_all_t_f))*100),2) AS overtotalelectors'),
			//DB::raw('ROUND(((SUM(C.total_valid_vote)/SUM(F.total_valid_votes))*100),2) AS overtotalvalidvotes'),
			 DB::raw('ROUND(((C.total_valid_vote/SUM(F.total_valid_votes))*100),2) AS overtotalvalidvotes'),
			'A.CCODE'
			);

            $where=array(
                'A.PARTYTYPE' =>'N',
                'B.application_status' => 6,
                'E.cand_gender' => 'female'
            );
            $data = DB::table('m_party AS A')
               ->select($rows)
              ->join('candidate_nomination_detail AS B','A.CCODE','B.party_id')
              ->join('candidate_count_ic AS C', 'C.con_cand_id','B.candidate_id')
              ->join('candidate_personal_detail AS E','E.candidate_id','B.candidate_id')
              ->join('winning_leading_candidate AS D','B.st_code','D.st_code')
              ->join('t_pc_ic AS F',function($join){
                                $join->on('C.pc_no','F.pc_no')
                                    ->on('C.st_code','F.st_code');
                            })
               ->where($where)
               ->groupBy('A.CCODE')               
               ->get()->toArray();

             // echo "<pre>"; print_r($data); //die;
             $vsselect = array(
                        'A.party_id',                        
                         DB::raw('SUM(B.total_valid_vote) as totalvote'));

             /*select A.party_id, SUM(B.total_valid_vote) as totalvote from candidate_nomination_detail AS A join candidate_count_ic AS B on A.candidate_id=B.con_cand_id GROUP By A.party_id*/
            $votessecuredbyparty = DB::table('candidate_nomination_detail AS A')
                                    ->select($vsselect)
                                    ->join('candidate_count_ic AS B','A.candidate_id','B.con_cand_id')
                                    ->groupby('A.party_id')
                                    ->get()->toArray();


            $sData = array();
                    // echo "<pre>"; print_r($votessecuredbyparty); die;
            foreach ($data as $key){
                foreach ($votessecuredbyparty as $value){
                    if($key->CCODE == $value->party_id){
                        $sData[] = array(
                            'PARTYNAME' => $key->PARTYNAME,
                            'PARTYTYPE' => $key->PARTYTYPE,
                            'totalvote' => $key->totalvote,
                            'totalcontested' => $key->totalcontested,
                            'totalwon' => $key->totalwon,
                            'overtotalelectors' => $key->overtotalelectors,
                            'overtotalvalidvotes' => $key->overtotalvalidvotes,
                            'ovsbp' => ($key->overtotalvalidvotes!=0 && $key->totalvote!=0)?(($key->totalvote/$key->overtotalvalidvotes)*100):0
                        );
                    }
                }
            }
            // echo "<pre>"; print_r($sData); //die;
            $sData = json_decode(json_encode($sData));
            if($request->path() == 'pcceo/ParticipationofWomenInNationalParties'){
                return view('IndexCardReports.StatisticalReports.Vol1.participation-of-women-in-national-parties',compact('sData','user_data','sched'));
            }elseif($request->path() == 'pcceo/ParticipationofWomenInNationalPartiesPDF'){
                 $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.participation-of-women-in-national-parties-pdf',[            
                'sData'=>$sData,
    			'user_data'=>$user_data,
    			'sched'=>$sched]);
            return $pdf->download('participation-of-Women-in-national-parties.pdf');
            }elseif ($request->path() == 'pcceo/ParticipationofWomenInNationalPartiesXls') {
                
                        $data = json_decode( json_encode($sData), true);             
        
                     return Excel::create('ParticipationofWomenInNationalPartiesXls', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:H1');
                        $sheet->cells('A1:H1', function($cells) {
                            $cells->setFont(array(
                                'size'       => '15',
                                'bold'       => true
                            ));
                            $cells->setAlignment('center');
                        });
                       $sheet->cell('A1', function($cells) {
                            $cells->setValue('Participation of Women In National Parties');
                        });

                         $sheet->mergeCells('A2:A3');
                        $sheet->cell('A2', function($cells) {
                            $cells->setValue('Party Name');
                        });

                       $sheet->cell('B2', function($cells) {
                            $cells->setValue('Candidates');
                        });
                        
                        $sheet->cell('C2', function($cells) {
                            $cells->setValue('Percentage');
                        });

                        $sheet->mergeCells('C2:D2');

                         $sheet->mergeCells('E2:E3');

                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('Votes Secured By Women Candidates');
                        });
                        $sheet->mergeCells('F2:H2');

                        $sheet->cell('F2', function($cells) {
                            $cells->setValue('% of votes secured');
                        });


                        $sheet->cell('B3', function($cells) {
                            $cells->setValue('Contested');
                        });
                        $sheet->cell('C3', function($cells) {
                            $cells->setValue('Won');
                        });
                         $sheet->cell('D3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('F3', function($cells) {
                            $cells->setValue('Over total electors in the State');
                        });
                        $sheet->cell('G3', function($cells) {
                            $cells->setValue('Over total valid votes in the State');
                        });

                        $sheet->cell('H3', function($cells) {
                            $cells->setValue('Over Votes secured by the party in State');
                        });
                        
                         if (!empty($data)) {
                             $i= 4;
                               $totalallcontested = $totalallwon = $totalwon = $totaldf = $totalvsecuredbyf = $totalelectors = $totalvalidvotes = $overvotessecuredbyparty = $tvsbp = 0;
                             //echo "<pre>";print_r($data);die;
                            foreach ($data as $row) {   
                                $totalallcontested+=$row['totalcontested'];
                                $totalallwon+=$row['totalwon'];
                                $totalvsecuredbyf+=$row['totalvote'];
                                $totalelectors+=$row['overtotalelectors'];
                                $totalvalidvotes+=$row['overtotalvalidvotes'];
                                $tvsbp+=$row['ovsbp'];
                                
                                    $sheet->cell('A'.$i, $row['PARTYNAME']); 
                                    $sheet->cell('B'.$i, ($row['totalcontested']) ? $row['totalcontested'] : '=(0)'); 
                                    $sheet->cell('C'.$i, ($row['totalwon']) ? $row['totalwon'] : '=(0)');
                                    $sheet->cell('D'.$i, 'N/A'); 
                                    $sheet->cell('E'.$i, ($row['totalvote']) ? $row['totalvote'] : '=(0)'); 
                                    $sheet->cell('F'.$i, ($row['overtotalelectors']) ? $row['overtotalelectors'] : '=(0)');

                                    $sheet->cell('G'.$i, ($row['overtotalvalidvotes']) ? $row['overtotalvalidvotes'] : '=(0)'); 
                                    $sheet->cell('H'.$i, ($row['ovsbp']) ? round($row['ovsbp'],2) : '=(0)'); 
                                    
                                      $i++;  
                                  }   $i +=1;

                                    $sheet->cell('A'.$i, 'Total'); 
                                    $sheet->cell('B'.$i, ($totalallcontested > 0) ? $totalallcontested:'=(0)' ); 
                                    $sheet->cell('C'.$i, ($totalallwon > 0) ? $totalallwon:'=(0)' ); 
                                    $sheet->cell('D'.$i, 'N/A'); 
                                    $sheet->cell('E'.$i, ($totalvsecuredbyf > 0) ? $totalvsecuredbyf:'=(0)' ); 
                                    $sheet->cell('F'.$i, ($totalelectors > 0) ? $totalelectors:'=(0)' ); 
                                    $sheet->cell('G'.$i, ($totalvalidvotes > 0) ? $totalvalidvotes:'=(0)' ); 
                                    $sheet->cell('H'.$i, ($tvsbp > 0) ? round($tvsbp,2):'=(0)' ); 
                                    
                            
                        }
                    });
                })->download('xls');

        }else{
            echo "Result not found";
        }

        
    }
    public function participationofwomeninstateparties(Request $request){
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
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
        /*SELECT * FROM m_party AS A
        JOIN candidate_nomination_detail AS B ON A.CCODE = B.party_id
        JOIN candidate_count_ic AS C ON B.candidate_id = C.con_cand_id
        JOIN (SELECT MAX(update_id) AS update_id FROM candidate_count_ic LIMIT 1) AS CC ON C.update_id = CC.update_id
        JOIN candidate_personal_detail AS D ON B.candidate_id = D.candidate_id
        JOIN m_state AS E ON B.st_code = E.ST_CODE
        JOIN (
            SELECT AA.party_id, SUM(BB.total_valid_vote) AS totalvote 
            FROM candidate_nomination_detail AS AA 
            JOIN candidate_count_ic AS BB ON AA.candidate_id=BB.con_cand_id 
            GROUP BY AA.party_id
        ) AS F ON A.CCODE = F.party_id
        JOIN t_pc_ic AS G ON B.st_code = G.st_code
        JOIN (SELECT MAX(created_at) AS created_at FROM t_pc_ic ORDER BY created_at DESC LIMIT 1) AS GG ON G.created_at = GG.created_at
        WHERE D.cand_gender = 'female' AND A.PARTYTYPE = 'S'
        GROUP BY A.CCODE*/
        $sSelect = array(
            'E.ST_CODE',
            'E.st_name',
            'A.PARTYNAME',
            DB::raw('COUNT(Distinct(B.candidate_id)) AS contested'),
            DB::raw('SUM(C.total_valid_vote) AS votes_secured'),
            'F.totalvote',
            DB::raw('ROUND(((F.totalvote/G.e_all_t)*100),2) AS OverTotElec'),
            DB::raw('ROUND(((F.totalvote/G.vt_all_t)*100),2) AS OverTotVot'),
            DB::raw('ROUND(((F.totalvote/(SUM(C.total_valid_vote)))*100),2) AS OverStVotes')
        );
        $sWhere = array(
            'D.cand_gender' => 'female',
            'A.PARTYTYPE' => 'S'
        );
        $sGroup = array(
            'A.CCODE'
        );
        $dJoin = '(
            SELECT AA.party_id, SUM(BB.total_valid_vote) AS totalvote 
            FROM candidate_nomination_detail AS AA 
            JOIN candidate_count_ic AS BB ON AA.candidate_id=BB.con_cand_id 
            GROUP BY AA.party_id
        ) AS F';
        $sTable = 'm_party AS A';
        $sResult = DB::table($sTable)
                        ->select($sSelect)
                        ->join('candidate_nomination_detail AS B','A.CCODE','B.party_id')
                        ->join('candidate_count_ic AS C','B.candidate_id','C.con_cand_id')
                        ->join(DB::raw('(Select MAX(update_id) AS update_id from candidate_count_ic limit 1) AS CC'), 'C.update_id', 'CC.update_id')
                        ->join('candidate_personal_detail AS D','B.candidate_id','D.candidate_id')
                        ->join('m_state AS E','B.st_code','E.ST_CODE')
                        ->join(DB::raw($dJoin),'A.CCODE','F.party_id')
                        ->join('t_pc_ic AS G','B.st_code','G.st_code')
                        ->where($sWhere)
                        ->groupBy($sGroup)
                        ->get()->toArray();
        $aaData = array();
        foreach ($sResult as $value) {
            $aaData[$value->PARTYNAME][$value->st_name] = array(
                'contested'         => ($value->contested)?$value->contested:0,
                'votes_secured'     => ($value->votes_secured)?$value->votes_secured:0,
                'totalvote'         => ($value->totalvote)?$value->totalvote:0,
                'OverTotElec'       => ($value->OverTotElec)?$value->OverTotElec:0,
                'OverTotVot'        => ($value->OverTotVot)?$value->OverTotVot:0,
                'OverStVotes'       => ($value->OverStVotes)?$value->OverStVotes:0
            );
        }
        $aaData = json_decode(json_encode($aaData));
        // echo "<pre>"; print_r($aaData); die;
        if($request->path() == 'pcceo/perWomenStatePartView'){
                return view('IndexCardReports.StatisticalReports.Vol1.performance_of_women_candidate_party_wise_view', compact('session','aaData','user_data'));
            }elseif($request->path() == 'pcceo/perWomenStatePartPdf'){
                $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.performance_of_women_candidate_party_wise_pdf', [
                    'session'=>$session,
                    'aaData'=>$aaData,
                    'user_data'=>$user_data
                ]);
            return $pdf->download('Performance_of_women_candidate_party_wise.pdf');
                
            }else{
                die('No Data Found');
            }
    }
	
	
    //getParticipationofWomenAsIndependentCandidates

    public function getParticipationofWomenAsIndependentCandidates(Request $request){

   dd("Hello");

    $user = Auth::user();
    $user_data = $user;
     //end session data
    
    DB::enableQueryLog();
    $data=DB::select("  SELECT *,
        (SELECT contested FROM (
        SELECT COUNT(cp.candidate_id) AS 'contested',cp.party_id
        FROM m_party m JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        LEFT JOIN winning_leading_candidate wlc
        ON wlc.candidate_id = cp.candidate_id
        AND m.ccode= wlc.lead_cand_partyid
        WHERE partytype ='Z'
        AND cand_gender = 'female'
        GROUP BY partyabbre,party_id)BB
         WHERE BB.PARTY_ID=TEMP.party_id)AS contested,
        (SELECT won
        FROM
        (SELECT COUNT(lead_total_vote) AS 'won',cp.party_id
        FROM m_party m JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        LEFT JOIN winning_leading_candidate wlc
        ON wlc.candidate_id = cp.candidate_id
        AND m.ccode= wlc.lead_cand_partyid
        WHERE partytype ='Z'
        AND cand_gender = 'female'
        GROUP BY partyabbre) CC
        WHERE CC.PARTY_ID=TEMP.party_id)AS WON,
        (SELECT SUM(df) FROM (
        SELECT lead_total_vote,partyabbre,cpd.candidate_id,cp.candidate_name,cp.party_id,
        CASE WHEN SUM(cp1.total_vote)/6 > cp.total_vote THEN 1 ELSE 0 END AS 'DF' FROM m_party m
        JOIN counting_pcmaster cp ON m.ccode= cp.party_id
        JOIN counting_pcmaster cp1
        ON cp.st_code = cp1.st_code
        AND cp.pc_no = cp1.pc_no
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        LEFT JOIN winning_leading_candidate wlc
        ON wlc.candidate_id = cp.candidate_id
        AND m.ccode= wlc.lead_cand_partyid
        WHERE partytype ='Z'
        AND cand_gender = 'female'
        AND lead_total_vote IS NULL
        GROUP BY cp.candidate_id,cp1.st_code, cp1.pc_no
        ) DD WHERE DD.party_id=TEMP.party_id) AS DF,
        (SELECT Total_electros_female
        FROM (
        SELECT partyabbre, party_id,PARTYNAME,SUM(electors_female) AS Total_electros_female
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
        WHERE partytype ='Z' AND cdac.year = 2019
        AND cand_gender = 'female'
        GROUP BY partyabbre )EEE WHERE EEE.party_id=TEMP.party_id) AS Total_electros_female,
        ( SELECT electrols_Total
        FROM (
        SELECT partyabbre, party_id,PARTYNAME,SUM(electors_total) AS electrols_Total
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
        WHERE partytype ='Z' AND cdac.year = 2019
        AND cand_gender = 'female'
        GROUP BY partyabbre ) FFF WHERE FFF.party_id=TEMP.party_id) AS electrols_Total,

        (SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
        WHERE party_id=TEMP.party_id GROUP BY party_id)AS totalvalid_valid_vote,

        (SELECT SUM(electors_total)AS totaleelctors FROM electors_cdac
        WHERE party_id=TEMP.party_id AND `year` = 2019 GROUP BY PARTY_ID )AS sum_of_total_eelctors,
        (SELECT SUM(total_vote) FROM counting_pcmaster ) AS OVER_ALL_TOTAL_VOTE
        FROM
        (
        SELECT partyabbre, party_id,PARTYNAME,SUM(total_vote) AS votes_secured_by_Women
        FROM m_party m
        JOIN counting_pcmaster cp
        ON m.ccode= cp.party_id
        JOIN candidate_personal_detail cpd
        ON cpd.candidate_id = cp.candidate_id
        WHERE partytype ='Z'
        AND cand_gender = 'female'
        GROUP BY partyabbre
        )TEMP");
        
        
        if($user->designation == 'ROPC'){
            $prefix     = 'ropc';
        }else if($user->designation == 'CEO'){  
            $prefix     = 'pcceo';
        }else if($user->role_id == '27'){
            $prefix     = 'eci-index';
        }else if($user->role_id == '7'){
            $prefix     = 'eci';
        }
        
        
        
        
            echo "<pre>"; print_r($data); die;
            if($request->path() == "$prefix/ParticipationofWomenAsIndependentCandidates"){
                return view('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-as-independent-candidate',compact('data','user_data'));
            }elseif($request->path() == "$prefix/ParticipationofWomenAsIndependentCandidatesPDF"){
                $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-as-independent-candidate-pdf',[            
                'data'=>$data,
                'user_data'=>$user_data
          ]);
           return $pdf->download('participation-of-women-in-registered-parties.pdf');
           }elseif($request->path() == "$prefix/ParticipationofWomenAsIndependentCandidatesXls"){
              $data = json_decode( json_encode($data), true);  
             //echo'<pre>'; print_r($data);die;               
        
                     return Excel::create('ParticipationofWomenInRegisteredPartiesXls', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:I1');
                        $sheet->cells('A1:I1', function($cells) {
                            $cells->setFont(array(
                                'size'       => '15',
                                'bold'       => true
                            ));
                            $cells->setAlignment('center');
                        });
                       $sheet->cell('A1', function($cells) {
                            $cells->setValue('28 - Participation of Women in Registered (Unrecognised) Parties');
                        });


                        $sheet->mergeCells('B1:D2');

                       $sheet->cell('B2', function($cells) {
                            $cells->setValue('Candidates');
                        });

                        $sheet->mergeCells('E2:F2');

                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('Percentage');
                        });

                         $sheet->mergeCells('H2:I2');
                        $sheet->cell('H2', function($cells) {
                            $cells->setValue('% of Votes Secured');
                        });
                        $sheet->cell('A3', function($cells) {
                            $cells->setValue('Party Name');
                        });

                        $sheet->cell('B3', function($cells) {
                            $cells->setValue('Contested');
                        });
                         $sheet->cell('C3', function($cells) {
                            $cells->setValue('Won');
                        });
                        $sheet->cell('D3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('E3', function($cells) {
                            $cells->setValue('Won');
                        });

                        $sheet->cell('F3', function($cells) {
                            $cells->setValue('DF');
                        });
                        $sheet->cell('G3', function($cells) {
                            $cells->setValue('Votes Secured By Women Candidates');
                        });
                        $sheet->cell('H3', function($cells) {
                            $cells->setValue('Over Total Electors In Country');
                        });
                         $sheet->cell('I3', function($cells) {
                            $cells->setValue('Over Total Valid Votes In Country');
                        });
                        
                         if (!empty($data)){
                             $i= 4;
                                $totalcontested = $twon = $won= $fd =  $secure = $electorspercent = $overtotalvaliedpercent = $ovsbp= $tfd = $totalVoteSecured = $totalElectors  = $tvv = 0;
                             //echo "<pre>";print_r($data);die;
                            foreach ($data as $row) {   
                                $totalcontested+=$row['contested'];
                                $twon+=$row['WON'];
                                $tfd+=$row['DF'];
                                $twonper=round(($twon/$totalcontested),2);
                                $tdfper=round(($tfd/$totalcontested),2);
                                $totalVoteSecured+=$row['votes_secured_by_Women'];
                                $totalElectors+=$row['electrols_Total'];
                                $ttotalElectors=($totalVoteSecured/$totalElectors)*100;
                                $totvv=($totalVoteSecured/$row['OVER_ALL_TOTAL_VOTE'])*100;
                                $tvv+=$row['totalvalid_valid_vote'];
                                $totvsp=($totalVoteSecured/$tvv)*100;

                                //
                                $peroverelectors = ($row['votes_secured_by_Women']/$row['electrols_Total'])*100;

                                $overTotalValidVotes = ($row['votes_secured_by_Women']/$row['OVER_ALL_TOTAL_VOTE'])*100;

                                $ovsbp = ($row['votes_secured_by_Women']/$row['totalvalid_valid_vote'])*100;
                                
                                    $sheet->cell('A'.$i, $row['partyabbre']); 
                                    $sheet->cell('B'.$i, ($row['contested']) ? $row['contested'] : '=(0)'); 
                                    $sheet->cell('C'.$i, ($row['WON']) ? $row['contested'] : '=(0)');
                                    $sheet->cell('D'.$i, ($row['DF']) ? $row['contested'] : '=(0)'); 
                                    $sheet->cell('E'.$i, round((($row['WON']/$row['contested'])*100),2)); 
                                    $sheet->cell('F'.$i, round((($row['DF']/$row['contested'])*100),2)); 
                                    $sheet->cell('G'.$i, ($row['votes_secured_by_Women']) ? $row['votes_secured_by_Women'] : '=(0)'); 
                                    $sheet->cell('H'.$i, round($peroverelectors,2));

                                    $sheet->cell('I'.$i, round($overTotalValidVotes,2)); 
                                    $sheet->cell('J'.$i,round($ovsbp,2)); 
                                    
                                      $i++;  
                                  }   $i +=1;

                                    $sheet->cell('A'.$i, 'Total'); 
                                    $sheet->cell('B'.$i, ($totalcontested > 0) ? $totalcontested:'=(0)' ); 
                                    $sheet->cell('C'.$i, ($twon > 0) ? $twon:'=(0)' ); 
                                    $sheet->cell('D'.$i, ($tfd > 0) ? $tfd:'=(0)' );
                                      
                                    $sheet->cell('E'.$i, ($twonper > 0) ? $twonper:'=(0)' ); 
                                    $sheet->cell('F'.$i, $tdfper); 
                                    $sheet->cell('G'.$i, ($totalVoteSecured > 0) ? $totalVoteSecured:'=(0)' ); 
                                    $sheet->cell('H'.$i, round($ttotalElectors,2)); 
                                    $sheet->cell('I'.$i, round($totvv,2)); 
                                    $sheet->cell('J'.$i, round($totvsp,2));
                                 }
                    });
                })->download('xls');

        }else{
            echo "Result not found";
        }        
    }
    


     //getParticipationofWomenInRegisteredParties

     public function getParticipationofWomenInRegisteredParties(Request $request){
		 // session data
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
        //echo "<pre>"; print_r($session); die;
		$election_detail = $session['election_detail'];
		$user_data = $d;
	   //end session data
        DB::enableQueryLog();
       // $session = $request->session()->all();
        //$year=$session['election_detail']['year'];
        $stcode=$session['election_detail']['st_code'];
        $stname=$session['election_detail']['st_name'];
        /*select `A`.`PARTYNAME`, `A`.`PARTYTYPE`, count(B.candidate_id) as totalcontested, count(D.candidate_id) as totalwon, SUM(C.total_valid_vote) as totalvotesecured, ROUND(((SUM(F.total_votes_polled)/SUM(F.e_all_t))*100),2) AS overtotalelectors, ROUND(((SUM(C.total_valid_vote)/SUM(F.total_valid_votes))*100),2) AS overtotalvalidvotes, ROUND(((SUM(F.e_all_t_f)/SUM(F.e_all_t))*100),2) AS securedbyparties, `A`.`CCODE` from `m_party` as `A` inner join `candidate_nomination_detail` as `B` on `A`.`CCODE` = `B`.`party_id` inner join `candidate_count_ic` as `C` on `C`.`con_cand_id` = `B`.`candidate_id` inner join `winning_leading_candidate` as `D` on `B`.`st_code` = `D`.`st_code` inner join `candidate_personal_detail` as `E` on `E`.`candidate_id` = `B`.`candidate_id` inner join `t_pc_ic` as `F` on `C`.`pc_no` = `F`.`pc_no` and `C`.`st_code` = `F`.`st_code` inner join (SELECT MAX(id) AS id,MAX(created_at) AS created_at FROM t_pc_ic     GROUP BY st_code, pc_no) AS G on `F`.`id` = `G`.`id` where (`A`.`PARTYTYPE` = 'U' and `B`.`application_status` = 6 and `E`.`cand_gender` = 'female' and `F`.`st_code` = 'S01') group by `A`.`CCODE`*/
        $rows = array(
            'A.PARTYNAME',
            'A.PARTYTYPE',                
            DB::raw('count(B.candidate_id) as totalcontested'),
            DB::raw('count(D.candidate_id) as totalwon'),
            DB::raw('SUM(C.total_valid_vote) as totalvotesecured'),
            DB::raw('ROUND(((SUM(F.total_votes_polled)/SUM(F.e_all_t))*100),2) AS overtotalelectors'),
            DB::raw('ROUND(((C.total_valid_vote/SUM(F.total_valid_votes))*100),2) AS overtotalvalidvotes'),
            DB::raw('ROUND(((SUM(F.e_all_t_f)/SUM(F.e_all_t))*100),2) AS securedbyparties'),
            'A.CCODE'
            );

        $where=array(
            'A.PARTYTYPE' =>'U',
            'B.application_status' => 6,
            'E.cand_gender' => 'female',
            'F.st_code' => $stcode);
        $data = DB::table('m_party AS A')
                  ->select($rows)
                  ->join('candidate_nomination_detail AS B','A.CCODE','B.party_id')
                  ->join('candidate_count_ic AS C', 'C.con_cand_id','B.candidate_id')
                  ->join('winning_leading_candidate AS D','B.st_code','D.st_code')
                  ->join('candidate_personal_detail AS E','E.candidate_id','B.candidate_id')
                  ->join('t_pc_ic AS F',function($join){
                        $join->on('C.pc_no','F.pc_no')
                            ->on('C.st_code','F.st_code');
                                })
                  ->join(DB::raw('(SELECT MAX(id) AS id,MAX(created_at) AS created_at FROM t_pc_ic 	GROUP BY st_code, pc_no) AS G'), 'F.id', 'G.id')
                   ->where($where)
                   ->groupBy('A.CCODE')               
                   ->get()->toArray();
               
        $queue = DB::getQueryLog();
        //echo'<pre>'; print_r($queue);die;                  
       // dd($data);
            
            
        if($request->path() == 'pcceo/ParticipationofWomenInRegisteredParties'){
            return view('IndexCardReports.StatisticalReports.Vol1.participation-of-women-in-registered-parties',compact('data','stname','user_data','sched'));
        }elseif($request->path() == 'pcceo/ParticipationofWomenInRegisteredPartiesPDF'){
            $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol1.participation-of-women-in-registered-parties-pdf',[            
            'data'=>$data,
            'stname'=>$stname,
			'user_data'=>$user_data,
			'sched' =>$sched]);
        return $pdf->download('participation-of-women-in-registered-parties.pdf');
        }elseif($request->path() == 'pcceo/ParticipationofWomenInRegisteredPartiesPDF'){
         $data = json_decode( json_encode($data), true);  
             //echo'<pre>'; print_r($data);die;               
        
                     return Excel::create('Participation-of-Women-As-Independent-Candidates', function($excel) use ($data) {
                         $excel->sheet('mySheet', function($sheet) use ($data)
                         {
                        $sheet->mergeCells('A1:I1');
                        $sheet->cells('A1:I1', function($cells) {
                            $cells->setFont(array(
                                'size'       => '15',
                                'bold'       => true
                            ));
                            $cells->setAlignment('center');
                        });
                       $sheet->cell('A1', function($cells) {
                            $cells->setValue('Participation of Women as Individual Candidates');
                        });

                        $sheet->mergeCells('A2:A3');
                        $sheet->cell('A2', function($cells) {
                            $cells->setValue('Party Name');
                        });

                        $sheet->mergeCells('B2:D2');
                        $sheet->cell('B2', function($cells) {
                            $cells->setValue('Candidates');
                        });
                        $sheet->mergeCells('E2:F2');
                        $sheet->cell('E2', function($cells) {
                            $cells->setValue('Percentage');
                        });

                        $sheet->mergeCells('G2:G3');
                        $sheet->cell('G2', function($cells) {
                            $cells->setValue('Votes Secured By Women Candidates');
                        });
                        $sheet->mergeCells('H2:I2');

                        $sheet->cell('H2', function($cells) {
                            $cells->setValue('% of votes secured');
                        });


                        $sheet->cell('B3', function($cells) {
                            $cells->setValue('Contested');
                        });
                        $sheet->cell('C3', function($cells) {
                            $cells->setValue('Won');
                        });
                         $sheet->cell('D3', function($cells) {
                            $cells->setValue('DF');
                        });
                          $sheet->cell('E3', function($cells) {
                            $cells->setValue('Won');
                        });
                        $sheet->cell('F3', function($cells) {
                            $cells->setValue('DF');
                        });                      

                        $sheet->cell('H3', function($cells) {
                            $cells->setValue('Over Total Electors in Country');
                        });
                        $sheet->cell('I3', function($cells) {
                            $cells->setValue('Over Total Valid Votes in Country');
                        });
                        
                         if (!empty($data)) {
                             $i= 4;
                              $totalc = $totalallwon = $totalvs = $totaloe = $totalvv = $totalwonpercent = $ttwonper = 0;
                             //echo "<pre>";print_r($data);die;
                            foreach ($data as $row) {   
                                $totalc += ($row['totalcontested'])? $row['totalcontested']:0;
                                $totalallwon += ($row['totalwon'])?$row['totalwon']:0;
                                $totalvs += ($row['totalvotesecured'])?$row['totalvotesecured']:0;
                                $totaloe += ($row['overtotalelectors'])?$row['overtotalelectors']:0;
                                $totalvv += ($row['overtotalvalidvotes'])?$row['overtotalvalidvotes']:0;
                                $totalwonpercent =round((($row['totalwon']/$row['totalcontested'])*100),2);
                                $ttwonper +=$totalwonpercent;
                                
                                    $sheet->cell('A'.$i, $row['PARTYNAME']); 
                                    $sheet->cell('B'.$i, ($row['totalcontested']) ? $row['totalcontested'] : '=(0)'); 
                                    $sheet->cell('C'.$i, ($row['totalwon']) ? $row['totalwon'] : '=(0)');
                                    $sheet->cell('D'.$i, 'N/A'); 
                                    $sheet->cell('E'.$i, ($totalwonpercent) ? $totalwonpercent : '=(0)'); 
                                    $sheet->cell('F'.$i,'N/A');

                                    $sheet->cell('G'.$i, ($row['totalvotesecured']) ? $row['totalvotesecured'] : '=(0)'); 
                                    $sheet->cell('H'.$i, ($row['overtotalelectors']) ? round($row['overtotalelectors'],2) : '=(0)');
                                     $sheet->cell('I'.$i, ($row['overtotalvalidvotes']) ? round($row['overtotalvalidvotes'],2) : '=(0)'); 
                                    
                                      $i++;  
                                  }   $i +=1;
                                    $sheet->cells('A'.$i, function($cells) {
                                        $cells->setFont(array(
                                            'size'       => '12',
                                            'bold'       => true
                                        ));
                                        $cells->setAlignment('center');
                                    });

                                    $sheet->cell('A'.$i, 'Total'); 
                                    $sheet->cell('B'.$i, ($totalc > 0) ? $totalc:'=(0)' ); 
                                    $sheet->cell('C'.$i, ($totalallwon > 0) ? $totalallwon:'=(0)' ); 
                                    $sheet->cell('D'.$i, 'N/A'); 
                                    $sheet->cell('E'.$i, ($ttwonper > 0) ? $ttwonper:'=(0)' ); 
                                    $sheet->cell('F'.$i,'N/A'); 
                                    $sheet->cell('G'.$i, ($totalvs > 0) ? $totalvs:'=(0)' ); 
                                    $sheet->cell('H'.$i, ($totaloe > 0) ? round($totaloe,2):'=(0)' ); 
                                    $sheet->cell('I'.$i, ($totalvv > 0) ? round($totalvv,2):'=(0)' ); 
                        }
                    });
                })->download('xls');

        }
        else{
            echo "No Record Found!";
        }

    }


	
}
