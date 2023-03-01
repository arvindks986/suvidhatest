<?php

namespace App\Http\Controllers\Affidavit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\AC;
use App\models\PC;
use App\models\Districts;
use App\models\Affidavit\MParty;
use App\models\Affidavit\AffCandDetail;
use App\Http\Traits\CommonTraits;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Helpers\SmsgatewayHelper;

class CommanFunctionController extends Controller
{   
    
    public $successStatus = 200;
    public $expireOPT = 201;
    use CommonTraits;

    public function getDistricts(Request $request){
      $state_code = base64_decode(trim($request->state_code));
      if ($request->ajax()) {
          if(!empty($request) ) { 
             $state_code = Districts::select('DIST_NO','DIST_NAME','DIST_NAME_HI')->where('ST_CODE',$state_code)->orderBy('DIST_NAME')->get();
              if(!empty($state_code)):
                return response()->json(['error'=>false, 'status'=>200,'result'=>$state_code]);
              else:
                return response()->json(['error'=>true, 'status'=>500,'result'=>'']);
              endif;  
          }
        }  
    }

    

    public function getACList(Request $request){
      $state_code = base64_decode(trim($request->state_code));
     // $dist_code = base64_decode(trim($request->dist_code));
      
      if ($request->ajax()) {
          if(!empty($request) ) { 
             $dist_code = PC::select('PC_NO','PC_NAME','PC_NAME_HI')
                            ->where('ST_CODE',$state_code)
                            //->where('DIST_NO_HDQTR',$dist_code)
                            ->orderBy('PC_NO')
                            ->get();
              if(!empty($dist_code)):
                return response()->json(['error'=>false, 'status'=>200,'result'=>$dist_code]);
              else:
                return response()->json(['error'=>true, 'status'=>500,'result'=>'']);
              endif;  
          }
        }  
    }
	
	public static function getDistName($st_code,$pc_no){
		
		return DB::select("SELECT GROUP_CONCAT(DISTINCT DIST_NAME) as DIST_NAME_EN FROM `m_district` join m_ac on m_ac.DIST_NO_HDQTR = m_district.DIST_NO and m_ac.ST_CODE = m_district.ST_CODE where
m_ac.ST_CODE = '$st_code' and m_pc.PC_NO = $pc_no ORDER BY m_district.DIST_NAME ASC");
	
		/* return DB::table('m_district AS dpm')
                    ->select('mac.AC_NO','dpm.DIST_NAME as DIST_NAME_EN','mac.AC_NAME','mac.AC_TYPE')
                  ->join('m_ac As mac', function($join){
					  $join->on('dpm.DIST_NO','mac.DIST_NO_HDQTR')
					      ->on('dpm.ST_CODE','mac.ST_CODE');
				  })
                  ->where('dpm.ST_CODE',$st_code)
                  ->where('mac.AC_NO',$ac_no)
                  ->first(); */
				  
	}

    public function GetHash($input, $key) {
    try{
        $hash = hash('sha512', $input.$key);
        return $hash;
      } catch (Exception $ex) {  
               return Redirect('/internalerror')->with('error', 'Internal Server Error');
            }
    }

    public function SearchEpic(Request $request){
      if ($request->ajax()){
        if( !empty($request->get('epic_no')) && !empty($request)){
          $epic_no = $request->get('epic_no');
          $key = 'ABCD1234#123521GISTECIKEY';
          $pass_key = $this->GetHash($epic_no, $key);
          $pass_keyNew = $pass_key;
          $search_type = "epic";
          $method = "GET";
        
          $header = array(
              "cache-control"=>"no-cache",
              "content-type"=>"application/json",
          );

          $url = 'https://electoralsearch.in/api/search?passKey='.$pass_keyNew.'&search_type='.$search_type.'&epic_no='.$epic_no.'';

          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_TIMEOUT, 5000000000000);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000000000000);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $data = curl_exec($ch);
          curl_close($ch);
          $jsonResponse = json_decode($data);
          if (empty($jsonResponse->response->docs)) {
            $msg = "Invalid EPIC Number";
            return response()->json(['error'=>true, 'status' =>401,'result'=>'','msg'=>$msg]);
          } else {
            return response()->json(['error'=>false, 'status'=>$this->successStatus,'result'=>$jsonResponse->response->docs[0]]);
          }
        }
      }else{
        $msg = "Invalid EPIC Number";
        return response()->json(['error'=>true, 'status' =>401,'result'=>'','msg'=>$msg]);
      }
    }

    public function AffPoliticalParty(Request $request){
      if ($request->ajax()){
        $party_type = base64_decode($request->party_type);
        $getParty = MParty::where('PARTYTYPE',$party_type)->get();
        if(!empty($getParty)):
          return response()->json(['error'=>false, 'status'=>$this->successStatus,'result'=>$getParty]);
        else:
          return response()->json(['error'=>true, 'status'=>500]);
        endif;

      }
    }

    public function CheckMobileNo(Request $request){
      if ($request->ajax()){
        $mobile_no = base64_decode($request->mobile_no);
        $getTblId = base64_decode($request->getTblId);
        
        $user_reg_otp = $this->generate_otp();
       // $otp = "The verification code for Affidavit is ".$user_reg_otp." Kindly use this code to verify your mobile";
		
		
		$message = "Dear Sir/Madam, your OTP is ".$user_reg_otp." for ECI Candidate Portal. Please enter the OTP to proceed.Do not share this OTP Team ECI.";
              //$this->sendmessage($mobile,$message);
        SmsgatewayHelper::gupshup($mobile_no,$message);
		
		
		
        //$this->sendotp($mobile_no,$otp);

        $saveData = AffCandDetail::where('affidavit_id',$getTblId)->first();
        $saveData->phoneno_1  = $mobile_no;
        $saveData->otp        = $user_reg_otp;
        $saveData->save();
        if(!empty($saveData)):
          $msg = 'OTP sent to your '. $mobile_no .' mobile no.';
          return response()->json(['error'=>false, 'status'=>$this->successStatus,'msg'=>$msg]);
        else:
          $msg = "Something is wrong Please try again.";
          return response()->json(['error'=>false, 'status'=>$this->successStatus,'msg'=>$msg]);
        endif;

        //$getResult = AffCandDetail::select('otp','phoneno_1','updated_at')->first();
      }
    }

    public function verifyOTP(Request $request){
      if ($request->ajax()){
        $mobile_no = base64_decode($request->mobile_number);
        $otpbtn = base64_decode($request->otpbtn);
        // dd($otpbtn);

        $getResult = AffCandDetail::select('otp','phoneno_1','updated_at')->where('phoneno_1',$mobile_no)->first();
        // dd($getResult);
        $currentTime = Carbon::now();
        $diff = $currentTime->diffInSeconds($getResult->updated_at);
          if($diff>=60){
            $msg = 'Your otp ' .$otpbtn. ' has been expired. Please try again';
            return response()->json(['error'=>true, 'status' =>$this->expireOPT,'msg'=>$msg]);
          }

        if($getResult->otp == $otpbtn && $getResult->phoneno_1 == $mobile_no){
            AffCandDetail::where(['phoneno_1'=>$mobile_no])->update(['mobile_verify_status'=>1,'otp'=>null]);
            $msg = 'Your mobile no has been successfully verified';
            return response()->json(['error'=>false, 'status' =>$this->successStatus,'msg'=>$msg]);
        } elseif($getResult->otp != $otpbtn){
            $msg = 'otp not matched';
            return response()->json(['error'=>true, 'status' =>402,'msg'=>$msg]);
        }else {
            $msg = 'We could not verify your phone. Please try again.';
            return response()->json(['error'=>true, 'status' =>401,'msg'=>$msg]);
        }
      }
    }

    public function CheckEmailAddress(Request $request){
      if($request->ajax()){
        $email_address = base64_decode($request->email_address);
        $getTblId = base64_decode($request->getTblId);

        $user_reg_otp = $this->generate_otp();
        //$otp = "Your OTP is - " . $user_reg_otp;
        //$this->sendotp($mobile_no,$otp);
        $messages = $user_reg_otp;
        $email = $email_address;
        $subject = "E-Mail verification code for Affidavit";
        $data = array('email'=>$email,'subject'=>$subject,'content' => $messages);
        Mail::send('affidavit.emails.email_verify', ['email' => $email,'content' => $messages], function ($message) use($data) {
            $message->from('no-reply@eci.gov.in', 'Election Commission of India');
            $message->to($data['email'])->subject($data['subject']);
        });

        $saveData = AffCandDetail::where('affidavit_id',$getTblId)->first();
        $saveData->emailid    = $email_address;
        $saveData->otp        = $user_reg_otp;
        $saveData->save();
        if(!empty($saveData)):
          $msg = 'OTP sent to your '. $email .' Email Id.';
          return response()->json(['error'=>false, 'status'=>$this->successStatus,'msg'=>$msg]);
        else:
          $msg = "Something is wrong Please try again.";
          return response()->json(['error'=>false, 'status'=>$this->successStatus,'msg'=>$msg]);
        endif;
      }
    }

    public function VerifyOTPEmailId(Request $request){
      if ($request->ajax()){
        $email_address = base64_decode($request->email_address);
        $otpbtn = base64_decode($request->otp_emailid);

        $getResult = AffCandDetail::select('otp','emailid','updated_at')->where('emailid',$email_address)->first();

        $currentTime = Carbon::now();
        $diff = $currentTime->diffInSeconds($getResult->updated_at);
          if($diff>=60){
            $msg = 'Your otp ' .$otpbtn. ' has been expired. Please try again';
            return response()->json(['error'=>true, 'status' =>$this->expireOPT,'msg'=>$msg]);
          }

        if($getResult->otp == $otpbtn && $getResult->emailid == $email_address){
            AffCandDetail::where(['emailid'=>$email_address])->update(['email_verify_status'=>1,'otp'=>null]);
            $msg = 'Your Email Id has been successfully verified';
            return response()->json(['error'=>false, 'status' =>$this->successStatus,'msg'=>$msg]);
        } elseif($getResult->otp != $otpbtn){
            $msg = 'otp not matched';
            return response()->json(['error'=>true, 'status' =>402,'msg'=>$msg]);
        } else {
            $msg = 'We could not verify your Email Id. Please try again.';
            return response()->json(['error'=>true, 'status' =>401,'msg'=>$msg]);
        }
      }
    }
}
