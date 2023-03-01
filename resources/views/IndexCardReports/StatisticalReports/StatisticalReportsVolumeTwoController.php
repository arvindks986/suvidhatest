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

class StatisticalReportsVolumeTwoController extends Controller
{
	public function __construct(){
       $this->middleware('adminsession');
       $this->middleware(['auth:admin','auth']);
       $this->middleware('ceo');
       $this->commonModel = new commonModel();
       $this->ceomodel = new CEOPCModel();
            $this->pcceoreportModel = new PCCeoReportModel();
       $this->xssClean = new xssClean;
   }
   
    public function getListofPoliticalPartiesParticipated(Request $request)
    {
    return view('StatisticalReports.Vol2.listof-political-parties-participated');
	}

    public function getListofPoliticalPartiesParticipatedPDF(Request $request)
    {
        $pdf=PDF::loadView('StatisticalReports.Vol2.listof-political-parties-participated-pdf');
        return $pdf->download('listof-political-parties-participated-report.pdf');
    }
	 public function getPartyDetailsReportVol2(Request $request)
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
			
		$data = DB::table('m_party')
		->select('m_party.PARTYTYPE','m_party.PARTYABBRE','m_symbol.SYMBOL_DES','m_party.PARTYNAME')
		->join('m_symbol','m_party.PARTYSYM', '=', 'm_symbol.SYMBOL_NO')	
		->orderBy('m_party.PARTYTYPE', 'ASC')
		->orderBy('m_party.PARTYNAME', 'ASC')
		->get();
		
		$queue = DB::getQueryLog();
		//echo '<pre>';print_r($queue);die;
				
		$partyDetailData = array();
		foreach($data as $key){
			$partyDetailData[$key->PARTYTYPE][] = array(
				'PARTYABBRE' 	=> $key->PARTYABBRE,
				'SYMBOL_DES' 	=> $key->SYMBOL_DES,
				'PARTYNAME' 	=> $key->PARTYNAME
			);
		}
		  if($request->path() == 'pcceo/PartyDetailsReportVol2'){
                return view('IndexCardReports.StatisticalReports.Vol2.party-details-report-vol2',compact('partyDetailData','user_data','sched'));
            }elseif($request->path() == 'pcceo/PartyDetailsReportPDFVol2'){
              $pdf=PDF::loadView('IndexCardReports.StatisticalReports.Vol2.party-details-report-pdf-vol2',[            
              'partyDetailData'=>$partyDetailData]);
              return $pdf->download('party-details-report.pdf');
		        }elseif($request->path() == 'pcceo/PartyDetailsReportXlsVol2'){
              $data = json_decode( json_encode($partyDetailData), true);
        
          
               return Excel::create('partyDetailsData', function($excel) use ($data) {
                  $excel->sheet('mySheet', function($sheet) use ($data)
                  {
                      //$sheet->fromArray($data);
              
              $sheet->cell('A1', function($cells) {
                $cells->setValue('LIST OF POLITICAL PARTIES PARTICIPATED');
              });
              
              $sheet->cells('A1:D1', function($cells) {
                $cells->setFont(array(
                  'size'       => '15',
                  'bold'       => true
                ));
              });
              
              $sheet->mergeCells('A1:D1');
              
              $sheet->cell('A2', function($cells) {
                $cells->setValue('S.No.');
              });

              $sheet->cell('B2', function($cells) {
                $cells->setValue('Abbreviation');
              });
              
              $sheet->cell('C2', function($cells) {
                $cells->setValue('Party Symbol');
              });
              
              $sheet->cell('D2', function($cells) {
                $cells->setValue('Party');
              });
              
                $sn = 1;
              
                $i= 3;
              
              
              if (!empty($data)) {
                foreach ($data as $key => $row){                            
                  if($key=='N'){  
                    $sheet->cell('A'.$i, 'Party Type');           
                    $sheet->mergeCells("A$i:B$i");          
                    $sheet->cell('C'.$i, 'National Parties'); 
                    $sheet->mergeCells("C$i:D$i");              
                  }else if($key=='S'){
                    $sheet->cell('A'.$i, 'Party Type');           
                    $sheet->mergeCells("A$i:B$i");            
                    $sheet->cell('C'.$i, 'State Parties'); 
                    $sheet->mergeCells("C$i:D$i");            
                  }else if($key=='U'){
                    $sheet->cell('A'.$i, 'Party Type');           
                    $sheet->mergeCells("A$i:B$i");            
                    $sheet->cell('C'.$i, 'Registered(unrecognised) Parties'); 
                    $sheet->mergeCells("C$i:D$i");              
                  }else if($key=='Z1'){
                    $sheet->cell('A'.$i, 'Party Type');           
                    $sheet->mergeCells("A$i:B$i");            
                    $sheet->cell('C'.$i, 'Nota'); 
                    $sheet->mergeCells("C$i:D$i");                          
                  }
                            

                $sheet->cells("A$i:D$i", function($cells) {
                      $cells->setFont(array(
                        'bold'       => true
                      ));
                    });
                $i++;
                    
                  foreach ($row as $keys => $rowData){          
                    $sheet->cell('A'.$i, $sn); 
                    $sheet->cell('B'.$i, $rowData['PARTYABBRE']); 
                    $sheet->cell('C'.$i, $rowData['SYMBOL_DES']); 
                    $sheet->cell('D'.$i, $rowData['PARTYNAME']); 
                    
                    $sn++;
                    $i++;
                  }
                }       
              
                    }

                });
             })->download('xls');
           }
      
	}
}
