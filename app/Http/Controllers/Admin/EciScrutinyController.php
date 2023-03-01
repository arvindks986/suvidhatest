<?php namespace App\Http\Controllers\Admin;
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
    use \PDF;
    use App\commonModel;  
    use App\models\Admin\ReportModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\models\Admin\StateModel;
    use App\Exports\ExcelExport;
    use Maatwebsite\Excel\Facades\Excel;

class EciScrutinyController extends Controller {
  
  public $base    = 'ro';
  public $folder  = 'eci';
  public $action    = 'eci/report/scrutiny';
  public $view_path = "admin.pc.eci";

  public function __construct(){
    $this->commonModel  = new commonModel();
    $this->report_model = new ReportModel();
  }

  public function get_report_by_state(Request $request){
      $data = [];
      $from_date  = NULL;
      $from_to    = NULL;
      
      //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
      $request_status = validate_request('',$request);
      if(!$request_status){
        return Redirect::to('logout');
      }
      //end validate request

      $request_array = [];
    
      if(!Auth::user()){
        return redirect('/officer-login');
      }

      $data['states'] = [];
      foreach(StateModel::get_states() as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }


      $data['phases'] = $this->report_model->get_phases();

      $data['phase'] = NULL;
      if($request->has('phase')){
        $data['phase'] = $request->phase;
        $request_array[] =  'phase='.$request->phase;
      }

      if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to        = date('Y-m-d',strtotime($request->to));
        $request_array[] = 'from='.$request->from;
        $request_array[] = 'to='.$request->to;
      }


      $data['action']         = url($this->action.'/state');
      $data['redirect_href']  = url($this->action);

      $results              = [];
      $total                = 0;
      $total_withdraw       = 0;
      $total_rejected       = 0;
      $total_accepted       = 0;
      $total_verify_by_ro   = 0;
      $total_receipt        = 0;
      $total_applied        = 0;
      $total_contested      = 0;
      $total_validated      = 0;

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Scrutiny report';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }
      $data['filter_buttons'] = $title_array;
      //end set title

      foreach ($data['states'] as $lis) {   

          $filter_data = [
            'from_date'     => $from_date,
            'to_date'       => $from_to,
            'st_code'       => base64_decode($lis['code']),
            'const_type'    => NULL,
            'phase'         => $data['phase']
          ];

          $count_total        = $this->report_model->get_total_nomination(0, $filter_data);
          $count_withdraw     = $this->report_model->get_total_nomination(5, $filter_data);
          $count_rejected     = $this->report_model->get_total_nomination(4, $filter_data);
          $count_accepted     = $this->report_model->get_total_nomination(6, $filter_data);
          $count_verify_by_ro = $this->report_model->get_total_nomination(2, $filter_data);
          $count_receipt      = $this->report_model->get_total_nomination(3, $filter_data);
          $count_applied      = $this->report_model->get_total_nomination(1, $filter_data);
          $count_contested    = $this->report_model->get_total_nomination(6, array_merge($filter_data,['final_accepted' => 1, 'symbol_excluded' => 1]));
          $count_validated    = $this->report_model->get_total_nomination(6, array_merge($filter_data,['final_accepted' => 1]));

          $total              += $count_total;
          $total_withdraw     += $count_withdraw;
          $total_rejected     += $count_rejected;
          $total_accepted     += $count_accepted;
          $total_verify_by_ro += $count_verify_by_ro;
          $total_receipt      += $count_receipt;
          $total_applied      += $count_applied;
          $total_contested    += $count_contested;
          $total_validated    += $count_validated;


          $results[] = [
            'label'              => $lis['name'],
            'filter'             => implode('&', array_merge($request_array,['state' => 'state='.$lis['code']])),
            'const_no'           => $lis['code'],
            'const_name'         => $lis['name'],
            'total'              => $count_total,
            'total_withdraw'     => $count_withdraw,
            'total_rejected'     => $count_rejected,
            'total_accepted'     => $count_accepted,
            'total_verify_by_ro' => $count_verify_by_ro,
            'total_receipt'      => $count_receipt,
            'total_applied'      => $count_applied,
            'total_validated'    => $count_validated,
            'total_contested'    => $count_contested,
          ];                        
    }   

    $data['totals'] = [
      'label'              => 'Total',
      'filter'             => '',
      'const_no'           => '',
      'const_name'         => 'Total',
      'total'              => $total,
      'total_withdraw'     => $total_withdraw,
      'total_rejected'     => $total_rejected,
      'total_accepted'     => $total_accepted,
      'total_verify_by_ro' => $total_verify_by_ro,
      'total_receipt'      => $total_receipt,
      'total_applied'      => $total_applied,
      'total_validated'    => $total_validated,
      'total_contested'    => $total_contested,
      'href'               => 'javascript:void(0)'
    ]; 

    $data['results']    =  $results;
    $data['user_data']  = Auth::user();
    $data['from']       = $from_date;
    $data['to']         = $from_to;

    $data['downlaod_to_excel'] = url($this->action.'/state/excel').'?'.implode('&', $request_array);
    $data['downlaod_to_pdf']   = url($this->action.'/state/pdf').'?'.implode('&', $request_array);
    $data['back_href']         = '';

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view($this->view_path.'.report.state_wise_report', $data);

  }

  public function get_report(Request $request){  

      $data = [];
      $from_date  = NULL;
      $from_to    = NULL;
      $request_array = [];

      //first argument must be string, second must be $request object if you want to verify base 64, send that variable in ccode parameter in request object using $request->merge(['ccode' => $somevalue]);
      $request_status = validate_request('',$request);
      if(!$request_status){
        return Redirect::to('logout');
      }
      //end validate request

      
      if(!Auth::user()){
        return redirect('/officer-login');
      }

      $data['states'] = [];
      foreach(StateModel::get_states() as $result){
        $data['states'][] = [
            'code' => base64_encode($result->ST_CODE),
            'name' => $result->ST_NAME,
        ];
      }

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state'] = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      $data['phases'] = $this->report_model->get_phases();

      $data['constituency'] = '';
      if($request->has('constituency')){
        $data['constituency'] = $request->constituency;
        $request_array[] =  'constituency='.$request->constituency;
      }

      $data['phase'] = NULL;
      if($request->has('phase')){
        $data['phase'] = $request->phase;
        $request_array[] =  'phase='.$request->phase;
      }

      if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to        = date('Y-m-d',strtotime($request->to));
        $request_array[] = 'from='.$request->from;
        $request_array[] = 'to='.$request->to;
      }
          
      $d              = Auth::user();

      if(Auth::user()->designation=='CEO'){
        $base = 'pcceo';
        $folder = 'ceo';
      }else{
        $base = 'ropc';
        $folder = 'ro';
      }

      $filter_election = [
        'state_code'    => ($data['state'])?$data['state']:NULL,
        'const_type'    => 'PC',
        'officerlevel'  => Auth::user()->officerlevel,
        'id'            => Auth::user()->id,
        'ac_no'         => Auth::user()->ac_no,
      ];


      $data['action'] = url('eci/report/scrutiny');


      $filter_report = array_merge($filter_election,[
        'pc_no'         => ($data['constituency'])?$data['constituency']:NULL,
        'phase_id'      => ($data['phase'])?$data['phase']:NULL,
      ]);
      $lists_all    = $this->report_model->get_scrutny_report_ceo($filter_election);
      $lists        = $this->report_model->get_scrutny_report_ceo($filter_report);

      $results = [];
      $total           = 0;
      $total_withdraw  = 0;
      $total_rejected  = 0;
      $total_accepted  = 0;
      $total_verify_by_ro  = 0;
      $total_receipt       = 0;
      $total_applied       = 0;
      $total_contested     = 0;
      $total_validated     = 0;

      //set title
      $title_array  = [];
      $data['heading_title'] = 'Scrutiny report';
      if(isset($from_date) && isset($from_to)){
        $data['heading_title'] .= ' between '.date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($from_to));
      }
      if($data['phase']){
        $title_array[] = "Phase: ".$data['phase'];
      }
      if($data['state']){
        $state_object = StateModel::get_state_by_code($data['state']);
        if($state_object){
          $title_array[]  = "State: ".$state_object['ST_NAME'];
        }
      }
      $data['filter_buttons'] = $title_array;

      //end set title

      $sort = array();
      foreach ($lists as $key => $lis) {   


          $const_name = NULL;
          if($lis->CONST_TYPE=='AC') {
            $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
            $const_name=$const->AC_NAME;
          }
          if($lis->CONST_TYPE=='PC') {
            $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->CONST_NO);
            $const_name=trim($const->PC_NAME);
          }

          $filter_data = [
            'from_date'     => $from_date,
            'to_date'       => $from_to,
            'st_code'       => $lis->ST_CODE,
            'const_type'    => $lis->CONST_TYPE,
            'const_no'      => $lis->CONST_NO,
            'phase'         => $data['phase'],
          ];

          $count_total        = $this->report_model->get_total_nomination(0, $filter_data);
          $count_withdraw     = $this->report_model->get_total_nomination(5, $filter_data);
          $count_rejected     = $this->report_model->get_total_nomination(4, $filter_data);
          $count_accepted     = $this->report_model->get_total_nomination(6, $filter_data);
          $count_verify_by_ro = $this->report_model->get_total_nomination(2, $filter_data);
          $count_receipt      = $this->report_model->get_total_nomination(3, $filter_data);
          $count_applied      = $this->report_model->get_total_nomination(1, $filter_data);
          $count_contested    = $this->report_model->get_total_nomination(6, array_merge($filter_data,['final_accepted' => 1, 'symbol_excluded' => 1]));
          $count_validated    = $this->report_model->get_total_nomination(6, array_merge($filter_data,['final_accepted' => 1]));

          $total              += $count_total;
          $total_withdraw     += $count_withdraw;
          $total_rejected     += $count_rejected;
          $total_accepted     += $count_accepted;
          $total_verify_by_ro += $count_verify_by_ro;
          $total_receipt      += $count_receipt;
          $total_applied      += $count_applied;
          $total_contested    += $count_contested;
          $total_validated    += $count_validated;

          $sort['const_name'][$key] = $lis->PC_NAME;
          $sort['const_no'][$key]   = $lis->PC_NO;
          $results[] = [
            'label'              => $lis->PC_NO.'-'.$lis->PC_NAME,
            'filter'             => implode('&', array_merge($request_array,['ccode' => 'ccode='.base64_encode($lis->CCODE)])),
            'const_no'           => $lis->PC_NO,
            'const_name'         => $lis->PC_NAME,
            'total'              => $count_total,
            'total_withdraw'     => $count_withdraw,
            'total_rejected'     => $count_rejected,
            'total_accepted'     => $count_accepted,
            'total_verify_by_ro' => $count_verify_by_ro,
            'total_receipt'      => $count_receipt,
            'total_applied'      => $count_applied,
            'total_validated'    => $count_validated,
            'total_contested'    => $count_contested,
          ];                        
    }  

    $data['totals'] = [
      'label'              => 'Total',
      'filter'             => '',
      'const_no'           => '',
      'const_name'         => 'Total',
      'total'              => $total,
      'total_withdraw'     => $total_withdraw,
      'total_rejected'     => $total_rejected,
      'total_accepted'     => $total_accepted,
      'total_verify_by_ro' => $total_verify_by_ro,
      'total_receipt'      => $total_receipt,
      'total_applied'      => $total_applied,
      'total_validated'    => $total_validated,
      'total_contested'    => $total_contested,
      'href'               => 'javascript:void(0)'
    ]; 

    $data['results']    =  $results;
    $data['list_const'] = $lists_all;
    $data['user_data']  = Auth::user();
    $data['from']       = $from_date;
    $data['to']         = $from_to;

    $data['downlaod_to_excel'] = url('eci/report/scrutiny/excel').'?'.implode('&', $request_array);
    $data['downlaod_to_pdf']   = url('eci/report/scrutiny/pdf').'?'.implode('&', $request_array);
    $data['back_href']         = url('eci/report/scrutiny/state').'?'.implode('&', $request_array);

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    return view('admin.pc.eci.report.date_wise_report', $data);   
  }    

  public function detail($id,Request $request){
    $data = [];
    $ccode = NULL;
    $symbol_excluded = NULL;
    $final_accepted = NULL;
    if($id=='accepted'){
      $status = 6;
    }else if($id=='withdraw'){
      $status = 5;
    }else if($id=='rejected'){
      $status = 4;
    }else if($id=='contested'){
      $status = 6;
      $final_accepted = 1;
      $symbol_excluded = 1;
    }else if($id=='validated'){
      $status = 6;
      $final_accepted = 1;
    }else{
      $status = 0;
    }

    if($id=='contested'){
      $status_name = 'Contesting';
    }else{
      $status_name = $id;
    }

    //first argument should be string, second would be $request object
    $request_status = validate_request('',$request);
    if(!$request_status){
      return Redirect::to('logout');
    }
    //end validate request

    $data['heading_title'] = "Scrutiny Report of ".$status_name." candidate(s)";

      
      $from_date  = NULL;
      $from_to    = NULL;
      $request_array = [];
      

      if(!Auth::user()){
        return redirect('/officer-login');
      }

      $data['phases'] = $this->report_model->get_phases();

      $data['constituency'] = NULL;
      if($request->has('constituency')){
        $data['constituency'] = $request->constituency;
        $request_array[] = 'constituency='.$request->constituency;
      }

      $data['phase'] = NULL;
      if($request->has('phase')){
        $data['phase'] = $request->phase;
        $request_array[] = 'phase='.$request->phase;
      }

      if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to        = date('Y-m-d',strtotime($request->to));
        $request_array[] = 'from='.$request->from;
        $request_array[] = 'to='.$request->to;

      }

      $data['state'] = NULL;
      if($request->has('state')){
        $data['state']  = base64_decode($request->state);
        $request_array[] = 'state='.$request->state;
      }

      $d              = Auth::user();

      //
      $filter_election = [
        'state_code'    => $data['state'],
        'const_type'    => 'PC',
      ];

      $data['action'] = url('eci/report/scrutiny');


      $filter_report = array_merge($filter_election,[
        'pc_no'         => ($data['constituency'])?$data['constituency']:NULL,
        'phase_id'      => ($data['phase'])?$data['phase']:NULL,
      ]);
      $lists_all    = $this->report_model->get_scrutny_report_ceo($filter_election);
      $lists        = $this->report_model->get_scrutny_report_ceo($filter_report);

      //

      if($request->has('from') && $request->has('to')){
        $from_date  = date('Y-m-d',strtotime($request->from));
        $from_to    = date('Y-m-d',strtotime($request->to));
      }

      if(isset($ele_details->ScheduleID)) {
        $sched      = $this->commonModel->getschedulebyid($ele_details->ScheduleID);
        $const_type = $ele_details->CONST_TYPE;
      }else {
        $sched      = '';
      }


      if($request->has('ccode')){
        $ccode = base64_decode($request->ccode);
        $filter_election['ccode'] = $ccode;
      }

      $lis      = $this->report_model->election_detail($filter_election);
      if(!$lis){
        return Redirect::to($data['action']);
      }
      
      $const_name = NULL;
      if($lis->CONST_TYPE=='AC') {
        $const=$this->commonModel->getacbyacno($lis->ST_CODE,$lis->CONST_NO);
        $const_name=$const->AC_NAME;
      }
      if($lis->CONST_TYPE=='PC') {
        $const=$this->commonModel->getpcname($lis->ST_CODE,$lis->CONST_NO);
        $const_name=trim($const->PC_NAME);
      }

      $filter_data = [
            'from_date'     => $from_date,
            'to_date'       => $from_to,
            'st_code'       => $data['state'],
            'const_type'    => $lis->CONST_TYPE,
            'const_no'        => $ccode,
            'final_accepted'  => $final_accepted, 
            'phase'           => $data['phase'],
            'symbol_excluded' => $symbol_excluded
      ];

      $pcs               = $this->report_model->get_pc_detail($filter_data);

      $candidates        = $this->report_model->get_nominations($status, $filter_data);
      $index = 0;
      $results = [];
      foreach ($candidates as $candidate) {
   
          $const=$this->commonModel->getpcname($candidate->ST_CODE,$candidate->PC_NO);   

          $name = $candidate->cand_name;
          if($candidate->finalaccepted == 1 && $status == 6){
            $status_name = 'Contesting';
          }else{
            $status_name = $candidate->status_name;
          }
          $results[] = [
            'index'          => $candidate->new_srno,
            'pc_no_name'     => $const->PC_NO.'-'.$const->PC_NAME,
            'candidate_id'   => $candidate->candidate_id,
            'name'           => $name,
            'h_name'         => $candidate->cand_hname,
            'email'          => $candidate->cand_email,
            'mobile'         => $candidate->cand_mobile,
            'status'         => $status_name,
            'party_name'     => $candidate->PARTYNAME,
            'party_symbol'   => ($candidate->SYMBOL_DES)?$candidate->SYMBOL_DES:'Not Alloted',
            'href'           => url('eci/report/scrutiny/detail-by-nomination/'.base64_encode($candidate->nomination_id))
          ]; 
          $index++;
      }

    $data['total_record']       = $index;
    $data['results']            =  $results;
    $data['user_data']          = Auth::user();
    

    return view('admin.pc.eci.report.date_wise_report_name', $data);     
  }  


  public function downlaod_to_excel(Request $request){
    set_time_limit(6000);
    $data = $this->get_report($request->merge(['is_excel' => 1]));

    $headings[] = [$data['heading_title']];
    $export_data[] = ['Constituency Name', 'Total Nominations','Accepted Nominations','Rejected Nominations', 'Withdrawn Nominations','Validately Nominations','Contesting'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
            'label'              => $lis['label'],
            'total_applied'      => $lis['total_applied'],
            'total_accepted'     => $lis['total_accepted'],
            'total_rejected'     => $lis['total_rejected'],
            'total_withdraw'     => $lis['total_withdraw'],
            'total_validated'    => $lis['total_validated'],
            'total_contested'    => $lis['total_contested'],
      ];
    }

    $export_data[] = [
            'label'              => $data['totals']['label'],
            'total_applied'      => $data['totals']['total_applied'],
            'total_accepted'     => $data['totals']['total_accepted'],
            'total_rejected'     => $data['totals']['total_rejected'],
            'total_withdraw'     => $data['totals']['total_withdraw'],
            'total_validated'    => $data['totals']['total_validated'],
            'total_contested'    => $data['totals']['total_contested'],
    ];

    $name_excel = 'scrutiny_report_'.date('d-m-Y').'_'.time();
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx'); 


    // \Excel::create('scrutiny_report_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
          
    //       $sheet->mergeCells('A1:G1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });


    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function state_downlaod_to_excel(Request $request){
    set_time_limit(6000);
    $data = $this->get_report_by_state($request->merge(['is_excel' => 1]));

    $export_data = [];
    $headings[] = [$data['heading_title']];
    $export_data[] = ['Constituency Name', 'Total Nominations','Accepted Nominations','Rejected Nominations', 'Withdrawn Nominations','Validately Nominations','Contesting'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
            'label'              => $lis['label'],
            'total_applied'      => $lis['total_applied'],
            'total_accepted'     => $lis['total_accepted'],
            'total_rejected'     => $lis['total_rejected'],
            'total_withdraw'     => $lis['total_withdraw'],
            'total_validated'    => $lis['total_validated'],
            'total_contested'    => $lis['total_contested'],
      ];
    }

    $export_data[] = [
            'label'              => $data['totals']['label'],
            'total_applied'      => $data['totals']['total_applied'],
            'total_accepted'     => $data['totals']['total_accepted'],
            'total_rejected'     => $data['totals']['total_rejected'],
            'total_withdraw'     => $data['totals']['total_withdraw'],
            'total_validated'    => $data['totals']['total_validated'],
            'total_contested'    => $data['totals']['total_contested'],
    ];

$name_excel = 'scrutiny_report_'.date('d-m-Y').'_'.time();
return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');  

    // \Excel::create('scrutiny_report_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //     $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //       $sheet->mergeCells('A1:G1');
    //       $sheet->cell('A1', function($cell) {
    //         $cell->setAlignment('center');
    //         $cell->setFontWeight('bold');
    //       });
    //       $sheet->fromArray($export_data,null,'A1',false,false);
    //     });
    // })->export('xls');

  }

  public function state_wise_pdf(Request $request){
    $data = $this->get_report_by_state($request->merge(['is_excel' => 1]));
    $pdf = \PDF::loadView('admin.pc.eci.report.pdf',$data);
    return $pdf->download('scrutiny_report_'.date('d-m-Y').'_'.time().'.pdf');
  }

  public function constancy_wise_pdf(Request $request){
    $data = $this->get_report($request->merge(['is_excel' => 1]));
    $pdf = \PDF::loadView('admin.pc.eci.report.pdf',$data);
    return $pdf->download('scrutiny_report_'.date('d-m-Y').'_'.time().'.pdf');
  }


}  // end class