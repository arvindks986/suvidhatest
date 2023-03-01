<?php 
namespace App\Http\Controllers\Admin\BoothAppRevamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB, Validator, Config, Session;
use App\commonModel;  
use App\models\Admin\BoothAppRevamp\{PollingStation, PollingStationOfficerModel, TblPollSummaryModel, VoterInfoModel, VoterInfoPollStatusModel, TblBoothUserModel, StateModel, AcModel, DistrictModel, JsonFile, OfficerAssignmentModel, PollingStationLocationModel};
use App\Http\Requests\Admin\BoothAppRevamp\OfficerRequest;
use App\Classes\xssClean;
use App\Helpers\SmsgatewayHelper;
use App\Http\Controllers\Admin\Common\CommonBoothAppController as Common;
use PDF;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Admin\BoothAppRevamp\PollingController;
use App\models\Admin\PhaseModel;
//current

class NotActivatedOfficerController extends Controller {

  public $folder        = 'booth-app-revamp';
  public $view          = "admin.booth-app-revamp";
  public $action        = "booth-app-revamp";
  public $ac_no         = NULL;
  public $st_code       = NULL;
  public $ps_no         = NULL;
  public $role_id       = 0;
  public $filter        = NULL;
  public $base          = 'ro';
  public $action_state  = 'eci/booth-app-revamp/not-activated-officer';
  public $action_ac     = 'eci/booth-app-revamp/not-activated-officer/state/ac';
  public $view_path     = "admin.booth-app-revamp";
  public $request_param = '';

  public function __construct(Request $request){
    $this->commonModel  = new commonModel();
    $this->middleware(function ($request, $next) {
      $default_values = Common::get_request_filter($request);
      $this->ac_no    = $default_values['ac_no'];
      $this->st_code  = $default_values['st_code'];
      $this->ps_no    = $default_values['ps_no'];
      $this->phase_no    = $default_values['phase_no'];
      $this->dist_no  = $default_values['dist_no'];
      $this->role_id  = $default_values['role_id'];
      $this->base     = $default_values['base'];
      $this->filter = [
        'st_code'   => $this->st_code,
        'ac_no'     => $this->ac_no,
        'dist_no'   => $this->dist_no,
        'ps_no'     => $this->ps_no,
        'phase_no'  => $this->phase_no
      ];
      $this->request_param   = http_build_query($this->filter);
      return $next($request);
    });
  }


  public function officer_assignment_report(Request $request){
	

    $data = [];
    $filter = [
      'st_code'         => $this->st_code,
	  'phase_no'         =>$this->phase_no
    ];

    //SETTING VARIABLES FOR SENDING RESULTS ON NEXT PAGE STARTS
    $data = [];
    $request_array = [];
    if($this->phase_no){
      $request_array[]  =  'phase_no='.$this->phase_no;
    }
    if($this->st_code){
      $request_array[]  =  'st_code='.$this->st_code;
    }
    if($this->dist_no){
      $request_array[]  =  'dist_no='.$this->dist_no;
    }
    if($this->ac_no){
      $request_array[]  =  'ac_no='.$this->ac_no;
    }

    //SETTING TITILE OF THE PAGE 
    $title_array  = [];
    $data['heading_title']  = 'Booth App - Officer Assignment Report';

    //GET STATE NAME, DIST NAME AND AC NAMECODE STARTS
    if($this->st_code){
      $state_object = StateModel::get_state_by_code($this->st_code);
      if($state_object){
        $title_array[]  = "State: ".$state_object['ST_NAME'];
      }
    }
    if($this->dist_no){
      $dist_object = DistrictModel::get_district([
        'dist_no' => $this->dist_no,
        'st_code' => $this->st_code
      ]);
      if($dist_object){
        $title_array[]  = "District: ".$dist_object['dist_name'];
      }
    }
    if($this->ac_no){
      $ac_object = AcModel::get_ac([
        'ac_no' => $this->ac_no,
        'state' => $this->st_code
      ]);
      if($ac_object){
        $title_array[]  = "AC: ".$ac_object['ac_name'];
      }
    }
    //GET STATE NAME, DIST NAME AND AC NAME CODE ENDS

    $data['filter_buttons'] = $title_array;
    $data['filter']   = implode('&', array_merge($request_array));

    //SETTING BUTTONS FOR REPORTS STARTS
    $data['buttons']    = [];
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  Common::generate_url('booth-app-revamp/not-activated-officer/excel').'?'.implode('&', $request_array),
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  Common::generate_url('booth-app-revamp/not-activated-officer/pdf').'?'.implode('&', $request_array),
      'target' => true
    ];
    //SETTING BUTTONS FOR REPORTS ENDS

    $data['action']         = url($this->action_state);

    $results    = [];

    $states_results = StateModel::get_states($filter);
	

    $grand_ps   = 0;
    $grand_blo  = 0;
    $grand_pro  = 0;
    $grand_po   = 0;
    $grand_sm   = 0;
    $grand_location = 0;
    $grand_mapped_location = 0;
    $grand_not_activated_blo  = 0;
    $grand_not_activated_pro  = 0;
    $grand_not_activated_po   = 0;
    $grand_not_activated_sm     = 0;
    $grand_total_not_activated  = 0;
    $grand_total = 0;

	
    //STATE LOOP STARTS
    foreach ($states_results as $key => $state_result) {

      //STATE NAME
      if($state_result){
        $state_name = $state_result['name'];
      }

      $filter_election = [
        'phase_no' => $this->phase_no,
        'st_code' => $state_result->ST_CODE,
      ];

      $total_ps           = PollingStation::total_poll_station_count($filter_election);
      //$officer_count      = PollingStationOfficerModel::get_total_count($filter_election);
      $location_object    = PollingStationLocationModel::total_location_count($filter_election);

      $officer_count = PollingStationOfficerModel::total_officer_by_query($filter_election);
	  
	  

      $individual_filter_array          = [];
      $individual_filter_array['st_code'] = 'st_code='.$state_result->ST_CODE;
      $individual_filter                = implode('&', $individual_filter_array);

      $grand_ps   += $total_ps;
      $grand_blo  += $officer_count['total_blo'];
      $grand_pro  += $officer_count['total_pro'];
      $grand_po   += $officer_count['total_po'];
      $grand_sm   += $officer_count['total_sm'];
      $grand_location += $location_object['total'];
      $grand_mapped_location += $location_object['mapped'];

      $blo_not_activated = $officer_count['total_blo'] - $officer_count['blo_activated'];
      $pro_not_activated = $officer_count['total_pro'] - $officer_count['pro_activated'];
      $po_not_activated = $officer_count['total_po'] - $officer_count['po_activated'];
      $sm_not_activated = $officer_count['total_sm'] - $officer_count['sm_activated'];

      $total_not_activated = $po_not_activated;
      $total_activated = $officer_count['total_po'];
      $grand_total   += $total_activated;

      $grand_total_not_activated += $total_not_activated;
      $grand_not_activated_blo  += $blo_not_activated;
      $grand_not_activated_pro  += $pro_not_activated;
      $grand_not_activated_po   += $po_not_activated;
      $grand_not_activated_sm   += $sm_not_activated;

      $percentage = 0;
      if($total_not_activated > 0 &&  $total_activated >= $total_not_activated){
        $percentage = ROUND(($total_not_activated / $total_activated)*100,2);
      }


      //SETTING DATABASE RESULTS FOR 
      $results[] = [
        'label'           => $state_result['ST_NAME'],
        'total_ps'        => $total_ps,
        'total_location'  => $location_object['total'],
        'total_mapped'    => $location_object['mapped'],
        'total_pro'       => $officer_count['total_pro'],
        'total_po'        => $officer_count['total_po'],
        'total_blo'       => $officer_count['total_blo'],
        'total_sm'        => $officer_count['total_sm'],
        'blo_not_activated'   => $blo_not_activated,
        'pro_not_activated'   => $pro_not_activated,
        'po_not_activated'    => $po_not_activated,
        'sm_not_activated'    => $sm_not_activated,
        'total_not_activated' => $total_not_activated,
        'total_activated'     => $total_activated,
        'percentage'          => $percentage,
        'href'            => Common::generate_url('booth-app-revamp/not-activated-officer/ac')."?".$individual_filter
      ]; 
    }//STATE LOOP ENDS

    $percentage = 0;
    if($grand_total_not_activated > 0 && $grand_total >= $grand_total_not_activated){
      $percentage = ROUND(($grand_total_not_activated / $grand_total)*100,2);
    }

    $results[]    = [
      'label'           => 'Total',
      'total_ps'        	=> $grand_ps,
      'total_location'  	=> $grand_location,
      'total_mapped'    	=> $grand_mapped_location,
      'total_pro'       	=> $grand_pro,
      'total_po'        	=> $grand_po,
      'total_blo'       	=> $grand_blo,
      'total_sm'        	=> $grand_sm,
      'blo_not_activated'   => $grand_not_activated_blo,
      'pro_not_activated'   => $grand_not_activated_pro,
      'po_not_activated'    => $grand_not_activated_po,
      'sm_not_activated'    => $grand_not_activated_sm,
      'total_not_activated' => $grand_total_not_activated,
      'total_activated'     => $grand_total,
      'percentage'          => $percentage,
      'href'            => "javascript::void(0)"
    ];

    $data['results']    =   $results;

    $data['user_data']  =   Auth::user();
    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }


  //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/not-activated-officer");
    $form_filter_array = [
      'phase_no'     => true,
      'st_code'     => true,
      'dist_no'     => false,
      'ac_no'       => false, 
      'ps_no'       => false, 
      'designation'     => false,
    ];
    $form_filters   = Common::get_form_filters($form_filter_array, $request);      
    $data['form_filters'] = $form_filters;

    return view($this->view.'.not-activated-officer.state', $data);

  }


//not-activated-officer excel function starts
  public function officer_assignment_report_excel(Request $request){

    set_time_limit(6000);
    $data = $this->officer_assignment_report($request->merge(['is_excel' => 1]));
    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $headings[] = [
      'State/UT Name',
      'Total PS',
      'PO Assigned',
      'PO Not Activated',
      'Total Activated',
      'Total Not Activated',
      'Percentage'
    ];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['total_ps'],
        $lis['total_po'],
        $lis['po_not_activated'],
        $lis['total_activated'],
        $lis['total_not_activated'],
        $lis['percentage'],
      ];
    }
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //   $excel->sheet('Sheet1', function($sheet) use ($export_data) {
    //     $sheet->mergeCells('A1:I1');
    //     $sheet->cell('A1', function($cell) {
    //       $cell->setAlignment('center');
    //       $cell->setFontWeight('bold');
    //     });
    //     $sheet->fromArray($export_data,null,'A1',false,false);
    //   });
    // })->export('xls');

  }
//not-activated-officer excel function ends


//not-activated-officer pdf function ends
  public function officer_assignment_report_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->officer_assignment_report($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.not-activated-officer.state-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
//not-activated-officer pdf function ends

  public function officer_assignment_report_ac(Request $request){

    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "officer assignment report";
    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no,
    ];
  //buttons
    $data['buttons']    = [];
    $data['buttons'][]  = [
      'name' => 'Export Excel',
      'href' =>  Common::generate_url('booth-app-revamp/not-activated-officer/ac/excel').'?'.$this->request_param,
      'target' => true
    ];
    $data['buttons'][]  = [
      'name' => 'Export Pdf',
      'href' =>  Common::generate_url('booth-app-revamp/not-activated-officer/ac/pdf').'?'.$this->request_param,
      'target' => true
    ];
    if($this->role_id == '7' || $this->role_id == '4'){
      $back_href = Common::generate_url('booth-app-revamp/not-activated-officer').'?'.$this->request_param;
    }else{
      $back_href = Common::generate_url('booth-app-revamp/dashboard').'?'.$this->request_param;
    }
    $data['buttons'][] = [
      'href' => $back_href,
      'name' => 'Back',
      'target' => false,
    ];
    $data['results'] = [];
    $grand_ps   = 0;
    $grand_blo  = 0;
    $grand_pro  = 0;
    $grand_po   = 0;
    $grand_sm   = 0;
    $grand_location = 0;
    $grand_mapped_location = 0;
    $grand_not_activated_blo  = 0;
    $grand_not_activated_pro  = 0;
    $grand_not_activated_po   = 0;
    $grand_not_activated_sm   = 0;
    $grand_total_not_activated = 0;
    $grand_total = 0;

    $acs_results = AcModel::get_acs($filter);

    // echo "<pre>";print_r($filter);die;

    foreach ($acs_results as $key => $iterate_ac) {
      $filter_iterate = [
        'phase_no'  => $this->phase_no,
        'st_code'   => $iterate_ac['st_code'],
        'ac_no'     => $iterate_ac['ac_no'],
      ];
      $st_name = '';
      $state_object = StateModel::get_state_by_code($iterate_ac['st_code']);
      if($state_object){
        $st_name  = $state_object['ST_NAME'];
      }
      $ac_name = '';
      $ac_object = AcModel::get_ac(['state' => $iterate_ac['st_code'], 'ac_no' => $iterate_ac['ac_no']]);
      if($ac_object){
        $ac_name  = $ac_object['ac_name'];
      }

      $total_ps           = PollingStation::total_poll_station_count($filter_iterate);
      //$officer_count      = PollingStationOfficerModel::get_total_count($filter_iterate);

      $officer_count = PollingStationOfficerModel::total_officer_by_query($filter_iterate);
      $location_object    = PollingStationLocationModel::total_location_count($filter_iterate);

      $individual_filter_array            = [];
      $individual_filter_array['st_code'] = 'st_code='.$iterate_ac['st_code'];
      $individual_filter_array['ac_no']   = 'ac_no='.$iterate_ac['ac_no'];
      $individual_filter                  = implode('&', $individual_filter_array);

      $grand_ps   += $total_ps;
      $grand_blo  += $officer_count['total_blo'];
      $grand_pro  += $officer_count['total_pro'];
      $grand_po   += $officer_count['total_po'];
      $grand_sm   += $officer_count['total_sm'];
      $grand_location += $location_object['total'];
      $grand_mapped_location += $location_object['mapped'];


      $blo_not_activated = $officer_count['total_blo'] - $officer_count['blo_activated'];
      $pro_not_activated = $officer_count['total_pro'] - $officer_count['pro_activated'];
      $po_not_activated = $officer_count['total_po'] - $officer_count['po_activated'];
      $sm_not_activated = $officer_count['total_sm'] - $officer_count['sm_activated'];
      $total_not_activated = $po_not_activated;
      $total_activated = $officer_count['total_po'];
      $grand_total   += $total_activated;

      $grand_total_not_activated += $total_not_activated;
      $grand_not_activated_blo  += $blo_not_activated;
      $grand_not_activated_pro  += $pro_not_activated;
      $grand_not_activated_po   += $po_not_activated;
      $grand_not_activated_sm   += $sm_not_activated;

      $percentage = 0;
      if($total_not_activated > 0 &&  $total_activated >= $total_not_activated){
        $percentage = ROUND(($total_not_activated / $total_activated)*100,2);
      }

      $data['results'][] = [
        'label'           => $st_name,
        'ac_name'         => $iterate_ac['ac_no']."-".$ac_name,
        'total_ps'        => $total_ps,
        'total_location'  => $location_object['total'],
        'total_mapped'    => $location_object['mapped'],
        'total_pro'       => $officer_count['total_pro'],
        'total_po'        => $officer_count['total_po'],
        'total_blo'       => $officer_count['total_blo'],
        'total_sm'        => $officer_count['total_sm'],
        'blo_not_activated'   => $blo_not_activated,
        'pro_not_activated'   => $pro_not_activated,
        'po_not_activated'    => $po_not_activated,
        'sm_not_activated'    => $sm_not_activated,
        'total_not_activated' => $total_not_activated,
        'total_activated'     => $total_activated,
        'percentage'          => $percentage,
        'href'            => Common::generate_url("booth-app-revamp/officers")."?".$individual_filter//Common::generate_url('booth-app-revamp/not-activated-officer/ac/ps')."?".$individual_filter
      ];  
    }

    $percentage = 0;
    if($grand_total_not_activated > 0 && $grand_total >= $grand_total_not_activated){
      $percentage = ROUND(($grand_total_not_activated / $grand_total)*100,2);
    }


    $data['results'][]    = [
      'label'           => 'Total',
      'ac_name'           => '',
      'total_ps'        => $grand_ps,
      'total_location'  => $grand_location,
      'total_mapped'    => $grand_mapped_location,
      'total_pro'       => $grand_pro,
      'total_po'        => $grand_po,
      'total_blo'       => $grand_blo,
      'total_sm'        => $grand_sm,
      'blo_not_activated'   => $grand_not_activated_blo,
      'pro_not_activated'   => $grand_not_activated_pro,
      'po_not_activated'    => $grand_not_activated_po,
      'sm_not_activated'    => $grand_not_activated_sm,
      'total_not_activated' => $grand_total_not_activated,
      'total_activated'     => $grand_total,
      'percentage'          => $percentage,
      'href'              => "javascript::void(0)"
    ];
    $data['user_data']      = Auth::user();
    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }
  //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/not-activated-officer/ac");
    $form_filter_array = [
      'st_code'     => true,
      'dist_no'     => false,
      'ac_no'       => true, 
      'ps_no'       => false, 
      'designation'     => false,
    ];
    $form_filters = Common::get_form_filters($form_filter_array, $request);      
    $data['form_filters'] = $form_filters;

    // echo "<pre>";print_r($form_filters);die;
    return view($this->view.'.not-activated-officer.ac', $data);


  }
//not-activated-officer-ac function starts

//not-activated-officer-ac excel function starts
  public function officer_assignment_report_ac_excel(Request $request){

    set_time_limit(6000);
    $data = $this->officer_assignment_report_ac($request->merge(['is_excel' => 1]));
    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $headings[] = [
      'State/UT Name',
      'AC No & Name',
      'Total PS',
      'PO Assigned',
      'PO Not Activated',
     
    ];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['ac_name'],
       
        $lis['total_ps'],
        
        $lis['total_po'],
        $lis['po_not_activated'],
        
      ];
    }
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');

    
    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //   $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //     $sheet->mergeCells('A1:I1');
    //     $sheet->cell('A1', function($cell) {
    //       $cell->setAlignment('center');
    //       $cell->setFontWeight('bold');
    //     });
    //     $sheet->fromArray($export_data,null,'A1',false,false);
    //   });
    // })->export('xls');

  }
//not-activated-officer-ac excel function ends


  //not-activated-officer-ac pdf function ends
  public function officer_assignment_report_ac_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->officer_assignment_report_ac($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.not-activated-officer.ac-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //not-activated-officer-ac pdf function ends

  public function officer_assignment_report_ps(Request $request){
	
    $data                   = [];
    $data['buttons']        = [];
    $data['role_id']        = $this->role_id;
    $data['heading_title']  = "officer assignment report";

    $filter = [
      'phase_no'        => $this->phase_no,
      'st_code'         => $this->st_code,
      'ac_no'           => $this->ac_no,
      'ps_no'           => $this->ps_no
    ];

    //buttons
    $data['buttons']    = [];
    // $data['buttons'][]  = [
    //   'name' => 'Export Excel',
    //   'href' =>  Common::generate_url('booth-app-revamp/not-activated-officer/ac/ps/excel').'?'.$this->request_param,
    //   'target' => true
    // ];
    // $data['buttons'][]  = [
    //   'name' => 'Export Pdf',
    //   'href' =>  Common::generate_url('booth-app-revamp/not-activated-officer/ac/ps/pdf').'?'.$this->request_param,
    //   'target' => true
    // ];
    if($this->role_id == '7' || $this->role_id == '4' || $this->role_id == '20'){
      $back_href = Common::generate_url('booth-app-revamp/not-activated-officer/ac').'?st_code='.$this->st_code.'&ac_no='.$this->ac_no;
    }else{
      $back_href = Common::generate_url('booth-app-revamp/dashboard').'?st_code='.$this->st_code;
    }
    $data['buttons'][]    = [
      'href' => $back_href,
      'name' => 'Back',
      'target' => false,
    ];

// echo "<pre>";print_r($request->all());die;

    $grand_ps   = 0;
    $grand_blo  = 0;
    $grand_pro  = 0;
    $grand_po   = 0;
    $grand_sm   = 0;

    // $polling_stations = PollingStation::get_polling_stations($filter);

    // echo "<pre>";print_r($request->all());die;


    $polling_stations_data = PollingStation::where('ST_CODE','S01');
    if(Auth::User()->role_id==20)
    {
      if($request->has('ps_no'))
      {
        $polling_stations_data->where('ps_no',$request->ps_no);
      }

        $polling_stations_data->where('AC_NO',Auth::User()->ac_no);
    }
    if(Auth::User()->role_id==18)
    {
      if($request->ac_no!=0)
      {
        $polling_stations_data->where('AC_NO',$request->ac_no);
      }
      if($request->has('ps_no'))
      {
        $polling_stations_data->where('ps_no',$request->ps_no);
      }

      $polling_stations_data->where('pc_no',Auth::User()->pc_no);
    }
    
    $polling_stations_data->orderBy('PART_NO', 'ASC');
    $polling_stations=$polling_stations_data->get()->toArray();
    

    


    $data['results']=[];
    foreach ($polling_stations as $key => $iterate_p_s) {
      $filter_iterate = array_merge($filter,['ps_no' => $iterate_p_s['PS_NO']]);
      $st_name = '';
      $state_object = StateModel::get_state_by_code($iterate_p_s['ST_CODE']);
      if($state_object){
        $st_name  = $state_object['ST_NAME'];
      }
      $ac_name = '';
      $ac_object = AcModel::get_ac(['state' => $iterate_p_s['ST_CODE'], 'ac_no' => $iterate_p_s['AC_NO']]);
      if($ac_object){
        $ac_name  = $ac_object['ac_name'];
      }
      $poll_station_name = $iterate_p_s['PS_NAME_EN'];

      $individual_filter_array            = [];
      $individual_filter_array['st_code'] = 'st_code='.$iterate_p_s['ST_CODE'];
      $individual_filter_array['ac_no']   = 'ac_no='.$iterate_p_s['AC_NO'];
      $individual_filter_array['ps_no']   = 'ps_no='.$iterate_p_s['PS_NO'];
      $individual_filter                  = implode('&', $individual_filter_array);

     

      $officer_count      = PollingStationOfficerModel::get_total_count($filter_iterate);
     
      // echo "<pre>";print_r($officer_count);

      $data['results'][] = [
        'label'           => $st_name,
        'ac_name'         => $iterate_p_s['AC_NO']."-".$ac_name,
        'ps_name'         => $iterate_p_s['PS_NO'].'-'.$poll_station_name,
        'total_pro'       => ($officer_count['total_pro'])?"Yes":"No",
        'total_po'        => ($officer_count['total_po'])?"Yes":"No",
        'total_blo'       => ($officer_count['total_blo'])?"Yes":"No",
        'total_sm'        => ($officer_count['total_sm'])?"Yes":"No",
        'blo_not_activated'   => ($officer_count['blo_not_activated'])?"Yes":"No",
        'pro_not_activated'   => ($officer_count['pro_not_activated'])?"Yes":"No",
        'po_not_activated'    => ($officer_count['po_not_activated'])?"Yes":"No",
        'sm_not_activated'    => ($officer_count['sm_not_activated'])?"Yes":"No",
        'href'            => Common::generate_url("booth-app-revamp/officers")."?".$individual_filter
      ];  
    }

  // die;


    $data['user_data']    = Auth::user();

    if($request->has('is_excel')){
      if(isset($title_array) && count($title_array)>0){
        $data['heading_title'] .= "- ".implode(', ', $title_array);
      }
      return $data;
    }

    //form filters
    $data['filter_action'] = Common::generate_url("booth-app-revamp/not-activated-officer/ac/ps");
    $form_filter_array = [
      'st_code'     => true,
      'dist_no'     => false,
      'ac_no'       => true, 
      'ps_no'       => true, 
      'designation' => false,
    ];
    $form_filters         = Common::get_form_filters($form_filter_array, $request);      
    $data['form_filters'] = $form_filters;
	
    // echo "<pre>";print_r($data);die;
    return view($this->view.'.not-activated-officer.ps', $data);
  }

  //not-activated-officer-ac excel function starts
  public function officer_assignment_report_ps_excel(Request $request){

    set_time_limit(6000);
    $data = $this->officer_assignment_report_ps($request->merge(['is_excel' => 1]));
    $export_data = [];
    $export_data[] = [$data['heading_title']];
    $headings[] = ['State/UT Name','AC No & Name','PS No & Name','PO Assigned'];
    foreach ($data['results'] as $lis) {
      $export_data[] = [
        $lis['label'],
        $lis['ac_name'],
        $lis['ps_name'],
        $lis['total_po'],
       
      ];
    }
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));

    return Excel::download(new ExcelExport($headings, $export_data), $name_excel.'_'.date('d-m-Y').'_'.time().'.xlsx');


    // \Excel::create($name_excel.'_'.date('d-m-Y').'_'.time(), function($excel) use($export_data) {
    //   $excel->sheet('Sheet1', function($sheet) use($export_data) {
    //     $sheet->mergeCells('A1:I1');
    //     $sheet->cell('A1', function($cell) {
    //       $cell->setAlignment('center');
    //       $cell->setFontWeight('bold');
    //     });
    //     $sheet->fromArray($export_data,null,'A1',false,false);
    //   });
    // })->export('xls');

  }
//not-activated-officer-ac excel function ends


  //not-activated-officer-ac pdf function ends
  public function officer_assignment_report_ps_pdf(Request $request){
    set_time_limit(6000);
    $data = $this->officer_assignment_report_ps($request->merge(['is_excel' => 1]));
    $name_excel = strtolower(str_replace([',',': ',' '], ['_','-','_'], $data['heading_title']));
    $pdf = \PDF::loadView($this->view_path.'.not-activated-officer.ps-pdf',$data);
    return $pdf->download($name_excel.'_'.date('d-m-Y').'_'.time().'.pdf');
  }
  //not-activated-officer-ac pdf function ends


}  // end class