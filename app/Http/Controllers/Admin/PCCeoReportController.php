<?php
    namespace App\Http\Controllers\Admin;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Session;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Redirect;
    use Carbon\Carbon;
    use DB;
    use Illuminate\Support\Facades\Hash;
    //use Maatwebsite\Excel\Facades\Excel;
    // use Maatwebsite\Excel\Excel;
    //use Excel;
    use Validator;
    use Config;
    use \PDF;
    use App\commonModel;  
    use App\adminmodel\CEOModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\PCCeoReportModel;

    use App\Exports\ExcelExport;
    use Maatwebsite\Excel\Facades\Excel;


  date_default_timezone_set('Asia/Kolkata');

class PCCeoReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
    //date_default_timezone_set('Asia/Kolkata');    
        $this->middleware(['auth:admin','auth']);
        $this->middleware('ceo');
        $this->commonModel = new commonModel();
        $this->ceomodel = new CEOModel();
        $this->pcceoreportModel = new PCCeoReportModel();
		
    }
/**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    protected function guard(){
        return Auth::guard();
    }

/**
 * @author Devloped By : Nazaryab Ali
 * @author Devloped Date : 14-02-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return lists By State fuction
 */

    public function pcceoduplicatesymbolpdf()
    {
     
      $users=Session::get('admin_login_details');
      $user = Auth::user();   
      if(session()->has('admin_login'))
      {  
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($uid);
        $edetails=$this->commonModel->election_details_cons($d->st_code,'','','CEO');
        $lists=$this->pcceoreportModel->duplicateSymboleCandidate($d->st_code);
        $user_data=$d;
        $st_code=$d->st_code;
          
$pdf = PDF::loadView('admin.pc.ceo.pcCeoDuplicateSymbolExportPdfHtml', compact('lists' ,'user_data','st_code'));
            return $pdf->download($st_code."-ceo-duplicateSymbolReport-".".pdf");

      } else {
              return redirect('/officer-login');
             } 

    }


 public function duplicatesymbolview()
  {
      $users=Session::get('admin_login_details');
      $user = Auth::user();   
      if(session()->has('admin_login'))
      {  
        $uid=$user->id;
        $d=$this->commonModel->getunewserbyuserid($uid);
        $edetails=$this->commonModel->election_details_cons($d->st_code,'','','CEO');
        $list=$this->pcceoreportModel->duplicateSymboleCandidate($d->st_code);
          
       return view('admin.pc.ceo.ceoduplicatesymbol',['user_data'=>$d,'st_code'=>$d->st_code,'lists'=>$list]);   
      } else {
              return redirect('/officer-login');
             }    
  }    

    /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 12-01-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return duplicatepartieslist fuction
 */

public function duplicatepartieslist(Request $request){
        $users=Session::get('admin_login_details');
       if(Auth::check()){
               $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
       // dd($d);
           $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
       // dd($ele_details);

         $duplicatePartyList=$this->pcceoreportModel->getduplicatenominationparty($d->st_code);
        //print_r($duplicatePartyList);  die;
       return view('admin.pc.ceo.duplicateparties',['user_data' => $d,'ele_details' => $ele_details,'duplicatePartyList' => $duplicatePartyList]);
     }
     else {
         return redirect('/officer-login');
     }
   }   // end duplicatepartieslist function

   public function pcceoduplicatepartypdf()
   {
   
    $users=Session::get('admin_login_details');
       if(Auth::check()){
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $lists=$this->pcceoreportModel->getduplicatenominationparty($d->st_code);
        $user_data = $d;
        $st_code = $d->st_code;
     $pdf = PDF::loadView('admin.pc.ceo.pcCeoDuplicatePartyExportPdfPdfHtml', compact('lists' ,'user_data','st_code'));
            return $pdf->download($st_code."-ceo-duplicate-party-wise-report".".pdf");
     }
     else {
         return redirect('/officer-login');
   }
 }
   

   public function pcceoduplicatepartyExcel()
   {
    $users=Session::get('admin_login_details');
       if(Auth::check()){
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $user_data = $d;
        $st_code = $d->st_code;
        $cur_time    = Carbon::now();

        $lists=$this->pcceoreportModel->getduplicatenominationparty($st_code);
        $arr  = array();
        $cand_party_type='Z'; $finalize='1';
        $user = Auth::user();
        $export_data[] = ['PC No.', 'PC Name', 'Candidate Name', 'Party Name'];
        $headings[]=[];

        foreach ($lists as $list) {
          //if($list->cand_party_type=='S'){
           $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
           $partyDetails=getById('m_party','CCODE',$list->party_id);
           $pcDetails=getpcbypcno($st_code,$list->pc_no); 
           $export_data[] = [
            $list->pc_no,
            $pcDetails->PC_NAME,
            $candidatedetails->cand_name,
            $partyDetails->PARTYNAME
    ];

          
                    }
                    $name_excel = 'duplicate-party-excel-report'.trim($st_code).'_'.$cur_time;
                    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');           

// \Excel::create('duplicate-party-excel-report'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
//       $excel->sheet('Sheet1', function($sheet) use($st_code) {


//       $lists=$this->pcceoreportModel->getduplicatenominationparty($st_code);
//       $arr  = array();
//       $cand_party_type='Z'; $finalize='1';
//       $user = Auth::user();
//       foreach ($lists as $list) {
//         //if($list->cand_party_type=='S'){
//          $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
//          $partyDetails=getById('m_party','CCODE',$list->party_id);
//          $pcDetails=getpcbypcno($st_code,$list->pc_no); 
//          $data =  array(
//                   $list->pc_no,
//                   $pcDetails->PC_NAME,
//                   $candidatedetails->cand_name,
//                   $partyDetails->PARTYNAME
//                         );
//                   array_push($arr, $data);
//                    // }
//                   }
//    $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
//                        'PC No.', 'PC Name', 'Candidate Name', 'Party Name'
//                )

//            );

//          });

//     })->export('xls');



     }
     else {
         return redirect('/officer-login');
   }
 } 

 public function pcceoduplicatesymbolexcel()
   {
    $users=Session::get('admin_login_details');
       if(Auth::check()){
        $user = Auth::user();
        $d=$this->commonModel->getunewserbyuserid($user->id);
        $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
        $lists=$this->pcceoreportModel->duplicateSymboleCandidate($d->st_code);
        $user_data = $d;
        $st_code = $d->st_code;
        $cur_time    = Carbon::now();

        $lists=$this->pcceoreportModel->duplicateSymboleCandidate($st_code);
        $arr  = array();
        $export_data[] = ['PC No.', 'PC Name', 'Symbol Name', 'Candidate Name','Party Name'];
        $headings[]=[];
        foreach ($lists as $list) {
           $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
           $pclist=getById('m_pc','PC_NO',$list->pc_no);
           $symbol_data=getsymbolbyid($list->symbol_id);
           $partyDetails=getById('m_party','CCODE',$list->party_id);
           $pcDetails=getpcbypcno($st_code,$list->pc_no);
          
           if(isset($symbol_data)){
            $SYMBOL_DES =$symbol_data->SYMBOL_DES;
           } else{
            $SYMBOL_DES = '';
           }

           $export_data[] = [
            $list->pc_no,
            $pcDetails->PC_NAME,
            $SYMBOL_DES,
            $candidatedetails->cand_name,
            $partyDetails->PARTYNAME
          ];

           
        }

        $name_excel = 'duplicate_symbol_report_excel_'.trim($st_code).'_'.$cur_time;
        return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 



//  \Excel::create('duplicate_symbol_report_excel_'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) {
        
//       $excel->sheet('Sheet1', function($sheet) use($st_code) {

//       $lists=$this->pcceoreportModel->duplicateSymboleCandidate($st_code);
//       $arr  = array();
//       foreach ($lists as $list) {
//          $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
//          $pclist=getById('m_pc','PC_NO',$list->pc_no);
//          $symbol_data=getsymbolbyid($list->symbol_id);
//          $partyDetails=getById('m_party','CCODE',$list->party_id);
//          $pcDetails=getpcbypcno($st_code,$list->pc_no);
        
//          if(isset($symbol_data)){
//           $SYMBOL_DES =$symbol_data->SYMBOL_DES;
//          } else{
//           $SYMBOL_DES = '';
//          }
//          $data =  array(
//                   $list->pc_no,
//                   $pcDetails->PC_NAME,
//                   $SYMBOL_DES,
//                   $candidatedetails->cand_name,
//                   $partyDetails->PARTYNAME
//                         );
//                   array_push($arr, $data);
//                     }

//    $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
//                        'PC No.', 'PC Name', 'Symbol Name', 'Candidate Name','Party Name'
//                )

//            );

//          });

//     })->export('xls'); 


    } else {
         return redirect('/officer-login');
   }
 }



 /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 13-01-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return pcList By State fuction
 */

public function pclist(Request $request){
 if(Auth::check()){
  $user = Auth::user();
  $d=$this->commonModel->getunewserbyuserid($user->id);
  $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
  $allPcList=$this->commonModel->getpcbystate($d->st_code);
  //print_r($allPcList);die;
  $allTypeCountArr = array();
  $i=0;
  $count =1;
  foreach($allPcList as $allPc){
   $rejected=$this->pcceoreportModel->getCountStatus($allPc->ST_CODE,$allPc->PC_NO,4);
   $withdrawn=$this->pcceoreportModel->getCountStatus($allPc->ST_CODE,$allPc->PC_NO,5);
   $accepted=$this->pcceoreportModel->getCountStatus($allPc->ST_CODE,$allPc->PC_NO,6);
      $allTypeCountArr[$i]['srno'] = $count;
      $allTypeCountArr[$i]['PC_NO'] = $allPc->PC_NO;
      $allTypeCountArr[$i]['pc_name'] = $allPc->PC_NO.' - '.$allPc->PC_NAME;
      $allTypeCountArr[$i]['accepted'] = $accepted;                   
      $allTypeCountArr[$i]['rejected'] = $rejected;
      $allTypeCountArr[$i]['Withdrawn'] = $withdrawn;
      $allTypeCountArr[$i]['total'] = $accepted+$rejected+$withdrawn;   
                     $i++;
                     $count++;
  }
  
  //print_r($duplicatePartyList);  die;
 return view('admin.pc.ceo.pclist',['user_data' => $d,'ele_details' => $ele_details,'allPcList' => $allTypeCountArr]);
}else {
   return redirect('/officer-login');
 }
}   // end pclist function
public function ceopclistexcel(Request $request){
 if(Auth::check()){
  $user = Auth::user();
  $d=$this->commonModel->getunewserbyuserid($user->id);
    $st_code=  $d->st_code;
    $cur_time    = Carbon::now();


  // \Excel::create('candidate-status-report'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
  //     $excel->sheet('Sheet1', function($sheet) use($st_code) {
      $lists=$this->pcceoreportModel->duplicateSymboleCandidate($st_code);
      $arr  = array();
      $cand_party_type='Z'; $finalize='1';
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $allPcList=$this->commonModel->getpcbystate($d->st_code);
     //print_r($independentCandList);die; 
      $count = 1;
      $export_data[]=[ 'Serial No.', 'PC Number&Name','Accepted','Rejected','Withdrawn','Total'];
      $headings[]=[];
      foreach ($allPcList as $list) {
        $rejected=$this->pcceoreportModel->getCountStatus($list->ST_CODE,$list->PC_NO,4);
   $withdrawn=$this->pcceoreportModel->getCountStatus($list->ST_CODE,$list->PC_NO,5);
   $accepted=$this->pcceoreportModel->getCountStatus($list->ST_CODE,$list->PC_NO,6);
          if($rejected==''){ $rejected='0';}
          if($withdrawn==''){ $withdrawn='0'; }
          if($accepted==''){  $accepted='0';}
          $total =$accepted+$rejected+$withdrawn;
          if($total==''){$total='0';}

          $export_data[] = [
            $count,
            $list->PC_NO.' - '.$list->PC_NAME,
           $accepted,
           $rejected,
           $withdrawn,
           $total
    ];

        
                  $count++;
                  }

                  $name_excel = 'candidate-status-report'.trim($st_code).'_'.$cur_time;
                  return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        

  //  $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
  //                      'Serial No.', 'PC Number&Name','Accepted','Rejected','Withdrawn','Total'
  //              )

  //          );

    //      });

    // })->export('xls');
    
}else {
   return redirect('/officer-login');
 }
}


/**
 * @author Devloped By : Nazaryab Ali
 * @author Devloped Date : 14-02-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return allPcList By State fuction
 */
public function pclistpdf(Request $request){
 if(Auth::check()){
  $user = Auth::user();
  $d=$this->commonModel->getunewserbyuserid($user->id);
  $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
  $allPcList=$this->commonModel->getpcbystate($d->st_code);

//$allPcList=$this->commonModel->getpcbystate($d->st_code);
  //print_r($allPcList);die;
  $allTypeCountArr = array();
  $i=0;
  $count =1;
  foreach($allPcList as $allPc){
   $rejected=$this->pcceoreportModel->getCountStatus($allPc->ST_CODE,$allPc->PC_NO,4);
   $withdrawn=$this->pcceoreportModel->getCountStatus($allPc->ST_CODE,$allPc->PC_NO,5);
   $accepted=$this->pcceoreportModel->getCountStatus($allPc->ST_CODE,$allPc->PC_NO,6);
  
      //if($i==0){ $i=$i+1;}
      $allTypeCountArr[$i]['srno'] = $count;
      $allTypeCountArr[$i]['PC_NO'] = $allPc->PC_NO;
      $allTypeCountArr[$i]['pc_name'] = $allPc->PC_NO.' - '.$allPc->PC_NAME;
      $allTypeCountArr[$i]['accepted'] = $accepted;                   
      $allTypeCountArr[$i]['rejected'] = $rejected;
      $allTypeCountArr[$i]['Withdrawn'] = $withdrawn;
      $allTypeCountArr[$i]['total'] = $accepted+$rejected+$withdrawn;   
                     $i++;
                     $count++;
  }

  $user_data = $d;
  $st_code=  $d->st_code;
  $pdf = PDF::loadView('admin.pc.ceo.pcCeoPcListExportPdf', compact('allTypeCountArr' ,'user_data','st_code'));
            return $pdf->download($st_code."-ceo-pc-count-report".".pdf");

 //return view('admin.pc.ceo.pclist',['user_data' => $d,'ele_details' => $ele_details,'allPcList' => $allPcList]);
}else {
   return redirect('/officer-login');
 }
}

 /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 13-01-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return pcList By State fuction
 */

 public function candidateListbyPC(Request $request,$pcno){
   if(Auth::check()){
    $user = Auth::user();


    $d=$this->commonModel->getunewserbyuserid($user->id);

    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $candListbyPC=$this->pcceoreportModel->getCandidateListbyPC($d->st_code,$pcno);
    $st_code =$d->st_code;
   return view('admin.pc.ceo.candidatelist',['st_code'=>$st_code,'user_data' => $d,'ele_details' => $ele_details,'candListbyPC' => $candListbyPC,'pc_no'=>$pcno]);
 }
 else {
     return redirect('/officer-login');
   }
 }   // end pclist function

 public function candidatelistpdf(Request $request,$pcno){
   if(Auth::check()){
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $candListbyPC=$this->pcceoreportModel->getCandidateListbyPC($d->st_code,$pcno);
   // print_r($candListbyPC);  die;
$user_data = $d;
  $st_code=  $d->st_code;
    $pdf = PDF::loadView('admin.pc.ceo.pcCandidateListPdf', compact('candListbyPC' ,'user_data','st_code'));
            return $pdf->download($st_code."-ceo-pc-candidate-list-report".".pdf");
   //return view('admin.pc.ceo.pcCandidateListPdf',['user_data' => $d,'ele_details' => $ele_details,'candListbyPC' => $candListbyPC,'pc_no'=>$pcno]);
 }
 else {
     return redirect('/officer-login');
   }
 }   // end pclist function

public function candidatelistexcelPC(Request $request,$pcno){
   if(Auth::check()){
    $user = Auth::user();
    $d=$this->commonModel->getunewserbyuserid($user->id);
    $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
    $candListbyPC=$this->pcceoreportModel->getCandidateListbyPC($d->st_code,$pcno);
    $st_code=  $d->st_code;
    $cur_time    = Carbon::now();
    \Excel::create('candidate-detail-excel'.trim($st_code).'_'.$cur_time, function($excel) use($st_code,$pcno) { 
      $excel->sheet('Sheet1', function($sheet) use($st_code,$pcno) {
      $candListbyPC=$this->pcceoreportModel->getCandidateListbyPC($st_code,$pcno);
      $arr  = array();
      $cand_party_type='Z'; $finalize='1';
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $allPcList=$this->commonModel->getpcbystate($d->st_code);
     //print_r($independentCandList);die; 
      $count = 1;
      foreach ($candListbyPC as $list) {
         $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
          $partyDetails=getById('m_party','CCODE',$list->party_id);
          $pcDetails=getpcbypcno($d->st_code,$list->pc_no);
          $symbolDetails=getsymbolbyid($list->symbol_id);
          if(!empty($pcDetails)){ $partyname =$partyDetails->PARTYNAME;} else{ $partyname='-'; }
if(!empty($symbolDetails)){ $symbolName =$symbolDetails->SYMBOL_DES;} else{ $symbolName='-'; }
          $data =  array(
                  $count,
                  $list->pc_no.' - '.$pcDetails->PC_NAME,
                  $candidatedetails->cand_name,
                  $partyname,
                  $symbolName
                        );
                  array_push($arr, $data);
                 // }
                  $count++;
                  }
   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                       'Serial No.','PC Number&Name' ,'Candidate Name','Party Name', 'Symbol'
               )

           );

         });

    })->export('xls');
 } else {
     return redirect('/officer-login');
   }
 }   

 /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 13-01-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return independentcandidatelist By State fuction     
  */
   
 public function independentcandidatelist(Request $request){
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $cand_party_type='Z'; $finalize='1';
   $independentCandList=$this->pcceoreportModel->independentcandidatelist($d->st_code,$cand_party_type,$finalize);
   //print_r($independentCandList);  die;
   return view('admin.pc.ceo.independentcandidatelist',['user_data' => $d,'ele_details' => $ele_details,'independentCandList' => $independentCandList]);
   }else {
    return redirect('/officer-login');
  }   
 }   // end independentcandidatelist function  

 public function independantecandpdf(Request $request){
  //echo "test";die;
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $cand_party_type='Z'; $finalize='1';
   $independentCandList=$this->pcceoreportModel->independentcandidatelist($d->st_code,$cand_party_type,$finalize);
   $user_data = $d;
  $st_code=  $d->st_code;
  // print_r($independentCandList);  die;

$pdf = PDF::loadView('admin.pc.ceo.ceoIndependantCandListHtml', compact('independentCandList' ,'user_data','st_code'));
            return $pdf->download($st_code."-ceo-pc-candidate-list-report".".pdf");

   //return view('admin.pc.ceo.ceoIndependantCandListHtml',['user_data' => $d,'ele_details' => $ele_details,'independentCandList' => $independentCandList]);
   }else {
    return redirect('/officer-login');
  }   
 }   // end independentcandidatelist function 

 public function ceoindependantecandexcel(Request $request){
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $st_code=  $d->st_code;
   $cur_time    = Carbon::now();
  \Excel::create('independent-candidate-ceo-report'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
      $excel->sheet('Sheet1', function($sheet) use($st_code) {
      $lists=$this->pcceoreportModel->duplicateSymboleCandidate($st_code);
      $arr  = array();
      $cand_party_type='Z'; $finalize='1';
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $independentCandList=$this->pcceoreportModel->independentcandidatelist($d->st_code,$cand_party_type,$finalize);
     //print_r($independentCandList);die; 
      foreach ($independentCandList as $list) {

        if($list->cand_party_type=='S'){
         $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
          $partyDetails=getById('m_party','CCODE',$list->party_id);
          $pcDetails=getpcbypcno($st_code,$list->pc_no);
          $symbolDetails=getsymbolbyid($list->symbol_id);
          $data =  array(
                  $list->new_srno,
                  $list->pc_no.' - '.$pcDetails->PC_NAME,
                  $candidatedetails->cand_name,
                  $partyDetails->PARTYNAME,
                  $symbolDetails->SYMBOL_DES
                        );
                  array_push($arr, $data);
                    }
                  }
   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                       'Serial No.', 'PC Number&Name', 'Candidate Name', 'Party Name','Symbol'
               )

           );

         });

    })->export('xls');
   }else {
    return redirect('/officer-login');
  }   
 }



    public function candidatesymbolno200(Request $request){
     
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $cand_party_type='Z'; $finalize='1';
   $independentCandList=$this->pcceoreportModel->ceosymbolno_200pdf($d->st_code);
   //print_r($independentCandList);  die;
   return view('admin.pc.ceo.candidatesymbolno_200',['user_data' => $d,'ele_details' => $ele_details,'independentCandList' => $independentCandList]);
   }else {
    return redirect('/officer-login');
  }   
 }

 public function ceosymbolno_200pdf(Request $request){

  //echo "test";die;
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $finalize='1';
   $independentCandList=$this->pcceoreportModel->ceosymbolno_200pdf($d->st_code,$finalize);
   $user_data = $d;
   $st_code=  $d->st_code;
   $pdf = PDF::loadView('admin.pc.ceo.ceocandidatesymbolno200Html', compact('independentCandList' ,'user_data','st_code'));
            return $pdf->download($st_code."candidate-symbol-not-alloted".".pdf");
   }else {
    return redirect('/officer-login');
  }   
 }

 public function ceosymbolno_200excel(Request $request){
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $st_code=  $d->st_code;
   $cur_time    = Carbon::now();

   $arr  = array();
   //$cand_party_type='Z'; 
   $finalize='1';
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $independentCandList=$this->pcceoreportModel->ceosymbolno_200pdf($d->st_code,$finalize);
  //print_r($independentCandList);die; 
  $export_data[] = ['Serial No.', 'PC Number&Name', 'Candidate Name', 'Party Name','Symbol'];
  $headings[]=[];

   foreach ($independentCandList as $list) {
      $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
       $partyDetails=getById('m_party','CCODE',$list->party_id);
       $pcDetails=getpcbypcno($st_code,$list->pc_no);
       $symbolDetails=getsymbolbyid($list->symbol_id);

       $export_data[] = [
        $list->new_srno,
        $list->pc_no.' - '.$pcDetails->PC_NAME,
        $candidatedetails->cand_name,
        $partyDetails->PARTYNAME,
        $symbolDetails->SYMBOL_DES
         ];


      }


  $name_excel = 'independent-candidate-ceo-report'.trim($st_code).'_'.$cur_time;
  return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');        


  // \Excel::create('independent-candidate-ceo-report'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
  //     $excel->sheet('Sheet1', function($sheet) use($st_code) {

  //     $arr  = array();
  //     //$cand_party_type='Z'; 
  //     $finalize='1';
  //     $user = Auth::user();
  //     $d=$this->commonModel->getunewserbyuserid($user->id);
  //     $independentCandList=$this->pcceoreportModel->ceosymbolno_200pdf($d->st_code,$finalize);
  //    //print_r($independentCandList);die; 
  //     foreach ($independentCandList as $list) {
  //        $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
  //         $partyDetails=getById('m_party','CCODE',$list->party_id);
  //         $pcDetails=getpcbypcno($st_code,$list->pc_no);
  //         $symbolDetails=getsymbolbyid($list->symbol_id);
  //         $data =  array(
  //                 $list->new_srno,
  //                 $list->pc_no.' - '.$pcDetails->PC_NAME,
  //                 $candidatedetails->cand_name,
  //                 $partyDetails->PARTYNAME,
  //                 $symbolDetails->SYMBOL_DES
  //                       );
  //                 array_push($arr, $data); 
  //                 }
  //  $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
  //                      'Serial No.', 'PC Number&Name', 'Candidate Name', 'Party Name','Symbol'
  //              )

  //          );

  //        });

  //   })->export('xls');
   }else {
    return redirect('/officer-login');
  }   
 }  

 public function ceocandidatesummary(Request $request){
  if(Auth::check()){
  $user = Auth::user();
  $d=$this->commonModel->getunewserbyuserid($user->id);
    $st_code=  $d->st_code;
    $cur_time    = Carbon::now();
  \Excel::create('candidate-status-report'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
      $excel->sheet('Sheet1', function($sheet) use($st_code) {
      $lists=$this->pcceoreportModel->duplicateSymboleCandidate($st_code);
      $arr  = array();
      $cand_party_type='Z'; $finalize='1';
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $allPcList=$this->commonModel->getpcbystate($d->st_code);
      $st=getstatebystatecode($d->st_code);  
      $state_name =  $st->ST_NAME;
     //print_r($allPcList);die; 
      $count = 1;
    foreach ($allPcList as $list) {
        $nominateMaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','male','');
        $nominateFeMaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','female','');
        $nominateThirdCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','third','');
        $rejectedMaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','male',4);
        $rejectedFeMaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','female',4);
        $rejectedOtherCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','third',4);
        $withdrawnMaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','male',5);
        $withdrawnFemaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','female',5);
        $withdrawnOtherCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','third',5);
        $cotestingMaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','male',6);
        $contestingFemaleCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','female',6);
        $contestingOtherCount=$this->pcceoreportModel->getCountForSummary($d->st_code, $list->PC_NO,'cand_gender','third',6);
          $data =  array(
                  $count,
                  $state_name,
                  $list->PC_NO,
                  $list->PC_NAME,
                  $nominateMaleCount,
                  $nominateFeMaleCount,
                  $nominateThirdCount,
                  $rejectedMaleCount,
                  $rejectedFeMaleCount,
                  $rejectedOtherCount,
                  $withdrawnMaleCount,
                  $withdrawnFemaleCount,
                  $withdrawnOtherCount,
                  $cotestingMaleCount,
                  $contestingFemaleCount,
                  $contestingOtherCount
                        );
                  array_push($arr, $data);
                   // }
                  $count++;
                  }

   $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                       'Serial No.', 'State Name','PC No.','PC Name',
                       'Nominated Male Count','Nominated FeMale Count',
                       'Nomination Other Count','Rejected Male Count',
                       'Rejected FeMale Count','Rejected Other Count',
                       'Withdrawn Male Count','Withdrawn FeMale Count',
                       'Withdrawn Other Count','Contesting Male Count',
                       'Contesting FeMale Count','Contesting other Count'
               )

           );

         });

    })->export('xls');
}else {
   return redirect('/officer-login');
 }
}
 public function ceologindetail(Request $request){
  
  //echo $date = date('y/m/d h:i:s a', time());
  //echo "test";die;
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $officerDetails =DB::table('officer_login')->where('st_code',$d->st_code)->get();
$st_code=$d->st_code;
//$officerDetails=$this->pcceoreportModel->getOfficerlistByROPC($d->st_code);
//print_r($officerDetails);die;
   return view('admin.pc.ceo.officerlogindetail',['user_data' => $d,'ele_details' => $ele_details,'officerDetails' => $officerDetails]);
   }else {
    return redirect('/officer-login');
  }   
 }

 public function logindetailpdf(Request $request){
  //echo "test";die;
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $st_code=$d->st_code;
   $allUsers =DB::table('officer_login')->where('st_code',$d->st_code)->get();
   $pdf = PDF::loadView('admin.pc.ceo.officeLoginDetailHtml', compact('st_code','allUsers'));
            return $pdf->download($st_code."-user-login-detail-report".".pdf");
   }else {
    return redirect('/officer-login');
  }   
 }

 public function logindetailexcel(Request $request){
  //echo "test";die;
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $st_code=$d->st_code;
$cur_time    = Carbon::now();

$arr  = array();
//$cand_party_type='Z'; 
$finalize='1';
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id);
   $allUsers =DB::table('officer_login')->where('st_code',$d->st_code)->get();
   $j=0;

   $export_data[] = ['Serial No.', 'User Id', 'Desigation', 'Officer Name','State','Officer Level',
   'Mobile Number','PC','AC','Password'];
   $headings[]=[];

foreach ($allUsers as $officerDetailsList) {
  $j++;
   $pcDetails=getpcbypcno($officerDetailsList->st_code,$officerDetailsList->pc_no); 
   //print_r($pcDetails);
   $acDetails =getacbyacno($officerDetailsList->st_code,$officerDetailsList->ac_no);
   $st=getstatebystatecode($officerDetailsList->st_code);
   if($pcDetails !=''){
    $pcName = $pcDetails->PC_NAME;
   } else{
    $pcName ='-';
   }
     if($acDetails !=''){
      $acName =$acDetails->AC_NAME;
     }else{
      $acName='-';
     }

     $export_data[] = [
            $j,
            $officerDetailsList->officername,
            $officerDetailsList->designation,
            $officerDetailsList->name,
            $officerDetailsList->st_code .'-' .$st->ST_NAME,
            $officerDetailsList->officerlevel,
            $officerDetailsList->Phone_no,
            //$officerDetailsList->pc_no.'-'.$pcDetails->PC_NAME,
            $pcName,
            $acName,
            //$officerDetailsList->ac_no.'-'.$acDetails->AC_NAME,
            'demo@1234'
            ];

            }


$name_excel = 'independent-candidate-ceo-report'.trim($st_code).'_'.$cur_time;
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 



  // \Excel::create('independent-candidate-ceo-report'.trim($st_code).'_'.$cur_time, function($excel) use($st_code) { 
  //     $excel->sheet('Sheet1', function($sheet) use($st_code) {

  //     $arr  = array();
  //     //$cand_party_type='Z'; 
  //     $finalize='1';
  //     $user = Auth::user();
  //     $d=$this->commonModel->getunewserbyuserid($user->id);
  //        $allUsers =DB::table('officer_login')->where('st_code',$d->st_code)->get();
  //        $j=0;
  //     foreach ($allUsers as $officerDetailsList) {
  //       $j++;
  //        $pcDetails=getpcbypcno($officerDetailsList->st_code,$officerDetailsList->pc_no); 
  //        //print_r($pcDetails);
  //        $acDetails =getacbyacno($officerDetailsList->st_code,$officerDetailsList->ac_no);
  //        $st=getstatebystatecode($officerDetailsList->st_code);
  //        if($pcDetails !=''){
  //         $pcName = $pcDetails->PC_NAME;
  //        } else{
  //         $pcName ='-';
  //        }
  //          if($acDetails !=''){
  //           $acName =$acDetails->AC_NAME;
  //          }else{
  //           $acName='-';
  //          }
  //         $data =  array(
  //                 $j,
  //                 $officerDetailsList->officername,
  //                 $officerDetailsList->designation,
  //                 $officerDetailsList->name,
  //                 $officerDetailsList->st_code .'-' .$st->ST_NAME,
  //                 $officerDetailsList->officerlevel,
  //                 $officerDetailsList->Phone_no,
  //                 //$officerDetailsList->pc_no.'-'.$pcDetails->PC_NAME,
  //                 $pcName,
  //                 $acName,
  //                 //$officerDetailsList->ac_no.'-'.$acDetails->AC_NAME,
  //                 'demo@1234'
  //                       );
  //                 array_push($arr, $data); 
  //                 }
  //  $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
  //                      'Serial No.', 'User Id', 'Desigation', 'Officer Name','State','Officer Level',
  //                      'Mobile Number','PC','AC','Password'
  //              )

  //          );

  //        });

  //   })->export('xls');


   }else {
    return redirect('/officer-login');
  }   
 }
 
 /**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 18-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return nominationadatewisereport filter By date     
  */
		public function nominationadatewisereport(Request $request){  
			//dd($request->all());
			if(Auth::check()){ 
			$user = Auth::user();
			$d=$this->commonModel->getunewserbyuserid($user->id);
			// dd($d);
    	$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
			//dd($ele_details);
						$from_date = ($request->from_date);
						$to_date = ($request->to_date); 
						$st_code = $request->st_code;
						$pc_no = $request->pc_no;

						if(isset($from_date)){
							if($from_date=='all' && $to_date=='all'){
								$from_date='';
								$to_date='';
							}
						}
						
						$timeInterval = $from_date.'~'.$to_date;
						$fromdate = date('Y-m-d',strtotime($from_date));
						$todate = date('Y-m-d',strtotime($to_date));  

						$datewisenominationreport=$this->pcceoreportModel->getDatewisenomination($st_code,$d->pc_no,$fromdate,$todate);
            // dd($datewisenominationreport);
							if(!empty($datewisenominationreport)){  $j=1;
								$canddetailsArray = array();
                $html='';
                $totalg=0;
									foreach ($datewisenominationreport as $listdata) { 
                    $j++;
										// dd($listdata);
										// $canddetailsArray=CandidateModel::where(['candidate_id' =>$listdata->candidate_id])->get();
										// $nominationArray=CandidateNomination::where(['st_code' =>$ele_details->ST_CODE,'pc_no' =>$ele_details->CONST_NO,'election_id' =>$ele_details->ELECTION_ID])->where(['candidate_id' =>$listdata->candidate_id])->get();
                     $pc=getpcbypcno($listdata->st_code,$listdata->pc_no);
                    // dd($pc);
                     $totalg=$totalg+$listdata->totalnomination;  
										 $html.='<tr>
											 <td>'.$pc->PC_NO.'</td>
											 <td><a target="" href=".'.'/datewisecandidatelist/'.base64_encode($pc->PC_NO).'/'.base64_encode($timeInterval).'/'.'.">'.$pc->PC_NAME.'</a></td>
                       <td><a target="" href=".'.'/datewisecandidatelist/'.base64_encode($pc->PC_NO).'/'.base64_encode($timeInterval).'/'.'.">'.$listdata->totalnomination.'</a></td>
                     </tr>';
                      }
                      $html.='<tr> 
                      <td>Total:- </td>
                      <td> </td> 
                      <td>'.$totalg.'</td>
                     </tr>';   
										}	else{
										 $html .= '<tr><td colspan="3" style="color:red; text-align:center;"><b>No Record Found.</b></td></tr>';
										}
											return $html;
				       	}else {
								return redirect('/officer-login');
							}
		 }// end nominationadatewisereport List function
		 
		     /**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 18-01-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return datewisecandidatelist By State fuction
 */

 public function datewisecandidatelist(Request $request,$pcno,$date){ 
   $dateRange=$date;
  $date=trim(base64_decode($request->date));
  $pcno=trim(base64_decode($request->pcno));
  $date_range = explode('~', $date);
  $from_date=$date_range[0];
  $to_date=$date_range[1];
  $fromdate = date('Y-m-d',strtotime($from_date));
  $todate = date('Y-m-d',strtotime($to_date));
 //echo $fromdate.'==>'.$todate.'==>'.$pcno; die('test');
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $st_code =$d->st_code;
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
  //$AllcandListbyPC=$this->pcceoreportModel->getAllCandidateListbyPC($d->st_code,$pcno);
   $AllcandListbyPC=$this->pcceoreportModel->getDatewiseCandidateListbyPC($st_code,$pcno,$fromdate,$todate);
  //dd($AllcandListbyPC);
  return view('admin.pc.ceo.datewisecandidatelist',['st_code'=>$st_code,'user_data' => $d,'ele_details' => $ele_details,'candListbyPC' => $AllcandListbyPC,'pc_no'=>$pcno,'dateRange'=>$dateRange]);
}
else {
    return redirect('/officer-login');
  }
}   // end datewisecandidatelist function

/**
  * @author Devloped By : Niraj Kumar
  * @author Devloped Date : 18-03-19
  * @author Modified By : 
  * @author Modified Date : 
  * @author param return getNominationreport report By PC wise     
  */
	public function getNominationreport(request $request){ 
		if(Auth::check()){
      $user = Auth::user();
      $d=$this->commonModel->getunewserbyuserid($user->id);
      $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
       // dd($ele_details);
      $check_finalize=candidate_finalizebyro($ele_details[0]->ST_CODE,$ele_details[0]->CONST_NO,$ele_details[0]->CONST_TYPE);
          $seched=getschedulebyid($ele_details[0]->ScheduleID);
          $sechdul=checkscheduledetails($seched);  
         // dd($ele_details);
             if(isset($ele_details[0]->ScheduleID)) {
                $sched=$this->commonModel->getschedulebyid($ele_details[0]->ScheduleID);
                $const_type=$ele_details[0]->CONST_TYPE;
             }
                else {
                  $sched='';
                }
      $allPcList= DB::table('candidate_nomination_detail')
			->select('*', DB::raw('count(nom_id) as totalnomination'))
      ->where('st_code',$d->st_code)
      ->where('party_id','!=','1180')->where('application_status','!=','11')
      ->groupBy('pc_no')
			->get();
//print_r($allPcList);die;
    return view('admin.pc.ceo.nomination-report', ['user_data' => $d,'cand_finalize_ceo' =>$check_finalize->finalize_by_ceo,'cand_finalize_ro' =>$check_finalize->finalized_ac,'sechdul' => $sechdul,'allPcList' => $allPcList,'ele_details'=>$ele_details]);
   }	else {
    return redirect('/officer-login');
   }
		} // end getNominationreport List function
		
		/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 25-03-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return pcList By State fuction
 */

public function nominatedcandListbyPC(Request $request,$pcno){
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $AllcandListbyPC=$this->pcceoreportModel->getAllCandidateListbyPC($d->st_code,$pcno);
   $st_code =$d->st_code;
  return view('admin.pc.ceo.candidatelist-pc',['st_code'=>$st_code,'user_data' => $d,'ele_details' => $ele_details,'candListbyPC' => $AllcandListbyPC,'pc_no'=>$pcno]);
}
else {
    return redirect('/officer-login');
  }
}   // end nominatedcandListbyPC function

/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 25-03-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return ViewNominationDetails By State fuction
 */
public function ViewNominationDetails($nomid){ 
	if(Auth::check()){ 
$user = Auth::user();
$d=$this->commonModel->getunewserbyuserid($user->id); 
$ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
$nom=getById('candidate_nomination_detail','nom_id',$nomid); 
$cand=getById('candidate_personal_detail','candidate_id',$nom->candidate_id); 
 return view('admin.pc.ceo.viewnomination', ['user_data' => $d, 'nomid'=>$nomid,'nomDetails'=>$nom,'persoanlDetails'=>$cand, 'ele_details'=>$ele_details]);	           
	}
	else{
		return redirect('/officer-login');
	}
} // end ViewNominationDetails


/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 25-03-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return exlfunction By State fuction
 */
public function nominatedcandidatelistexcelPC(Request $request,$pcno){
  if(Auth::check()){
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $candListbyPC=$this->pcceoreportModel->getAllCandidateListbyPC($d->st_code,$pcno);
   $st_code=  $d->st_code;
   $cur_time    = Carbon::now();

   $candListbyPC=$this->pcceoreportModel->getAllCandidateListbyPC($st_code,$pcno);
   $arr  = array();
   $cand_party_type='Z'; $finalize='1';
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   $allPcList=$this->commonModel->getpcbystate($d->st_code);
  //print_r($independentCandList);die; 
   $count = 1;
   $export_data[] = ['Serial No.','PC Number&Name' ,'Candidate Name','Candidate Name Hindi','Party Name', 'Symbol'];
   $headings[]=[];

   foreach ($candListbyPC as $list) {
      $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
       $partyDetails=getById('m_party','CCODE',$list->party_id);
       $pcDetails=getpcbypcno($d->st_code,$list->pc_no);
       $symbolDetails=getsymbolbyid($list->symbol_id);
       if(!empty($pcDetails)){ $partyname =$partyDetails->PARTYNAME;} else{ $partyname='-'; }
if(!empty($symbolDetails)){ $symbolName =$symbolDetails->SYMBOL_DES;} else{ $symbolName='-'; }

              $export_data[] = [
                            $count,
                            $list->pc_no.' - '.$pcDetails->PC_NAME,
                            $candidatedetails->cand_name,
                            $candidatedetails->cand_hname,
                            $partyname,
                            $symbolName
              ];

     
               $count++;
               }


   $name_excel = 'nominated-candidate-detail-excel'.trim($st_code).'_'.$cur_time;
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


//    \Excel::create('nominated-candidate-detail-excel'.trim($st_code).'_'.$cur_time, function($excel) use($st_code,$pcno) { 
//      $excel->sheet('Sheet1', function($sheet) use($st_code,$pcno) {

//      $candListbyPC=$this->pcceoreportModel->getAllCandidateListbyPC($st_code,$pcno);
//      $arr  = array();
//      $cand_party_type='Z'; $finalize='1';
//      $user = Auth::user();
//      $d=$this->commonModel->getunewserbyuserid($user->id);
//      $allPcList=$this->commonModel->getpcbystate($d->st_code);
//     //print_r($independentCandList);die; 
//      $count = 1;
//      foreach ($candListbyPC as $list) {
//         $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
//          $partyDetails=getById('m_party','CCODE',$list->party_id);
//          $pcDetails=getpcbypcno($d->st_code,$list->pc_no);
//          $symbolDetails=getsymbolbyid($list->symbol_id);
//          if(!empty($pcDetails)){ $partyname =$partyDetails->PARTYNAME;} else{ $partyname='-'; }
// if(!empty($symbolDetails)){ $symbolName =$symbolDetails->SYMBOL_DES;} else{ $symbolName='-'; }
//          $data =  array(
//                  $count,
//                  $list->pc_no.' - '.$pcDetails->PC_NAME,
//                  $candidatedetails->cand_name,
//                  $candidatedetails->cand_hname,
//                  $partyname,
//                  $symbolName
//                        );
//                  array_push($arr, $data);
//                 // }
//                  $count++;
//                  }
//             $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
//                       'Serial No.','PC Number&Name' ,'Candidate Name','Candidate Name Hindi','Party Name', 'Symbol'
//               )

//           );

//         });

//    })->export('xls');
} else {
    return redirect('/officer-login');
  }
}   
/**
 * @author Devloped By : Niraj Kumar
 * @author Devloped Date : 25-03-19
 * @author Modified By :
 * @author Modified Date :
 * @author param return datewisenomcandlistexcelPC By State fuction
 */
public function datewisenomcandlistexcelPC(Request $request,$pcno,$date){
  
  if(Auth::check()){
  $date=trim(base64_decode($request->date));
  $pcno=$request->pcno;
  $date_range = explode('~', $date);
  $from_date=$date_range[0];
  $to_date=$date_range[1];
  $fromdate = date('Y-m-d',strtotime($from_date));
  $todate = date('Y-m-d',strtotime($to_date));
  
   $user = Auth::user();
   $d=$this->commonModel->getunewserbyuserid($user->id);
   //dd($d);
   $ele_details=$this->commonModel->election_details($d->st_code,$d->ac_no,$d->pc_no,$d->id,$d->officerlevel);
   $st_code= $d->st_code;
   $candListbyPC=$this->pcceoreportModel->getDatewiseCandidateListbyPC($st_code,$pcno,$fromdate,$todate);
   
   $cur_time    = Carbon::now();
   \Excel::create('nominated-candidate-detail-excel'.trim($st_code).'_'.$cur_time, function($excel) use($st_code,$pcno,$fromdate,$todate) { 
     $excel->sheet('Sheet1', function($sheet) use($st_code,$pcno,$fromdate,$todate) {
     $candListbyPC=$this->pcceoreportModel->getDatewiseCandidateListbyPC($st_code,$pcno,$fromdate,$todate);
     $arr  = array();
     $cand_party_type='Z'; $finalize='1';
     $user = Auth::user();
     $d=$this->commonModel->getunewserbyuserid($user->id);
     $allPcList=$this->commonModel->getpcbystate($d->st_code);
    //print_r($independentCandList);die; 
     $count = 1;
     foreach ($candListbyPC as $list) {
        $candidatedetails=getById('candidate_personal_detail','candidate_id',$list->candidate_id);
         $partyDetails=getById('m_party','CCODE',$list->party_id);
         $pcDetails=getpcbypcno($d->st_code,$list->pc_no);
         $symbolDetails=getsymbolbyid($list->symbol_id);
         if(!empty($pcDetails)){ $partyname =$partyDetails->PARTYNAME;} else{ $partyname='-'; }
if(!empty($symbolDetails)){ $symbolName =$symbolDetails->SYMBOL_DES;} else{ $symbolName='-'; }
         $data =  array(
                 $count,
                 $list->pc_no.' - '.$pcDetails->PC_NAME,
                 $candidatedetails->cand_name,
                 $candidatedetails->cand_hname,
                 $partyname,
                 $symbolName
                       );
                 array_push($arr, $data);
                // }
                 $count++;
                 }
            $sheet->fromArray($arr,null,'A1',false,false)->prependRow(array(
                      'Serial No.','PC Number&Name' ,'Candidate Name','Candidate Name Hindi','Party Name', 'Symbol'
              )

          );

        });

   })->export('xls');
} else {
    return redirect('/officer-login');
  }
}  

 
}  // end class