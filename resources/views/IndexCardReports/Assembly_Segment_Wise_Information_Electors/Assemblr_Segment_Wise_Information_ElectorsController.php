<?php


namespace App\Http\Controllers\IndexCardReports\Assembly_Segment_Wise_Information_Electors;

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

        ini_set("memory_limit","1500M");
        set_time_limit('240');
        ini_set("pcre.backtrack_limit", "10000000");

class Assemblr_Segment_Wise_Information_ElectorsController extends Controller

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



  public function index(Request $request){
        //dd("hello");
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
    if($user->role_id == '7' || $user->role_id == '27'){



       $allstate= DB::table('m_state')
                  ->select('ST_NAME','ST_CODE')
                  ->orderby('ST_CODE')
                  ->get()->toArray();

      //dd($allstate);
      $datanew = array();
       foreach ($allstate as  $value) {
         # code...

        $data = DB::table('m_ac AS A')

                          ->select('C.st_code','C.st_name','B.PC_NO','A.ac_no','B.pc_name','A.ac_name',

                          DB::raw("SUM(E.total_vote) AS t_votes_evm"),

                          'D.gen_electors_male as maletotalelector',
                          'D.gen_electors_female as femaletotalelector',
                          'D.gen_electors_other as othertotalelector',


                          'D.nri_male_electors as malenrielector',
                          'D.nri_female_electors as femalenrielector',
                          'D.nri_third_electors as othernrielector',



                          'D.service_male_electors as maleserelector',
                          'D.service_female_electors as femaleserelector',
                          'D.service_third_electors as totalserelector'


                          )

                          ->join('m_pc AS B', function($join){
                                       $join->on('B.PC_NO','A.PC_NO')
                                           ->on('B.ST_CODE','A.ST_CODE');
                                   })
                          ->join("counting_master_".strtolower($value->ST_CODE)." as E",function($join){
                                        $join->on('A.ac_no','E.ac_no')
                                             ->on('B.PC_NO','E.pc_no');
                          })


                          ->join('electors_cdac as D',function($join){
                                        $join->on('A.ST_CODE','D.st_code')
                                             ->on('A.PC_NO','D.pc_no')
                                             ->on('A.ac_no','D.ac_no');
                          })

                        ->join('m_state AS C','A.ST_CODE','C.ST_CODE')
                        ->join('m_election_details as med', function($join){
                                          $join->on('med.st_code','A.ST_CODE')
                                               ->on('med.CONST_NO','A.PC_NO');

                        })


                         ->where('A.st_code',$value->ST_CODE)
                         ->where('D.year', 2019)

                         ->where('med.CONST_TYPE', 'PC')
                         ->where('med.election_status', 1)
                         ->where('med.ELECTION_ID', 1)


                          ->groupby ('C.ST_CODE','B.PC_NO','A.ac_no')
                          ->get()->toArray();


                          foreach ($data as  $value) {

                            $datanew[$value->st_name][$value->pc_name]['st_code'] = $value->st_code;
                            $datanew[$value->st_name][$value->pc_name]['st_name'] = $value->st_name;
                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['name'] = $value->ac_name;
                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['gen_electors_male'] = $value->maletotalelector;
                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['gen_electors_female'] = $value->femaletotalelector;
                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['gen_electors_other'] = $value->othertotalelector;

                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['gen_total'] = $value->maletotalelector+$value->femaletotalelector+$value->othertotalelector;

                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['nri_male_electors'] = $value->malenrielector;
                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['nri_female_electors'] = $value->femalenrielector;
                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['nri_third_electors'] = $value->othernrielector;

                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['nri_total'] = $value->malenrielector+$value->femalenrielector+$value->othernrielector;

                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['service_male_electors'] = $value->maleserelector;
                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['service_female_electors'] = $value->femaleserelector;

                           $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['ser_total'] = $value->maleserelector+$value->femaleserelector;

                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['total_elector'] = $value->maletotalelector+$value->femaletotalelector+$value->othertotalelector+$value->malenrielector+$value->femalenrielector+$value->othernrielector+$value->maleserelector+$value->femaleserelector;

                            $datanew[$value->st_name][$value->pc_name]['ac_name'][$value->ac_no]['votes_total_evm_all'] = $value->t_votes_evm;

                           }


       }
        $datanew = json_decode(json_encode($datanew));

        if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
          }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
          }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
          }else if($user->role_id == '7'){
                  $prefix     = 'eci';
        }

//echo "<pre>"; print_r($datanew); die;

 if($request->path() == "$prefix/AssemblySegmentWiseInformationElectors"){
 return view('IndexCardReports.AssemblySegmentWiseInformationElectors.AssemblySegmentWiseInformationElectors', compact('datanew','user_data'));
 }
 elseif($request->path() == "$prefix/AssemblySegmentWiseInformationElectorsPDF"){
 $pdf=PDF::loadView
 ('IndexCardReports.AssemblySegmentWiseInformationElectors.AssemblySegmentWiseInformationElectorsPDF',[

     'datanew'=>$datanew
 ]);
 return $pdf->download('Assembly_Segment_Wise_Information_Electors_Report.pdf');


 }
 elseif($request->path() == "$prefix/AssemblySegmentWiseInformationElectorsXLS"){


  //echo "<pre>"; print_r($datanew); die;

  return Excel::create('AssemblySegmentWiseInformationElectors'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($datanew) {
                       $excel->sheet('mySheet', function($sheet) use ($datanew) {
                       $sheet->mergeCells('B1:P1');
                       $sheet->mergeCells('D2:O2');
                       $sheet->mergeCells('D3:K3');
                       $sheet->mergeCells('L3:N3');
                       $sheet->mergeCells('L4:N4');
                       $sheet->mergeCells('D4:G4');
                       $sheet->mergeCells('H4:K4');
                       $sheet->mergeCells('O3:O5');
                       $sheet->mergeCells('P2:P5');
                       $sheet->getStyle('P2')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('C')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('B')->getAlignment()->setWrapText(true);

                       $sheet->cells('B1', function($cells) {
                          $cells->setValue('15. Assembly SegmentWise Information Electors');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                       });

                       $sheet->cells('O3', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->setSize('O3', 10, 25);

                       $sheet->cells('P2', function($cells) {
                          $cells->setValue('VOTES POLLED ON EVM');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->setSize('P2', 10, 25);

                       $sheet->cells('D2', function($cells) {
                          $cells->setValue('ELECTORS');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                       $sheet->cells('D3', function($cells) {
                          $cells->setValue('GENERAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });
                       $sheet->cells('L3', function($cells) {
                          $cells->setValue('SERVICE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                       $sheet->cells('D4', function($cells) {
                          $cells->setValue('OTHER THAN NRI');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                       $sheet->cells('H4', function($cells) {
                          $cells->setValue('NRI');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                        $sheet->cells('A4', function($cells) {
                          $cells->setValue('STATE NAME');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('A4', 15,25);

                       $sheet->cells('B5', function($cells) {
                          $cells->setValue('AC NAME');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('B5', 15,25);
                       $sheet->cells('C5', function($cells) {
                          $cells->setValue('PC NAME');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('C5', 15,25);

                       $sheet->cells('D5', function($cells) {
                          $cells->setValue('MALE');
                          $cells->setAlignment('center');

                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('D5', 10,25);

                       $sheet->cells('E5', function($cells) {
                          $cells->setValue('FEMALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('E5', 10, 25);
                       $sheet->cells('F5', function($cells) {
                          $cells->setValue('OTHER');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('F5', 10,25);
                       $sheet->cells('G5', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('G5', 10, 25);

                       $sheet->cells('H5', function($cells) {
                          $cells->setValue('MALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('H5', 10, 25);

                       $sheet->cells('I5', function($cells) {
                          $cells->setValue('FEMALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('I5', 10, 25);
                       $sheet->cells('J5', function($cells) {
                          $cells->setValue('OTHER');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('J5', 10,25);
                       $sheet->cells('K5', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('K5', 10, 25);
                        $sheet->cells('L5', function($cells) {
                          $cells->setValue('MALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                        $sheet->setSize('L5', 10, 25);
                       $sheet->cells('M5', function($cells) {
                          $cells->setValue('FEMALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('M5', 10, 25);
                       $sheet->cells('N5', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('N5', 10, 25);

                       //echo "<pre>"; print_r($datanew); die;

                          $i = 6;
                           if (!empty($datanew)) {
                         foreach ($datanew as $key => $value1) {
                        foreach ($value1 as $key2 => $value2){
                        foreach($value2->ac_name as $kkey => $vvalue) {
                        //echo '<pre>'; print_r($value);die;

                                     $sheet->cell('A' . $i, $key);
                                     $sheet->cell('B' . $i, $vvalue->name);
                                     $sheet->cell('C' . $i, $key2);
                                     $sheet->cell('D' . $i, $vvalue->gen_electors_male ? :'=(0)');
                                     $sheet->cell('E' . $i, $vvalue->gen_electors_female? :'=(0)');
                                     $sheet->cell('F' . $i, $vvalue->gen_electors_other? :'=(0)');
                                     $sheet->cell('G' . $i, $vvalue->gen_total? :'=(0)');
                                     $sheet->cell('H' . $i, $vvalue->nri_male_electors? :'=(0)');
                                     $sheet->cell('I' . $i, $vvalue->nri_female_electors? :'=(0)');
                                     $sheet->cell('J' . $i, $vvalue->nri_third_electors? :'=(0)');
                                     $sheet->cell('K' . $i, $vvalue->nri_total? :'=(0)');
                                     $sheet->cell('L' . $i, $vvalue->service_male_electors? :'=(0)');
                                     $sheet->cell('M' . $i, $vvalue->service_female_electors? :'=(0)');
                                     $sheet->cell('N' . $i, $vvalue->ser_total? :'=(0)');
                                     $sheet->cell('O' . $i, $vvalue->total_elector? :'=(0)');
                                     $sheet->cell('P' . $i, $vvalue->votes_total_evm_all? :'=(0)');
                                   // $sheet->cell('G' . $i, $value['margin']);
                                   // $sheet->cell('H' . $i, $value['percent']);
                                  // $sheet->cell('B'.$i, ($value['gen'] > 0) ? $value['gen']:'=(0)' );
                              $i++; }
                               }
                           }}
                       });
                   })->export();

 }


        //echo "<pre>"; print_r($datanew); die;
}


    $st_code = $session['admin_login_details']['st_code'];

    $st_name = $session['admin_login_details']['placename'];

   DB::enableQueryLog();
	 $data = DB::table('m_ac AS A')

                          ->select('C.st_code','C.st_name','B.PC_NO','A.ac_no','B.pc_name','A.ac_name', DB::raw("SUM(E.total_vote) AS t_votes_evm"),

                          'D.gen_electors_male as maletotalelector',
                          'D.gen_electors_female as femaletotalelector',
                          'D.gen_electors_other as othertotalelector',


                          'D.nri_male_electors as malenrielector',
                          'D.nri_female_electors as femalenrielector',
                          'D.nri_third_electors as othernrielector',



                          'D.service_male_electors as maleserelector',
                          'D.service_female_electors as femaleserelector',
                          'D.service_third_electors as totalserelector'


                          )

                          ->join('m_pc AS B', function($join){
                                       $join->on('B.PC_NO','A.PC_NO')
                                           ->on('B.ST_CODE','A.ST_CODE');
                                   })
                          ->join("counting_master_".strtolower($st_code)." as E",function($join){
                                        $join->on('A.ac_no','E.ac_no')
                                             ->on('B.PC_NO','E.pc_no');
                          })


                          ->join('electors_cdac as D',function($join){
                                        $join->on('A.ST_CODE','D.st_code')
                                             ->on('A.PC_NO','D.pc_no')
                                             ->on('A.ac_no','D.ac_no');
                          })

                        ->join('m_state AS C','A.ST_CODE','C.ST_CODE')
                         ->where('A.st_code',$st_code)
                         ->where('D.year', 2019)


                          ->groupby ('C.ST_CODE','B.PC_NO','A.ac_no')
                          ->get()->toArray();
    //dd($data);
    $query = DB::getQueryLog();
    //echo "<pre>"; print_r($data); die;

    foreach ($data as  $value) {

      $datanew[$value->pc_name]['st_code'] = $value->st_code;
      $datanew[$value->pc_name]['st_name'] = $value->st_name;
      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['name'] = $value->ac_name;
      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['gen_electors_male'] = $value->maletotalelector;
      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['gen_electors_female'] = $value->femaletotalelector;
      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['gen_electors_other'] = $value->othertotalelector;

      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['gen_total'] = $value->maletotalelector+$value->femaletotalelector+$value->othertotalelector;

      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['nri_male_electors'] = $value->malenrielector;
      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['nri_female_electors'] = $value->femalenrielector;
      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['nri_third_electors'] = $value->othernrielector;

      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['nri_total'] = $value->malenrielector+$value->femalenrielector+$value->othernrielector;

      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['service_male_electors'] = $value->maleserelector;
      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['service_female_electors'] = $value->femaleserelector;

     $datanew[$value->pc_name]['ac_name'][$value->ac_no]['ser_total'] = $value->maleserelector+$value->femaleserelector;

      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['total_elector'] = $value->maletotalelector+$value->femaletotalelector+$value->othertotalelector+$value->malenrielector+$value->femalenrielector+$value->othernrielector+$value->maleserelector+$value->femaleserelector;

      $datanew[$value->pc_name]['ac_name'][$value->ac_no]['votes_total_evm_all'] = $value->t_votes_evm;

}


$datanew = json_decode(json_encode($datanew));

if($user->designation == 'ROPC'){
            $prefix     = 'ropc';
  }else if($user->designation == 'CEO'){
            $prefix     = 'pcceo';
  }else if($user->role_id == '27'){
          $prefix     = 'eci-index';
  }else if($user->role_id == '7'){
          $prefix     = 'eci';
        }

//echo "<pre>"; print_r($datanew); die;

 if($request->path() == "$prefix/AssemblySegmentWiseInformationElectors"){
 return view('IndexCardReports.AssemblySegmentWiseInformationElectors.AssemblySegmentWiseInformationElectors', compact('datanew','user_data'));
 }
 elseif($request->path() == "$prefix/AssemblySegmentWiseInformationElectorsPDF"){
 $pdf=PDF::loadView
 ('IndexCardReports.AssemblySegmentWiseInformationElectors.AssemblySegmentWiseInformationElectorsPDF',[

     'datanew'=>$datanew
 ]);
 return $pdf->download('Assembly_Segment_Wise_Information_Electors_Report.pdf');


 }

 elseif($request->path() == "$prefix/AssemblySegmentWiseInformationElectorsXLS"){


  //echo "<pre>"; print_r($datanew); die;

  return Excel::create('AssemblySegmentWiseInformationElectors'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($datanew) {
                       $excel->sheet('mySheet', function($sheet) use ($datanew) {
                       $sheet->mergeCells('B1:P1');
                       $sheet->mergeCells('D2:O2');
                       $sheet->mergeCells('D3:K3');
                       $sheet->mergeCells('L3:N3');
                       $sheet->mergeCells('L4:N4');
                       $sheet->mergeCells('D4:G4');
                       $sheet->mergeCells('H4:K4');
                       $sheet->mergeCells('O3:O5');
                       $sheet->mergeCells('P2:P5');
                       $sheet->getStyle('P2')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('C')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('B')->getAlignment()->setWrapText(true);

                       $sheet->cells('B1', function($cells) {
                          $cells->setValue('15. Assembly SegmentWise Information Electors');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                       });

                       $sheet->cells('O3', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->setSize('O3', 10, 25);

                       $sheet->cells('P2', function($cells) {
                          $cells->setValue('VOTES POLLED ON EVM');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->setSize('P2', 10, 25);

                       $sheet->cells('D2', function($cells) {
                          $cells->setValue('ELECTORS');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                       $sheet->cells('D3', function($cells) {
                          $cells->setValue('GENERAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });
                       $sheet->cells('L3', function($cells) {
                          $cells->setValue('SERVICE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                       $sheet->cells('D4', function($cells) {
                          $cells->setValue('OTHER THAN NRI');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                       $sheet->cells('H4', function($cells) {
                          $cells->setValue('NRI');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                       });

                        $sheet->cells('A4', function($cells) {
                          $cells->setValue('STATE NAME');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('A4', 15,25);

                       $sheet->cells('B5', function($cells) {
                          $cells->setValue('AC NAME');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('B5', 15,25);
                       $sheet->cells('C5', function($cells) {
                          $cells->setValue('PC NAME');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('C5', 15,25);

                       $sheet->cells('D5', function($cells) {
                          $cells->setValue('MALE');
                          $cells->setAlignment('center');

                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('D5', 10,25);

                       $sheet->cells('E5', function($cells) {
                          $cells->setValue('FEMALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('E5', 10, 25);
                       $sheet->cells('F5', function($cells) {
                          $cells->setValue('OTHER');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('F5', 10,25);
                       $sheet->cells('G5', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('G5', 10, 25);

                       $sheet->cells('H5', function($cells) {
                          $cells->setValue('MALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('H5', 10, 25);

                       $sheet->cells('I5', function($cells) {
                          $cells->setValue('FEMALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('I5', 10, 25);
                       $sheet->cells('J5', function($cells) {
                          $cells->setValue('OTHER');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('J5', 10,25);
                       $sheet->cells('K5', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('K5', 10, 25);
                        $sheet->cells('L5', function($cells) {
                          $cells->setValue('MALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                        $sheet->setSize('L5', 10, 25);
                       $sheet->cells('M5', function($cells) {
                          $cells->setValue('FEMALE');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('M5', 10, 25);
                       $sheet->cells('N5', function($cells) {
                          $cells->setValue('TOTAL');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('N5', 10, 25);

                       //echo "<pre>"; print_r($datanew); die;

                          $i = 6;
                         if (!empty($datanew)) {
                         foreach ($datanew as $key => $value1) {
                        foreach ($value1 as $key2 => $value2){
                        foreach($value2->ac_name as $kkey => $vvalue) {
                        //echo '<pre>'; print_r($value);die;

                                     $sheet->cell('A' . $i, $key);
                                     $sheet->cell('B' . $i, $vvalue->name);
                                     $sheet->cell('C' . $i, $key2);
                                     $sheet->cell('D' . $i, $vvalue->gen_electors_male ? :'=(0)');
                                     $sheet->cell('E' . $i, $vvalue->gen_electors_female? :'=(0)');
                                     $sheet->cell('F' . $i, $vvalue->gen_electors_other? :'=(0)');
                                     $sheet->cell('G' . $i, $vvalue->gen_total? :'=(0)');
                                     $sheet->cell('H' . $i, $vvalue->nri_male_electors? :'=(0)');
                                     $sheet->cell('I' . $i, $vvalue->nri_female_electors? :'=(0)');
                                     $sheet->cell('J' . $i, $vvalue->nri_third_electors? :'=(0)');
                                     $sheet->cell('K' . $i, $vvalue->nri_total? :'=(0)');
                                     $sheet->cell('L' . $i, $vvalue->service_male_electors? :'=(0)');
                                     $sheet->cell('M' . $i, $vvalue->service_female_electors? :'=(0)');
                                     $sheet->cell('N' . $i, $vvalue->ser_total? :'=(0)');
                                     $sheet->cell('O' . $i, $vvalue->total_elector? :'=(0)');
                                     $sheet->cell('P' . $i, $vvalue->votes_total_evm_all? :'=(0)');
                                   // $sheet->cell('G' . $i, $value['margin']);
                                   // $sheet->cell('H' . $i, $value['percent']);
                                  // $sheet->cell('B'.$i, ($value['gen'] > 0) ? $value['gen']:'=(0)' );
                              $i++; }
                               }
                           }}
                       });
                   })->export();

 }


  }

/// winning candidate overseas voters  start

public function winningcandidateoverseasevoters(Request $request){
  $user_data = Auth::user();

  $arrayData = DB::select("select ST_CODE,st_name,lead_total_vote1 as total_lead_vote,total_vote as E_total,cc1 as Total_Sheet,
zero_to_10,one_to_20,two_to_30,three_to_40,four_to_50,five_to_60,six_to_70,seven_to_80
from(
select temp.*,count(cc)as cc1,group_concat(lead_total_vote)lead_total_vote1,
group_concat(total_vote)as electors_total1,
sum(CASE WHEN temp.cc >= '1' AND temp.cc <= '10' = '1' THEN 1 ELSE 0 END) AS zero_to_10,
sum(CASE WHEN temp.cc >= '11' AND temp.cc <= '20' = '1' THEN 1 ELSE 0 END) AS one_to_20,
sum(CASE WHEN temp.cc >= '21' AND temp.cc <= '30' = '1' THEN 1 ELSE 0 END )AS two_to_30,
sum(CASE WHEN temp.cc >= '31' AND temp.cc <= '40' = '1' THEN 1 ELSE 0 END) AS three_to_40,
sum(CASE WHEN temp.cc >= '41' AND temp.cc <= '50' = '1' THEN 1 ELSE 0 END) AS four_to_50,
sum(CASE WHEN temp.cc >= '51' AND temp.cc <= '60' = '1' THEN 1 ELSE 0 END) AS five_to_60,
sum(CASE WHEN temp.cc >= '61' AND temp.cc <= '70' = '1' THEN 1 ELSE 0 END)AS six_to_70,
sum(CASE WHEN temp.cc >= '71' AND temp.cc <= '80' = '1' THEN 1 ELSE 0 END )AS seven_to_80
from (
SELECT mpc.ST_CODE,cdac.pc_no,
winn.lead_total_vote,sum(cdac.total_vote) as total_vote ,m.st_name,
round((lead_total_vote/sum(cdac.total_vote) *100),0) AS CC
FROM `winning_leading_candidate` AS `winn` INNER JOIN `m_pc` AS `mpc`
ON `mpc`.`ST_CODE` = `winn`.`st_code` AND `mpc`.`PC_NO` = `winn`.`pc_no`
INNER JOIN m_state m ON m.st_code = winn.st_code
INNER JOIN `counting_pcmaster` AS `cdac` ON `winn`.`st_code` = `cdac`.`st_code`
AND `winn`.`pc_no` = `cdac`.`pc_no`
GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
)temp
group by st_code
)
temp1");

///echo "<pre>"; print_r($arrayData);die;

 return view('IndexCardReports/StatisticalReports/Vol2/eciwinning-condidate-analysis-over-total-voters', compact('arrayData','user_data'));

}
public function winningcandidateoverseasevoterspdf(Request $request){
  $user_data = Auth::user();

  $arrayData = DB::select("select ST_CODE,st_name,lead_total_vote1 as total_lead_vote,total_vote as E_total,cc1 as Total_Sheet,
zero_to_10,one_to_20,two_to_30,three_to_40,four_to_50,five_to_60,six_to_70,seven_to_80
from(
select temp.*,count(cc)as cc1,group_concat(lead_total_vote)lead_total_vote1,
group_concat(total_vote)as electors_total1,
sum(CASE WHEN temp.cc >= '1' AND temp.cc <= '10' = '1' THEN 1 ELSE 0 END) AS zero_to_10,
sum(CASE WHEN temp.cc >= '11' AND temp.cc <= '20' = '1' THEN 1 ELSE 0 END) AS one_to_20,
sum(CASE WHEN temp.cc >= '21' AND temp.cc <= '30' = '1' THEN 1 ELSE 0 END )AS two_to_30,
sum(CASE WHEN temp.cc >= '31' AND temp.cc <= '40' = '1' THEN 1 ELSE 0 END) AS three_to_40,
sum(CASE WHEN temp.cc >= '41' AND temp.cc <= '50' = '1' THEN 1 ELSE 0 END) AS four_to_50,
sum(CASE WHEN temp.cc >= '51' AND temp.cc <= '60' = '1' THEN 1 ELSE 0 END) AS five_to_60,
sum(CASE WHEN temp.cc >= '61' AND temp.cc <= '70' = '1' THEN 1 ELSE 0 END)AS six_to_70,
sum(CASE WHEN temp.cc >= '71' AND temp.cc <= '80' = '1' THEN 1 ELSE 0 END )AS seven_to_80
from (
SELECT mpc.ST_CODE,cdac.pc_no,
winn.lead_total_vote,sum(cdac.total_vote) as total_vote ,m.st_name,
round((lead_total_vote/sum(cdac.total_vote) *100),0) AS CC
FROM `winning_leading_candidate` AS `winn` INNER JOIN `m_pc` AS `mpc`
ON `mpc`.`ST_CODE` = `winn`.`st_code` AND `mpc`.`PC_NO` = `winn`.`pc_no`
INNER JOIN m_state m ON m.st_code = winn.st_code
INNER JOIN `counting_pcmaster` AS `cdac` ON `winn`.`st_code` = `cdac`.`st_code`
AND `winn`.`pc_no` = `cdac`.`pc_no`
GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
)temp
group by st_code
)
temp1");

///echo "<pre>"; print_r($arrayData);die;
  $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/eciwinning-condidate-analysis-over-total-voters-pdf', compact('arrayData'));
       return $pdf->download('winning-condidate-analysis-over-total-electors.pdf');

}

public function winningcandidateoverseasevotersxls(Request $request){
  $user_data = Auth::user();
$arrayData = DB::select("select ST_CODE,st_name,lead_total_vote1 as total_lead_vote,electors_total1 as E_total,cc1 as Total_Sheet,
      zero_to_10,one_to_20,two_to_30,three_to_40,four_to_50,five_to_60,six_to_70,seven_to_80
      from(select temp.*,count(cc)as cc1,group_concat(lead_total_vote)lead_total_vote1,
      group_concat(electors_total)as electors_total1,
      sum(CASE WHEN temp.cc >= '1' AND temp.cc <= '10' = '1' THEN 1 ELSE 0 END) AS zero_to_10,
      sum(CASE WHEN temp.cc >= '11' AND temp.cc <= '20' = '1' THEN 1 ELSE 0 END) AS one_to_20,
      sum(CASE WHEN temp.cc >= '21' AND temp.cc <= '30' = '1' THEN 1 ELSE 0 END )AS two_to_30,
      sum(CASE WHEN temp.cc >= '31' AND temp.cc <= '40' = '1' THEN 1 ELSE 0 END) AS three_to_40,
      sum(CASE WHEN temp.cc >= '41' AND temp.cc <= '50' = '1' THEN 1 ELSE 0 END) AS four_to_50,
      sum(CASE WHEN temp.cc >= '51' AND temp.cc <= '60' = '1' THEN 1 ELSE 0 END) AS five_to_60,
      sum(CASE WHEN temp.cc >= '61' AND temp.cc <= '70' = '1' THEN 1 ELSE 0 END)AS six_to_70,
      sum(CASE WHEN temp.cc >= '71' AND temp.cc <= '80' = '1' THEN 1 ELSE 0 END )AS seven_to_80
      from ( SELECT mpc.ST_CODE,cdac.PC_NO,
      winn.lead_total_vote,sum(cdac.electors_total) as electors_total ,m.st_name,
      round((lead_total_vote/sum(cdac.electors_total) *100),0) AS CC
      FROM `winning_leading_candidate` AS `winn` INNER JOIN `m_pc` AS `mpc`
      ON `mpc`.`ST_CODE` = `winn`.`st_code` AND `mpc`.`PC_NO` = `winn`.`pc_no`
      INNER JOIN m_state m ON m.st_code = winn.st_code
      INNER JOIN `electors_cdac` AS `cdac` ON `winn`.`st_code` = `cdac`.`st_code`
      AND `winn`.`pc_no` = `cdac`.`pc_no` AND cdac.year = 2019
      GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
      )temp
      group by st_code
      )
      temp1");

return Excel::create('winning-condidate-analysis-over-voters'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($arrayData) {
                    $excel->sheet('mySheet', function($sheet) use ($arrayData) {
                        $sheet->mergeCells('A1:K1');
                        $sheet->mergeCells('C1:J1');

                        $sheet->cells('A1', function($cells) {
                            $cells->setValue('Winning candidate analysis over total electors');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('A2', function($cells) {
                            $cells->setValue('Name of State/UT');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('B2', function($cells) {
                            $cells->setValue('No. Of Seats');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('C2', function($cells) {
                            $cells->setValue('No. Of Candidates Secured The % Of Votes Over The Total Electors In The Constituency');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setFontColor("#ffffff");
                            $cells->setBackground("#042179");
                            $cells->setAlignment('center');
                        });

                        $last_key = 0;
                        $last = $last_key + 10;
                        $col = 'B' . $last . ':' . 'J' . $last;

                        $sheet->cells($col, function($cells) {
                            $cells->setFont(array(
                                'name' => 'Times New Roman',
                                'size' => 12,
                                'bold' => true
                            ));

                            $cells->setAlignment('center');
                        });


                        $sheet->cell('A3', function($cell) {
                            $cell->setValue('');
                        });
                        $sheet->cell('B3', function($cell) {
                            $cell->setValue('');
                        });
                        $sheet->cell('C3', function($cell) {
                            $cell->setValue('Winner with <= 10%');
                        });

                        $sheet->cell('D3', function($cell) {
                            $cell->setValue('Winner with >10% to <= 20%');
                        });
                        $sheet->cell('E3', function($cell) {
                            $cell->setValue('Winner with >20% to <=30%');
                        });
                        $sheet->cell('F3', function($cell) {
                            $cell->setValue('Winner with >30% to <=40%');
                        });

                        $sheet->cell('G3', function($cell) {
                            $cell->setValue('Winner with >40% to <=50%');
                        });
                        $sheet->cell('H3', function($cell) {
                            $cell->setValue('Winner with >50% to <=60%');
                        });
                        $sheet->cell('I3', function($cell) {
                            $cell->setValue('Winner with >60% to <=70%');
                        });

                        $sheet->cell('J3', function($cell) {
                            $cell->setValue('Winner with > 70%');
                        });
                        $i = 4;
                        $totalsheet = $totalzero_to_10 = $totalone_to_20 = $totaltwo_to_30 = $totalthree_to_40
                        = $totalfour_to_50 = $totalfive_to_60 = $totalsix_to_70 = $totalseven_to_80 = 0;

                        if (!empty($arrayData)) {
                            foreach ($arrayData as $key => $values) {

                                $sheet->cell('A' . $i, $values->st_name);
                                $sheet->cell('B' . $i, $values->Total_Sheet);
                                $sheet->cell('C' . $i, $values->zero_to_10);
                                $sheet->cell('E' . $i, $values->one_to_20);
                                $sheet->cell('F' . $i, $values->two_to_30);
                                $sheet->cell('G' . $i, $values->three_to_40);
                                $sheet->cell('H' . $i, $values->four_to_50);
                                $sheet->cell('I' . $i, $values->five_to_60);
                                $sheet->cell('J' . $i, $values->six_to_70);
                                $sheet->cell('K' . $i, $values->seven_to_80);

                                $totalsheet += $values->Total_Sheet;
                                $totalzero_to_10 += $values->zero_to_10;
                                $totalone_to_20 += $values->one_to_20;
                                $totaltwo_to_30 += $values->two_to_30;
                                $totalthree_to_40 += $values->three_to_40;
                                $totalfour_to_50 += $values->four_to_50;
                                $totalfive_to_60 += $values->five_to_60;
                                $totalsix_to_70 += $values->six_to_70;
                                $totalseven_to_80 += $values->seven_to_80;

                           $i++; }


                        }
                        $sheet->cell('A' . $i, 'Total Seats');
                        $sheet->cell('B' . $i, $totalsheet);
                        $sheet->cell('C' . $i, $totalzero_to_10);
                        $sheet->cell('E' . $i, $totalone_to_20);
                        $sheet->cell('F' . $i, $totaltwo_to_30);
                        $sheet->cell('G' . $i, $totalthree_to_40);
                        $sheet->cell('H' . $i, $totalfour_to_50);
                        $sheet->cell('I' . $i, $totalfive_to_60);
                        $sheet->cell('J' . $i, $totalsix_to_70);
                        $sheet->cell('K' . $i, $totalseven_to_80);

                    });
                })->export();

}
/// winning candidate overseas voters end

// Performance of Unrecognised party start


public function performanceofunrecognisedparties(Request $request){
    $user = Auth::user();
    $user_data = $user;
    $performanceofst = DB::select("SELECT a.party_id,a.partyname,a.partyabbre,a.st_code,a.st_name,a.contested, a.won, a.vote_secured_by_party,(SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes
FROM `counting_pcmaster` AS cp1 WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),2) < .1666  THEN 1 ELSE 0 END) AS fd FROM `counting_pcmaster` AS cp WHERE a.party_id = cp.party_id AND a.st_code = cp.st_code AND a.party_id != (SELECT lead_cand_partyid FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code) GROUP BY cp.party_id) AS fd,b.egeneral,b.total_vote

FROM(
SELECT p.party_id,q.partyname,q.partyabbre,m.st_code,m.st_name,COUNT(DISTINCT p.candidate_id)contested, COUNT(DISTINCT w.`candidate_id`)won, SUM(p.total_vote) vote_secured_by_party FROM `counting_pcmaster` p LEFT JOIN `winning_leading_candidate` w ON p.candidate_id=w.candidate_id JOIN m_party q ON p.party_id=q.ccode JOIN m_state m ON m.st_code = p.st_code WHERE q.PARTYTYPE = 'U' GROUP BY p.party_id,m.st_code) a
JOIN
(SELECT TEMP.*,SUM(cpm.total_vote) AS 'total_vote' FROM (SELECT m.st_code, m.st_name,mpc.pc_no, mpc.PC_NAME,SUM(cda.electors_total+cda.electors_service) AS egeneral
FROM electors_cdac cda,m_pc mpc ,m_state m WHERE mpc.ST_CODE = cda.st_code AND mpc.pc_no = cda.pc_no AND  m.st_code = cda.st_code AND cda.year = 2019 GROUP BY mpc.st_code)TEMP,counting_pcmaster cpm WHERE TEMP.st_code=cpm.st_code GROUP BY TEMP.st_code) b ON a.st_code = b.st_code");
       $i =0;
foreach($performanceofst as $rowsdata){
                    //$arraydata[$rowsdata->CCODE]['partyabbr'] = $rowsdata->PARTYABBRE;
                    $arraydata[$rowsdata->party_id]['partyname'] = $rowsdata->partyname;
                    $arraydata[$rowsdata->party_id]['partyabbre'] = $rowsdata->partyabbre;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['statename'] = $rowsdata->st_name;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['contested'] = $rowsdata->contested;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['won'] = $rowsdata->won;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['df'] = $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['Securedvotes'] = $rowsdata->vote_secured_by_party;
                    $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['total_vote'] = $rowsdata->total_vote;

                    if($rowsdata->total_vote !=0){
                  $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['poledvotespercent'] = round($rowsdata->vote_secured_by_party/$rowsdata->total_vote*100,4);

                   }else{ $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['poledvotespercent'] = '0%';
                   }

                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['totalelectors'] = round($rowsdata->vote_secured_by_party/$rowsdata->egeneral*100,4);

                   }else{ $arraydata[$rowsdata->party_id]['partydata'][$rowsdata->st_code]['totalelectors'] = '0%'; }
                  $arraydata[$rowsdata->party_id]['totalcontested'][] =  $rowsdata->contested;
                   $arraydata[$rowsdata->party_id]['won'][] =  $rowsdata->won;
                   $arraydata[$rowsdata->party_id]['DF'][] =  $rowsdata->fd;
                    $arraydata[$rowsdata->party_id]['Securedvotes'][] =  $rowsdata->vote_secured_by_party;
// votepercents
                    if($rowsdata->total_vote !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentvote'][] = round($rowsdata->vote_secured_by_party/$rowsdata->total_vote*100,4);

                   }else{ $arraydata[$rowsdata->party_id]['totalpercentvote'][] = '0%';
                   }
//electors percents
                   if($rowsdata->egeneral !=0){
                   $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = round(($rowsdata->vote_secured_by_party/$rowsdata->egeneral)*100,4);
                   }else{ $arraydata[$rowsdata->party_id]['totalpercentelectors'][] = '0%'; }

                   $i++;}

                  if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                  $prefix     = 'eci';
                  }

                    if($request->path() == "$prefix/performance-of-unrecognised-partys"){
                      return view('IndexCardReports/StatisticalReports/Vol2/performanceofunrecognisedparties',compact('arraydata','user_data'));
                    }elseif($request->path() == "$prefix/performance-of-unrecognised-partys-pdf"){

                     $pdf=PDF::loadView('IndexCardReports/StatisticalReports/Vol2/performanceofunrecognisedparties_pdf',[
                    'arraydata'=>$arraydata,
                    'user_data'=>$user_data

                    ]);
                    return $pdf->download('performance-of-unrecognised-parties.pdf');
                     }






}    //Performance of unrecognised party ends


public function getParticipationofWomenInStateParties(Request $request)
    {
    // session data
    $user = Auth::user();
    $user_data = $user;
     //end session data

    DB::enableQueryLog();
    $data=DB::select("SELECT *,
      (SELECT contested FROM (

      SELECT COUNT(cp.candidate_id) AS 'contested',cp.party_id,ms.st_code
      FROM m_party m JOIN counting_pcmaster cp
      ON m.ccode= cp.party_id
      JOIN candidate_personal_detail cpd
      ON cpd.candidate_id = cp.candidate_id
      JOIN m_state AS ms ON ms.`ST_CODE` = cp.`st_code`
      LEFT JOIN winning_leading_candidate wlc
      ON wlc.candidate_id = cp.candidate_id
      AND m.ccode= wlc.lead_cand_partyid
      WHERE partytype ='S'
      AND cand_gender = 'female'
      GROUP BY partyabbre,party_id,ms.`st_code`

      )BB
       WHERE BB.PARTY_ID=TEMP.party_id AND BB.st_code = TEMP.st_code)AS contested,
      (SELECT won
      FROM
      (SELECT COUNT(lead_total_vote) AS 'won',cp.party_id,ms.st_code
      FROM m_party m JOIN counting_pcmaster cp
      ON m.ccode= cp.party_id
      JOIN candidate_personal_detail cpd
      ON cpd.candidate_id = cp.candidate_id
      JOIN m_state AS ms ON ms.`ST_CODE` = cp.`st_code`
      LEFT JOIN winning_leading_candidate wlc
      ON wlc.candidate_id = cp.candidate_id
      AND m.ccode= wlc.lead_cand_partyid
      WHERE partytype ='S'
      AND cand_gender = 'female'
      GROUP BY partyabbre,ms.st_code) CC
      WHERE CC.PARTY_ID=TEMP.party_id AND CC.st_code = TEMP.st_code)AS WON,
      (SELECT SUM(df) FROM (
      SELECT lead_total_vote,partyabbre,cpd.candidate_id,cp.candidate_name,cp.party_id,ms.st_code,
      CASE WHEN SUM(cp1.total_vote)/6 > cp.total_vote THEN 1 ELSE 0 END AS 'DF' FROM m_party m
      JOIN counting_pcmaster cp ON m.ccode= cp.party_id
      JOIN counting_pcmaster cp1
      ON cp.st_code = cp1.st_code
      AND cp.pc_no = cp1.pc_no
      JOIN candidate_personal_detail cpd
      ON cpd.candidate_id = cp.candidate_id
      JOIN m_state AS ms ON ms.`ST_CODE` = cp.`st_code`
      LEFT JOIN winning_leading_candidate wlc
      ON wlc.candidate_id = cp.candidate_id
      AND m.ccode= wlc.lead_cand_partyid
      WHERE partytype ='S'
      AND cand_gender = 'female'
      AND lead_total_vote IS NULL
      GROUP BY cp.candidate_id,cp1.st_code, cp1.pc_no
      ) DD WHERE DD.party_id=TEMP.party_id AND DD.st_code = TEMP.st_code) AS DF,
(SELECT Total_electros_female
      FROM (
      SELECT partyabbre, party_id,PARTYNAME,SUM(electors_female) AS Total_electros_female
      FROM m_party m
      JOIN counting_pcmaster cp
      ON m.ccode= cp.party_id
      JOIN candidate_personal_detail cpd
      ON cpd.candidate_id = cp.candidate_id
      JOIN electors_cdac cdac ON cdac.pc_no=cp.pc_no
      WHERE partytype ='S' AND cdac.year = 2019
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
      WHERE partytype ='S' AND cdac.year = 2019
      AND cand_gender = 'female'
      GROUP BY partyabbre ) FFF WHERE FFF.party_id=TEMP.party_id) AS electrols_Total,

      (SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
      WHERE party_id=TEMP.party_id AND st_code = TEMP.st_code GROUP BY party_id, st_code)AS totalvalid_valid_vote,

      (SELECT SUM(electors_total)AS totaleelctors FROM electors_cdac
      WHERE party_id=TEMP.party_id AND `year` = 2019 AND st_code = TEMP.st_code GROUP BY PARTY_ID,st_code )AS sum_of_total_eelctors,
      (SELECT SUM(total_vote) FROM counting_pcmaster WHERE st_code = TEMP.st_code GROUP BY st_code ) AS OVER_ALL_TOTAL_VOTE_state
      FROM
      (
      SELECT partyabbre, party_id,PARTYNAME,SUM(total_vote) AS votes_secured_by_Women , cp.`ST_CODE`, ms.`ST_NAME`
      FROM m_party m
      JOIN counting_pcmaster cp
      ON m.ccode= cp.party_id
      JOIN m_state AS ms ON ms.`ST_CODE` = cp.`st_code`
      JOIN candidate_personal_detail cpd
      ON cpd.candidate_id = cp.candidate_id
      WHERE partytype ='S'
      AND cand_gender = 'female'
      GROUP BY partyabbre, ms.`st_code`
      )TEMP");

     //echo "<pre>"; print_r($data); die;

     foreach ($data as  $value) {

      $datanew[$value->partyabbre][$value->ST_NAME] = array(

      'PARTYNAME' => $value->PARTYNAME,
      'st_code'   =>$value->ST_CODE,
      'st_name'   =>$value->ST_NAME,
      'votes_secured_by_Women' =>$value->votes_secured_by_Women,
      'contested' => $value->contested,

      'WON' => $value->WON,
      'DF' => $value->DF,
      'Total_electros_female' => $value->Total_electros_female,
      'electrols_Total' => $value->electrols_Total,

      'totalvalid_valid_vote' => $value->totalvalid_valid_vote,
      'sum_of_total_eelctors' => $value->sum_of_total_eelctors,
      'OVER_ALL_TOTAL_VOTE_state' => $value->OVER_ALL_TOTAL_VOTE_state


      );
       # code...

     }

     //echo "<pre>"; print_r($datanew); die;




        if($user->designation == 'ROPC'){
            $prefix     = 'ropc';
        }else if($user->designation == 'CEO'){
            $prefix     = 'pcceo';
        }else if($user->role_id == '27'){
          $prefix     = 'eci-index';
        }else if($user->role_id == '7'){
          $prefix     = 'eci';
        }

            if($request->path() == "$prefix/ParticipationofWomenInStateParties"){
                return view('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-state-parties',compact('datanew','user_data'));
            }elseif($request->path() == "$prefix/ParticipationofWomenInStatePartiesPDF"){
                $pdf=PDF::loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-state-parties-pdf',[
                'datanew'=>$datanew,
                'user_data'=>$user_data
          ]);
           return $pdf->download('participation-of-women-in-state-parties.pdf');
           }elseif($request->path() == "$prefix/ParticipationofWomenInStatePartiesXls"){
              //ata = json_decode( json_encode($data), true);
               //echo'<pre>'; print_r($data);die;

                     return Excel::create('ParticipationofWomenInStatePartiesXls', function($excel) use ($datanew) {
                     $excel->sheet('mySheet', function($sheet) use ($datanew)
                     {
                    $sheet->mergeCells('A1:K1');
                    $sheet->cells('A1:K1', function($cells) {
                        $cells->setFont(array(
                            'size'       => '15',
                            'bold'       => true
                        ));
                        $cells->setAlignment('center');
                    });

                     $sheet->getStyle('H')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('B')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('I')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('A')->getAlignment()->setWrapText(true);

                     $sheet->getStyle('J')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('K')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('L')->getAlignment()->setWrapText(true);


                   $sheet->cell('A1', function($cells) {
                        $cells->setValue('Participation of Women In State Parties');
                    });

                    $sheet->mergeCells('A2:A3');
                    $sheet->cell('A2', function($cells) {
                        $cells->setValue('Party Name');
                         $cells->setFont(array('bold' => true));
                    });

                    $sheet->cell('B3', function($cells) {
                        $cells->setValue('State');
                         $cells->setFont(array('bold' => true));
                    });

                   $sheet->cell('C2', function($cells) {
                        $cells->setValue('Candidates');
                         $cells->setFont(array('bold' => true));
                    });

                    $sheet->cell('F2', function($cells) {
                        $cells->setValue('Percentage');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('F2', 10, 25);

                    $sheet->mergeCells('C2:E2');
                    $sheet->mergeCells('F2:G2');

                    $sheet->mergeCells('H2:H3');
                    $sheet->mergeCells('I2:I3');
                    $sheet->mergeCells('J2:L2');

                    $sheet->cell('C3', function($cells) {
                        $cells->setValue('Contested');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->cell('D3', function($cells) {
                        $cells->setValue('Won');
                         $cells->setFont(array('bold' => true));
                    });
                     $sheet->cell('E3', function($cells) {
                        $cells->setValue('DF');
                         $cells->setFont(array('bold' => true));
                    });


                    $sheet->cell('F3', function($cells) {
                        $cells->setValue('Won');
                         $cells->setFont(array('bold' => true));
                    });
                     $sheet->cell('G3', function($cells) {
                        $cells->setValue('DF');
                         $cells->setFont(array('bold' => true));
                    });


                    $sheet->cell('H2', function($cells) {
                        $cells->setValue('Votes Secured By Women Candidate');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('H2', 10, 45);

                    $sheet->cell('I2', function($cells) {
                        $cells->setValue('Votes Secured By Party In State');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('I2', 10, 45);

                    $sheet->cell('J2', function($cells) {
                        $cells->setValue('% of Votes Secured');
                         $cells->setFont(array('bold' => true));
                    });


                    $sheet->cell('I3', function($cells) {
                        $cells->setValue('Over total valid votes in the State');
                         $cells->setFont(array('bold' => true));
                    });

                    $sheet->cell('J3', function($cells) {
                        $cells->setValue('Over Total Electors in the State');
                         $cells->setFont(array('bold' => true));
                    });

                    $sheet->setSize('J3', 10, 50);

                    $sheet->cell('K3', function($cells) {
                        $cells->setValue('Over Total Voters In State');
                         $cells->setFont(array('bold' => true));
                    });

                    $sheet->setSize('K3', 10, 50);

                    $sheet->cell('L3', function($cells) {
                        $cells->setValue('Over Votes secured by the party in State');
                        $cells->setFont(array('bold' => true));
                    });

                    $sheet->setSize('L3', 10, 50);

                    //data loop


                    $i = 4;
                    foreach($datanew as $key => $value){




                        if (!empty($datanew)) {
                            foreach($value as $key1 => $value1) {

                               $peroverelectors = ($value1['votes_secured_by_Women']/$value1['sum_of_total_eelctors'])*100;

                                $overTotalValidVotes = ($value1['votes_secured_by_Women']/$value1['OVER_ALL_TOTAL_VOTE_state'])*100;

                                $ovsbp = ($value1['votes_secured_by_Women']/$value1['totalvalid_valid_vote'])*100;

                                $sheet->cell('A' . $i, $key);
                                $sheet->cell('B' . $i, $key1);
                                $sheet->cell('C' . $i, $value1['contested'] ? :'=(0)');
                                $sheet->cell('D' . $i, $value1['WON']? :'=(0)');
                                $sheet->cell('E' . $i, $value1['DF']? :'=(0)');
                                $sheet->cell('F' . $i, round((($value1['WON']/$value1['contested'])*100),2)? :'=(0)');
                                $sheet->cell('G' . $i, round((($value1['DF']/$value1['contested'])*100),2)? :'=(0)');
                                $sheet->cell('H' . $i, $value1['votes_secured_by_Women']);

                                $sheet->cell('I' . $i, $value1['totalvalid_valid_vote']);
                                $sheet->cell('J' . $i, round($peroverelectors,2));
                                $sheet->cell('K' . $i, round($overTotalValidVotes,2));
                                $sheet->cell('L' . $i, round($ovsbp,2));


                           $i++; }}
                        }




                });
                })->download('xls');

        }else{
            echo "Result not found";
        }
    }    // Participation of women in state party ends



  public function NoOffCandidatePCWISE(Request $request)
    {
    $user = Auth::user();
    $user_data = $user;
    $pcCount = DB::select('SELECT a.st_code,m.st_name,COUNT(cand_count) AS "No_of_Seats",
    SUM(CASE WHEN cand_count = 1 THEN 1 ELSE 0 END) AS "one" ,
    SUM(CASE WHEN cand_count <= 15 THEN 1 ELSE 0 END) AS "Nota",
    SUM(CASE WHEN cand_count > 15 AND cand_count <= 31 THEN 1 ELSE 0 END) AS "threeone",
    SUM(CASE WHEN cand_count > 31 AND cand_count <= 47 THEN 1 ELSE 0 END) AS "fourseven",
    SUM(CASE WHEN cand_count > 47 AND cand_count <= 63 THEN 1 ELSE 0 END) AS "sixthree",
    SUM(CASE WHEN cand_count > 63 THEN 1 ELSE 0 END) AS "lesssixthree", SUM(cand_count) AS "Total_Candidates", 
    MIN(cand_count) AS mincan,MAX(cand_count) AS maxcan,ROUND(SUM(cand_count)/COUNT(cand_count),0) AS "Avg" 
    FROM (SELECT st_code,pc_no,COUNT(candidate_id) "cand_count" FROM candidate_nomination_detail 
    WHERE application_status = 6 AND finalaccepted = 1 GROUP BY st_code, pc_no) a 
    INNER JOIN m_state m ON a.st_code = m.st_code 
    INNER JOIN m_election_details AS med ON med.st_code = m.st_code AND med.CONST_NO = a.pc_no
    WHERE   med.CONST_TYPE = "PC" AND  med.election_status = "1" AND med.ELECTION_ID = "1"

    GROUP BY a.st_code');


return view('IndexCardReports/StatisticalReports/Vol2/numberofcandidatepercostituency',compact('pcCount','user_data'));
}

  public function noofcandidateperconsitituencypdf(Request $request)
    {
    $user = Auth::user();
    $user_data = $user;
      $pcCount = DB::select('SELECT a.st_code,m.st_name,COUNT(cand_count) AS "No_of_Seats",
    SUM(CASE WHEN cand_count = 1 THEN 1 ELSE 0 END) AS "one" ,
    SUM(CASE WHEN cand_count <= 15 THEN 1 ELSE 0 END) AS "Nota",
    SUM(CASE WHEN cand_count > 15 AND cand_count <= 31 THEN 1 ELSE 0 END) AS "threeone",
    SUM(CASE WHEN cand_count > 31 AND cand_count <= 47 THEN 1 ELSE 0 END) AS "fourseven",
    SUM(CASE WHEN cand_count > 47 AND cand_count <= 63 THEN 1 ELSE 0 END) AS "sixthree",
    SUM(CASE WHEN cand_count > 63 THEN 1 ELSE 0 END) AS "lesssixthree", SUM(cand_count) AS "Total_Candidates", 
    MIN(cand_count) AS mincan,MAX(cand_count) AS maxcan,ROUND(SUM(cand_count)/COUNT(cand_count),0) AS "Avg" 
    FROM (SELECT st_code,pc_no,COUNT(candidate_id) "cand_count" FROM candidate_nomination_detail 
    WHERE application_status = 6 AND finalaccepted = 1 GROUP BY st_code, pc_no) a 
    INNER JOIN m_state m ON a.st_code = m.st_code 
    INNER JOIN m_election_details AS med ON med.st_code = m.st_code AND med.CONST_NO = a.pc_no
    WHERE   med.CONST_TYPE = "PC" AND  med.election_status = "1" AND med.ELECTION_ID = "1"

    GROUP BY a.st_code');

      $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/numberofcandidatepercostituencypdf', compact('pcCount'));
        return $pdf->download('8_numberofcandidatepercostituency.pdf');
}

  public function noofcandidateperconsitituencyexcel(Request $request)
    {

    $user = Auth::user();
    $user_data = $user;
      $data = DB::select('SELECT a.st_code,m.st_name,COUNT(cand_count) AS "No_of_Seats",
    SUM(CASE WHEN cand_count = 1 THEN 1 ELSE 0 END) AS "one" ,
    SUM(CASE WHEN cand_count <= 15 THEN 1 ELSE 0 END) AS "Nota",
    SUM(CASE WHEN cand_count > 15 AND cand_count <= 31 THEN 1 ELSE 0 END) AS "threeone",
    SUM(CASE WHEN cand_count > 31 AND cand_count <= 47 THEN 1 ELSE 0 END) AS "fourseven",
    SUM(CASE WHEN cand_count > 47 AND cand_count <= 63 THEN 1 ELSE 0 END) AS "sixthree",
    SUM(CASE WHEN cand_count > 63 THEN 1 ELSE 0 END) AS "lesssixthree", SUM(cand_count) AS "Total_Candidates", 
    MIN(cand_count) AS mincan,MAX(cand_count) AS maxcan,ROUND(SUM(cand_count)/COUNT(cand_count),0) AS "Avg" 
    FROM (SELECT st_code,pc_no,COUNT(candidate_id) "cand_count" FROM candidate_nomination_detail 
    WHERE application_status = 6 AND finalaccepted = 1 GROUP BY st_code, pc_no) a 
    INNER JOIN m_state m ON a.st_code = m.st_code 
    INNER JOIN m_election_details AS med ON med.st_code = m.st_code AND med.CONST_NO = a.pc_no
    WHERE   med.CONST_TYPE = "PC" AND  med.election_status = "1" AND med.ELECTION_ID = "1"

    GROUP BY a.st_code');

      return Excel::create('8_numberofcandidatepercostituency', function($excel) use ($data) {
          $excel->sheet('mySheet', function($sheet) use ($data)
          {
         $sheet->mergeCells('A1:L1');
         $sheet->cells('A1:I1', function($cells) {
             $cells->setFont(array(
                 'size'       => '15',
                 'bold'       => true
             ));
             $cells->setAlignment('center');
         });
        $sheet->cell('A1', function($cells) {
             $cells->setValue('8.NUMBER OF CANDIDATES PER CONSTITUENCY');
         });


         $sheet->mergeCells('C2:G2');
         $sheet->mergeCells('J1:L2');

        $sheet->cell('A2', function($cells) {
             $cells->setValue('');
         });

        $sheet->cell('B2', function($cells) {
             $cells->setValue('');
         });


         $sheet->cell('C3', function($cells) {
             $cells->setValue('Constituencies with candidates numbering');
         });

         $sheet->cell('C3', function($cells) {
             $cells->setValue('1');
         });

          $sheet->cell('D3', function($cells) {
             $cells->setValue('<=15 + NOTA');
         });
         $sheet->cell('E3', function($cells) {
             $cells->setValue('>15 <=31');
         });
         $sheet->cell('F3', function($cells) {
             $cells->setValue('>31 <=47');
         });

         $sheet->cell('G3', function($cells) {
             $cells->setValue('>47 <=63');
         });
         $sheet->cell('H3', function($cells) {
             $cells->setValue('>63');
         });
         $sheet->cell('I3', function($cells) {
             $cells->setValue('Total Candidates');
         });
         $sheet->cell('J3', function($cells) {
             $cells->setValue('Min');
         });
         $sheet->cell('K3', function($cells) {
             $cells->setValue('Max');
         });
         $sheet->cell('L3', function($cells) {
             $cells->setValue('Avg');
         });


         $seartotal = $searonetotal = $searNotatotal = $searThreeOnetotal = $searFourSeventotal 

          = $searSixThreetotal = $searLessSixThreetotal = $totalcandidate = 0 ;


          if (!empty($data)){
              $i= 4;
              //echo "<pre>";print_r($data);die;
             foreach ($data as $value) {
                     $sheet->cell('A'.$i, $value->st_name);
                     $sheet->cell('B'.$i, ($value->No_of_Seats) ? $value->No_of_Seats : '=(0)');
                     $sheet->cell('C'.$i, $value->one);
                     $sheet->cell('D'.$i, $value->Nota);
                     $sheet->cell('E'.$i, $value->threeone);
                     $sheet->cell('F'.$i, $value->fourseven);
                     $sheet->cell('G'.$i, $value->sixthree);
                     $sheet->cell('H'.$i,$value->lesssixthree);
                     $sheet->cell('I'.$i,$value->Total_Candidates);
                     $sheet->cell('J'.$i,$value->Avg);
                     $sheet->cell('K'.$i,$value->mincan);
                     $sheet->cell('L'.$i,$value->maxcan);

                       $i++;

                       $seartotal += $value->No_of_Seats;
                  $searonetotal += $value->one;
                  $searNotatotal += $value->Nota;
                  $searThreeOnetotal += $value->threeone;
                  $searFourSeventotal += $value->fourseven;
                  $searSixThreetotal += $value->sixthree;
                  $searLessSixThreetotal += $value->lesssixthree;
                  $totalcandidate  += $value->Total_Candidates;
                }

                   

                     $sheet->cell('A'. $i,'Grand Total');
                     $sheet->cell('B'.  $i, $seartotal);
                     $sheet->cell('C'.$i,$searonetotal );
                     $sheet->cell('D'.$i, $searNotatotal);
                     $sheet->cell('E'.$i, $searThreeOnetotal);
                     $sheet->cell('F'.$i, $searFourSeventotal);
                     $sheet->cell('G'.$i, $searSixThreetotal);
                     $sheet->cell('H'.$i,$searLessSixThreetotal);
                     $sheet->cell('I'.$i,$totalcandidate);
                     $sheet->cell('J'.$i,'4');
                     $sheet->cell('K'.$i,'186');
                     $sheet->cell('L'.$i,round($totalcandidate/$seartotal,2));




        }
      });
      })->download('xls');








}


public function highlights(request $request){
  $user_data = Auth::user();
    $pctypecount = DB::select("SELECT SUM(CASE WHEN pc_type = 'GEN' THEN 1 ELSE 0 END) AS genpc,
      SUM(CASE WHEN pc_type = 'SC' THEN 1 ELSE 0 END) AS scpc,
      SUM(CASE WHEN pc_type = 'ST' THEN 1 ELSE 0 END) AS stpc
    FROM m_pc inner join m_election_details as med on med.st_code = m_pc.ST_CODE and med.CONST_NO = m_pc.PC_NO
    where med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1'");

    $pctypecount = array_map(function ($value) {
    return (array)$value;
    }, $pctypecount);



    $pctypecount = json_decode(json_encode($pctypecount));

    $contestents = DB::select("SELECT COUNT(cand_count) AS 'No_of_Seats',
    SUM(CASE WHEN cand_count = 1 THEN 1 ELSE 0 END) AS 'one' ,
    SUM(CASE WHEN cand_count = 2 THEN 1 ELSE 0 END) AS 'two',
    SUM(CASE WHEN cand_count = 3 THEN 1 ELSE 0 END) AS 'three',
    SUM(CASE WHEN cand_count = 4 THEN 1 ELSE 0 END) AS 'four',
    SUM(CASE WHEN cand_count = 5 THEN 1 ELSE 0 END) AS 'five',
    SUM(CASE WHEN cand_count > 5 AND cand_count <= 10 THEN 1 ELSE 0 END) AS 'fiveten',
    SUM(CASE WHEN cand_count > 10 AND cand_count <= 15 THEN 1 ELSE 0 END) AS 'tenfifteen',
    SUM(CASE WHEN cand_count > 15  THEN 1 ELSE 0 END) AS 'fifteen',

    SUM(cand_count) AS 'Total_Candidates',
    MIN(cand_count) as maxcnd,MAX(cand_count) as mincnd,ROUND(SUM(cand_count)/COUNT(cand_count),0) AS 'Avg' FROM (SELECT cnd.st_code,cnd.pc_no,
    COUNT(cnd.candidate_id) 'cand_count' FROM candidate_nomination_detail as cnd
    inner join m_election_details as med on med.st_code = cnd.ST_CODE and med.CONST_NO = cnd.PC_NO
    WHERE cnd.application_status = 6 AND cnd.finalaccepted = 1 AND cnd.candidate_id!= '4319' and med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1' GROUP BY cnd.st_code, cnd.pc_no) a");

    $contestents = array_map(function ($value) {
    return (array)$value;
    }, $contestents);

    $contestents = array_merge($contestents[0],array(

                    'genpc'=> $pctypecount[0]->genpc,
                    'scpc'=> $pctypecount[0]->scpc,
                    'stpc'=> $pctypecount[0]->stpc,



                    ));



    $electorsvotersdata = DB::select("SELECT
          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS maleElectors,
          SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS femaleElectors,
          SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS thirdElectors,
          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+
            ec.service_female_electors+ec.nri_female_electors+ec.gen_electors_other+
            ec.nri_third_electors+ec.service_third_electors) AS totalElectors,
          SUM(ec.service_male_electors) AS maleServiceElector,
          SUM(ec.service_female_electors) AS femaleServiceElector

          FROM electors_cdac as ec

          inner join m_election_details as med on med.st_code = ec.ST_CODE and med.CONST_NO = ec.PC_NO


          WHERE ec.YEAR = '2019' and  med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1'");

          $electorsvotersdata = array_map(function ($value) {
          return (array)$value;
          }, $electorsvotersdata);




          $contestents = array_merge($contestents,array(

                          'maleElectors'=> $electorsvotersdata[0]['maleElectors'],
                          'femaleElectors'=> $electorsvotersdata[0]['femaleElectors'],
                          'thirdElectors'=> $electorsvotersdata[0]['thirdElectors'],
                          'totalElectors'=> $electorsvotersdata[0]['totalElectors'],
                          'maleServiceElector'=> $electorsvotersdata[0]['maleServiceElector'],
                          'femaleServiceElector'=> $electorsvotersdata[0]['femaleServiceElector'],
          ));


          $postalvotetotal = DB::select("SELECT SUM(totalpostalvotereceived) AS total_postal_vote_received,
          SUM(totalevmvote) AS total_evm_postal_vote,SUM(tended_votes) AS tended_votes,
          SUM(totalvalidpostalvote) AS total_valid_postal_vote,SUM(evm_vote) AS total_evm_vote,
          SUM(rejectedpostalvote) AS rejected_postal_vote
          FROM
          (SELECT cp.st_code,cp.pc_no,a.postaltotalvote AS 'totalpostalvotereceived',
          SUM(cp.total_vote) AS 'totalevmvote',
          SUM(cp.postal_vote) AS 'totalvalidpostalvote',
          SUM(cp.tended_votes) AS 'tended_votes',
          SUM(cp.evm_vote+cp.migrate_votes) 'evm_vote', a.rejectedvote AS 'rejectedpostalvote'   FROM counting_pcmaster cp,m_election_details med,
          (SELECT st_code,pc_no,rejectedvote,postaltotalvote FROM counting_pcmaster  GROUP BY st_code, pc_no) a
           WHERE cp.st_code = a.st_code AND cp.pc_no = a.pc_no AND med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
           AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1' GROUP BY cp.st_code,cp.pc_no) b");


          $postalvotetotal = array_map(function ($value) {
                            return (array)$value;
                            }, $postalvotetotal);


          $contestents = array_merge($contestents,array(

                                              'total_postal_vote_received'=> $postalvotetotal[0]['total_postal_vote_received'],
                                              //'total_evm_postal__vote'=> $postalvotetotal[0]['total_evm_postal__vote'],
                                              'total_valid_postal_vote'=> $postalvotetotal[0]['total_valid_postal_vote'],
                                              'total_evm_vote'=> $postalvotetotal[0]['total_evm_vote'],
                                              'rejected_postal_vote'=> $postalvotetotal[0]['rejected_postal_vote'],
                                              'tended_votes'=> $postalvotetotal[0]['tended_votes'],


                              ));


            $totalvote =  DB::select("SELECT SUM(ecoi.general_male_voters+ecoi.nri_male_voters) AS totalMaleVoters,
                          SUM(ecoi.general_female_voters+ecoi.nri_female_voters) AS totalFemaleVoters,
                          SUM(ecoi.general_other_voters+ecoi.nri_other_voters) AS totalOtherVoters,
                          SUM(ecoi.total_polling_station_s_i_t_c) AS totalpollingstation,
                          SUM(ecoi.votes_not_retreived_from_evm) AS votes_not_retreived_from_evm ,
                          SUM(ecoi.rejected_votes_due_2_other_reason) AS rejected_votes_due_2_other_reason ,
                          SUM(ecoi.proxy_votes) AS proxy_votes

                          FROM electors_cdac_other_information AS ecoi

                          INNER JOIN m_election_details AS med ON med.st_code = ecoi.ST_CODE AND med.CONST_NO = ecoi.PC_NO


                          WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");




            $totalvote = array_map(function ($value) {
                                          return (array)$value;
                                }, $totalvote);






            $notavote = DB::select("SELECT SUM(cp.evm_vote+cp.migrate_votes) AS evmnota,
                        SUM(cp.postal_vote) AS postalnota
                        FROM `counting_pcmaster` AS cp
                        INNER JOIN m_election_details AS med ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
                        WHERE cp.candidate_id = '4319' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");

                       $fddata = DB::select("SELECT SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
                       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdmale,

                       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
                       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdfemale,

                       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
                       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdthird,


                       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
                       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdtotal

                       FROM `counting_pcmaster` AS cp
                       JOIN candidate_personal_detail AS C ON C.candidate_id = cp.candidate_id
                       INNER JOIN m_election_details AS med ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
                       WHERE cp.candidate_id != (SELECT candidate_id FROM winning_leading_candidate AS w1
                       WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1')
                       AND cp.candidate_id != 4319");

           $wincandidatedatemale = DB::select("SELECT COUNT(leading_id) AS totalwinnermale
                                  FROM winning_leading_candidate AS wlc
                                  INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                                  INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                                  WHERE cpd.cand_gender = 'male' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");
          $wincandidatedatefemale = DB::select("SELECT COUNT(leading_id) AS totalwinnerfemale
                                  FROM winning_leading_candidate AS wlc
                                  INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                                  INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                                  WHERE cpd.cand_gender = 'female' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");

          $wincandidatedatethird = DB::select("   SELECT COUNT(leading_id) AS totalwinnerthird
                                  FROM winning_leading_candidate AS wlc
                                  INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                                  INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                                  WHERE cpd.cand_gender = 'third' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1' ");


           $totalnominatedmale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedmale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                              WHERE cpd.cand_gender = 'male' AND wlc.application_status = '6' AND wlc.finalaccepted = '1' AND wlc.candidate_id != '4319'
                              AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");

          $totalnominatedfemale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedfemale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                              WHERE cpd.cand_gender = 'female' AND wlc.application_status = '6' AND wlc.finalaccepted = '1' AND wlc.candidate_id != '4319'
                              AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");

          $totalnominatedthird = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedthird
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                              WHERE cpd.cand_gender = 'third' AND wlc.application_status = '6' AND wlc.finalaccepted = '1' AND wlc.candidate_id != '4319'
                              AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");


          $noofrepolls = DB::select("SELECT COUNT(date_of_repoll) AS total_repoll FROM `electors_cdac_other_information`
          INNER JOIN m_election_details AS med ON med.st_code = electors_cdac_other_information.st_code AND med.CONST_NO = electors_cdac_other_information.pc_no
          WHERE  electors_cdac_other_information.date_of_repoll !='Null' AND med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1'");

          //echo "<pre>"; print_r($noofrepolls); die;


            $wincandidatedatemale = array_map(function ($value) {
                          return (array)$value;
                        }, $wincandidatedatemale);

            $noofrepolls = array_map(function ($value) {
                          return (array)$value;
                        }, $noofrepolls);

            $wincandidatedatefemale = array_map(function ($value) {
                          return (array)$value;
                        }, $wincandidatedatefemale);

            $wincandidatedatethird = array_map(function ($value) {
                          return (array)$value;
                        }, $wincandidatedatethird);

             $totalnominatedmale = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedmale);

        $totalnominatedfemale = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedfemale);

        $totalnominatedthird = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedthird);


           $fddata = array_map(function ($value) {
                         return (array)$value;
                       }, $fddata);

            $notavote = array_map(function ($value) {
                          return (array)$value;
                        }, $notavote);

            $contestents = array_merge($contestents,array(

                  'totalMaleVoters'=> $totalvote[0]['totalMaleVoters'],
                  'totalFemaleVoters'=> $totalvote[0]['totalFemaleVoters'],
                  'totalOtherVoters'=> $totalvote[0]['totalOtherVoters'],
                  'votes_not_retreived_from_evm'=> $totalvote[0]['votes_not_retreived_from_evm'],
                  'rejected_votes_due_2_other_reason'=> $totalvote[0]['rejected_votes_due_2_other_reason'],
                  'totalpollingstation'=> $totalvote[0]['totalpollingstation'],
                  'proxy_votes'=> $totalvote[0]['proxy_votes'],
                  'evmnota'=> $notavote[0]['evmnota'],
                  'postalnota'=> $notavote[0]['postalnota'],
                  'total_repoll'=> $noofrepolls[0]['total_repoll'],

                  'fdmale'=> $fddata[0]['fdmale'],
                  'fdfemale'=> $fddata[0]['fdfemale'],
                  'fdthird'=> $fddata[0]['fdthird'],
                  'fdtotal'=> $fddata[0]['fdtotal'],

                  'totalwinnermale'=> $wincandidatedatemale[0]['totalwinnermale'],
                  'totalwinnerfemale'=> $wincandidatedatefemale[0]['totalwinnerfemale'],
                  'totalwinnerthird'=> $wincandidatedatethird[0]['totalwinnerthird'],

                   'totalnominatedmale'=> $totalnominatedmale[0]['totalnominatedmale'],
                    'totalnominatedfemale'=> $totalnominatedfemale[0]['totalnominatedfemale'],
                    'totalnominatedthird'=> $totalnominatedthird[0]['totalnominatedthird'],
                ));

               // echo "<pre>"; print_r($contestents); die;

 return view('IndexCardReports/StatisticalReports/Vol2/highlights', compact('contestents','user_data'));

}

public function highlightspdf(){
//highlights-pdf

$pctypecount = DB::select("SELECT SUM(CASE WHEN pc_type = 'GEN' THEN 1 ELSE 0 END) AS genpc,
      SUM(CASE WHEN pc_type = 'SC' THEN 1 ELSE 0 END) AS scpc,
      SUM(CASE WHEN pc_type = 'ST' THEN 1 ELSE 0 END) AS stpc
    FROM m_pc inner join m_election_details as med on med.st_code = m_pc.ST_CODE and med.CONST_NO = m_pc.PC_NO
    where med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1'");

$pctypecount = array_map(function ($value) {
return (array)$value;
}, $pctypecount);



$pctypecount = json_decode(json_encode($pctypecount));

$contestents = DB::select("SELECT COUNT(cand_count) AS 'No_of_Seats',
    SUM(CASE WHEN cand_count = 1 THEN 1 ELSE 0 END) AS 'one' ,
    SUM(CASE WHEN cand_count = 2 THEN 1 ELSE 0 END) AS 'two',
    SUM(CASE WHEN cand_count = 3 THEN 1 ELSE 0 END) AS 'three',
    SUM(CASE WHEN cand_count = 4 THEN 1 ELSE 0 END) AS 'four',
    SUM(CASE WHEN cand_count = 5 THEN 1 ELSE 0 END) AS 'five',
    SUM(CASE WHEN cand_count > 5 AND cand_count <= 10 THEN 1 ELSE 0 END) AS 'fiveten',
    SUM(CASE WHEN cand_count > 10 AND cand_count <= 15 THEN 1 ELSE 0 END) AS 'tenfifteen',
    SUM(CASE WHEN cand_count > 15  THEN 1 ELSE 0 END) AS 'fifteen',

    SUM(cand_count) AS 'Total_Candidates',
    MIN(cand_count) as maxcnd,MAX(cand_count) as mincnd,ROUND(SUM(cand_count)/COUNT(cand_count),0) AS 'Avg' FROM (SELECT cnd.st_code,cnd.pc_no,
    COUNT(cnd.candidate_id) 'cand_count' FROM candidate_nomination_detail as cnd
    inner join m_election_details as med on med.st_code = cnd.ST_CODE and med.CONST_NO = cnd.PC_NO
    WHERE cnd.application_status = 6 AND cnd.finalaccepted = 1 AND cnd.candidate_id!= '4319' and med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1' GROUP BY cnd.st_code, cnd.pc_no) a");

$contestents = array_map(function ($value) {
return (array)$value;
}, $contestents);

$contestents = array_merge($contestents[0],array(

                'genpc'=> $pctypecount[0]->genpc,
                'scpc'=> $pctypecount[0]->scpc,
                'stpc'=> $pctypecount[0]->stpc,



                ));



$electorsvotersdata = DB::select("SELECT
          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS maleElectors,
          SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS femaleElectors,
          SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS thirdElectors,
          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+
            ec.service_female_electors+ec.nri_female_electors+ec.gen_electors_other+
            ec.nri_third_electors+ec.service_third_electors) AS totalElectors,
          SUM(ec.service_male_electors) AS maleServiceElector,
          SUM(ec.service_female_electors) AS femaleServiceElector

          FROM electors_cdac as ec

          inner join m_election_details as med on med.st_code = ec.ST_CODE and med.CONST_NO = ec.PC_NO


          WHERE ec.YEAR = '2019' and  med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1'");

                      $electorsvotersdata = array_map(function ($value) {
                      return (array)$value;
                      }, $electorsvotersdata);




                      $contestents = array_merge($contestents,array(

                                      'maleElectors'=> $electorsvotersdata[0]['maleElectors'],
                                      'femaleElectors'=> $electorsvotersdata[0]['femaleElectors'],
                                      'thirdElectors'=> $electorsvotersdata[0]['thirdElectors'],
                                      'totalElectors'=> $electorsvotersdata[0]['totalElectors'],
                                      'maleServiceElector'=> $electorsvotersdata[0]['maleServiceElector'],
                                      'femaleServiceElector'=> $electorsvotersdata[0]['femaleServiceElector'],
                      ));


        $postalvotetotal = DB::select("SELECT SUM(totalpostalvotereceived) AS total_postal_vote_received,
          SUM(totalevmvote) AS total_evm_postal_vote,SUM(tended_votes) AS tended_votes,
          SUM(totalvalidpostalvote) AS total_valid_postal_vote,SUM(evm_vote) AS total_evm_vote,
          SUM(rejectedpostalvote) AS rejected_postal_vote
          FROM
          (SELECT cp.st_code,cp.pc_no,a.postaltotalvote AS 'totalpostalvotereceived',
          SUM(cp.total_vote) AS 'totalevmvote',
          SUM(cp.postal_vote) AS 'totalvalidpostalvote',
          SUM(cp.tended_votes) AS 'tended_votes',
          SUM(cp.evm_vote+cp.migrate_votes) 'evm_vote', a.rejectedvote AS 'rejectedpostalvote'   FROM counting_pcmaster cp,m_election_details med,
          (SELECT st_code,pc_no,rejectedvote,postaltotalvote FROM counting_pcmaster  GROUP BY st_code, pc_no) a
           WHERE cp.st_code = a.st_code AND cp.pc_no = a.pc_no AND med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
           AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1' GROUP BY cp.st_code,cp.pc_no) b");


        $postalvotetotal = array_map(function ($value) {
                          return (array)$value;
                          }, $postalvotetotal);


      $contestents = array_merge($contestents,array(

                                          'total_postal_vote_received'=> $postalvotetotal[0]['total_postal_vote_received'],
                                          'total_evm_postal__vote'=> $postalvotetotal[0]['total_evm_postal_vote'],
                                          'total_valid_postal_vote'=> $postalvotetotal[0]['total_valid_postal_vote'],
                                          'total_evm_vote'=> $postalvotetotal[0]['total_evm_vote'],
                                          'rejected_postal_vote'=> $postalvotetotal[0]['rejected_postal_vote'],
                                          'tended_votes'=> $postalvotetotal[0]['tended_votes'],


                          ));


        $totalvote =  DB::select("SELECT SUM(general_male_voters+nri_male_voters) AS totalMaleVoters,
                      SUM(general_female_voters+nri_female_voters) AS totalFemaleVoters,
                      SUM(general_other_voters+nri_other_voters) AS totalOtherVoters,
                      SUM(total_polling_station_s_i_t_c) AS totalpollingstation,
                      sum(votes_not_retreived_from_evm) as votes_not_retreived_from_evm ,
                      sum(rejected_votes_due_2_other_reason) as rejected_votes_due_2_other_reason ,
                      sum(proxy_votes) as proxy_votes

                    FROM electors_cdac_other_information");




        $totalvote = array_map(function ($value) {
                                      return (array)$value;
                            }, $totalvote);






        $notavote = DB::select("SELECT sum(evm_vote+migrate_votes) as evmnota,
                  sum(postal_vote) as postalnota FROM `counting_pcmaster`
                  WHERE candidate_id = '4319'");

       $fddata = DB::select("SELECT


       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdmale,

       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdfemale,

       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdthird,


       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdtotal

       FROM `counting_pcmaster` AS cp
       JOIN candidate_personal_detail AS C ON C.candidate_id = cp.candidate_id
       WHERE cp.candidate_id != (SELECT candidate_id FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code)
       AND cp.candidate_id != 4319");

       $wincandidatedatemale = DB::select("SELECT COUNT(leading_id) AS totalwinnermale
                              FROM winning_leading_candidate AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'male'");
      $wincandidatedatefemale = DB::select("SELECT COUNT(leading_id) AS totalwinnerfemale
                              FROM winning_leading_candidate AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'female'");
      $wincandidatedatethird = DB::select("SELECT COUNT(leading_id) AS totalwinnerthird
                              FROM winning_leading_candidate AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'third'");


       $totalnominatedmale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedmale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'male' and wlc.application_status = '6' and wlc.finalaccepted = '1' and wlc.candidate_id != '4319'");
          $totalnominatedfemale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedfemale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'female' and wlc.application_status = '6' and wlc.finalaccepted = '1' and wlc.candidate_id != '4319'");
          $totalnominatedthird = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedthird
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'third' and wlc.application_status = '6' and wlc.finalaccepted = '1' and wlc.candidate_id != '4319'");

          $noofrepolls = DB::select("SELECT count(date_of_repoll) as total_repoll FROM `electors_cdac_other_information` WHERE date_of_repoll !='Null'");



        $wincandidatedatemale = array_map(function ($value) {
                      return (array)$value;
                    }, $wincandidatedatemale);

         $noofrepolls = array_map(function ($value) {
                          return (array)$value;
                        }, $noofrepolls);

        $wincandidatedatefemale = array_map(function ($value) {
                      return (array)$value;
                    }, $wincandidatedatefemale);

        $wincandidatedatethird = array_map(function ($value) {
                      return (array)$value;
                    }, $wincandidatedatethird);

        $totalnominatedmale = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedmale);

        $totalnominatedfemale = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedfemale);

        $totalnominatedthird = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedthird);

       $fddata = array_map(function ($value) {
                     return (array)$value;
                   }, $fddata);

        $notavote = array_map(function ($value) {
                      return (array)$value;
                    }, $notavote);

        $contestents = array_merge($contestents,array(

              'totalMaleVoters'=> $totalvote[0]['totalMaleVoters'],
              'totalFemaleVoters'=> $totalvote[0]['totalFemaleVoters'],
              'totalOtherVoters'=> $totalvote[0]['totalOtherVoters'],
              'votes_not_retreived_from_evm'=> $totalvote[0]['votes_not_retreived_from_evm'],
              'rejected_votes_due_2_other_reason'=> $totalvote[0]['rejected_votes_due_2_other_reason'],
              'totalpollingstation'=> $totalvote[0]['totalpollingstation'],
              'proxy_votes'=> $totalvote[0]['proxy_votes'],
              'evmnota'=> $notavote[0]['evmnota'],
              'postalnota'=> $notavote[0]['postalnota'],

              'fdmale'=> $fddata[0]['fdmale'],
              'fdfemale'=> $fddata[0]['fdfemale'],
              'fdthird'=> $fddata[0]['fdthird'],
              'fdtotal'=> $fddata[0]['fdtotal'],
              'total_repoll'=> $noofrepolls[0]['total_repoll'],

              'totalwinnermale'=> $wincandidatedatemale[0]['totalwinnermale'],
              'totalwinnerfemale'=> $wincandidatedatefemale[0]['totalwinnerfemale'],
              'totalwinnerthird'=> $wincandidatedatethird[0]['totalwinnerthird'],

               'totalnominatedmale'=> $totalnominatedmale[0]['totalnominatedmale'],
               'totalnominatedfemale'=> $totalnominatedfemale[0]['totalnominatedfemale'],
               'totalnominatedthird'=> $totalnominatedthird[0]['totalnominatedthird'],
            ));


$pdf=PDF::loadView('IndexCardReports/StatisticalReports/Vol2/highlightspdf',compact('contestents'));
return $pdf->download('1heighlightspdf.pdf');

}

public function highlightsexcel(){

$pctypecount = DB::select("SELECT SUM(CASE WHEN pc_type = 'GEN' THEN 1 ELSE 0 END) AS genpc,
      SUM(CASE WHEN pc_type = 'SC' THEN 1 ELSE 0 END) AS scpc,
      SUM(CASE WHEN pc_type = 'ST' THEN 1 ELSE 0 END) AS stpc
    FROM m_pc inner join m_election_details as med on med.st_code = m_pc.ST_CODE and med.CONST_NO = m_pc.PC_NO
    where med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1'");

$pctypecount = array_map(function ($value) {
return (array)$value;
}, $pctypecount);



$pctypecount = json_decode(json_encode($pctypecount));

$contestents = DB::select("SELECT COUNT(cand_count) AS 'No_of_Seats',
    SUM(CASE WHEN cand_count = 1 THEN 1 ELSE 0 END) AS 'one' ,
    SUM(CASE WHEN cand_count = 2 THEN 1 ELSE 0 END) AS 'two',
    SUM(CASE WHEN cand_count = 3 THEN 1 ELSE 0 END) AS 'three',
    SUM(CASE WHEN cand_count = 4 THEN 1 ELSE 0 END) AS 'four',
    SUM(CASE WHEN cand_count = 5 THEN 1 ELSE 0 END) AS 'five',
    SUM(CASE WHEN cand_count > 5 AND cand_count <= 10 THEN 1 ELSE 0 END) AS 'fiveten',
    SUM(CASE WHEN cand_count > 10 AND cand_count <= 15 THEN 1 ELSE 0 END) AS 'tenfifteen',
    SUM(CASE WHEN cand_count > 15  THEN 1 ELSE 0 END) AS 'fifteen',

    SUM(cand_count) AS 'Total_Candidates',
    MIN(cand_count) as maxcnd,MAX(cand_count) as mincnd,ROUND(SUM(cand_count)/COUNT(cand_count),0) AS 'Avg' FROM (SELECT cnd.st_code,cnd.pc_no,
    COUNT(cnd.candidate_id) 'cand_count' FROM candidate_nomination_detail as cnd
    inner join m_election_details as med on med.st_code = cnd.ST_CODE and med.CONST_NO = cnd.PC_NO
    WHERE cnd.application_status = 6 AND cnd.finalaccepted = 1 AND cnd.candidate_id!= '4319' and med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1' GROUP BY cnd.st_code, cnd.pc_no) a");

$contestents = array_map(function ($value) {
return (array)$value;
}, $contestents);

$contestents = array_merge($contestents[0],array(

                'genpc'=> $pctypecount[0]->genpc,
                'scpc'=> $pctypecount[0]->scpc,
                'stpc'=> $pctypecount[0]->stpc,



                ));



$electorsvotersdata = DB::select("SELECT
          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors) AS maleElectors,
          SUM(ec.gen_electors_female+ec.service_female_electors+ec.nri_female_electors) AS femaleElectors,
          SUM(ec.gen_electors_other+ec.nri_third_electors+ec.service_third_electors) AS thirdElectors,
          SUM(ec.gen_electors_male+ec.service_male_electors+ec.nri_male_electors+ec.gen_electors_female+
            ec.service_female_electors+ec.nri_female_electors+ec.gen_electors_other+
            ec.nri_third_electors+ec.service_third_electors) AS totalElectors,
          SUM(ec.service_male_electors) AS maleServiceElector,
          SUM(ec.service_female_electors) AS femaleServiceElector

          FROM electors_cdac as ec

          inner join m_election_details as med on med.st_code = ec.ST_CODE and med.CONST_NO = ec.PC_NO


          WHERE ec.YEAR = '2019' and  med.CONST_TYPE = 'PC' and  med.election_status = '1' and med.ELECTION_ID = '1'");

                      $electorsvotersdata = array_map(function ($value) {
                      return (array)$value;
                      }, $electorsvotersdata);




                      $contestents = array_merge($contestents,array(

                                      'maleElectors'=> $electorsvotersdata[0]['maleElectors'],
                                      'femaleElectors'=> $electorsvotersdata[0]['femaleElectors'],
                                      'thirdElectors'=> $electorsvotersdata[0]['thirdElectors'],
                                      'totalElectors'=> $electorsvotersdata[0]['totalElectors'],
                                      'maleServiceElector'=> $electorsvotersdata[0]['maleServiceElector'],
                                      'femaleServiceElector'=> $electorsvotersdata[0]['femaleServiceElector'],
                      ));


        $postalvotetotal = DB::select("SELECT SUM(totalpostalvotereceived) AS total_postal_vote_received,
          SUM(totalevmvote) AS total_evm_postal_vote,SUM(tended_votes) AS tended_votes,
          SUM(totalvalidpostalvote) AS total_valid_postal_vote,SUM(evm_vote) AS total_evm_vote,
          SUM(rejectedpostalvote) AS rejected_postal_vote
          FROM
          (SELECT cp.st_code,cp.pc_no,a.postaltotalvote AS 'totalpostalvotereceived',
          SUM(cp.total_vote) AS 'totalevmvote',
          SUM(cp.postal_vote) AS 'totalvalidpostalvote',
          SUM(cp.tended_votes) AS 'tended_votes',
          SUM(cp.evm_vote+cp.migrate_votes) 'evm_vote', a.rejectedvote AS 'rejectedpostalvote'   FROM counting_pcmaster cp,m_election_details med,
          (SELECT st_code,pc_no,rejectedvote,postaltotalvote FROM counting_pcmaster  GROUP BY st_code, pc_no) a
           WHERE cp.st_code = a.st_code AND cp.pc_no = a.pc_no AND med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
           AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1' AND med.ELECTION_ID = '1' GROUP BY cp.st_code,cp.pc_no) b");


        $postalvotetotal = array_map(function ($value) {
                          return (array)$value;
                          }, $postalvotetotal);


      $contestents = array_merge($contestents,array(

                                          'total_postal_vote_received'=> $postalvotetotal[0]['total_postal_vote_received'],
                                          'total_evm_postal__vote'=> $postalvotetotal[0]['total_evm_postal_vote'],
                                          'total_valid_postal_vote'=> $postalvotetotal[0]['total_valid_postal_vote'],
                                          'total_evm_vote'=> $postalvotetotal[0]['total_evm_vote'],
                                          'rejected_postal_vote'=> $postalvotetotal[0]['rejected_postal_vote'],
                                          'tended_votes'=> $postalvotetotal[0]['tended_votes'],


                          ));


        $totalvote =  DB::select("SELECT SUM(general_male_voters+nri_male_voters) AS totalMaleVoters,
                      SUM(general_female_voters+nri_female_voters) AS totalFemaleVoters,
                      SUM(general_other_voters+nri_other_voters) AS totalOtherVoters,
                      SUM(total_polling_station_s_i_t_c) AS totalpollingstation,
                      sum(votes_not_retreived_from_evm) as votes_not_retreived_from_evm ,
                      sum(rejected_votes_due_2_other_reason) as rejected_votes_due_2_other_reason ,
                      sum(proxy_votes) as proxy_votes

                    FROM electors_cdac_other_information");




        $totalvote = array_map(function ($value) {
                                      return (array)$value;
                            }, $totalvote);






        $notavote = DB::select("SELECT sum(evm_vote) as evmnota,
                  sum(postal_vote) as postalnota FROM `counting_pcmaster`
                  WHERE candidate_id = '4319'");

       $fddata = DB::select("SELECT


       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdmale,

       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdfemale,

       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code AND  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdthird,


       SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) AS pctotalvotes FROM `counting_pcmaster` AS cp1
       WHERE cp1.pc_no = cp.pc_no AND cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) AS fdtotal

       FROM `counting_pcmaster` AS cp
       JOIN candidate_personal_detail AS C ON C.candidate_id = cp.candidate_id
       WHERE cp.candidate_id != (SELECT candidate_id FROM winning_leading_candidate AS w1 WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code)
       AND cp.candidate_id != 4319");

       $noofrepolls = DB::select("SELECT count(date_of_repoll) as total_repoll FROM `electors_cdac_other_information` WHERE date_of_repoll !='Null'");




         $noofrepolls = array_map(function ($value) {
                          return (array)$value;
                        }, $noofrepolls);

       $wincandidatedatemale = DB::select("SELECT COUNT(leading_id) AS totalwinnermale
                              FROM winning_leading_candidate AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'male'");
      $wincandidatedatefemale = DB::select("SELECT COUNT(leading_id) AS totalwinnerfemale
                              FROM winning_leading_candidate AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'female'");
      $wincandidatedatethird = DB::select("SELECT COUNT(leading_id) AS totalwinnerthird
                              FROM winning_leading_candidate AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'third'");

      $totalnominatedmale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedmale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'male' and wlc.application_status = '6' and wlc.finalaccepted = '1' and wlc.candidate_id != '4319'");
      $totalnominatedfemale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedfemale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'female' and wlc.application_status = '6' and wlc.finalaccepted = '1' and wlc.candidate_id != '4319'");
      $totalnominatedthird = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedthird
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              WHERE cpd.cand_gender = 'third' and wlc.application_status = '6' and wlc.finalaccepted = '1' and wlc.candidate_id != '4319'");


        $wincandidatedatemale = array_map(function ($value) {
                      return (array)$value;
                    }, $wincandidatedatemale);

        $wincandidatedatefemale = array_map(function ($value) {
                      return (array)$value;
                    }, $wincandidatedatefemale);

        $wincandidatedatethird = array_map(function ($value) {
                      return (array)$value;
                    }, $wincandidatedatethird);

        $totalnominatedmale = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedmale);

        $totalnominatedfemale = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedfemale);

        $totalnominatedthird = array_map(function ($value) {
                      return (array)$value;
                    }, $totalnominatedthird);

       $fddata = array_map(function ($value) {
                     return (array)$value;
                   }, $fddata);

        $notavote = array_map(function ($value) {
                      return (array)$value;
                    }, $notavote);

        $contestents = array_merge($contestents,array(

              'totalMaleVoters'=> $totalvote[0]['totalMaleVoters'],
              'totalFemaleVoters'=> $totalvote[0]['totalFemaleVoters'],
              'totalOtherVoters'=> $totalvote[0]['totalOtherVoters'],
              'votes_not_retreived_from_evm'=> $totalvote[0]['votes_not_retreived_from_evm'],
              'rejected_votes_due_2_other_reason'=> $totalvote[0]['rejected_votes_due_2_other_reason'],
              'totalpollingstation'=> $totalvote[0]['totalpollingstation'],
              'proxy_votes'=> $totalvote[0]['proxy_votes'],
              'evmnota'=> $notavote[0]['evmnota'],
              'postalnota'=> $notavote[0]['postalnota'],

              'fdmale'=> $fddata[0]['fdmale'],
              'fdfemale'=> $fddata[0]['fdfemale'],
              'fdthird'=> $fddata[0]['fdthird'],
              'fdtotal'=> $fddata[0]['fdtotal'],
              'total_repoll'=> $noofrepolls[0]['total_repoll'],

              'totalwinnermale'=> $wincandidatedatemale[0]['totalwinnermale'],
              'totalwinnerfemale'=> $wincandidatedatefemale[0]['totalwinnerfemale'],
              'totalwinnerthird'=> $wincandidatedatethird[0]['totalwinnerthird'],

              'totalnominatedmale'=> $totalnominatedmale[0]['totalnominatedmale'],
              'totalnominatedfemale'=> $totalnominatedfemale[0]['totalnominatedfemale'],
              'totalnominatedthird'=> $totalnominatedthird[0]['totalnominatedthird'],
            ));






            return Excel::create('highlight'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($contestents) {
                         $excel->sheet('mySheet', function($sheet) use ($contestents) {
                             $sheet->mergeCells('A1:K1');
                             $sheet->cells('A1', function($cells) {
                               $cells->setValue('Highlight');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                                 $cells->setFontColor("#ffffff");
                                 $cells->setBackground("#042179");
                                 $cells->setAlignment('center');
                             });


                             $sheet->cells('A2', function($cells) {
                                $cells->setValue('1.');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });


                             $sheet->cells('B2', function($cells) {
                                $cells->setValue('1. No. Of Constituency');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });
                             $sheet->cells('B3', function($cells) {
                                $cells->setValue('Type Of Constituency');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });
                             $sheet->cells('B4', function($cells) {
                                $cells->setValue('Number Of Constituency');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                             $sheet->cell('C3' ,'GEN');
                             $sheet->cell('D3' ,'SC');
                             $sheet->cell('E3' ,'ST');
                             $sheet->cell('F3' ,'Total');

                           $sheet->cell('C4' ,$contestents['genpc']);
                           $sheet->cell('D4' ,$contestents['scpc']);
                           $sheet->cell('E4' ,$contestents['stpc']);
                           $sheet->cell('F4' ,$contestents['genpc']+$contestents['scpc']+$contestents['stpc']);


                           $sheet->cells('A5', function($cells) {
                              $cells->setValue('2');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cells('B5', function($cells) {
                              $cells->setValue('No. of Contestants');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cells('B6', function($cells) {
                              $cells->setValue('No of Contstants in a Constituency');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cell('C6' ,'1');
                           $sheet->cell('D6' ,'2');
                           $sheet->cell('E6' ,'3');
                           $sheet->cell('F6' ,'4');
                           $sheet->cell('G6' ,'5');
                           $sheet->cell('H6' ,'6-10');
                           $sheet->cell('I6' ,'11-15');
                           $sheet->cell('J6' ,'Above 15');

                           $sheet->cells('B7', function($cells) {
                              $cells->setValue('Number Of Such Constituency');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                              $sheet->cell('C7' ,$contestents['one']? :'=(0)');
                              $sheet->cell('D7' ,$contestents['two']? :'=(0)');
                              $sheet->cell('E7' ,$contestents['three']? :'=(0)');
                              $sheet->cell('F7' ,$contestents['four']? :'=(0)');
                              $sheet->cell('G7' ,$contestents['five']? :'=(0)');
                              $sheet->cell('H7' ,$contestents['fiveten']);
                              $sheet->cell('I7' ,$contestents['tenfifteen']);
                              $sheet->cell('J7' ,$contestents['fifteen']);



                               $sheet->cells('B8', function($cells) {
                                  $cells->setValue('Total Contestants in a Fray');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });
                            $sheet->cell('J8' ,$contestents['Total_Candidates']);


                               $sheet->cells('B9', function($cells) {
                                  $cells->setValue('Average contestants per constituency');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });
                            $sheet->cell('J9' ,$contestents['Avg']);

                               $sheet->cells('B10', function($cells) {
                                  $cells->setValue('Minimum contestants in a constituency');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });

                            $sheet->cell('J10' ,$contestents['maxcnd']);

                               $sheet->cells('B11', function($cells) {
                                  $cells->setValue('Maximum contestants in a constituency');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });
                            $sheet->cell('J11' ,$contestents['mincnd']);



                               $sheet->cells('A12', function($cells) {
                                  $cells->setValue('3');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });


                               $sheet->cells('B12', function($cells) {
                                  $cells->setValue('Electors');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });

                               $sheet->cells('C12', function($cells) {
                                  $cells->setValue('Male');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });
                               $sheet->cells('D12', function($cells) {
                                  $cells->setValue('Female');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });
                               $sheet->cells('E12', function($cells) {
                                  $cells->setValue('Third Gender');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });

                               $sheet->cells('F12', function($cells) {
                                  $cells->setValue('Total');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });

                               $sheet->cells('A13', function($cells) {
                                  $cells->setValue('i');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });

                               $sheet->cells('B13', function($cells) {
                                  $cells->setValue('Number of Electors');
                                  $cells->setAlignment('center');
                                  $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                               });
                              $sheet->cell('C13' ,$contestents['maleElectors']);
                              $sheet->cell('D13' ,$contestents['femaleElectors']);
                              $sheet->cell('E13' ,$contestents['thirdElectors']);
                              $sheet->cell('F13' ,$contestents['totalElectors']);

                              $sheet->cells('A14', function($cells) {
                                 $cells->setValue('ii');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('B14', function($cells) {
                                 $cells->setValue('No. of electors who voted');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });
                              $sheet->cell('C14' ,$contestents['totalMaleVoters']);
                              $sheet->cell('D14' ,$contestents['totalFemaleVoters']);
                              $sheet->cell('E14' ,$contestents['totalOtherVoters']);
                              $sheet->cell('F14' ,$contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters']);

                              $sheet->cells('A15', function($cells) {
                                 $cells->setValue('iii');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('B15', function($cells) {
                                 $cells->setValue('POLLING PERCENTAGE');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });
                              $sheet->cell('C15' ,round($contestents['totalMaleVoters']/$contestents['maleElectors'] * 100,2));
                              $sheet->cell('D15' ,round($contestents['totalFemaleVoters']/$contestents['femaleElectors'] * 100,2));
                              $sheet->cell('E15' ,round($contestents['totalOtherVoters']/$contestents['thirdElectors'] * 100,2));
                              $sheet->cell('F15' ,round(($contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters'])/$contestents['totalElectors']*100,2));

                              $sheet->cells('A16', function($cells) {
                                 $cells->setValue('4');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('B16', function($cells) {
                                 $cells->setValue('NO. OF SERVICE ELECTORS');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('A17', function($cells) {
                                 $cells->setValue('i');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('B17', function($cells) {
                                 $cells->setValue('MALE');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                            $sheet->cell('F17' ,$contestents['maleServiceElector']);

                              $sheet->cells('A18', function($cells) {
                                 $cells->setValue('ii');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('B18', function($cells) {
                                 $cells->setValue('FEMALE');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });


                            $sheet->cell('F18' ,$contestents['femaleServiceElector']);

                              $sheet->cells('A19', function($cells) {
                                 $cells->setValue('5');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('B19', function($cells) {
                                 $cells->setValue('NUMBER OF POSTAL BALLOT RECEIVED');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cell('F19' ,$contestents['total_postal_vote_received']);

                              $sheet->cells('A20', function($cells) {
                                 $cells->setValue('6');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });

                              $sheet->cells('B20', function($cells) {
                                 $cells->setValue('POLL %(INCLUDING POSTAL BALLOT)');
                                 $cells->setAlignment('center');
                                 $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                              });
                             $total = $contestents['total_evm_vote']+$contestents['total_postal_vote_received'];
                            $sheet->cell('F20' ,round($total/$contestents['totalElectors']*120,2));

                            $sheet->cells('A21', function($cells) {
                               $cells->setValue('7.');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });

                            $sheet->cells('B21', function($cells) {
                               $cells->setValue('NUMBER OF VALID VOTES');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });

                            $sheet->cells('B22', function($cells) {
                               $cells->setValue('VALID VOTES POLLED ON EVM');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });
                           $sheet->cell('F22' ,$contestents['total_evm_vote']);

                            $sheet->cells('B23', function($cells) {
                               $cells->setValue('VALID POSTAL VOTES');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });


                          $sheet->cell('F23' ,$contestents['total_valid_postal_vote']);

                            $sheet->cells('A24', function($cells) {
                               $cells->setValue('8.');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });

                            $sheet->cells('B24', function($cells) {
                               $cells->setValue('TOTAL NOTA VOTES');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });

                            $sheet->cells('A25', function($cells) {
                               $cells->setValue('i');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });

                            $sheet->cells('B25', function($cells) {
                               $cells->setValue('NOTA VOTES ON EVM');
                               $cells->setAlignment('center');
                               $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                            });
                          $sheet->cell('F25' ,$contestents['evmnota']);


                          $sheet->cells('A26', function($cells) {
                             $cells->setValue('i');
                             $cells->setAlignment('center');
                             $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });

                          $sheet->cells('B26', function($cells) {
                             $cells->setValue('NOTA VOTES ON POSTAL BALLOT');
                             $cells->setAlignment('center');
                             $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });
                          $sheet->cell('F26' ,$contestents['postalnota']);

                          $sheet->cells('A27', function($cells) {
                             $cells->setValue('9.');
                             $cells->setAlignment('center');
                             $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });

                          $sheet->cells('B27', function($cells) {
                             $cells->setValue('NO. OF VOTES REJECTED');
                             $cells->setAlignment('center');
                             $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });


                          $sheet->cells('A28', function($cells) {
                             $cells->setValue('i');
                             $cells->setAlignment('center');
                             $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });

                          $sheet->cells('B28', function($cells) {
                             $cells->setValue('POSTAL');
                             $cells->setAlignment('center');
                             $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                          });

                        $sheet->cell('F28' ,$contestents['rejected_postal_vote']);
                        $sheet->cells('A29', function($cells) {
                           $cells->setValue('ii');
                           $cells->setAlignment('center');
                           $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                        });

                        $sheet->cells('B29', function($cells) {
                           $cells->setValue('VOTES NOT RETRIEVED ON EVM');
                           $cells->setAlignment('center');
                           $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                        });

                      $sheet->cell('F29' ,$contestents['votes_not_retreived_from_evm']);


                      $sheet->cells('A30', function($cells) {
                         $cells->setValue('iii');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                      $sheet->cells('B30', function($cells) {
                         $cells->setValue('VOTES REJECTED DUE TO OTHER REASON(AT POLLING STATION');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                    $sheet->cell('F30' ,$contestents['rejected_votes_due_2_other_reason']);

                      $sheet->cells('A31', function($cells) {
                         $cells->setValue('10');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                      $sheet->cells('B31', function($cells) {
                         $cells->setValue('PROXY VOTES');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                    $sheet->cell('F31' ,$contestents['proxy_votes']);

                      $sheet->cells('A32', function($cells) {
                         $cells->setValue('11');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                      $sheet->cells('B32', function($cells) {
                         $cells->setValue('NO. OF POLLING STATION');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                    $sheet->cell('F32' ,$contestents['totalpollingstation']);

                      $sheet->cells('A33', function($cells) {
                         $cells->setValue('13');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                      $sheet->cells('B33', function($cells) {
                         $cells->setValue('AVERAGE NO. OF ELECTORS PER POLLING STATION');
                         $cells->setAlignment('center');
                         $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                      });

                    $sheet->cell('F33' ,round($contestents['totalElectors']/$contestents['totalpollingstation'],0));



                    $sheet->cells('A34', function($cells) {
                       $cells->setValue('14');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });


                    $sheet->cells('B34', function($cells) {
                       $cells->setValue('NO. OF RE-POLLS HELD');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });

                    $sheet->cell('F34' ,$contestents['total_repoll']);


                    $sheet->cells('A35', function($cells) {
                       $cells->setValue('15.');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });


                    $sheet->cells('B35', function($cells) {
                       $cells->setValue('PERFORMANCE OF CONTESTING CANDIDATES');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });



                    $sheet->cells('C35', function($cells) {
                       $cells->setValue('MALE');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });
                    $sheet->cells('D35', function($cells) {
                       $cells->setValue('FEMALE');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });
                    $sheet->cells('E35', function($cells) {
                       $cells->setValue('THIRD GENDER');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });

                    $sheet->cells('F35', function($cells) {
                       $cells->setValue('TOTAL');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });


                    $sheet->cells('A36', function($cells) {
                       $cells->setValue('I');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });

                    $sheet->cells('B36', function($cells) {
                       $cells->setValue('NO. OF CONTESTANTS');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });



                    $sheet->cell('C36' ,$contestents['totalnominatedmale']);
                    $sheet->cell('D36' ,$contestents['totalnominatedfemale']);
                    $sheet->cell('E36' ,$contestents['totalnominatedthird']);
                    $sheet->cell('F36' ,$contestents['totalnominatedmale']+$contestents['totalnominatedfemale']+$contestents['totalnominatedthird']);

                    $sheet->cells('A37', function($cells) {
                       $cells->setValue('ii');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });

                    $sheet->cells('B37', function($cells) {
                       $cells->setValue('ELECTED');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });

                    $sheet->cell('C37' ,$contestents['totalwinnermale']);
                    $sheet->cell('D37' ,$contestents['totalwinnerfemale']);
                    $sheet->cell('E37' ,$contestents['totalwinnerthird']? :'=(0)');
                    $sheet->cell('F37' ,$contestents['totalwinnermale']+$contestents['totalwinnerfemale']+$contestents['totalwinnerthird']);



                    $sheet->cells('A38', function($cells) {
                       $cells->setValue('ii');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });

                    $sheet->cells('B38', function($cells) {
                       $cells->setValue('FORFEITED DEPOSITS');
                       $cells->setAlignment('center');
                       $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                    });





                    $sheet->cell('C38' ,$contestents['fdmale']);
                    $sheet->cell('D38' ,$contestents['fdfemale']);
                    $sheet->cell('E38' ,$contestents['fdthird']);
                    $sheet->cell('F38' ,$contestents['fdtotal']);
                    });
                 })->export();



}





}
