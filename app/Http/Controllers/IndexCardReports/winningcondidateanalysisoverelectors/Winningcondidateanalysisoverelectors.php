<?php

namespace App\Http\Controllers\IndexCardReports\winningcondidateanalysisoverelectors;
use DB;
use Session;
use PDF;
use Excel;
use App\commonModel;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Winningcondidateanalysisoverelectors extends Controller
{
     public function __construct(){
       $this->middleware('adminsession');
       $this->middleware(['auth:admin','auth']);
       $this->middleware('ceo');
       $this->commonModel = new commonModel();
   }
 public function index()
    {
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
       // echo "<pre>"; print_r($session); die;
       $election_detail = $session['election_detail'];
       $user_data = $d;
         
        $select = array('mpc.ST_CODE',
            'mpc.PC_NO',
            'mpc.ST_CODE',
            DB::raw("GROUP_CONCAT(winn.lead_total_vote) as `totalleadvote`"), DB::raw("GROUP_CONCAT(ic.e_all_t) as `etotal`"),
            'ic.st_code',
             'ic.pc_no',
             'B.st_name'
            , 'ic.ac_no',
            DB::raw("count(mpc.PC_NO) as `Totalseat`")
            );
        
        $winning = DB::table('m_pc as mpc')
                ->select($select)
                        ->leftjoin('winning_leading_candidate as winn',function($query){
                            $query->on('mpc.PC_NO','winn.pc_no')
                            ->on('mpc.ST_CODE','winn.st_code');
                        })
                        ->join('m_state AS B', 'B.ST_CODE', 'mpc.st_code')
                        ->leftjoin('t_pc_ic as ic',function($query1){
                            $query1->on('mpc.PC_NO','ic.pc_no')
                            ->on('mpc.ST_CODE','ic.st_code');
                        })
                ->groupby('mpc.ST_CODE')
                       ->get(); 
                       
                    $arrayData = array();
                     $totalper = 0; 
                     $countArray = array();
                    foreach ($winning as $values) {

            $arrayData[$values->ST_CODE]['statname'] = $values->ST_CODE;
            $arrayData[$values->ST_CODE]['stname'] = $values->st_name;
            $arrayData[$values->ST_CODE]['totalseat'] = $values->Totalseat;
            $arrayData[$values->ST_CODE]['etotal'] = array_sum(explode(',', $values->etotal));
            $b = $arrayData[$values->ST_CODE]['etotal'];

            if ($b)
                
                $arrayData[$values->ST_CODE]['etotal'] = array_map(function($a) use($b) {
                    return round(($a / $b) * 100, 2);
                }, explode(',', $values->totalleadvote));
                
                 //$arrayData[$values->ST_CODE]['etotal']= $totalper;

                //$arrayData['total_valid_votes']= $totalper; 
                 
              $arrayData[$values->ST_CODE]['statname'] = $values->ST_CODE;
              
              ///dd($arrayData);
              
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
       
           if($arrayData[$values->ST_CODE]['etotal'] != 0){
               foreach($arrayData[$values->ST_CODE]['etotal'] as  $value) {
                 
                 if($value<=10){
                   $countArray['10'] += 1;
                  }
                  if(($value>=11 && $value<=20)){
                   $countArray['20'] += 1;
                  }
                  if(($value>=21 && $value <=30)){
                   $countArray['30'] += 1;
                  }
                   if(($value>=31 && $value <=40)){
                   $countArray['40'] += 1;
                  }
                   if(($value>=41 && $value <=50)){
                   $countArray['50'] += 1;
                  }
                   if(($value>=51 && $value <=60)){
                   $countArray['60'] += 1;
                  }
                   if(($value>=61 && $value <=70)){
                   $countArray['70'] += 1;
                  }
                   if(($value>=71 && $value <=100)){
                   $countArray['80'] += 1;
                  }

               }
               $arrayData[$values->ST_CODE]['count'] = $countArray;
          //$datanew[$values['ST_CODE']] = $countArray;
           }else{
               $countArray = 0;
               $arrayData[$values->ST_CODE]['count'] = $countArray;
             // $datanew[$values['ST_CODE']] = $countArray;
           }
    
        }
       
        return view('IndexCardReports/StatisticalReports/Vol2/winning-condidate-analysis-over-total-electors', compact('arrayData','user_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function winningcpndidateanalysisoverelectorpdf()
    {
        $select = array('mpc.ST_CODE',
                'mpc.PC_NO',
            'mpc.ST_CODE',
            DB::raw("GROUP_CONCAT(winn.lead_total_vote) as `totalleadvote`"), DB::raw("GROUP_CONCAT(ic.e_all_t) as `etotal`"),
            'ic.st_code',
             'ic.pc_no',
             'B.st_name'
            , 'ic.ac_no',
            DB::raw("count(mpc.PC_NO) as `Totalseat`")
            );
        
        $winning = DB::table('m_pc as mpc')
                ->select($select)
                        ->leftjoin('winning_leading_candidate as winn',function($query){
                            $query->on('mpc.PC_NO','winn.pc_no')
                            ->on('mpc.ST_CODE','winn.st_code');
                        })
                        ->join('m_state AS B', 'B.ST_CODE', 'mpc.st_code')
                        ->leftjoin('t_pc_ic as ic',function($query1){
                            $query1->on('mpc.PC_NO','ic.pc_no')
                            ->on('mpc.ST_CODE','ic.st_code');
                        })
                ->groupby('mpc.ST_CODE')
                       ->get(); 
                       
                    $arrayData = array();
                     $totalper = 0; 
                     $countArray = array();
                    foreach ($winning as $values) {

            $arrayData[$values->ST_CODE]['statname'] = $values->ST_CODE;
            $arrayData[$values->ST_CODE]['stname'] = $values->st_name;
            $arrayData[$values->ST_CODE]['totalseat'] = $values->Totalseat;
            $arrayData[$values->ST_CODE]['etotal'] = array_sum(explode(',', $values->etotal));
            $b = $arrayData[$values->ST_CODE]['etotal'];

            if ($b)
                
                $arrayData[$values->ST_CODE]['etotal'] = array_map(function($a) use($b) {
                    return round(($a / $b) * 100, 2);
                }, explode(',', $values->totalleadvote));
                
                 //$arrayData[$values->ST_CODE]['etotal']= $totalper;

                //$arrayData['total_valid_votes']= $totalper; 
                 
              $arrayData[$values->ST_CODE]['statname'] = $values->ST_CODE;
              
              ///dd($arrayData);
              
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
       
           if($arrayData[$values->ST_CODE]['etotal'] != 0){
               foreach($arrayData[$values->ST_CODE]['etotal'] as  $value) {
                 
                 if($value<=10){
                   $countArray['10'] += 1;
                  }
                  if(($value>=11 && $value<=20)){
                   $countArray['20'] += 1;
                  }
                  if(($value>=21 && $value <=30)){
                   $countArray['30'] += 1;
                  }
                   if(($value>=31 && $value <=40)){
                   $countArray['40'] += 1;
                  }
                   if(($value>=41 && $value <=50)){
                   $countArray['50'] += 1;
                  }
                   if(($value>=51 && $value <=60)){
                   $countArray['60'] += 1;
                  }
                   if(($value>=61 && $value <=70)){
                   $countArray['70'] += 1;
                  }
                   if(($value>=71 && $value <=100)){
                   $countArray['80'] += 1;
                  }

               }
               $arrayData[$values->ST_CODE]['count'] = $countArray;
          //$datanew[$values['ST_CODE']] = $countArray;
           }else{
               $countArray = 0;
               $arrayData[$values->ST_CODE]['count'] = $countArray;
             // $datanew[$values['ST_CODE']] = $countArray;
           }
         
        }
        
                   
     $pdf = PDF::loadView('IndexCardReports/StatisticalReports/Vol2/winning-condidate-analysis-over-total-electors-pdf', compact('arrayData'));
       return $pdf->download('winning-condidate-analysis-over-total-electors.pdf');
//        
        //winning-condidate-analysis-over-total-electors-pdf
    }

    
    
      public function winningcpndidateanalysisoverelectorxls()
    {
        $select = array('mpc.ST_CODE',
                'mpc.PC_NO',
            'mpc.ST_CODE',
            DB::raw("GROUP_CONCAT(winn.lead_total_vote) as `totalleadvote`"), DB::raw("GROUP_CONCAT(ic.e_all_t) as `etotal`"),
            'ic.st_code',
             'ic.pc_no',
             'B.st_name'
            , 'ic.ac_no',
            DB::raw("count(mpc.PC_NO) as `Totalseat`")
            );
        
        $winning = DB::table('m_pc as mpc')
                ->select($select)
                        ->leftjoin('winning_leading_candidate as winn',function($query){
                            $query->on('mpc.PC_NO','winn.pc_no')
                            ->on('mpc.ST_CODE','winn.st_code');
                        })
                        ->join('m_state AS B', 'B.ST_CODE', 'mpc.st_code')
                        ->leftjoin('t_pc_ic as ic',function($query1){
                            $query1->on('mpc.PC_NO','ic.pc_no')
                            ->on('mpc.ST_CODE','ic.st_code');
                        })
                ->groupby('mpc.ST_CODE')
                       ->get(); 
                       
                    $arrayData = array();
                     $totalper = 0; 
                     $countArray = array();
                    foreach ($winning as $values) {

            $arrayData[$values->ST_CODE]['statname'] = $values->ST_CODE;
            $arrayData[$values->ST_CODE]['stname'] = $values->st_name;
            $arrayData[$values->ST_CODE]['totalseat'] = $values->Totalseat;
            $arrayData[$values->ST_CODE]['etotal'] = array_sum(explode(',', $values->etotal));
            $b = $arrayData[$values->ST_CODE]['etotal'];

            if ($b)
                
                $arrayData[$values->ST_CODE]['etotal'] = array_map(function($a) use($b) {
                    return round(($a / $b) * 100, 2);
                }, explode(',', $values->totalleadvote));
                
                 //$arrayData[$values->ST_CODE]['etotal']= $totalper;

                //$arrayData['total_valid_votes']= $totalper; 
                 
              $arrayData[$values->ST_CODE]['statname'] = $values->ST_CODE;
              
              ///dd($arrayData);
              
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
       
           if($arrayData[$values->ST_CODE]['etotal'] != 0){
               foreach($arrayData[$values->ST_CODE]['etotal'] as  $value) {
                 
                 if($value<=10){
                   $countArray['10'] += 1;
                  }
                  if(($value>=11 && $value<=20)){
                   $countArray['20'] += 1;
                  }
                  if(($value>=21 && $value <=30)){
                   $countArray['30'] += 1;
                  }
                   if(($value>=31 && $value <=40)){
                   $countArray['40'] += 1;
                  }
                   if(($value>=41 && $value <=50)){
                   $countArray['50'] += 1;
                  }
                   if(($value>=51 && $value <=60)){
                   $countArray['60'] += 1;
                  }
                   if(($value>=61 && $value <=70)){
                   $countArray['70'] += 1;
                  }
                   if(($value>=71 && $value <=100)){
                   $countArray['80'] += 1;
                  }

               }
               $arrayData[$values->ST_CODE]['count'] = $countArray;
          //$datanew[$values['ST_CODE']] = $countArray;
           }else{
               $countArray = 0;
               $arrayData[$values->ST_CODE]['count'] = $countArray;
             // $datanew[$values['ST_CODE']] = $countArray;
           }
         
        }
        
return Excel::create('winning-condidate-analysis-over-electors', function($excel) use ($arrayData) {
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
                        $col = 'B' . $last . ':' . 'R' . $last;

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
                        if (!empty($arrayData)) {
                            foreach ($arrayData as $key => $values) {
                                
                                
                                
                                $sheet->cell('A' . $i, $values['stname']);
                                $sheet->cell('B' . $i, $values['totalseat']);
                                $sheet->cell('C' . $i, ($values['count']!=0)?$values['count']['10']:0);
                                $sheet->cell('D' . $i, ($values['count']!=0)?$values['count']['20']:0);
                                $sheet->cell('E' . $i, ($values['count']!=0)?$values['count']['30']:0);
                                $sheet->cell('F' . $i, ($values['count']!=0)?$values['count']['40']:0);
                                $sheet->cell('G' . $i, ($values['count']!=0)?$values['count']['50']:0);
                                $sheet->cell('H' . $i, ($values['count']!=0)?$values['count']['60']:0);
                                $sheet->cell('I' . $i, ($values['count']!=0)?$values['count']['70']:0);
                                $sheet->cell('J' . $i, ($values['count']!=0)?$values['count']['80']:0);
                           $i++; }
                        }
                    });
                })->export();
        
    }
    
    
}
