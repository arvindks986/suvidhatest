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
    use App\models\Admin\ElectionModel;
    use Validator;
    use Config;
    use \PDF;
    use App\commonModel;  
    use App\adminmodel\ECIModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\PCCountingModel; 
    use Maatwebsite\Excel\Facades\Excel;
    use App\Exports\ExcelExport;

class EciController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){  
        $this->middleware(['auth:admin','auth']);
        // $this->middleware('eci');
        $this->commonModel = new commonModel();
        $this->ECIModel = new ECIModel();
        $this->CountingModel = new PCCountingModel();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
    */

    protected function guard(){
        return Auth::guard();
    }

   
  public function dashboard(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $filter_election = [
        'group_by'  => 'state',
        'order_by'  => 'state'
      ];

      $object   = ElectionModel::state_schedule($filter_election);
      $results = [];
      foreach ($object as $result) {
        //START NOMINATION DATE DIFF
        $start_nomi_class   = ElectionModel::date_diff($result['start_nomi_date']);
        //LAST NOMINATION DATE DIFF
        $last_nomi_class   = ElectionModel::date_diff($result['last_nomi_date']);
        //SCRUTINY DATE DIFF
        $scr_date_class   = ElectionModel::date_diff($result['dt_nomi_scr']);
        //LAST WIDRAWL DATE DIFF
        $wid_date_class   = ElectionModel::date_diff($result['last_wid_date']);
        //POLL DATE DIFF
        $poll_date_class   = ElectionModel::date_diff($result['poll_date']);
        //COUNT DATE DIFF
        $count_date_class   = ElectionModel::date_diff($result['count_date']);
        //COMPLETE DATE DIFF
        $comp_date_class   = ElectionModel::date_diff($result['complete_date']);
        $results[] = [
          'label'                    => $result['state'],
          'st_code'                  => $result['st_code'],
          'sid'                      => $result['sid'],
          'acs'                      => $result['acs'],
          'start_nomi_class'         => $start_nomi_class,
          'start_nomi_date'          => $result['start_nomi_date'],
          'last_nomi_class'          => $last_nomi_class,
          'last_nomi_date'           => $result['last_nomi_date'],
          'nomi_scr_class'           => $scr_date_class,
          'dt_nomi_scr'              => $result['dt_nomi_scr'],
          'last_wid_class'           => $wid_date_class,
          'last_wid_date'            => $result['last_wid_date'],
          'poll_date_class'          => $poll_date_class,
          'poll_date'                => $result['poll_date'],
          'count_date_class'         => $count_date_class,
          'count_date'               => $result['count_date'],
          'complete_date_class'      => $comp_date_class,
          'complete_date'            => $result['complete_date']
        ];
      }
      return view('admin.pc.eci.dashboard', ['user_data' => $d, 'results' => $results]);
    } else {
      return redirect('/officer-login');
    }
  }   // end dashboard function

     public function generateofficersloginname()
        {  
          $list_state=getallstate();
              $password="demo@1234";
         foreach($list_state as $st)
                  {
                   $ndata = array('st_code'=>$st->ST_CODE,
                                        'ac_no'=>'0',
                                        'pc_no'=>'0',
                                        'dist_no'=>'0',
                                        'password'=> bcrypt($password),
                                        'officername'=>'CEO'.$st->ST_CODE,
                                        'designation'=>'CEO',
                                        'placename'=>$st->ST_NAME,
                                        'role_id'=>'4',
                                        'officerlevel'=>'CEO',
                                        'added_at'=>date('Y-m-d'),
                                        'created_at'=>date('Y-m-d h:i:s'),
                                        );
                            $check=$schedule = DB::table('officer_login')->where('st_code',$st->ST_CODE)->first();
                            if(!isset($check))
                              $this->commonModel->insertData('officer_login',$ndata); 
                  }
          foreach($list_state as $st)
                  {   
                    $listalldist=getalldistrictbystate($st->ST_CODE);
                    foreach($listalldist as $dist)
                        {   set_time_limit(0);
                            $v = sprintf( '%02d', $dist->DIST_NO);
                             
                         $disdata = array('st_code'=>$st->ST_CODE,
                                              'ac_no'=>'0',
                                              'pc_no'=>'0',
                                              'dist_no'=>$dist->DIST_NO,
                                              'password'=> bcrypt($password),
                                              'officername'=>'DEO'.$st->ST_CODE."D".$v,
                                              'designation'=>'DEO',
                                              'placename'=>$dist->DIST_NAME,
                                              'role_id'=>'5',
                                              'officerlevel'=>'DEO',
                                              'added_at'=>date('Y-m-d'),
                                              'created_at'=>date('Y-m-d h:i:s'),
                                              );
                         
                            $check1=DB::table('officer_login')->where('st_code',$st->ST_CODE)->where('dist_no',$dist->DIST_NO)->first();
                            if(!isset($check1)){
                              $this->commonModel->insertData('officer_login',$disdata); 
                            }
                        
                         
                  }
                }
           foreach($list_state as $st)
                  {  set_time_limit(0);
                     
                    $listallpc=getpcbystate($st->ST_CODE);
                    
                    foreach($listallpc as $pc)
                        { set_time_limit(0);
                           
                          $v1 = sprintf( '%02d', $pc->PC_NO);
                          $dist=statepcwisedist($st->ST_CODE,$pc->PC_NO);
                          
                         $pcdata = array('st_code'=>$st->ST_CODE,
                                              'ac_no'=>'0',
                                              'pc_no'=>$pc->PC_NO,
                                              'dist_no'=>$dist->DIST_NO,
                                              'password'=> bcrypt($password),
                                              'officername'=>'ROPC'.$st->ST_CODE."P".$v1,
                                              'designation'=>'ROPC',
                                              'placename'=>$pc->PC_NAME,
                                              'role_id'=>'18',
                                              'officerlevel'=>'PC',
                                              'added_at'=>date('Y-m-d'),
                                              'created_at'=>date('Y-m-d h:i:s'),
                                              );
                         
                          
                        $check2=DB::table('officer_login')->where('st_code',$st->ST_CODE)->where('dist_no',$dist->DIST_NO)->where('pc_no',$dist->PC_NO)->first();
                            if(!isset($check2)){
                              $this->commonModel->insertData('officer_login',$pcdata); 
                            }
                       
                  }
                }
             foreach($list_state as $st)
                  {  set_time_limit(0);
                     
                    $listallac=getacbystate($st->ST_CODE);
                    foreach($listallac as $ac)
                        { set_time_limit(0);
                           
                          $v2 = sprintf( '%03d', $ac->AC_NO);  //
                          //$dist=statepcwisedist($st->ST_CODE,$ac->PC_NO);
                          
                         $acdata = array('st_code'=>$st->ST_CODE,
                                              'ac_no'=>$ac->AC_NO,
                                              'pc_no'=>$ac->PC_NO,
                                              'dist_no'=>$ac->DIST_NO_HDQTR,
                                              'password'=> bcrypt($password),
                                              'officername'=>'ARO'.$st->ST_CODE."A".$v2,
                                              'designation'=>'ARO',
                                              'placename'=>$ac->AC_NAME,
                                              'role_id'=>'20',
                                              'officerlevel'=>'AC',
                                              'added_at'=>date('Y-m-d'),
                                              'created_at'=>date('Y-m-d h:i:s'),
                                              );
                           
                         $check3= DB::table('officer_login')->where('st_code',$st->ST_CODE)->where('dist_no',$ac->DIST_NO_HDQTR)->where('pc_no',$ac->PC_NO)->where('ac_no',$ac->AC_NO)->first();
                            if(!isset($check3)){
                              $this->commonModel->insertData('officer_login',$acdata); 
                            }
                         
                         
                  }
                }
        }
  public function sendnominationmessage()
            {
             $nom_details =DB::table('candidate_personal_detail')->where('cand_mobile','<>','')->get();
             foreach($nom_details as $nom)
                        { set_time_limit(0);

                          if($nom->cand_mobile!='') {
                            $mob_message="Now you can check your nomination/ permission status through suvidha candidate android app. Download from here https://goo.gl/YGoMmM and login using this mobile number.";
                            echo count($mob_message)."<br>";
                            $response = SmsgatewayHelper::gupshup($nom->cand_mobile,$mob_message);
                            //echo $nom->candidate_id."=".$mob_message;
                          }   
                        }
            }
  public function updatesymbole()
          {
           $nomdetails = DB::table('candidate_nomination_detail')->where('cand_party_type','=','S')->get();
            //```
          foreach( $nomdetails as $nom){
                      $partyDetails = DB::table('m_party')
                          ->leftjoin('d_party', 'm_party.PARTYABBRE', '=', 'd_party.PARTYABBRE') 
                          ->where('m_party.PARTYTYPE','=','S')
                          ->where('d_party.ST_CODE','=',$nom->st_code)
                          ->where('m_party.CCODE','=',$nom->party_id)
                          ->select('m_party.*')->first();
                          if(isset($partyDetails)){
                                $partytype = $partyDetails->PARTYTYPE;
                               }
                               else{
                                   $partytype ='U';
                               }
                    $can = array('cand_party_type'=> $partytype);
                    $n = DB::table('candidate_nomination_detail')->where('nom_id', $nom->nom_id)->update($can);
                }
          }

   public function electrosdataupdate()
          {  
          $ndata = DB::table('electors_cdacnew')->get();
     
          foreach( $ndata as $n){
                      
                    $c = array('electors_male'=> $n->electors_male,'electors_female'=> $n->electors_female,'electors_other'=> $n->electors_other,'electors_total'=> $n->electors_total);
                   
                    $k = DB::table('electors_cdac')->where('st_code', $n->st_code)->where('ac_no', $n->ac_no)->where('year','2019')->where('scheduledid','3')->update($c);
                     $c1 = array('electors_total'=> $n->electors_total);
                     
                    $k1 = DB::table('pd_scheduledetail')->where('st_code', $n->st_code)->where('ac_no', $n->ac_no)->where('scheduleid','3')->update($c1);
                }
          }
  function generate_counting_data()
        {
        $list_state=getallstate();
         
        foreach($list_state as $st)
          {  set_time_limit(0);
                     
                    $listallpc=getpcbystate($st->ST_CODE);
                    
        foreach($listallpc as $pc)
            { 
               set_time_limit(0);
                 $new_table=strtolower("counting_master_".$st->ST_CODE);
                  $date = Carbon::now();
                  $currentTime = $date->format('Y-m-d H:i:s');
                  $currentdate = $date->format('Y-m-d');
                  $ele_details=getelectiondetailbystcode($st->ST_CODE,$pc->PC_NO,"PC");
                  if(!isset($ele_details))  continue;
                  $record=$this->CountingModel->getallacbypcno($st->ST_CODE,$pc->PC_NO);
                  $cand_data=$this->CountingModel->cantestesting_nomination($st->ST_CODE,$pc->PC_NO,$ele_details->ELECTION_ID);
           
       
          DB::beginTransaction();
            try{       
             foreach($cand_data as $list){
              $check = DB::table('counting_pcmaster')->where('nom_id',$list->nom_id)->where('st_code',$list->st_code)
                  ->where('pc_no',$list->pc_no)->where('election_id',$list->election_id)->first();
              
              if(!isset($check)){
                      $can=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$list->candidate_id);
                      $p=getpartybyid($list->party_id);
                      $ca_data = array('nom_id'=>$list->nom_id,'candidate_id'=>$list->candidate_id,'st_code'=>$list->st_code,'pc_no'=>$list->pc_no,'election_id'=>$list->election_id,'created_at'=>date("Y-m-d h:m:s"),'created_by'=>'ECI','candidate_name'=>$can->cand_name,'candidate_hname'=>$can->cand_hname,'party_id'=>$list->party_id,'party_abbre'=>$p->PARTYABBRE,'party_habbre'=>$p->PARTYHABBR,'party_name'=>$p->PARTYNAME,'party_hname'=>$p->PARTYHNAME,'added_create_at'=>$currentdate,'created_by'=>'ECI'); 
                      
                      $this->commonModel->insertData('counting_pcmaster', $ca_data);
                      }
                }        
           
         foreach($record as $r)
           {   
           foreach($cand_data as $list){
              $check = DB::table($new_table)->where('nom_id',$list->nom_id)->where('ac_no',$r->AC_NO)->where('pc_no',$list->pc_no)->where('election_id',$list->election_id)->first();
              
              if(!isset($check)){
                $can=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$list->candidate_id);
                $p=getpartybyid($list->party_id);
                               $ca_data = array('nom_id'=>$list->nom_id,'candidate_id'=>$list->candidate_id,'ac_no'=>$r->AC_NO,'pc_no'=>$list->pc_no,'election_id'=>$list->election_id,'created_at'=>$currentTime,'created_by'=>'ECI','added_create_at'=>$currentdate,'candidate_name'=>$can->cand_name,'party_id'=>$list->party_id,'party_abbre'=>$p->PARTYABBRE,'party_name'=>$p->PARTYNAME,'candidate_hname'=>$can->cand_hname,'party_habbre'=>$p->PARTYHABBR,'party_hname'=>$p->PARTYHNAME,'month'=>date("m"),'year'=>date("Y")); 
                               $this->commonModel->insertData($new_table, $ca_data);
                                }
                   } 
           }
        $lis_st=$this->commonModel->getstatebystatecode($ele_details->ST_CODE);
            $lis_pc=$this->commonModel->getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);

        $check_d=DB::table('winning_leading_candidate')->where('st_code',$ele_details->ST_CODE)->where('pc_no',$ele_details->CONST_NO)->where('election_id',$ele_details->ELECTION_ID)->first();   
        if(!isset($check_d)){   
         $winn_data=array('election_id'=>$ele_details->ELECTION_ID,'constituency_type'=>$ele_details->CONST_TYPE,'st_code'=>$ele_details->ST_CODE,
                          'st_name'=>$lis_st->ST_NAME,'st_hname'=>$lis_st->ST_NAME_HI,'pc_no'=>$ele_details->CONST_NO,'pc_name'=>$lis_pc->PC_NAME,
                          'pc_hname'=>$lis_pc->PC_NAME_HI,'created_at'=>date("Y-m-d h:m:s"),'added_create_at'=>$currentdate);
                      
                      $this->commonModel->insertData('winning_leading_candidate', $winn_data);
              }    
             }
            catch(\Exception $e){
                DB::rollback();
        
                \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
                return Redirect::back();
            }
            DB::commit();

 
         
          }
        }
      
        
        } 

function calculate_totalvotes()
        {
            $list_state=getallstate();
         
          foreach($list_state as $st)
            {  
                  set_time_limit(0);
                 
               $new_table=strtolower("counting_master_".$st->ST_CODE);       
              $get_all_records=DB::table($new_table)->orderBy('id', 'ASC')->get();
              if(!$get_all_records->isEmpty()) {
                foreach( $get_all_records as $record ) { 
                      
                   $filter = ['id'=>$record->id,'nom_id'=>$record->nom_id,'ac_no'=> $record->ac_no,'pc_no'=> $record->pc_no];
                 
                  $total_value='';
                  $total_value=grandtotalsum($new_table,$filter);
                 
                  $total_vote=$total_value->grant_total;
                  $evmupdate = array('total_vote'=>$total_vote); 
                      DB::table($new_table)->where('id',$record->id)->update($evmupdate); 


              } // end foreach

             } // end if

              $get_all_pcrecords=DB::table('counting_pcmaster')->orderBy('id', 'ASC')->get();
              
               if(!$get_all_pcrecords->isEmpty()) {
                foreach( $get_all_pcrecords as $record1) { 
                      $res = DB::table($new_table)->select([DB::raw('nom_id'),DB::raw('SUM(total_vote) AS sum_total')])  
                                  ->where('nom_id',$record1->nom_id)->where('pc_no',$record1->pc_no)->groupBy('nom_id')->first(); 
                      print_r( $res);
                      echo "<br><br>ss";
                       $net = $res->sum_total+$postaldata->postal_vote+$postaldata->migrate_votes;
                       $w_data=array('evm_vote'=>$res->sum_total,'total_vote'=>$net);
                       DB::table('counting_pcmaster')->where('st_code',$st->ST_CODE)->where('pc_no',$record1->pc_no)->where('nom_id',$record1->nom_id)->update($w_data);
                   }
                }
           
          }
        
        
        } 


  function winning_leading()
        {
             
             $get_all_records=DB::table('winning_leading_candidate')->orderBy('leading_id', 'ASC')->get();

             if(!$get_all_records->isEmpty()) {
                foreach( $get_all_records as $record ) { 

               $fdata=$this->CountingModel->selectfirsthightvalueofcounting('counting_pcmaster',$record->st_code,$record->pc_no,'PC',$record->election_id);
               $sdata=$this->CountingModel->selectsecondhightvalueofcounting('counting_pcmaster',$record->st_code,$record->pc_no,'PC',$record->election_id);
               if(isset($fdata) and isset($sdata)) {
                $lead_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$fdata->candidate_id);
                $lead_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$fdata->nom_id);
                $lead_party=$this->commonModel->selectone('m_party','CCODE',$lead_nom->party_id);
            
              $trail_cand=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$sdata->candidate_id);
              $trail_nom=$this->commonModel->selectone('candidate_nomination_detail','nom_id',$sdata->nom_id);
              $trail_party=$this->commonModel->selectone('m_party','CCODE',$trail_nom->party_id);
           

            $margin=$fdata->max_total-$sdata->max_total;
            $winn_update=array('candidate_id'=>$fdata->candidate_id,
                      'nomination_id'=>$fdata->nom_id,'lead_cand_name'=>$lead_cand->cand_name,
                      'lead_cand_partyid'=>$lead_party->CCODE,'lead_cand_party'=>$lead_party->PARTYNAME,
                      'lead_party_type'=>$lead_party->PARTYTYPE,'lead_party_abbre'=>$lead_party->PARTYABBRE,
                      'lead_cand_hname'=>$lead_cand->cand_hname,'lead_cand_hparty'=>$lead_party->PARTYHNAME,
                      'lead_hpartyabbre'=>$lead_party->PARTYHABBR,
                      'trail_candidate_id'=>$sdata->candidate_id,'trail_nomination_id'=>$sdata->nom_id,
                      'trail_cand_name'=>$trail_cand->cand_name,'trail_cand_partyid'=>$trail_party->CCODE,
                      'trail_cand_party'=>$trail_party->PARTYNAME,'trail_party_type'=>$trail_party->PARTYTYPE,
                      'trail_party_abbre'=>$trail_party->PARTYABBRE,'trail_cand_hname'=>$trail_cand->cand_hname,
                      'trail_cand_hparty'=>$trail_party->PARTYHNAME,'trail_hpartyabbre'=>$trail_party->PARTYHABBR,
                      'margin'=>$margin,'lead_total_vote'=>$fdata->max_total,'trail_total_vote'=>$sdata->max_total);
            
             DB::table('winning_leading_candidate')->where('leading_id',$record->leading_id)->update($winn_update);

           }
          
            } // end for each

          }  // end if
        
        } 

    public function dummy_pswise_dataentry()
        {  
          // $listac=getacbystate('S24');
            $listpc=getpcbystate('S24');
            //getallacbypcno($st,$pcno)    SELECT `AC_NO`,COUNT(*) AS cnt FROM `polling_station` WHERE `st_code`='S24'   GROUP BY 1

           foreach($listpc as $pc) 
             {     set_time_limit(0);
               $listpcac=getallacbypcno("S24",$pc->PC_NO);

                  foreach($listpcac as $pcac) 
                    {    set_time_limit(0);
                        $get_all_records=DB::table('polling_station')->WHERE('st_code', 'S24')->WHERE('AC_NO', $pcac->AC_NO)->get();
                        $get_all_candidate=DB::table('candidate_nomination_detail')->select('nom_id','candidate_id')->WHERE('st_code', 'S24')->WHERE('pc_no', $pc->PC_NO)->get();
                       foreach( $get_all_records as $record) 
                            {   set_time_limit(0);
                              for($i=1;$i<=15;$i++)
                              {
                                 foreach( $get_all_candidate as $cand) 
                                        {
                                           set_time_limit(0);
                                          $newdata = array('nom_id'=>$cand->nom_id,'candidate_id'=>$cand->candidate_id,'ac_no'=>$pcac->AC_NO, 
                                              'pc_no'=>$pc->PC_NO,
                                              'election_id'=>'1',
                                              'election_typeid'=>'1',
                                              'month'=>'08',
                                              'year'=>'2019',
                                              'ps_no'=>$record->PS_NO,
                                              'bu_no'=>'20',
                                              'cu_no'=>'11', 'vvpat_no'=>'11', 'table_id'=>$i, 'round_id'=>$i, 'evm_vote'=>'300',
                                              'added_create_at'=>date('Y-m-d'),
                                              'created_at'=>date('Y-m-d h:i:s'),
                                              );
                                            $this->commonModel->insertData('counting_ps_stcode',$newdata); 

                                        }
                              }
                            } 
                    }
             }

                  
        }






public function get_ca_cand_list(Request $request)
  {



    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $list_details = array();
      $dataArr = array();

      $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();
      $list = DB::table('m_election_details')->select('ST_CODE')
        ->whereIn('ELECTION_TYPEID', [1, 2])
        ->groupBy('m_election_details.ST_CODE')
        ->orderBy('ST_CODE', 'ASC')
        ->get();
      $st_list = array();
      foreach ($list as $key) {
        array_push($st_list, $key->ST_CODE);
      }

      $res = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->leftjoin('m_schedule', 'm_schedule.SCHEDULEID', '=', 'candidate_nomination_detail.scheduleid')
         ->leftjoin('m_election_details', 'm_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID')
        ->join("m_pc", function ($join) {
          $join->on("m_pc.ST_CODE", "=", "candidate_nomination_detail.st_code")
            ->on("m_pc.PC_NO", "=", "candidate_nomination_detail.pc_no");
        })
        // ->leftjoin("m_district", function ($join) {
        //   $join->on("m_district.DIST_NO", "=", "m_ac.DIST_NO_HDQTR")
        //     ->on("m_district.ST_CODE", "=", "candidate_nomination_detail.st_code");
        // })
        // 'm_pc.PC_NAME','DIST_NO', 'DIST_NAME',
        ->select('nom_id', 'cand_name', 'candidate_father_name', 'cand_email', 'cand_mobile', 'PARTYABBRE', 'PARTYNAME', 'candidate_nomination_detail.st_code', 'm_state.ST_NAME', 'candidate_nomination_detail.candidate_id', 'candidate_personal_detail.is_criminal',   'm_schedule.SCHEDULEID','m_election_details.StatePHASE_NO','m_pc.PC_NAME', DB::raw('(CASE 
          WHEN candidate_nomination_detail.application_status = 1 THEN "Applied"
          WHEN candidate_nomination_detail.application_status = 2 THEN "Submited and verified by RO"
          WHEN candidate_nomination_detail.application_status = 3 THEN "Receipt Generated "
          WHEN candidate_nomination_detail.application_status = 4 THEN "Rejected"
          WHEN candidate_nomination_detail.application_status = 5 THEN "Withdrawn"
          WHEN  candidate_nomination_detail.finalaccepted = 1 AND candidate_nomination_detail.application_status = 6  THEN "Contesting"
          WHEN candidate_nomination_detail.application_status = 6 AND candidate_nomination_detail.finalaccepted = 0 THEN "Accepted"

          ELSE "None" END) AS application_status'))
        ->where("candidate_nomination_detail.party_id", "!=",  1180)
        //->where("candidate_nomination_detail.candidate_id", "!=",  1513)
        ->where("candidate_nomination_detail.application_status", "!=", 11)
        //->where("candidate_nomination_detail.finalize", '1')
        ->where("candidate_nomination_detail.symbol_id", "!=",  '200');

      $state_list = array();
      if (!empty($request->phase)) {
        
        $res->where('candidate_nomination_detail.scheduleid', $request->phase);
        $state_list = DB::table('m_st_schedule')->where('SCHEDULEID', $request->phase)
          ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
          ->select('m_state.ST_CODE', 'm_state.ST_NAME')
          ->orderBy('m_state.ST_CODE', 'ASC')
          ->groupBy('m_st_schedule.SCHEDULEID')
          ->groupBy('m_st_schedule.ST_CODE')
          ->get();
      } else {
        
        $state_list = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->whereIn('ST_CODE', $st_list)->get();
      }


      $district_list = array();
      if (!empty($request->state_id) && !empty($request->phase)) {
        $res->where('candidate_nomination_detail.st_code', $request->state_id);

        $district_list = DB::table('m_st_schedule')
          ->leftjoin("m_pc", function ($join) {
            $join->on("m_pc.ST_CODE", "=", "m_st_schedule.ST_CODE")
              ->on("m_pc.PC_NO", "=", "m_st_schedule.CONST_NO");
          })
          // ->leftjoin("m_district", function ($join) {
          //   $join->on("m_district.DIST_NO", "=", "m_ac.DIST_NO_HDQTR")
          //     ->on("m_district.ST_CODE", "=", "m_st_schedule.ST_CODE");
          // })
          ->where('m_st_schedule.ST_CODE', $request->state_id)
          ->where('m_st_schedule.SCHEDULEID', $request->phase)
          //->select('DIST_NO', 'DIST_NAME')
          //->groupBy('DIST_NO')
          ->get();
      } else if (!empty($request->state_id)) {
        $res->where('candidate_nomination_detail.st_code', $request->state_id);
        $district_list = DB::table('m_st_schedule')
          ->leftjoin("m_pc", function ($join) {
            $join->on("m_pc.ST_CODE", "=", "m_st_schedule.ST_CODE")
              ->on("m_pc.PC_NO", "=", "m_st_schedule.CONST_NO");
          })
          // ->leftjoin("m_district", function ($join) {
          //   $join->on("m_district.DIST_NO", "=", "m_ac.DIST_NO_HDQTR")
          //     ->on("m_district.ST_CODE", "=", "m_st_schedule.ST_CODE");
          // })
          ->where('m_st_schedule.ST_CODE', $request->state_id)
          //->select('DIST_NO', 'DIST_NAME')
         // ->groupBy('DIST_NO')
          ->get();
      }

      $ac_list = array();
      if ($request->state_id  && !empty($request->phase)) {
       // $res->where('m_pc.DIST_NO_HDQTR', $request->district);
        $ac_list = DB::table('m_st_schedule')
          ->leftjoin("m_pc", function ($join) {
            $join->on("m_pc.ST_CODE", "=", "m_st_schedule.ST_CODE")
              ->on("m_pc.PC_NO", "=", "m_st_schedule.CONST_NO");
          })
          
          ->where('m_st_schedule.ST_CODE', $request->state_id)
          ->where('m_st_schedule.SCHEDULEID', $request->phase)
          //->where('m_ac.DIST_NO_HDQTR', $request->district)
          ->select('PC_NO', 'PC_NAME')
          ->get();
      } else if ($request->state_id) {
       // $res->where('m_ac.DIST_NO_HDQTR', $request->district);
        $ac_list = DB::table('m_st_schedule')
          ->leftjoin("m_pc", function ($join) {
            $join->on("m_pc.ST_CODE", "=", "m_st_schedule.ST_CODE")
              ->on("m_pc.PC_NO", "=", "m_st_schedule.CONST_NO");
          })
          // ->leftjoin("m_district", function ($join) {
          //   $join->on("m_district.DIST_NO", "=", "m_ac.DIST_NO_HDQTR")
          //     ->on("m_district.ST_CODE", "=", "m_st_schedule.ST_CODE");
          // })
          ->where('m_st_schedule.ST_CODE', $request->state_id)
        //  ->where('m_pc.DIST_NO_HDQTR', $request->district)
          ->select('PC_NO', 'PC_NAME')
          ->get();
      }



      if (!empty($request->ac_id))
        $res->where('candidate_nomination_detail.pc_no', $request->ac_id);

      if (!empty($request->party_id))
        $res->where('candidate_nomination_detail.party_id', $request->party_id);

      if (!empty($request->cand_type)) {
        if ($request->cand_type == 2)
          $cand_type = '0';
        else
          $cand_type = '1';

        $res->where('candidate_personal_detail.is_criminal', $cand_type);
      }

      if (!empty($request->app_status)) {

        if ($request->app_status == 12){
         $res->where('candidate_nomination_detail.application_status', '6');
         $res->where('candidate_nomination_detail.finalaccepted', 1);
         }else if($request->app_status == 6){
            $res->where('candidate_nomination_detail.application_status', '6');
             $res->where('candidate_nomination_detail.finalaccepted', 0);
         }else{

        $res->where('candidate_nomination_detail.application_status', $request->app_status);
      }
        
      }

      $res->groupBy("candidate_nomination_detail.candidate_id");
     // $res->groupBy("candidate_nomination_detail.party_id");
      $res->orderBy("candidate_nomination_detail.scheduleid");
      $res->orderBy("candidate_nomination_detail.st_code");
      $data = $res->get();

      $party_list = DB::table('m_party')->select('CCODE', 'PARTYABBRE', 'PARTYNAME')->orderBy('PARTYNAME')->get();
      $status_list = DB::table('m_status')->select('status', 'id')->orderBy('status')
        ->whereNotIn("id", array(7, 11))
        ->get();

      /*echo "<pre>";
        print_r($data);
        die;*/
        // 'ac_list' => $ac_list,
      return view('admin.pc.eci.ca_list')->with(array('user_data' => $d, 'phase_list' => $phase_list, 'district_list' => $district_list, 'data' => $data, 'state_list' => $state_list, 'party_list' => $party_list,  'status_list' => $status_list));
    } else {
      return redirect('/officer-login');
    }
  }


    public function get_ac(Request $request)
  {

    $res = DB::table('m_st_schedule')
      ->leftjoin("m_pc", function ($join) {
        $join->on("m_pc.ST_CODE", "=", "m_st_schedule.ST_CODE")
          ->on("m_pc.PC_NO", "=", "m_st_schedule.CONST_NO");
      })
    
      ->where('m_st_schedule.ST_CODE', $request->state_id)
      //->where('m_ac.DIST_NO_HDQTR', $request->district_id)
      ->select('PC_NO', 'PC_NAME');

    if (!empty($request->schedule_id))
      $res->where('m_st_schedule.SCHEDULEID', $request->schedule_id);

    $acs = $res->get();
    $data = array();
    for ($i = 0; $i < count($acs); $i++) {
      $data[] = array('id' => $acs[$i]->PC_NO, 'name' => $acs[$i]->PC_NAME);
    }
    $output  = $data;

    echo json_encode($output);
  }

  public function get_state(Request $request)
  {
    if ($request->id != 0) {
      $states = DB::table('m_st_schedule')->where('SCHEDULEID', $request->id)
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
        ->select('m_state.ST_CODE', 'm_state.ST_NAME')
        ->orderBy('m_state.ST_CODE', 'ASC')
        ->groupBy('m_st_schedule.SCHEDULEID')
        ->groupBy('m_st_schedule.ST_CODE')
        ->get();
      $data = array();
      for ($i = 0; $i < count($states); $i++) {
        $data[] = array('id' => $states[$i]->ST_CODE, 'name' => $states[$i]->ST_NAME);
      }
      $output  = $data;
      echo json_encode($output);
    } else {
      $list = DB::table('m_election_details')->select('ST_CODE')
        ->whereIn('ELECTION_TYPEID', [1, 2])
        ->groupBy('m_election_details.ST_CODE')
        ->orderBy('ST_CODE', 'ASC')
        ->get();
      $st_list = array();
      foreach ($list as $key) {
        array_push($st_list, $key->ST_CODE);
      }
      $state_list = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->whereIn('ST_CODE', $st_list)->get();
      for ($i = 0; $i < count($state_list); $i++) {
        $data[] = array('id' => $state_list[$i]->ST_CODE, 'name' => $state_list[$i]->ST_NAME);
      }
      $output  = $data;
      echo json_encode($output);
    }
  }

/*
  public function get_district(Request $request)
  {
    $res = DB::table('m_st_schedule')
      ->leftjoin("m_ac", function ($join) {
        $join->on("m_ac.ST_CODE", "=", "m_st_schedule.ST_CODE")
          ->on("m_ac.AC_NO", "=", "m_st_schedule.CONST_NO");
      })
      ->leftjoin("m_district", function ($join) {
        $join->on("m_district.DIST_NO", "=", "m_ac.DIST_NO_HDQTR")
          ->on("m_district.ST_CODE", "=", "m_st_schedule.ST_CODE");
      })
      ->where('m_st_schedule.ST_CODE', $request->id)
      ->select('DIST_NO', 'DIST_NAME')
      ->groupBy('DIST_NO');

    if (!empty($request->schedule_id))
      $res->where('m_st_schedule.SCHEDULEID', $request->schedule_id);

    $districts = $res->get();

    $data = array();
    for ($i = 0; $i < count($districts); $i++) {
      $data[] = array('id' => $districts[$i]->DIST_NO, 'name' => $districts[$i]->DIST_NAME);
    }
    $output  = $data;
    echo json_encode($output);
  }
*/
  public function get_ca_cand_list_pdf(Request $request)
  {
  //  dd($request);
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $list_details = array();
      $dataArr = array();

      $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();

      $res = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->leftjoin('m_schedule', 'm_schedule.SCHEDULEID', '=', 'candidate_nomination_detail.scheduleid')
        ->leftjoin('m_symbol', 'm_symbol.SYMBOL_NO', '=', 'candidate_nomination_detail.symbol_id')
        ->leftjoin('m_election_details', 'm_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID')
        ->join("m_pc", function ($join) {
          $join->on("m_pc.ST_CODE", "=", "candidate_nomination_detail.st_code")
            ->on("m_pc.PC_NO", "=", "candidate_nomination_detail.pc_no");
        })
        // ->leftjoin("m_district", function ($join) {
        //   $join->on("m_district.DIST_NO", "=", "m_ac.DIST_NO_HDQTR")
        //     ->on("m_district.ST_CODE", "=", "candidate_nomination_detail.st_code");
        // })
        ->select('nom_id', 'cand_name', 'candidate_father_name', 'cand_email', 'cand_mobile', 'PARTYABBRE', 'PARTYNAME', 'candidate_nomination_detail.st_code', 'm_state.ST_NAME', 'm_state.ST_CODE', 'candidate_nomination_detail.candidate_id', 'candidate_personal_detail.is_criminal',  'candidate_personal_detail.cand_gender', 'candidate_personal_detail.cand_age', 'candidate_personal_detail.cand_category', 'm_pc.PC_NAME', 'm_pc.PC_NO', 'm_schedule.SCHEDULEID', 'm_symbol.SYMBOL_DES', 'm_election_details.StatePHASE_NO', DB::raw('(CASE 
          WHEN candidate_nomination_detail.application_status= 1 THEN "Applied"
          WHEN candidate_nomination_detail.application_status = 2 THEN "Submited and verified by RO"
          WHEN candidate_nomination_detail.application_status = 3 THEN "Receipt Generated "
          WHEN candidate_nomination_detail.application_status = 4 THEN "Rejected"
          WHEN candidate_nomination_detail.application_status = 5 THEN "Withdrawn"
          WHEN  candidate_nomination_detail.finalaccepted = 1 AND candidate_nomination_detail.application_status = 6  THEN "Contesting"
          WHEN candidate_nomination_detail.application_status = 6 AND candidate_nomination_detail.finalaccepted = 0 THEN "Accepted"
          ELSE "None" END) AS application_status'))
        ->where("candidate_nomination_detail.party_id", "!=",  1180)
        //->where("candidate_nomination_detail.candidate_id", "!=",  1513)
        ->where("candidate_nomination_detail.application_status", "!=", 11)
       // ->where("candidate_nomination_detail.finalize", '1')
        ->where("candidate_nomination_detail.symbol_id", "!=",  '200');


      $state_list = array();
      if (!empty($request->phase)) {
        $res->where('candidate_nomination_detail.scheduleid', $request->phase);

        $state_list = DB::table('m_st_schedule')->where('SCHEDULEID', $request->phase)
          ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
          ->select('m_state.ST_CODE', 'm_state.ST_NAME')
          ->orderBy('m_state.ST_CODE', 'ASC')
          ->groupBy('m_st_schedule.SCHEDULEID')
          ->groupBy('m_st_schedule.ST_CODE')
          ->get();

        $data['phase_name'] = "Phase " . $request->phase;
      } else {
        $phase_name_list = array();
        foreach ($phase_list as $keys) {
          array_push($phase_name_list, "Phase " . $keys->SCHEDULEID);
        }
        $data['phase_name'] = implode(",", $phase_name_list);
        $state_list = DB::table('m_election_details')->select('ST_CODE')
          ->whereIn('ELECTION_TYPEID', [1, 2])
          ->groupBy('m_election_details.ST_CODE')
          ->orderBy('ST_CODE', 'ASC')
          ->get();
      }

      if (!empty($request->state_id)) {
        $res->where('candidate_nomination_detail.st_code', $request->state_id);
        $state_data = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->where('ST_CODE', $request->state_id)->first();
        $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->where('ST_CODE', $request->state_id)->get();
        $data['state_name'] = $state_data->ST_NAME;
      } else {
        $st_list = array();
        foreach ($state_list as $key) {
          array_push($st_list, $key->ST_CODE);
        }
        $res->whereIn('candidate_nomination_detail.st_code', $st_list);
        $state_list = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->whereIn('ST_CODE', $st_list)->get();
        $st_name_list = array();
        foreach ($state_list as $keys) {
          array_push($st_name_list, $keys->ST_NAME);
        }
        $data['state_list_pdf'] = $state_list;
        $data['state_name'] = implode(",", $st_name_list);
      }

      if (!empty($request->district)) {
        $res->where('m_ac.DIST_NO_HDQTR', $request->district);
      }

      if (!empty($request->ac_id_report))
        $res->where('candidate_nomination_detail.ac_no', $request->ac_id_report);

      if (!empty($request->party_id))
        $res->where('candidate_nomination_detail.party_id', $request->party_id);

      if (!empty($request->cand_type)) {
        if ($request->cand_type == 2)
          $cand_type = '0';
        else
          $cand_type = '1';

        $res->where('candidate_personal_detail.is_criminal', $cand_type);
      }

      // if (!empty($request->app_status)) {
      //   $res->where('candidate_nomination_detail.application_status', $request->app_status);
      //   if ($request->app_status == 6)
      //     $res->where('candidate_nomination_detail.finalaccepted', 1);
      // }

      if (!empty($request->app_status)) {


        if ($request->app_status == 12){
         $res->where('candidate_nomination_detail.application_status', '6');
         $res->where('candidate_nomination_detail.finalaccepted', 1);
         }else if($request->app_status == 6){
            $res->where('candidate_nomination_detail.application_status', '6');
             $res->where('candidate_nomination_detail.finalaccepted', 0);
         }else{

        $res->where('candidate_nomination_detail.application_status', $request->app_status);
      }
        
      }

      $res->groupBy("candidate_nomination_detail.candidate_id");
      //$res->groupBy("candidate_nomination_detail.party_id");
      $res->orderBy("candidate_nomination_detail.scheduleid");
      $res->orderBy("candidate_nomination_detail.st_code");
      $data['results'] = $res->get();
      /*echo "<pre>";
        print_r($data['results']);
        die;*/
      $name_pdf = "Candidate_CA_Report";
      //return view('admin.ac.eci.ca_list_summary_pdf', ['data'=>$data]);
      $pdf = \PDF::loadView('admin.pc.eci.ca_list_pdf', $data);
      return $pdf->download($name_pdf . '_' . date('d-m-Y') . '_' . time() . '.pdf');
    }
  }

  public function get_ca_cand_list_excel(Request $request)
  {
    if (Auth::check()) {
      $user = Auth::user();
      $d = $this->commonModel->getunewserbyuserid($user->id);
      $list_details = array();
      $dataArr = array();

      $phase_list = DB::table('m_schedule')->select('SCHEDULEID')->get();

      $res = DB::table('candidate_nomination_detail')
        ->leftjoin('candidate_personal_detail', 'candidate_personal_detail.candidate_id', '=', 'candidate_nomination_detail.candidate_id')
        ->leftjoin('m_party', 'm_party.CCODE', '=', 'candidate_nomination_detail.party_id')
        ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'candidate_nomination_detail.st_code')
        ->leftjoin('m_schedule', 'm_schedule.SCHEDULEID', '=', 'candidate_nomination_detail.scheduleid')
        ->leftjoin('m_symbol', 'm_symbol.SYMBOL_NO', '=', 'candidate_nomination_detail.symbol_id')
        ->leftjoin('m_election_details', 'm_election_details.ScheduleID', '=', 'm_schedule.SCHEDULEID')
        
        ->join("m_pc", function ($join) {
          $join->on("m_pc.ST_CODE", "=", "candidate_nomination_detail.st_code")
            ->on("m_pc.PC_NO", "=", "candidate_nomination_detail.pc_no");
        })
        // ->leftjoin("m_district", function ($join) {
        //   $join->on("m_district.DIST_NO", "=", "m_ac.DIST_NO_HDQTR")
        //     ->on("m_district.ST_CODE", "=", "candidate_nomination_detail.st_code");
        // })
        ->select('nom_id', 'cand_name', 'candidate_father_name', 'cand_email', 'cand_mobile', 'PARTYABBRE', 'PARTYNAME', 'candidate_nomination_detail.st_code', 'm_state.ST_NAME',  'm_state.ST_CODE', 'candidate_nomination_detail.candidate_id', 'candidate_personal_detail.is_criminal',  'candidate_personal_detail.cand_gender',  'candidate_personal_detail.cand_age', 'candidate_personal_detail.cand_category', 'm_pc.PC_NAME', 'm_pc.PC_NO',  'm_schedule.SCHEDULEID', 'm_symbol.SYMBOL_DES','m_election_details.StatePHASE_NO', DB::raw('(CASE 
          WHEN candidate_nomination_detail.application_status = 1 THEN "Applied"
          WHEN candidate_nomination_detail.application_status = 2 THEN "Submited and verified by RO"
          WHEN candidate_nomination_detail.application_status = 3 THEN "Receipt Generated "
          WHEN candidate_nomination_detail.application_status = 4 THEN "Rejected"
          WHEN candidate_nomination_detail.application_status = 5 THEN "Withdrawn"
          WHEN  candidate_nomination_detail.finalaccepted = 1 AND candidate_nomination_detail.application_status = 6  THEN "Contesting"
          WHEN candidate_nomination_detail.application_status = 6 AND candidate_nomination_detail.finalaccepted = 0 THEN "Accepted"
          ELSE "None" END) AS application_status'))
        ->where("candidate_nomination_detail.party_id", "!=",  1180)
        //->where("candidate_nomination_detail.candidate_id", "!=",  1513)
        ->where("candidate_nomination_detail.application_status", "!=", 11)
        //->where("candidate_nomination_detail.finalize", '1')
        ->where("candidate_nomination_detail.symbol_id", "!=",  '200');

      $state_list = array();
      if (!empty($request->phase)) {
        $res->where('candidate_nomination_detail.scheduleid', $request->phase);

        $state_list = DB::table('m_st_schedule')->where('SCHEDULEID', $request->phase)
          ->leftjoin('m_state', 'm_state.ST_CODE', '=', 'm_st_schedule.ST_CODE')
          ->select('m_state.ST_CODE', 'm_state.ST_NAME')
          ->orderBy('m_state.ST_CODE', 'ASC')
          ->groupBy('m_st_schedule.SCHEDULEID')
          ->groupBy('m_st_schedule.ST_CODE')
          ->get();

        $data['phase_name'] = "Phase " . $request->phase;
      } else {
        $phase_name_list = array();
        foreach ($phase_list as $keys) {
          array_push($phase_name_list, "Phase " . $keys->SCHEDULEID);
        }
        $data['phase_name'] = implode(",", $phase_name_list);
        $state_list = DB::table('m_election_details')->select('ST_CODE')
          ->whereIn('ELECTION_TYPEID', [1, 2])
          ->groupBy('m_election_details.ST_CODE')
          ->orderBy('ST_CODE', 'ASC')
          ->get();
      }

      if (!empty($request->state_id)) {
        $res->where('candidate_nomination_detail.st_code', $request->state_id);
        $state_data = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->where('ST_CODE', $request->state_id)->first();
        $data['state_list_pdf'] = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->where('ST_CODE', $request->state_id)->get();
        $data['state_name'] = $state_data->ST_NAME;
      } else {
        $st_list = array();
        foreach ($state_list as $key) {
          array_push($st_list, $key->ST_CODE);
        }
        $res->whereIn('candidate_nomination_detail.st_code', $st_list);
        $state_list = DB::table('m_state')->select('ST_CODE', 'ST_NAME')->whereIn('ST_CODE', $st_list)->get();
        $st_name_list = array();
        foreach ($state_list as $keys) {
          array_push($st_name_list, $keys->ST_NAME);
        }
        $data['state_list_pdf'] = $state_list;
        $data['state_name'] = implode(",", $st_name_list);
      }

      if (!empty($request->district)) {
        $res->where('m_ac.DIST_NO_HDQTR', $request->district);
      }

      if (!empty($request->ac_id_report))
        $res->where('candidate_nomination_detail.ac_no', $request->ac_id_report);

      if (!empty($request->party_id))
        $res->where('candidate_nomination_detail.party_id', $request->party_id);

      if (!empty($request->cand_type)) {
        if ($request->cand_type == 2)
          $cand_type = '0';
        else
          $cand_type = '1';

        $res->where('candidate_personal_detail.is_criminal', $cand_type);
      }

      // if (!empty($request->app_status)) {
      //   $res->where('candidate_nomination_detail.application_status', $request->app_status);
      //   if ($request->app_status == 6)
      //     $res->where('candidate_nomination_detail.finalaccepted', 1);
      // }

      if (!empty($request->app_status)) {

        if ($request->app_status == 12){
         $res->where('candidate_nomination_detail.application_status', '6');
         $res->where('candidate_nomination_detail.finalaccepted', 1);
         }else if($request->app_status == 6){
            $res->where('candidate_nomination_detail.application_status', '6');
             $res->where('candidate_nomination_detail.finalaccepted', 0);
         }else{

        $res->where('candidate_nomination_detail.application_status', $request->app_status);
      }
        
      }

      $res->groupBy("candidate_nomination_detail.candidate_id");
      //$res->groupBy("candidate_nomination_detail.party_id");
      $res->orderBy("candidate_nomination_detail.scheduleid");
      $res->orderBy("candidate_nomination_detail.st_code");
      $data['results'] = $res->get();
      /*echo "<pre>";
        print_r($data['results']);
        die;*/
      //return view('admin.ac.eci.ca_list_summary_pdf', ['data'=>$data]);
      $export_data = [];
      $headings[] = ["Phase(s): " . $data['phase_name'] . "\n State: " . $data['state_name'] . "\n Date: " . date("d-m-Y")];

      $export_data[] = ['Phase', 'NOMINATION ID', 'CANDIDATE NAME', 'SON/HUSBAND OF', 'GENDER', 'AGE', 'CATEGORY', 'STATE NO', 'STATE',  'PC NO', 'PC', 'PARTY', 'SYMBOL', 'IS CRIMINAL', 'IS CRIMINAL FLAG', 'STATUS'];

      foreach ($data['results'] as $lis) {


        if ($lis->is_criminal == 1)
          $is_criminal =  "Yes";
        else
          $is_criminal =  "No";

        $export_data[] = [
          "Phase: " . $lis->StatePHASE_NO,
          $lis->nom_id,
          $lis->cand_name,
          $lis->candidate_father_name,
          $lis->cand_gender,
          $lis->cand_age,
          $lis->cand_category,
          $lis->ST_CODE,
          $lis->ST_NAME,
          //$lis->DIST_NO,
          //$lis->DIST_NAME,
          $lis->PC_NO,
          $lis->PC_NAME,
          $lis->PARTYNAME,
          $lis->SYMBOL_DES,
          $is_criminal,
          $lis->is_criminal,
          $lis->application_status
        ];
      }
      $name_excel = "Candidate_CA_Report";

      return Excel::download(new ExcelExport($headings, $export_data), $name_excel . '_' . date('d-m-Y') . '_' . time() . '.xlsx');
    }
  }















}  // end class