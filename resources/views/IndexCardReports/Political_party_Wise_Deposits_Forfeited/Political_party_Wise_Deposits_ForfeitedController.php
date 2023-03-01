<?php

namespace App\Http\Controllers\IndexCardReports\Political_party_Wise_Deposits_Forfeited;
use DB;
use Auth;
use Session;
use PDF;
use App\Http\Controllers\Controller;
use App\commonModel;
use Illuminate\Http\Request;
use App;
use Excel;

class Political_party_Wise_Deposits_ForfeitedController extends Controller
{

  // public function __construct()
  // {
  //    $this->middleware('adminsession');
  //    $this->middleware(['auth:admin','auth']);
  //    $this->middleware('ceo');
  //    $this->commonModel = new commonModel();
  // }

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

    $statewisedata = array();
    $electedcanddata = array();


    $sSelect = array(
           'B.ST_NAME','B.st_code',
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

       // echo '<pre>';  print_r($countSeats);

      $datacandidate = DB::table('candidate_nomination_detail AS A')
                        ->select('C.PARTYTYPE','A.st_code',
                          DB::raw('COUNT(DISTINCT(A.candidate_id)) AS totalcand')
                                )

                         ->join('m_party AS C', 'A.party_id', 'C.CCODE')
                         ->groupby('A.st_code','C.PARTYTYPE')
                         ->WHERE('A.application_status', '6')
                         ->WHERE('A.finalaccepted', '1')
                         ->WHERE('A.candidate_id', '!=', '4319')
                         ->get()->toArray();

    //echo '<pre>';  print_r($datacandidate); die;

    $dataelectedcandidate = DB::table('winning_leading_candidate')
                            ->select('st_code','lead_party_type',
                              DB::raw('COUNT(leading_id) AS totalelected')
                                    )
                              ->groupby('st_code','lead_party_type')
                              ->WHERE('status', '1')
                             ->get()->toArray();


   // echo '<pre>';  print_r($dataelectedcandidate); die;

    $totalstate = DB::table('m_state')->select('ST_NAME','ST_CODE')->orderby('st_code')->get()->toArray();

    $fdarray = array();

    foreach ($totalstate as  $value) {

        //echo '<pre>';  print_r($value); die;



    	$datafdcandidate = DB::select("SELECT cp.st_code,
           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.PARTYTYPE = 'N' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdN,

           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.PARTYTYPE = 'S' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdS,

           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.PARTYTYPE = 'U' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdU,

           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.PARTYTYPE = 'Z' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdZ,


           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd

           FROM `counting_pcmaster` as cp
           join m_party as C on C.CCODE = cp.party_id
           WHERE cp.candidate_id != (select candidate_id from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code)
           AND cp.candidate_id != 4319 AND cp.st_code = '$value->ST_CODE'");


        foreach ($datafdcandidate as $subvalue) {
          $fdarray[$value->ST_CODE] =$subvalue;
        }

        }



        foreach ($countSeats as $value1) {
          foreach ($datacandidate as $value2) {
          	foreach ($fdarray as $value4) {
            if($dataelectedcandidate){
              foreach ($dataelectedcandidate as $value3) {
                if($value3->st_code == $value2->st_code && $value2->st_code == $value1->st_code && $value4->st_code == $value2->st_code){
                  $statewisedata[$value1->st_code]['st_code'] = $value1->st_code;
                  $statewisedata[$value1->st_code]['ST_NAME'] = $value1->ST_NAME;
                  $statewisedata[$value1->st_code]['TotalSeats'] = $value1->TotalSeats;
                  $statewisedata[$value1->st_code][$value2->PARTYTYPE] = $value2->totalcand;
                  $statewisedata[$value1->st_code]['totalwinner'][$value3->lead_party_type] = $value3->totalelected;
                  $statewisedata[$value1->st_code]['totalfd']['N'] = $value4->fdN;
                  $statewisedata[$value1->st_code]['totalfd']['S'] = $value4->fdS;
                  $statewisedata[$value1->st_code]['totalfd']['U'] = $value4->fdU;
                  $statewisedata[$value1->st_code]['totalfd']['Z'] = $value4->fdZ;
                  $statewisedata[$value1->st_code]['totalfd']['FDT'] = $value4->fd;
                }
              }

            }else{
              if($value2->st_code == $value1->st_code){
                $statewisedata[$value1->st_code]['st_code'] = $value1->st_code;
                $statewisedata[$value1->st_code]['ST_NAME'] = $value1->ST_NAME;
                $statewisedata[$value1->st_code]['TotalSeats'] = $value1->TotalSeats;
                $statewisedata[$value1->st_code][$value2->PARTYTYPE] = $value2->totalcand;
                $statewisedata[$value1->st_code]['totalwinner'][$value2->PARTYTYPE] = 0;
              }
            }
          }
        }
    }

      $statewisedata = json_decode(json_encode($statewisedata));

     // echo "<pre>"; print_r($statewisedata); die;
       foreach ($statewisedata as $key) {
         // echo "<pre>"; print_r($key); die;
         if(!array_key_exists('N',$key)){
          $key->N = 0;
         }
         if(!array_key_exists('N',$key->totalwinner)){
          $key->totalwinner->N = 0;
         }
        if(!array_key_exists('S',$key)){
          $key->S = 0;
        }
        if(!array_key_exists('S',$key->totalwinner)){
          $key->totalwinner->S = 0;
        }
        if(!array_key_exists('U',$key)){
          $key->U = 0;
        }
        if(!array_key_exists('U',$key->totalwinner)){
          $key->totalwinner->U = 0;
        }
        if(!array_key_exists('Z',$key)){
          $key->Z = 0;
        }
        if(!array_key_exists('Z',$key->totalwinner)){
          $key->totalwinner->Z = 0;
        }
        if(!array_key_exists('Z1',$key)){
          $key->Z1 = 0;
        }
        if(!array_key_exists('Z1',$key->totalwinner)){
          $key->totalwinner->Z1 = 0;
        }
         // echo "<pre>"; var_dump(array_key_exists('N',$key));
       }//die;
       // echo "<pre>"; print_r($statewisedata); die;
      //var_dump($statewisedata); die;
      //   $statewisedata = (array) $statewisedata;
      //   $electedcanddata = (array)$electedcanddata;
      // $result = array_merge_recursive($statewisedata, $electedcanddata);
      // dd($result);

        if($user->designation == 'ROPC'){
                    $prefix     = 'ropc';
          }else if($user->designation == 'CEO'){
                    $prefix     = 'pcceo';
          }else if($user->role_id == '27'){
                  $prefix     = 'eci-index';
          }else if($user->role_id == '7'){
                  $prefix     = 'eci';
        }

       if($request->path() == "$prefix/Political_party_Wise_Deposits_Forfeited"){
        return view('IndexCardReports.PoliticalPartyWiseDepositsForfeited.PoliticalpartyWiseDepositsForfeited', compact('statewisedata','user_data'));
        }elseif($request->path() == "$prefix/Political_party_Wise_Deposits_ForfeitedPDF"){
        $pdf=PDF::loadView
        ('IndexCardReports.PoliticalPartyWiseDepositsForfeited.PoliticalpartyWiseDepositsForfeitedPDF',[

            'statewisedata'=>$statewisedata
        ]);
        return $pdf->download('Political_party_Wise_Deposits_ForfeitedReport.pdf');
        }
        elseif($request->path() == "$prefix/Political_party_Wise_Deposits_ForfeitedXLS"){


  //echo "<pre>"; print_r($datanew); die;

  return Excel::create('PoliticalPartyWiseDepositsForfeited'.'_'.date('d-m-Y').'_'.time(), function($excel) use ($statewisedata) {
                      $excel->sheet('mySheet', function($sheet) use ($statewisedata) {
                       $sheet->mergeCells('B1:P1');
                       $sheet->mergeCells('C2:G2');
                       $sheet->mergeCells('H2:L2');
                       $sheet->mergeCells('M2:Q2');
                       $sheet->getStyle('P2')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('C')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('B')->getAlignment()->setWrapText(true);
                       $sheet->getStyle('A')->getAlignment()->setWrapText(true);

                       $sheet->cells('B1', function($cells) {
                          $cells->setValue('Political Party Wise Deposits Forfeited');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 15, 'bold' => true));
                       });
                       $sheet->cells('A2', function($cells) {
                          $cells->setValue('State/UT');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->setSize('A2', 10, 25);
                       $sheet->cells('B2', function($cells) {
                          $cells->setValue('No. Of Seats');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });


                       $sheet->cells('C2', function($cells) {
                          $cells->setValue('Total No. Of Candidates');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->setSize('C2', 10, 25);
                       $sheet->cells('H2', function($cells) {
                          $cells->setValue('Total No. Of Elected Candidates');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->setSize('H2', 10, 25);

                       $sheet->cells('M2', function($cells) {
                          $cells->setValue('Total No. Of Candidates with Forfeiture Of Deposit');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->setSize('M2', 10, 25);

                       $sheet->cells('C3', function($cells) {
                          $cells->setValue('N');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('D3', function($cells) {
                          $cells->setValue('S');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('E3', function($cells) {
                          $cells->setValue('U');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('F3', function($cells) {
                          $cells->setValue('i');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('G3', function($cells) {
                          $cells->setValue('Tot');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->cells('H3', function($cells) {
                          $cells->setValue('N');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('I3', function($cells) {
                          $cells->setValue('S');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('J3', function($cells) {
                          $cells->setValue('U');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('K3', function($cells) {
                          $cells->setValue('i');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('L3', function($cells) {
                          $cells->setValue('Tot');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });

                       $sheet->cells('M3', function($cells) {
                          $cells->setValue('N');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('N3', function($cells) {
                          $cells->setValue('S');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('O3', function($cells) {
                          $cells->setValue('U');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('P3', function($cells) {
                          $cells->setValue('i');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                       $sheet->cells('Q3', function($cells) {
                          $cells->setValue('Tot');
                          $cells->setAlignment('center');
                          $cells->setFont(array('name' => 'Times New Roman', 'size' => 12));
                       });
                          $i = 4;
                           if (!empty($statewisedata)) {
                        foreach($statewisedata as $kkey => $value) {
                                     $sheet->cell('A' . $i, $value->ST_NAME);
                                     $sheet->cell('B' . $i, $value->TotalSeats);
                                     $sheet->cell('C' . $i, $value->N ? :'=(0)');
                                     $sheet->cell('D' . $i, $value->S ? :'=(0)');
                                     $sheet->cell('E' . $i, $value->U ? :'=(0)');
                                     $sheet->cell('F' . $i, ($value->Z+$value->Z1) ? :'=(0)');
                                     $sheet->cell('G' . $i, ($value->N+$value->S+$value->U+$value->Z+$value->Z1) ? :'=(0)');
                                     $sheet->cell('H' . $i, $value->totalwinner->N ? :'=(0)');
                                     $sheet->cell('I' . $i, $value->totalwinner->S ? :'=(0)');
                                     $sheet->cell('J' . $i, $value->totalwinner->U ? :'=(0)');
                                     $sheet->cell('K' . $i, ($value->totalwinner->Z+$value->totalwinner->Z1) ? :'=(0)');
                                     $sheet->cell('L' . $i, ($value->totalwinner->Z+$value->totalwinner->Z1+$value->totalwinner->U+$value->totalwinner->S+$value->totalwinner->N) ? :'=(0)');
                                     $sheet->cell('M' . $i, $value->totalfd->N ? :'=(0)');
                                     $sheet->cell('N' . $i, $value->totalfd->S ? :'=(0)');
                                     $sheet->cell('O' . $i, $value->totalfd->U ? :'=(0)');
                                     $sheet->cell('P' . $i, $value->totalfd->Z ? :'=(0)');
                                     $sheet->cell('Q' . $i, $value->totalfd->FDT ? :'=(0)');
                              $i++; }
                           }
                       });

                   })->export();

 }



  } //Function index end here


  public function participationofwomencandidateinpoll(Request $request){
    //dd("Hello");

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

        //$candatawise = App\models\Admin\CandidateModel::get_count_by_status_category();
        $candatawise = App\models\Admin\CandidateModel::get_count_by_status_category();
        //echo "<pre>"; print_r($candatawise); die;
    $dfdata = DB::select("SELECT MP.ST_NAME,cp.st_code,C.cand_category as category,
            SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'male' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdmale,

           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'female' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdfemale,

           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code and  C.cand_gender = 'third' GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fdthird,


           SUM(CASE WHEN ROUND(cp.total_vote/(SELECT SUM(cp1.`total_vote`) as pctotalvotes FROM `counting_pcmaster` as cp1
           where cp1.pc_no = cp.pc_no and cp.st_code =cp1.st_code GROUP BY cp1.`pc_no` ),4) < .1666 THEN 1 ELSE 0 END) as fd
           FROM `counting_pcmaster` as cp
           join candidate_personal_detail as C on C.candidate_id = cp.candidate_id
           join m_state as MP on MP.ST_CODE = cp.st_code
           WHERE cp.candidate_id != (select candidate_id from winning_leading_candidate as w1 where w1.pc_no = cp.pc_no and w1.st_code = cp.st_code)
           AND cp.candidate_id != 4319
           GROUP By MP.ST_CODE,C.cand_category
       order By MP.ST_CODE asc");


      $dfdata = json_decode( json_encode($dfdata), true);
       foreach ($candatawise as  $value1) {
         $i= 1;

            if($dfdata){
              foreach ($dfdata as $value2) {
                //if($value2['st_code'] == $value1['st_code'] && $value2['category'] == $value1['category']){
                //echo "<pre>";print_r($value2);
                if($value2['st_code'] == $value1['st_code']){

           $datacandidate[$value1['st_code']]['state'] =$value2['ST_NAME'];
           $datacandidate[$value1['st_code']]['pcinfo'][$value1['category']]['category'] = $value1['category'];

          $datacandidate[$value1['st_code']]['pcinfo'][$value1['category']]['cont_female'] = $value1['cont_female'];
           $datacandidate[$value1['st_code']]['pcinfo'][$i]['fdfemale'] = $value2['fdfemale'];

                 // $datacandidate[$value1['st_code']][$value2['category']] = array(
                 //   'st_code'    => $value1['st_code'],
                 //   'category'   => $value2['category'],
                 //   'cont_female' =>$value1['cont_female'],
                 //    'ST_NAME' =>$value2['ST_NAME'],
                 //    'fdfemale' =>$value2['fdfemale']
                 // );
              }
            $i++;}

         }
     }
     //dd("hello");
echo "<pre>";print_r($datacandidate);die;
    //dd('hellodd');
return view('IndexCardReports.PoliticalPartyWiseDepositsForfeited.participationwomwninpoll', compact('datacandidate','user_data'));

//


  }



}
