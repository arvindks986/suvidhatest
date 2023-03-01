<?php
    namespace App\Http\Controllers\Admin\Common;
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
    use MPDF;
    use App\commonModel;  
    use App\adminmodel\ECIModel;
    use App\adminmodel\MELECMaster;
    use App\adminmodel\ElectiondetailsMaster;
    use App\adminmodel\Electioncurrentelection;
    use App\Helpers\SmsgatewayHelper;
    use App\adminmodel\CandidateModel;
    use App\adminmodel\PartyMaster;
    use App\adminmodel\CandidateNomination;
    use App\adminmodel\PCCountingModel;  
    use App\Classes\xssClean;
   
class ECICommonController extends Controller
{
    
    public function __construct(){   
            $this->middleware('adminsession');
            $this->middleware(['auth:admin','auth']);
            $this->middleware('eci');
            $this->commonModel = new commonModel();
            $this->ECIModel = new ECIModel();
            $this->CountingModel = new PCCountingModel();
            $this->xssClean = new xssClean;
            
      }

    protected function guard(){
        return Auth::guard();
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
           
       
          // DB::beginTransaction();
          //   try{       
             foreach($cand_data as $list){
              $check = DB::table('counting_pcmaster')
                      ->where('nom_id',$list->nom_id)
                      ->where('st_code',$list->st_code)
                      ->where('pc_no',$list->pc_no)
                      ->where('election_id',$list->election_id)->first();
              
              if(!isset($check)){
                      $can=$this->commonModel->selectone('candidate_personal_detail','candidate_id',$list->candidate_id);
                      $p=getpartybyid($list->party_id);
                      $ca_data = array('nom_id'=>$list->nom_id,
                                  'candidate_id'=>$list->candidate_id,
                                  'st_code'=>$list->st_code,
                                  'pc_no'=>$list->pc_no,
                                  'election_id'=>$list->election_id,
                                  'dist_no'=>$list->district_no,
                                  'new_srno'=>$list->new_srno,
                                  'created_at'=>date("Y-m-d h:m:s"),
                                  'created_by'=>'ECI',
                                  'candidate_name'=>$can->cand_name,
                                  'candidate_hname'=>$can->cand_hname,
                                  'party_id'=>$list->party_id,
                                  'party_abbre'=>$p->PARTYABBRE,
                                  'party_habbre'=>$p->PARTYHABBR,
                                  'party_name'=>$p->PARTYNAME,
                                  'party_hname'=>$p->PARTYHNAME,
                                  'added_create_at'=>$currentdate,
                                  'created_by'=>'ECI'); 
                      
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
                        $ca_data = array('nom_id'=>$list->nom_id,
                              'candidate_id'=>$list->candidate_id,
                              'ac_no'=>$r->AC_NO,
                              'pc_no'=>$list->pc_no,
                              'election_id'=>$list->election_id,
                              'dist_no'=>$list->district_no,
                              'new_srno'=>$list->new_srno,
                              'created_at'=>$currentTime,
                              'created_by'=>'ECI',
                              'added_create_at'=>$currentdate,
                              'candidate_name'=>$can->cand_name,
                              'party_id'=>$list->party_id,
                              'party_abbre'=>$p->PARTYABBRE,
                              'party_name'=>$p->PARTYNAME,
                              'candidate_hname'=>$can->cand_hname,
                              'party_habbre'=>$p->PARTYHABBR,
                              'party_hname'=>$p->PARTYHNAME,
                              'month'=>date("m"),'year'=>date("Y")); 
                               $this->commonModel->insertData($new_table, $ca_data);
                          }
                   } 
           }
        $lis_st=$this->commonModel->getstatebystatecode($ele_details->ST_CODE);
            $lis_pc=$this->commonModel->getpcbypcno($ele_details->ST_CODE,$ele_details->CONST_NO);

        $check_d=DB::table('winning_leading_candidate')
                      ->where('st_code',$ele_details->ST_CODE)
                      ->where('pc_no',$ele_details->CONST_NO)
                      ->where('election_id',$ele_details->ELECTION_ID)->first();   
        if(!isset($check_d)){   
         $winn_data=array('election_id'=>$ele_details->ELECTION_ID,
                          'constituency_type'=>$ele_details->CONST_TYPE,
                          'st_code'=>$ele_details->ST_CODE,
                          'st_name'=>$lis_st->ST_NAME,
                          'st_hname'=>$lis_st->ST_NAME_HI,
                          'pc_no'=>$ele_details->CONST_NO,
                          'pc_name'=>$lis_pc->PC_NAME,
                          'pc_hname'=>$lis_pc->PC_NAME_HI,
                          'dist_no'=>'0',
                          'created_at'=>date("Y-m-d h:m:s"),
                          'added_create_at'=>$currentdate);
                      
                      $this->commonModel->insertData('winning_leading_candidate', $winn_data);
              }    
            //  }
            // catch(\Exception $e){
            //     DB::rollback();
        
            //     \Session::flash('unsuccess_insert', 'Request timeout. Please try again');
            //     return Redirect::back();
            // }
            // DB::commit();

 
         
          }
        }
      
        
        } 


 
     
   public function generateofficersloginname()
        {  
          $list_state=getallstate();
               $password="demo@1234";
               $pin="1234";
          
           foreach($list_state as $st)
                  {  set_time_limit(0);
                     
                    $listallpc=getpcbystate($st->ST_CODE);
                    
                    foreach($listallpc as $pc)
                        { set_time_limit(0);
                    
                    $ele_details =DB::table('m_election_details')->where('ST_CODE',$st->ST_CODE)->where('CONST_TYPE','PC')->where('CONST_NO',$pc->PC_NO)->first();

                      if(!isset($ele_details))
                         {  continue;   }    
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
   // public function generateofficersloginname()
   //      {  
   //        $list_state=getallstate();
   //            $password="demo@1234";
   //            $pin="1234";
            
   //           foreach($list_state as $st)
   //                {  set_time_limit(0);
                     
   //                  $listallac=getacbystate($st->ST_CODE);
   //                  foreach($listallac as $ac)
   //                      { set_time_limit(0);

   //                      $ele_details =DB::table('m_election_details')->where('ST_CODE',$st->ST_CODE)->where('CONST_TYPE','AC')->where('CONST_NO',$ac->AC_NO)->first();

   //                    if(!isset($ele_details))
   //                       {  continue;   }  
   //                       $v ='DEO'.$st->ST_CODE."D".sprintf('%02d', $ac->DIST_NO_HDQTR);
   //                       $v1 ='ROAC'.$st->ST_CODE."A".sprintf('%03d', $ac->AC_NO); 
   //                       $dist=getdistrictbydistrictno($st->ST_CODE,$ac->DIST_NO_HDQTR);
   //                       echo $v;
   //                       echo "--".$v1;
                         
                        
   //                      $disdata = array('st_code'=>$st->ST_CODE,
   //                                            'ac_no'=>'0',
   //                                            'pc_no'=>'0',
   //                                            'dist_no'=>$ac->DIST_NO_HDQTR,
   //                                            'password'=> bcrypt($password),
   //                                            'two_step_pin'=> bcrypt($pin),
   //                                            'officername'=>$v,
   //                                            'designation'=>'DEO',
   //                                            'placename'=>$dist->DIST_NAME,
   //                                            'role_id'=>'5',
   //                                            'officerlevel'=>'DEO',
   //                                            'added_at'=>date('Y-m-d'),
   //                                            'created_at'=>date('Y-m-d h:i:s'),
   //                                            'election_id'=>$ele_details->ELECTION_ID,
   //                                            'is_active'=>'1',
   //                                            );  
                          
   //                       $acdata = array('st_code'=>$st->ST_CODE,
   //                                            'ac_no'=>$ac->AC_NO,
   //                                            'pc_no'=>$ac->PC_NO,
   //                                            'dist_no'=>$ac->DIST_NO_HDQTR,
   //                                            'password'=> bcrypt($password),
   //                                            'two_step_pin'=> bcrypt($pin),
   //                                            'officername'=>$v1,
   //                                            'designation'=>'ROAC',
   //                                            'placename'=>$ac->AC_NAME,
   //                                            'role_id'=>'19',
   //                                            'officerlevel'=>'AC',
   //                                            'added_at'=>date('Y-m-d'),
   //                                            'created_at'=>date('Y-m-d h:i:s'),
   //                                            'election_id'=>$ele_details->ELECTION_ID,
   //                                            'is_active'=>'1',
   //                                            );
   //                       $check1= DB::table('officer_login')->where('officername',$v)->first();
   //                          if(!isset($check1)){
   //                             $this->commonModel->insertData('officer_login',$disdata); 
   //                          }    
   //                       $check= DB::table('officer_login')->where('officername',$v1)->first();
   //                          if(!isset($check)){
   //                            $this->commonModel->insertData('officer_login',$acdata); 
   //                          }
                        
                         
   //                }
   //              }
   //      }
   
   public function insertfinalize()   // create finalize by admin
        {
          $list_state=getallstate();
          foreach($list_state as $st)
            {
                  $listallpc=getpcbystate($st->ST_CODE);
                  foreach($listallpc as $pc)
                    { 
                      set_time_limit(0);
                            
                        $ele_details =DB::table('m_election_details')->where('ST_CODE',$st->ST_CODE)->where('CONST_TYPE','PC')->where('CONST_NO',$pc->PC_NO)->first();
                        
                    if(isset($ele_details)){   
                            $ndata = array(
                                        'st_code'=>$st->ST_CODE,
                                        'const_no'=>$pc->PC_NO,
                                        'const_type'=>'PC',
                                        'election_id'=>$ele_details->ELECTION_ID,
                                        'created_at'=>date('Y-m-d h:i:s'),
                                        'added_create_at'=>date('Y-m-d'),
                                        'created_by'=>'ECI-sach',
                                        );
                            $check=$schedule = DB::table('candidate_finalized_ac')->where('st_code',$st->ST_CODE)->where('const_no',$pc->PC_NO)->where('const_type','PC')->first();
                            if(!isset($check))
                              $this->commonModel->insertData('candidate_finalized_ac',$ndata);
                             
                          }//
                        }
               }
        }
  public function insertcountingfinalize()  // master data
        {
          $list_state=getallstate();
          foreach($list_state as $st)
                {
                   $listallpc=getpcbystate($st->ST_CODE);
                  foreach($listallpc as $pc)
                    { 
                      set_time_limit(0);
                            
                        $ele_details =DB::table('m_election_details')->where('ST_CODE',$st->ST_CODE)->where('CONST_TYPE','PC')->where('CONST_NO',$pc->PC_NO)->first();
                        
                    if(isset($ele_details)){
                       $listac=getallacbypcno($st->ST_CODE,$pc->PC_NO);
                       foreach($listac as $ac)
                          {
                            $ndata = array(
                                        'st_code'=>$st->ST_CODE,
                                        'pc_no'=>$pc->PC_NO,
                                        'ac_no'=>$ac->AC_NO,
                                        'election_id'=>$ele_details->ELECTION_ID,
                                        'created_at'=>date('Y-m-d h:i:s'),
                                        'added_create_at'=>date('Y-m-d'),
                                        'created_by'=>'ECI-sach',
                                        );
                            $check=$schedule = DB::table('counting_finalized_ac')->where('st_code',$st->ST_CODE)->where('pc_no',$pc->PC_NO)->where('ac_no',$ac->AC_NO)->first();
                            if(!isset($check))
                              $this->commonModel->insertData('counting_finalized_ac',$ndata);
                          }
                            //print_r($ndata);
                          }
                          }//
                }
        } 
    public function generate_voterturnout_data()
        {  
            $list_state=getallstate();
           
            foreach($list_state as $st)
                  {  
                      set_time_limit(0);
                    
             $listallpc=getpcbystate($st->ST_CODE);
                  foreach($listallpc as $pc)
                    {         
                      set_time_limit(0);
                 
                 $ele_details =DB::table('m_election_details')->where('ST_CODE',$st->ST_CODE)->where('CONST_TYPE','PC')->where('CONST_NO',$pc->PC_NO)->first();
                        
                    if(isset($ele_details)){
                       $listac=getallacbypcno($st->ST_CODE,$pc->PC_NO);
                       set_time_limit(0);
                       $g = DB::table('m_ac')->where('ST_CODE',$st->ST_CODE)->where('PC_NO',$pc->PC_NO)->first();

                         $newdata = array('st_code'=>$st->ST_CODE,
                              'district_no'=>$g->DIST_NO_HDQTR,
                              'ac_no'=>'0',
                              'pc_no'=>$pc->PC_NO, 
                              'const_type'=> $ele_details->CONST_TYPE,
                              'electionid'=> $ele_details->ELECTION_ID,
                              'election_type_id'=> $ele_details->ELECTION_TYPEID,
                              'month'=>date("m"),
                              'year'=>date("Y"),
                              'schedule_id'=>$ele_details->ScheduleID,
                              'state_phase_no'=>$ele_details->StatePHASE_NO,
                              'm_election_detail_ccode'=>$ele_details->CCODE,  
                              'added_create_at'=>date('Y-m-d'),
                              'created_at'=>date('Y-m-d h:i:s'),
                              'created_by'=>'ECI-sach',
                              'election_id'=> $ele_details->ELECTION_ID,
                         );
                         
                          $check=DB::table('pd_schedulemaster')
                                  ->where('st_code',$st->ST_CODE)
                                  ->where('pc_no',$pc->PC_NO)
                                  ->where('const_type',"PC")->first();
                            if(!isset($check)){
                                $this->commonModel->insertData('pd_schedulemaster',$newdata); 
                    $rec=DB::table('pd_schedulemaster')
                                    ->where('st_code',$st->ST_CODE)
                                    ->where('pc_no',$pc->PC_NO)
                                    ->where('const_type',"PC")->first();
                         foreach ($listac as $ac) {
                            $detdata = array('st_code'=>$st->ST_CODE,
                                            'pc_no'=>$pc->PC_NO,
                                            'ac_no'=>$ac->AC_NO, 
                                            'pd_scheduleid'=> $rec->pd_scheduleid,
                                            'scheduleid'=> $ele_details->ELECTION_ID,
                                            'election_id'=> $ele_details->ELECTION_ID,
                                            'added_create_at'=>date('Y-m-d'),
                                            'created_at'=>date('Y-m-d h:i:s'),
                                            'created_by'=>'ECI-sach',
                                            );
                            $check1=DB::table('pd_scheduledetail')
                                  ->where('st_code',$st->ST_CODE)
                                  ->where('pc_no',$pc->PC_NO)
                                  ->where('ac_no',$ac->AC_NO)->first();
                            if(!isset($check1)){
                                $this->commonModel->insertData('pd_scheduledetail',$detdata); 
                              }
                            }


                          }
                           
                    }
             }

                  
        }
     }
  public function send_link()
          {
            $userinfo = DB::table('officer_login')
            ->where('password', '=', '')
            ->where('email','<>','')
            ->where('email','<>','')
            ->get();

         foreach ($userinfo as   $val) {
               $date = Carbon::now();
                $currentTime = $date->format('Y-m-d H:i:s'); 
                $code = Hash::make(str_random(10));
                $mobile_otp =rand(100000,999999);
                
              $record = array(
                'name'=>$val->name,
                //'password'=>'',
                'Phone_no'=>$val->Phone_no,
                'email'=>$val->email,
                'mobile_otp' => $mobile_otp,
                'otp_time' => $currentTime,
                'auth_token' => $code,
             );
              $n = DB::table('officer_login')->where('id', $val->id)->update($record);
                $encodeid=encrypt_string($val->id);
                $passcreaturl = url("/updateprofile/".$encodeid);
            $html = "Dear ".$val->name.",\n\n";
                                  $html .= "Your account has been updated in Suvidha Portal"
                                      . "Your account must be activated before you use it. For activating your account and updating your particular, please click on the following link. Alternatively, you could copy and paste the link in your browser.\n\n";
                                  $html .= $passcreaturl." \n\n";
                                  $html .= "OTP: ".$mobile_otp." \n\n";
                                  $html .= "Login ID: ".$val->officername." \n\n";
                                  $html .= "For verifying  your account,  kindly enter OTP ".$mobile_otp." and this OTP has also sent on your registered mobile no.: \n\n";
                                  
                                  $html .= "Thanks & Regards,\n\n";
                                  $html .= "Suvidha Team,\n\n";

                                $html = strip_tags($html);
                                  
                                 // mail ($val->email, 'UserLogin Credential',$html,'suvidha.eci.gov.in');
                            
                
          if($mobile!=""){
            $mob_message = "Dear Sir/Madam, your OTP is ".$mobile_otp." and Login ID: ".$val->officername." for SUVIDHA Portal.Activation link has been sent on your email. ".$passcreaturl." Please enter that link and enter OTP to proceed. Do not share this OTP Team ECI";
              $response = SmsgatewayHelper::gupshup($val->Phone_no,$mob_message);
            }   
 
          }
    } // end function 
}  // end class