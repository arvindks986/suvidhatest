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

        ini_set("memory_limit","48000M");
        set_time_limit('6000');
        ini_set("pcre.backtrack_limit", "5000000000");

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
                          //->where('D.election_id', 1)
						  ->where('D.year', getElectionYear())

                         ->where('med.CONST_TYPE', 'PC')
                         ->where('med.election_status', 1)
                         //->where('med.ELECTION_ID', 1)


                          ->groupby ('C.ST_CODE','B.PC_NO','A.ac_no')
                          ->get()->toArray();


                          foreach ($data as  $value) {

                            $datanew[$value->st_name][$value->pc_name]['st_code'] = $value->st_code;
							 $datanew[$value->st_name][$value->pc_name]['pc_no'] = $value->PC_NO;
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
	 
			
			$pdf = PDF::loadView('IndexCardReports.AssemblySegmentWiseInformationElectors.AssemblySegmentWiseInformationElectorsPDF',[

     'datanew'=>$datanew
 ]);

   if(verifyreport(15)){
        
                  $file_name = 'Assembly_Segment_Wise_Information_Electors_Report'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/15/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '15',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
 return $pdf->download('Assembly_Segment_Wise_Information_Electors_Report.pdf');


 }
	elseif($request->path() == "$prefix/AssemblySegmentWiseInformationElectorsXLS"){


 // echo "<pre>"; print_r($datanew); die;

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
                          $cells->setValue('THIRD GENDER');
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
                          $cells->setValue('THIRD GENDER');
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
							  
							  
							     $notretrive = \App\models\Admin\VoterModel::get_notretrive($value2->st_code,$value2->pc_no);
										
										 $sheet->cell('A' . $i, $key);
										 $sheet->cell('B' . $i, 'Votes not retrieved from EVM');
										 $sheet->cell('C' . $i, $key2);
										 $sheet->cell('D' . $i, '=(0)');
										 $sheet->cell('E' . $i, '=(0)');
										 $sheet->cell('F' . $i, '=(0)');
										 $sheet->cell('G' . $i, '=(0)');
										 $sheet->cell('H' . $i, '=(0)');
										 $sheet->cell('I' . $i, '=(0)');
										 $sheet->cell('J' . $i, '=(0)');
										 $sheet->cell('K' . $i, '=(0)');
										 $sheet->cell('L' . $i, '=(0)');
										 $sheet->cell('M' . $i, '=(0)');
										 $sheet->cell('N' . $i, '=(0)');
										 $sheet->cell('O' . $i, '=(0)');
										 $sheet->cell('P' . $i, ($notretrive[0]->votes_not_retreived > 0 ) ? $notretrive[0]->votes_not_retreived : '=(0)');

										$i++;
							  
							  
							  
							   $rejecteddue = \App\models\Admin\VoterModel::get_rejecteddue($value2->st_code,$value2->pc_no);
										
										 $sheet->cell('A' . $i, $key);
										 $sheet->cell('B' . $i, 'Rejected due to other reason');
										 $sheet->cell('C' . $i, $key2);
										 $sheet->cell('D' . $i, '=(0)');
										 $sheet->cell('E' . $i, '=(0)');
										 $sheet->cell('F' . $i, '=(0)');
										 $sheet->cell('G' . $i, '=(0)');
										 $sheet->cell('H' . $i, '=(0)');
										 $sheet->cell('I' . $i, '=(0)');
										 $sheet->cell('J' . $i, '=(0)');
										 $sheet->cell('K' . $i, '=(0)');
										 $sheet->cell('L' . $i, '=(0)');
										 $sheet->cell('M' . $i, '=(0)');
										 $sheet->cell('N' . $i, '=(0)');
										 $sheet->cell('O' . $i, '=(0)');
										 $sheet->cell('P' . $i, ($rejecteddue[0]->rejected_votes_due > 0) ? $rejecteddue[0]->rejected_votes_due : '=(0)' );

										$i++;
							  

							  
							   if($value2->st_code == 'S09' && in_array($value2->pc_no, [1,2,3]) ){
										
										$migratedata = \App\models\Admin\VoterModel::get_migrante($value2->st_code,$value2->pc_no);
										
										 $sheet->cell('A' . $i, $key);
										 $sheet->cell('B' . $i, 'DelhiUdhampurJammu');
										 $sheet->cell('C' . $i, $key2);
										 $sheet->cell('D' . $i, '=(0)');
										 $sheet->cell('E' . $i, '=(0)');
										 $sheet->cell('F' . $i, '=(0)');
										 $sheet->cell('G' . $i, '=(0)');
										 $sheet->cell('H' . $i, '=(0)');
										 $sheet->cell('I' . $i, '=(0)');
										 $sheet->cell('J' . $i, '=(0)');
										 $sheet->cell('K' . $i, '=(0)');
										 $sheet->cell('L' . $i, '=(0)');
										 $sheet->cell('M' . $i, '=(0)');
										 $sheet->cell('N' . $i, '=(0)');
										 $sheet->cell('O' . $i, '=(0)');
										 $sheet->cell('P' . $i, ($migratedata[0]->migrate_votes > 0 ) ? $migratedata[0]->migrate_votes : '=(0)');

										$i++;
										
									 }
                               }
                           }}

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

 }

	}
       
}


   

// winning candidate overseas voters  start

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
AND `winn`.`pc_no` = `cdac`.`pc_no` and cdac.party_id != '1180'
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
AND `winn`.`pc_no` = `cdac`.`pc_no` and cdac.party_id != '1180'
GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
)temp
group by st_code
)
temp1");

///echo "<pre>"; print_r($arrayData);die;
  // $pdf = \App::make('dompdf.wrapper');
		// 	$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/eciwinning-condidate-analysis-over-total-voters-pdf', compact('arrayData'));


                 if(verifyreport(30)){
        
                  $file_name = 'winning-condidate-analysis-over-total-voters'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/30/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '30',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
       return $pdf->download('winning-condidate-analysis-over-total-voters.pdf');

}

public function winningcandidateoverseasevotersxls(Request $request){
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
AND `winn`.`pc_no` = `cdac`.`pc_no` and cdac.party_id != '1180'
GROUP BY `mpc`.`ST_CODE`,cdac.pc_no
)temp
group by st_code
)
temp1");

return Excel::create('winning-condidate-analysis-over-voters'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($arrayData) {
                    $excel->sheet('mySheet', function($sheet) use ($arrayData) {
                        $sheet->mergeCells('A1:J1');
                        $sheet->mergeCells('C2:J2');

                        $sheet->cells('A1', function($cells) {
                            $cells->setValue('WINNING CANDIDATES ANALYSIS OVER TOTAL VALID VOTES');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 14, 'bold' => true));
                            $cells->setAlignment('center');
                        });

                        $sheet->cells('A2', function($cells) {
                            $cells->setValue('Name of State/UT');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('B2', function($cells) {
                            $cells->setValue('No. Of Seats');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
                            $cells->setAlignment('center');
                        });
                        $sheet->cells('C2', function($cells) {
                            $cells->setValue('No. Of Candidates Secured The % Of Votes Over The Total Valid Votes In The Constituency');
                            $cells->setFont(array('name' => 'Times New Roman', 'size' => 10, 'bold' => true));
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
                                $sheet->cell('C' . $i, $values->zero_to_10 ? :'=(0)');
                                $sheet->cell('D' . $i, $values->one_to_20 ? :'=(0)');
                                $sheet->cell('E' . $i, $values->two_to_30 ? :'=(0)');
                                $sheet->cell('F' . $i, $values->three_to_40 ? :'=(0)');
                                $sheet->cell('G' . $i, $values->four_to_50 ? :'=(0)');
                                $sheet->cell('H' . $i, $values->five_to_60 ? :'=(0)');
                                $sheet->cell('I' . $i, $values->six_to_70 ? :'=(0)');
                                $sheet->cell('J' . $i, $values->seven_to_80 ? :'=(0)');

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
                        $sheet->cell('C' . $i, $totalzero_to_10 ? :'=(0)');
                        $sheet->cell('D' . $i, $totalone_to_20 ? :'=(0)');
                        $sheet->cell('E' . $i, $totaltwo_to_30 ? :'=(0)');
                        $sheet->cell('F' . $i, $totalthree_to_40 ? :'=(0)');
                        $sheet->cell('G' . $i, $totalfour_to_50 ? :'=(0)');
                        $sheet->cell('H' . $i, $totalfive_to_60 ? :'=(0)');
                        $sheet->cell('I' . $i, $totalsix_to_70 ? :'=(0)');
                        $sheet->cell('J' . $i, $totalseven_to_80 ? :'=(0)');

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

}
// winning candidate overseas voters end

// Performance of Unrecognised party start


public function performanceofunrecognisedparties(Request $request){
    $user = Auth::user();
    $user_data = $user;
    $performanceofst = DB::select("SELECT TEMP1.PARTYNAME,PARTYTYPE,
totalcontested,DF,totalvalid_valid_vote_party,
IFNULL(WON,0)AS won,SUM(totalvalidvote_state) AS totalvalidvote_state ,SUM(TOTAL_ELECT_VOTE) AS TOTAL_ELECT_VOTE,
SUM(CCC) AS Total_Valid_Vote_State
FROM
(
SELECT *,
(SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
WHERE st_code =TEMP.st_code  and party_id != '1180'
GROUP BY TEMP.st_code) AS CCC,
(SELECT SUM(gen_electors_male+gen_electors_female+gen_electors_other
+service_male_electors+service_female_electors+service_third_electors+
nri_male_electors+nri_female_electors+nri_third_electors)
AS TOTAL_ELECT_VOTE  FROM electors_cdac cdac
WHERE  cdac.st_code=TEMP.st_code
 GROUP BY TEMP.st_code,party_id)AS TOTAL_ELECT_VOTE,
(SELECT won
     FROM
     (SELECT COUNT(lead_total_vote) AS 'won',cnd.party_id
     FROM candidate_personal_detail cpd,candidate_nomination_detail cnd,
          m_party m ,counting_pcmaster cp
      LEFT JOIN winning_leading_candidate wlc
     ON wlc.candidate_id = cp.candidate_id
     WHERE cnd.cand_party_type ='U'
     AND cnd.application_status = '6'
    AND cnd.finalaccepted = '1'
     AND cpd.candidate_id=cnd.candidate_id
     AND m.ccode= cnd.party_id
     AND cpd.candidate_id = cp.candidate_id
     AND m.ccode= wlc.lead_cand_partyid
     GROUP BY partyabbre) CC
      WHERE CC.PARTY_ID=TEMP.party_id)AS WON,
(SELECT SUM(df) FROM (
      SELECT lead_total_vote,m.partyabbre,cp.candidate_id,cp.candidate_name,cnd.party_id,
     CASE WHEN SUM(cp1.total_vote)/6 > cp.total_vote THEN 1 ELSE 0 END AS 'DF'
     FROM m_party m, candidate_nomination_detail cnd,counting_pcmaster cp1,
     candidate_personal_detail cpd,counting_pcmaster cp
     LEFT JOIN winning_leading_candidate wlc
     ON wlc.candidate_id = cp.candidate_id
     WHERE cnd.cand_party_type ='U'
    
    AND cnd.application_status = '6'
    AND cnd.finalaccepted = '1'
     AND cpd.candidate_id=cnd.candidate_id
     AND m.ccode= cnd.party_id
     AND cp.st_code = cp1.st_code
     AND cp.pc_no = cp1.pc_no
     AND cpd.candidate_id = cp.candidate_id
     AND lead_total_vote IS NULL
     GROUP BY cp.candidate_id,cp1.st_code, cp1.pc_no
     ) DD WHERE DD.party_id=TEMP.party_id) AS DF,
(SELECT count(DISTINCT(B.candidate_id)) AS totalcontested from candidate_nomination_detail B join m_election_details med on med.ST_CODE=B.st_code AND med.CONST_NO = B.pc_no 
WHERE B.PARTY_ID=TEMP.party_id AND B.application_status='6' AND election_status = '1'
AND B.finalaccepted='1' AND B.cand_party_type='U' GROUP BY TEMP.party_id)AS totalcontested,
(SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster as cpmmm join candidate_nomination_detail as cnd on cpmmm.candidate_id = cnd.candidate_id WHERE cnd.party_id=TEMP.party_id AND cnd.party_id != '1180' and application_status = '6' and finalaccepted = '1' AND cnd.cand_party_type='U' GROUP BY cnd.party_id)AS totalvalid_valid_vote_party
FROM
(
SELECT ms.st_code,ms.ST_NAME,mp.PARTYABBRE,mp.PARTYNAME,mp.PARTYTYPE,dd.party_id,dd.candidate_id,dd.pc_no,
SUM(cp.total_vote)AS totalvalidvote_state
          FROM m_state ms,counting_pcmaster cp,m_party mp,candidate_personal_detail E,
          candidate_nomination_detail dd
          WHERE cp.st_code=ms.st_code
          AND dd.party_id=mp.ccode
          AND dd.cand_party_type='U'
          AND dd.candidate_id=E.candidate_id
          AND cp.candidate_id=dd.candidate_id
          AND cp.st_code=dd.st_code
        
          AND dd.application_status ='6'
          AND dd.finalaccepted='1'
          GROUP BY dd.party_id,st_code
)TEMP )TEMP1
WHERE TEMP1.totalcontested IS NOT NULL
GROUP BY party_id");

$datacandidateindependent = DB::select("SELECT TEMP1.PARTYNAME,PARTYTYPE,
totalcontested,DF,totalvalid_valid_vote_party,
IFNULL(WON,0)AS won,SUM(totalvalidvote_state) AS totalvalidvote_state ,SUM(TOTAL_ELECT_VOTE) AS TOTAL_ELECT_VOTE,
SUM(CCC) AS Total_Valid_Vote_State
FROM
(
SELECT *,
(SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
WHERE st_code =TEMP.st_code  AND party_id != '1180'
GROUP BY TEMP.st_code) AS CCC,
(SELECT SUM(gen_electors_male+gen_electors_female+gen_electors_other
+service_male_electors+service_female_electors+service_third_electors+
nri_male_electors+nri_female_electors+nri_third_electors)
AS TOTAL_ELECT_VOTE  FROM electors_cdac cdac
WHERE  cdac.st_code=TEMP.st_code
 GROUP BY TEMP.st_code,party_id)AS TOTAL_ELECT_VOTE,
(SELECT won
     FROM
     (SELECT COUNT(lead_total_vote) AS 'won',cnd.party_id
     FROM candidate_personal_detail cpd,candidate_nomination_detail cnd,
          m_party m ,counting_pcmaster cp
      LEFT JOIN winning_leading_candidate wlc
     ON wlc.candidate_id = cp.candidate_id
     WHERE cnd.cand_party_type ='Z'
     AND cnd.application_status = '6'
    AND cnd.finalaccepted = '1'
     AND cpd.candidate_id=cnd.candidate_id
     AND m.ccode= cnd.party_id
     AND cpd.candidate_id = cp.candidate_id
     AND m.ccode= wlc.lead_cand_partyid
     GROUP BY partyabbre) CC
      WHERE CC.PARTY_ID=TEMP.party_id)AS WON,
(SELECT SUM(df) FROM (
      SELECT lead_total_vote,m.partyabbre,cp.candidate_id,cp.candidate_name,cnd.party_id,
     CASE WHEN SUM(cp1.total_vote)/6 > cp.total_vote THEN 1 ELSE 0 END AS 'DF'
     FROM m_party m, candidate_nomination_detail cnd,counting_pcmaster cp1,
     candidate_personal_detail cpd,counting_pcmaster cp
     LEFT JOIN winning_leading_candidate wlc
     ON wlc.candidate_id = cp.candidate_id
     WHERE cnd.cand_party_type ='Z'
    
    AND cnd.application_status = '6'
    AND cnd.finalaccepted = '1'
     AND cpd.candidate_id=cnd.candidate_id
     AND m.ccode= cnd.party_id
     AND cp.st_code = cp1.st_code
     AND cp.pc_no = cp1.pc_no
     AND cpd.candidate_id = cp.candidate_id
     AND lead_total_vote IS NULL
     GROUP BY cp.candidate_id,cp1.st_code, cp1.pc_no
     ) DD WHERE DD.party_id=TEMP.party_id) AS DF,
( SELECT COUNT(DISTINCT(B.candidate_id)) AS totalcontested FROM candidate_nomination_detail B join m_election_details med on med.ST_CODE=B.st_code AND med.CONST_NO = B.pc_no 
WHERE B.PARTY_ID=TEMP.party_id AND B.application_status='6' AND election_status = '1'
AND B.finalaccepted='1' AND B.cand_party_type='Z' AND med.CONST_TYPE='PC' GROUP BY TEMP.party_id )AS totalcontested,
(SELECT SUM(total_vote)AS totalvalid_st_vote  FROM counting_pcmaster
WHERE party_id=TEMP.party_id AND party_id != '1180' GROUP BY party_id)AS totalvalid_valid_vote_party
FROM
(
SELECT ms.st_code,ms.ST_NAME,mp.PARTYABBRE,mp.PARTYNAME,mp.PARTYTYPE,dd.party_id,dd.candidate_id,dd.pc_no,
SUM(cp.total_vote)AS totalvalidvote_state
          FROM m_state ms,counting_pcmaster cp,m_party mp,candidate_personal_detail E,
          candidate_nomination_detail dd
          WHERE cp.st_code=ms.st_code
          AND dd.party_id=mp.ccode
          AND dd.cand_party_type='Z'
          AND dd.candidate_id=E.candidate_id
          AND cp.candidate_id=dd.candidate_id       
          AND dd.application_status ='6'
          AND dd.finalaccepted='1'
          GROUP BY dd.party_id,st_code
)TEMP )TEMP1
WHERE TEMP1.totalcontested IS NOT NULL
GROUP BY party_id");



$performanceofst = array_merge($performanceofst,$datacandidateindependent);
    

       

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
                      return view('IndexCardReports/StatisticalReports/Vol2/performanceofunrecognisedparties',compact('performanceofst','user_data'));
                    }elseif($request->path() == "$prefix/performance-of-unrecognised-partys-pdf"){

                     $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports/Vol2/performanceofunrecognisedparties_pdf',[
                    'performanceofst'=>$performanceofst,
                    'user_data'=>$user_data

                    ]);

                     if(verifyreport(22)){
        
                  $file_name = 'PERFORMANCE OF REGISTERED (UNRECOGNISED) PARTIES'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/22/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '22',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
                    return $pdf->download('PERFORMANCE OF REGISTERED (UNRECOGNISED) PARTIES.pdf');

                    }elseif($request->path() == "$prefix/performance-of-unrecognised-partys_xls"){

                      return Excel::create('PERFORMANCE OF REGISTERED (UNRECOGNISED) PARTIES', function($excel) use ($performanceofst) {
                     $excel->sheet('mySheet', function($sheet) use ($performanceofst)
                     {
                    $sheet->mergeCells('B1:H1');
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
                     $sheet->getStyle('F3')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('G3')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('H3')->getAlignment()->setWrapText(true);
                     $sheet->getStyle('B1:H1')->getAlignment()->setWrapText(true);

                    

                   $sheet->cell('B1', function($cells) {
                        $cells->setValue('PERFORMANCE OF REGISTERED (UNRECOGNISED) PARTIES');
                    });

                    
                    $sheet->cell('A3', function($cells) {
                        $cells->setValue('Sl No.');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('A3', 15, 15);

                    $sheet->cell('B3', function($cells) {
                        $cells->setValue('PARTY NAME');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('B3', 15, 15);

                   $sheet->cell('C2', function($cells) {
                        $cells->setValue('Candidates');
                         $cells->setFont(array('bold' => true));
                    });

                   
                    $sheet->setSize('C2', 10, 25);

                    $sheet->mergeCells('C2:E2');
                    $sheet->mergeCells('G2:H2');

                    
                   

                    $sheet->cell('C3', function($cells) {
                        $cells->setValue('Contested');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('C3', 15, 15);
                    $sheet->cell('D3', function($cells) {
                        $cells->setValue('Won');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('D3', 15, 15);
                     $sheet->cell('E3', function($cells) {
                        $cells->setValue('DF');
                         $cells->setFont(array('bold' => true));
                    });
                     $sheet->setSize('E3', 15, 15);



                    $sheet->cell('F3', function($cells) {
                        $cells->setValue('Votes Secured By Party In State');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('F3', 25, 30);

                    $sheet->cell('G2', function($cells) {
                        $cells->setValue('% of Votes Secured');
                         $cells->setFont(array('bold' => true));
                    });


                    $sheet->cell('G3', function($cells) {
                        $cells->setValue('Over Total Elector in the State');
                         $cells->setFont(array('bold' => true));
                    });
                    $sheet->setSize('G3', 25, 30);

                    $sheet->cell('H3', function($cells) {
                        $cells->setValue('Over Total valid Votes Polled in the State');
                         $cells->setFont(array('bold' => true));
                    });

                    $sheet->setSize('H3', 25, 30);


                   

                    //data loop

                   


                     $i = 4; $count = 1;
                    




                        if (!empty($performanceofst)) {

                            $grandtotalcon = $grandtotalwon = $grandtotalDf = $grandtotalvalid_vote_party 
                            = $grandtotalcon = $grandtotalelector = $grandtotalvotestate = 0;

                            foreach($performanceofst as $value) {

                              

                                $sheet->cell('A' . $i, $count);
                                if($value->PARTYTYPE == 'S') {
                                $sheet->cell('B' . $i, $value->PARTYNAME.'*');
                               } else{
                                $sheet->cell('B' . $i, $value->PARTYNAME);
                               }
                                $sheet->cell('C' . $i, $value->totalcontested ? :'=(0)');
                                $sheet->cell('D' . $i, $value->won ? :'=(0)');
                                $sheet->cell('E' . $i, $value->DF ? :'=(0)');
                                $sheet->cell('F' . $i, $value->totalvalid_valid_vote_party? :'=(0)');
                                $sheet->cell('G' . $i, round($value->totalvalid_valid_vote_party/$value->TOTAL_ELECT_VOTE*100,4) ? :'=(0)');
                                $sheet->cell('H' . $i, round($value->totalvalid_valid_vote_party/$value->Total_Valid_Vote_State*100,4));

                                $grandtotalcon += $value->totalcontested;
                                $grandtotalwon += $value->won;
                                $grandtotalDf += $value->DF;
                                $grandtotalvalid_vote_party += $value->totalvalid_valid_vote_party;
                                $grandtotalelector +=  $value->TOTAL_ELECT_VOTE;
                                $grandtotalvotestate +=  $value->Total_Valid_Vote_State;

                                


                           $i++; $count++;
                         }}

                          $sheet->cell('A' . $i, 'Grand Total');
                               
                                $sheet->cell('C' . $i, $grandtotalcon ? :'=(0)');
                                $sheet->cell('D' . $i, $grandtotalwon ? :'=(0)');
                                $sheet->cell('E' . $i, $grandtotalDf ? :'=(0)');
                                $sheet->cell('F' . $i, $grandtotalvalid_vote_party ? :'=(0)');
                                $sheet->cell('G' . $i, round($grandtotalvalid_vote_party/$grandtotalelector*100,4) ? :'=(0)');
                                $sheet->cell('H' . $i, round($grandtotalvalid_vote_party/$grandtotalvotestate*100,4) ? :'=(0)');


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
            echo "Result not found";
        }
}    //Performance of unrecognised party ends


public function getParticipationofWomenInStateParties(Request $request)
    {
    // session data
    $user = Auth::user();
    $user_data = $user;
    $datanew = array();
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

      JOIN m_election_details med 
      ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO 
      WHERE  med.CONST_TYPE = 'PC' AND  med.election_status = '1' 

      AND partytype ='S'
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
                $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports.IndexCardEciReport.Vol1.participation-of-women-in-state-parties-pdf',[
                'datanew'=>$datanew,
                'user_data'=>$user_data
          ]);

                 if(verifyreport(27)){
        
                  $file_name = 'participation-of-women-in-state-parties'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/27/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '27',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
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
                               $sheet->cell('J' . $i, round($peroverelectors,2)? :'=(0)');
                               $sheet->cell('K' . $i, round($overTotalValidVotes,2)? :'=(0)');
                               $sheet->cell('L' . $i, round($ovsbp,2)? :'=(0)');


                           $i++; }}
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
    WHERE application_status = 6 AND finalaccepted = 1  AND party_id != "1180" GROUP BY st_code, pc_no) a
    INNER JOIN m_state m ON a.st_code = m.st_code
    INNER JOIN m_election_details AS med ON med.st_code = m.st_code AND med.CONST_NO = a.pc_no
    WHERE   med.CONST_TYPE = "PC" AND  med.election_status = "1" GROUP BY a.st_code');


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
    WHERE application_status = 6 AND finalaccepted = 1 AND party_id != "1180" GROUP BY st_code, pc_no) a
    INNER JOIN m_state m ON a.st_code = m.st_code
    INNER JOIN m_election_details AS med ON med.st_code = m.st_code AND med.CONST_NO = a.pc_no
    WHERE   med.CONST_TYPE = "PC" AND  med.election_status = "1"  GROUP BY a.st_code');

   //    $pdf = \App::make('dompdf.wrapper');
			// $pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/numberofcandidatepercostituencypdf', compact('pcCount'));

        if(verifyreport(8)){
        
                  $file_name = 'Number of Candidates Per Constituency'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/8/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '8',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
        return $pdf->download('Number of Candidates Per Constituency.pdf');
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
    WHERE application_status = 6 AND finalaccepted = 1  AND party_id != "1180" GROUP BY st_code, pc_no) a
    INNER JOIN m_state m ON a.st_code = m.st_code
    INNER JOIN m_election_details AS med ON med.st_code = m.st_code AND med.CONST_NO = a.pc_no
    WHERE   med.CONST_TYPE = "PC" AND  med.election_status = "1"  GROUP BY a.st_code');

      return Excel::create('Number of Candidates Per Constituency', function($excel) use ($data) {
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


         $sheet->mergeCells('C2:I2');
         $sheet->mergeCells('J2:L2');

         $sheet->cell('C2', function($cells) {
             $cells->setValue('Constituencies with candidates numbering');
             $cells->setAlignment('center');
         });

         $sheet->cell('J2', function($cells) {
             $cells->setValue('Candidates in a Constituency');
         });
         $sheet->getStyle('J2')->getAlignment()->setWrapText(true);

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
             $cells->setValue('<=15 ');
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
          $sheet->setSize('L3', 15,20);
        


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
                     $sheet->cell('J'.$i,$value->mincan);
                     $sheet->cell('K'.$i,$value->maxcan);
                     $sheet->cell('L'.$i,$value->Avg);

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

                      $minnumber = array_column($data, 'mincan');
                      $maxnumber = array_column($data, 'maxcan');
                      $min = min($minnumber);
                      $max = max($maxnumber);

                     $sheet->cell('A'. $i,'Grand Total');
                     $sheet->cell('B'.  $i, $seartotal);
                     $sheet->cell('C'.$i,$searonetotal );
                     $sheet->cell('D'.$i, $searNotatotal);
                     $sheet->cell('E'.$i, $searThreeOnetotal);
                     $sheet->cell('F'.$i, $searFourSeventotal);
                     $sheet->cell('G'.$i, $searSixThreetotal);
                     $sheet->cell('H'.$i,$searLessSixThreetotal);
                     $sheet->cell('I'.$i,$totalcandidate);
                     $sheet->cell('J'.$i,$min);
                     $sheet->cell('K'.$i,$max);
                     $sheet->cell('L'.$i,round($totalcandidate/$seartotal,2));




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








}


public function highlights(request $request){

    $user_data = Auth::user();
    $user = Auth::user();

    $pctypecount = DB::select("SELECT SUM(CASE WHEN pc_type = 'GEN' THEN 1 ELSE 0 END) AS genpc,
      SUM(CASE WHEN pc_type = 'SC' THEN 1 ELSE 0 END) AS scpc,
      SUM(CASE WHEN pc_type = 'ST' THEN 1 ELSE 0 END) AS stpc
      FROM m_pc inner join m_election_details as med on med.st_code = m_pc.ST_CODE and med.CONST_NO = m_pc.PC_NO
      where med.CONST_TYPE = 'PC' and  med.election_status = '1'");

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
    WHERE cnd.application_status = 6 AND cnd.finalaccepted = 1 AND cnd.party_id!= '1180' and med.CONST_TYPE = 'PC' and  med.election_status = '1'  GROUP BY cnd.st_code, cnd.pc_no) a");

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


          WHERE  med.CONST_TYPE = 'PC' and  med.election_status = '1' ");

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


          $postalvotetotal = DB::select("SELECT 
          SUM(totalevmvote) AS total_evm_postal_vote,SUM(tended_votes) AS tended_votes,
          SUM(totalvalidpostalvote) AS total_valid_postal_vote,SUM(evm_vote) AS total_evm_vote,
          SUM(rejectedpostalvote) AS rejected_postal_vote
          FROM
          (SELECT cp.st_code,cp.pc_no,a.postaltotalvote AS 'totalpostalvotereceived',
          SUM(cp.total_vote) AS 'totalevmvote',
          SUM(cp.postal_vote) AS 'totalvalidpostalvote',
          (cp.tended_votes) AS 'tended_votes',
          SUM(cp.evm_vote+cp.migrate_votes) 'evm_vote', a.rejectedvote AS 'rejectedpostalvote'   FROM counting_pcmaster cp,m_election_details med,
          (SELECT st_code,pc_no,rejectedvote,postaltotalvote FROM counting_pcmaster  GROUP BY st_code, pc_no) a
           WHERE cp.st_code = a.st_code AND cp.pc_no = a.pc_no AND med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
           AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'  AND cp.party_id != '1180' GROUP BY cp.st_code,cp.pc_no) b");


          $postalvotetotal = array_map(function ($value) {
                            return (array)$value;
                            }, $postalvotetotal);


          $contestents = array_merge($contestents,array(

                                              // 'total_postal_vote_received'=> $postalvotetotal[0]['total_postal_vote_received'],
                                             
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
                          SUM(ecoi.proxy_votes) AS proxy_votes,
                          SUM(ecoi.test_votes_49_ma) AS test_votes_49_ma,
                          SUM(ecoi.service_postal_votes_under_section_8+ecoi.service_postal_votes_gov) AS total_postal_vote_received


                          FROM electors_cdac_other_information AS ecoi

                          INNER JOIN m_election_details AS med ON med.st_code = ecoi.ST_CODE AND med.CONST_NO = ecoi.PC_NO


                          WHERE   med.CONST_TYPE = 'PC' AND  med.election_status = '1'");




            $totalvote = array_map(function ($value) {
                                          return (array)$value;
                                }, $totalvote);






            $notavote = DB::select("SELECT SUM(cp.evm_vote+cp.migrate_votes) AS evmnota,
                        SUM(cp.postal_vote) AS postalnota
                        FROM `counting_pcmaster` AS cp
                        INNER JOIN m_election_details AS med ON med.st_code = cp.ST_CODE AND med.CONST_NO = cp.PC_NO
                        WHERE cp.party_id = '1180' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");



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
                       WHERE w1.pc_no = cp.pc_no AND w1.st_code = cp.st_code AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1')
                       AND cp.party_id != 1180");

           $wincandidatedatemale = DB::select("SELECT COUNT(leading_id) AS totalwinnermale
                                  FROM winning_leading_candidate AS wlc
                                  INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                                  INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                                  WHERE cpd.cand_gender = 'male' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");
          $wincandidatedatefemale = DB::select("SELECT COUNT(leading_id) AS totalwinnerfemale
                                  FROM winning_leading_candidate AS wlc
                                  INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                                  INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                                  WHERE cpd.cand_gender = 'female' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");

          $wincandidatedatethird = DB::select("   SELECT COUNT(leading_id) AS totalwinnerthird
                                  FROM winning_leading_candidate AS wlc
                                  INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                                  INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                                  WHERE cpd.cand_gender = 'third' AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");


           $totalnominatedmale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedmale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                              WHERE cpd.cand_gender = 'male' AND wlc.application_status = '6' AND wlc.finalaccepted = '1' AND wlc.party_id != '1180'
                              AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");

          $totalnominatedfemale = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedfemale
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                              WHERE cpd.cand_gender = 'female' AND wlc.application_status = '6' AND wlc.finalaccepted = '1' AND wlc.party_id != '1180'
                              AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");

          $totalnominatedthird = DB::select("SELECT COUNT(wlc.candidate_id) AS totalnominatedthird
                              FROM candidate_nomination_detail AS wlc
                              INNER JOIN candidate_personal_detail AS cpd ON cpd.`candidate_id` = wlc.`candidate_id`
                              INNER JOIN m_election_details AS med ON med.st_code = wlc.st_code AND med.CONST_NO = wlc.pc_no
                              WHERE cpd.cand_gender = 'third' AND wlc.application_status = '6' AND wlc.finalaccepted = '1' AND wlc.party_id != '1180'
                              AND  med.CONST_TYPE = 'PC' AND  med.election_status = '1'");



          /*$noofrepolls = DB::select("SELECT COUNT(no_of_ps_repoll) AS total_repoll FROM `repoll_pc_ic`
                        INNER JOIN m_election_details AS med ON med.st_code = repoll_pc_ic.st_code AND med.CONST_NO = repoll_pc_ic.pc_no
                        WHERE  repoll_pc_ic.no_of_ps_repoll !='0' AND med.CONST_TYPE = 'PC' AND  med.election_status = '1' ");*/
						
			$noofrepolls = DB::select("SELECT sum(no_of_ps_repoll) AS total_repoll FROM `electors_cdac_other_information` as ecoi WHERE ecoi.no_of_ps_repoll > 0 ");			
						

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
                  'total_postal_vote_received'=> $totalvote[0]['total_postal_vote_received'],
                  'test_votes_49_ma'=> $totalvote[0]['test_votes_49_ma'],
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

                if($user->designation == 'ROPC'){
                            $prefix     = 'ropc';
                  }else if($user->designation == 'CEO'){
                            $prefix     = 'pcceo';
                  }else if($user->role_id == '27'){
                          $prefix     = 'eci-index';
                  }else if($user->role_id == '7'){
                          $prefix     = 'eci';
                }

                if($request->path() == "$prefix/highlights"){
                 return view('IndexCardReports/StatisticalReports/Vol2/highlights', compact('contestents','user_data'));
                 }elseif($request->path() == "$prefix/highlights-pdf"){
                   $pdf = \App::make('dompdf.wrapper');
			$pdf->getDomPDF()->set_option("enable_php", true);
			
			$pdf->loadView('IndexCardReports/StatisticalReports/Vol2/highlightspdf',compact('contestents','user_data'));

                   if(verifyreport(2)){
        
                  $file_name = 'Highlights'.date('YmdHis').'.pdf';
                  $date = date('Y-m-d H:i:s');
                  
                  
                  $ip = get_client_ip();


                  $pdf->save(public_path('uploads/statistical_report/2/'.$file_name));

                  $insertData = [
                        'file_name' => $file_name,
                        'report_no' => '2',
                        'download_time' => $date,
                        'user_ip' =>$ip,
                      ];

                  DB::table('statical_report_download_logs')->insert($insertData);


      }
                   return $pdf->download('1heighlightspdf.pdf');
                 }elseif($request->path() == "$prefix/highlights-excel"){
                   return Excel::create('Highlight'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($contestents) {
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
                                        $cells->setValue('POLLING PERCENTAGE (EXCLUDE POSTAL BALLOT)');
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
                                    $total = $contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+
                          $contestents['totalOtherVoters']+$contestents['total_postal_vote_received'];
                                   $sheet->cell('F20' ,round($total/$contestents['totalElectors']*100,2));

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
                                  $sheet->cell('F22' ,($contestents['totalMaleVoters']+$contestents['totalFemaleVoters']+$contestents['totalOtherVoters'])-($contestents['votes_not_retreived_from_evm']+$contestents['rejected_votes_due_2_other_reason']+$contestents['evmnota']));

                                   $sheet->cells('B23', function($cells) {
                                      $cells->setValue('VALID POSTAL VOTES');
                                      $cells->setAlignment('center');
                                      $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                                   });


                                 $sheet->cell('F23' ,($contestents['total_postal_vote_received'])-($contestents['postalnota']+$contestents['rejected_postal_vote']));

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
                                $cells->setValue('Tendered Votes');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                           $sheet->cell('F31' ,$contestents['tended_votes']);

                             $sheet->cells('A32', function($cells) {
                                $cells->setValue('11');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                             $sheet->cells('B32', function($cells) {
                                $cells->setValue('PROXY VOTES');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                           $sheet->cell('F32' ,$contestents['proxy_votes']);


                             $sheet->cells('A33', function($cells) {
                                $cells->setValue('12');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                             $sheet->cells('B33', function($cells) {
                                $cells->setValue('NO. OF POLLING STATION');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                           $sheet->cell('F33' ,$contestents['totalpollingstation']);


                             $sheet->cells('A34', function($cells) {
                                $cells->setValue('13');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                             $sheet->cells('B34', function($cells) {
                                $cells->setValue('AVERAGE NO. OF ELECTORS PER POLLING STATION');
                                $cells->setAlignment('center');
                                $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                             });

                           $sheet->cell('F34' ,round(($contestents['totalElectors'])/$contestents['totalpollingstation'],0));



                           $sheet->cells('A35', function($cells) {
                              $cells->setValue('14');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });


                           $sheet->cells('B35', function($cells) {
                              $cells->setValue('NO. OF RE-POLLS HELD');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cell('F35' ,$contestents['total_repoll']);


                           $sheet->cells('A36', function($cells) {
                              $cells->setValue('15.');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });


                           $sheet->cells('B36', function($cells) {
                              $cells->setValue('PERFORMANCE OF CONTESTING CANDIDATES');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });



                           $sheet->cells('C36', function($cells) {
                              $cells->setValue('MALE');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cells('D36', function($cells) {
                              $cells->setValue('FEMALE');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });
                           $sheet->cells('E36', function($cells) {
                              $cells->setValue('THIRD GENDER');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cells('F36', function($cells) {
                              $cells->setValue('TOTAL');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });


                           $sheet->cells('A37', function($cells) {
                              $cells->setValue('I');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cells('B37', function($cells) {
                              $cells->setValue('NO. OF CONTESTANTS');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });



                           $sheet->cell('C37' ,$contestents['totalnominatedmale']);
                           $sheet->cell('D37' ,$contestents['totalnominatedfemale']);
                           $sheet->cell('E37' ,$contestents['totalnominatedthird']);
                           $sheet->cell('F37' ,$contestents['totalnominatedmale']+$contestents['totalnominatedfemale']+$contestents['totalnominatedthird']);

                           $sheet->cells('A38', function($cells) {
                              $cells->setValue('ii');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cells('B38', function($cells) {
                              $cells->setValue('ELECTED');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cell('C38' ,$contestents['totalwinnermale']);
                           $sheet->cell('D38' ,$contestents['totalwinnerfemale']);
                           $sheet->cell('E38' ,$contestents['totalwinnerthird']? :'=(0)');
                           $sheet->cell('F38' ,$contestents['totalwinnermale']+$contestents['totalwinnerfemale']+$contestents['totalwinnerthird']);



                           $sheet->cells('A39', function($cells) {
                              $cells->setValue('ii');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });

                           $sheet->cells('B39', function($cells) {
                              $cells->setValue('FORFEITED DEPOSITS');
                              $cells->setAlignment('center');
                              $cells->setFont(array('name' => 'Times New Roman', 'size' => 12, 'bold' => true));
                           });





                           $sheet->cell('C39' ,$contestents['fdmale']);
                           $sheet->cell('D39' ,$contestents['fdfemale']);
                           $sheet->cell('E39' ,$contestents['fdthird']);
                           $sheet->cell('F39' ,$contestents['fdtotal']);



          

                          
                          $sheet->cell('A43', function($cells) {
                            $cells->setValue('Disclaimer');
                            $cells->setFont(array('name' => 'Times New Roman','size' => 12,'bold' => true));
                          });

                          

                          $sheet->getStyle('A44')->getAlignment()->setWrapText(true);
                          $sheet->setSize('A44', 25,40);



                      $sheet->mergeCells("A44:G44");
                      $sheet->cell('A44', function($cells) {
                      $cells->setValue('This report is based on Index Cards data made available by concerned Returning Officers on the basis of Statutory data maintained in the forms. In case of any dispute, the data maintained in the Statutory Forms by the concerned Returning Officers shall prevail.');
                      $cells->setFont(array('name' => 'Times New Roman','size' => 10));
          });
                           });
                        })->export();

       }

}

}
