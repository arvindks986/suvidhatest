<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
class CEOApplicationModel extends Model
{
	protected $connection = 'mysql2';
	public function getAdvertisementDetail($stcode){
		//DB::enableQueryLog();
		$getAdvertisementDetail = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'applicant_kyc.id', '=', 'application_details.applicant_id')
            ->select('advertisement_details.*', 'applicant_kyc.update_by')
			->where('applicant_kyc.state_id',$stcode)
			->where('advertisement_details.finalize_by_applicant',1)
			->where('advertisement_details.ad_status',3)
			->where('application_details.applicant_type_id',1)
			->distinct('advertisement_details.applicant_detail_id')
            ->get();
		//$queries = DB::getQueryLog();
		//dd($queries);
		return ($getAdvertisementDetail);
	}
	public function getAdStatusFinalized($stcode){
		//DB::enableQueryLog();
		$getAdvertisementDetail = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'applicant_kyc.id', '=', 'application_details.applicant_id')
            ->select('advertisement_details.*', 'applicant_kyc.update_by')
			->where('applicant_kyc.state_id',$stcode)
			->where('advertisement_details.finalize_by_applicant',1)
			->where('advertisement_details.ad_status','>',3)
			->where('application_details.applicant_type_id',1)
			->distinct('advertisement_details.applicant_detail_id')
            ->get();
		//$queries = DB::getQueryLog();
		//dd($queries);
		return ($getAdvertisementDetail);
	}
	public function getChannelNamebyId($channelId){
		$getChannelNamebyId = DB::connection('mysql2')->table('channel_master')->where('channel_id',$channelId)->first();
		return ($getChannelNamebyId);
	}
	public function getChannelTypebyId($channelTypeId){
		$getChannelTypebyId = DB::connection('mysql2')->table('channel_type')->where('id',$channelTypeId)->first();
		return ($getChannelTypebyId);
	}
	public function getAssignedById($applicantId){
		$getAssignedById = DB::connection('mysql2')->table('applicant_kyc')->where('id',$applicantId)->first();
		return ($getAssignedById);
	}
	public function getApplicationStatus($statusid){
		$getApplicationStatus = DB::connection('mysql2')->table('m_status')->where('id',$statusid)->first();
		return ($getApplicationStatus);
	}
	public function getAllStatus(){
		$getAllStatus = DB::connection('mysql2')->table('m_status')->where('id','>', 3)->orderBy('id', 'desc')->get();
		return ($getAllStatus);
	}
	public function getApplicationDetails($applicantid){
        $getApplicationDetails = DB::connection('mysql2')->table('applicant_kyc')
           ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
           ->join('application_details', 'application_details.id', '=', 'advertisement_details.applicant_detail_id')
           ->select('advertisement_details.*', 'applicant_kyc.*', 'application_details.*')
            ->where('application_details.id',$applicantid)
            ->where('advertisement_details.finalize_by_applicant',1)
           ->get();
        return ($getApplicationDetails);
    }
	public function getStateDetailbyStId($stcode){
		$getStatebyStId = DB::connection('mysql2')->table('m_state')->where('ST_CODE',$stcode)->first();
		return ($getStatebyStId);
	}
	public function getDisttDetailbyStId($stcode,$disttId){
		$getDisttDetailbyStId = DB::connection('mysql2')->table('m_district')->where('ST_CODE',$stcode)->where('DIST_NO',$disttId)->first();
		return ($getDisttDetailbyStId);
	}
	public function getAllPendingListByPolitical($stcode){
		$getAllPendingListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'applicant_kyc.id', '=', 'application_details.applicant_id')
            ->select('advertisement_details.*', 'applicant_kyc.update_by', 'application_details.applicant_type_id', 'application_details.political_party_id','application_details.political_party_type_id')
			->where('applicant_kyc.state_id',$stcode)
			->where('advertisement_details.finalize_by_applicant',1)
			->where('advertisement_details.ad_status','=',3)
			->where('application_details.applicant_type_id',1)
			->distinct('advertisement_details.applicant_detail_id')
            ->get();
		return ($getAllPendingListByPolitical);
	}
	public function getAllListByPolitical($stcode){
		$getAllListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'applicant_kyc.id', '=', 'application_details.applicant_id')
            ->select('advertisement_details.*', 'applicant_kyc.update_by', 'application_details.applicant_type_id', 'application_details.political_party_id','application_details.political_party_type_id')
			->where('applicant_kyc.state_id',$stcode)
			->where('advertisement_details.finalize_by_applicant',1)
			->where('application_details.applicant_type_id',1)
			->distinct('advertisement_details.applicant_detail_id')
            ->get();
		return ($getAllListByPolitical);
	}
	public function getAllPendListByPolByAppId($stcode,$applicantId){
		$getAllPendingListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id')
			->where('applicant_kyc.id',$applicantId)
			->where('advertisement_details.finalize_by_applicant',1)
			->where('advertisement_details.ad_status','=',3)
			->where('application_details.applicant_type_id',1)			
			->where('applicant_kyc.state_id',$stcode)			
            ->first();
		return ($getAllPendingListByPolitical);
	}
	
	public function getPartyDetailsbyId($partyId,$partyType){
		$getPartyDetailsbyId = DB::connection('mysql2')->table('m_party')->where('CCODE',$partyId)->where('PARTYTYPE',$partyType)->first();
		return ($getPartyDetailsbyId);
	}
	public function getRefDetailbyId($referenceNo){
		$getRefDetailbyId = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id')
			->where('advertisement_details.finalize_by_applicant',1)
			->where('advertisement_details.ad_status','=',3)
			->where('application_details.applicant_type_id',1)	
			->where('advertisement_details.reference_no','like',"%$referenceNo%")
			->get();
        return ($getRefDetailbyId);
	}
	


	
	///////////update by vinay for api/////////
	public function getAppdetailsById($applicantId){
		$getAllPendingListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			 
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('applicant_kyc.id',$applicantId)
			->where('advertisement_details.finalize_by_applicant',1)
			->where('advertisement_details.ad_status','=',3)
			->where('application_details.applicant_type_id',1)			
						
            ->first();
		return ($getAllPendingListByPolitical);
	}


	public function getAppdetailsByformId($formId){
		$getAllPendingListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			 
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('advertisement_details.reference_no',$formId)
			// ->where('advertisement_details.finalize_by_applicant',1)
			// ->where('advertisement_details.ad_status','=',3)
			// ->where('application_details.applicant_type_id',1)			
						
            ->first();
		return ($getAllPendingListByPolitical);
	}
	
	
	public function getAppdetailsBycurrent_Date(){ //die("123");

	    //DB::enableQueryLog();
//dd(DB::getQueryLog());
	      
          
          $getAllPendingListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			->join('officer_login', 'advertisement_details.approved_by', '=', 'officer_login.id')
			 
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status'  , 'officer_login.designation as approved_byk','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('advertisement_details.date_of_telecast_to', '>' ,\Carbon\Carbon::now())
			->where('advertisement_details.finalize_by_applicant',1)
			->where('advertisement_details.finalize_by_applicant',1)
			//->where('advertisement_details.ad_status','=',6)
			->where('officer_login.id', 1)			
						
            ->first();
         // dd(DB::getQueryLog());
            
          return ($getAllPendingListByPolitical);
	}

	public function getAppdetailsByQRcode($qrcode){
		$getAllPendingListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			
			 ->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('advertisement_details.qrcode',$qrcode)
			->where('advertisement_details.finalize_by_applicant',1)
			//->where('advertisement_details.ad_status','=',6)
			->where('application_details.applicant_type_id',1)			
						
            ->first();
		return ($getAllPendingListByPolitical);
	}


	
	
	public function getAppdetailsBySearch($request){
	$getAllPendingListByPolitical = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			
			 ->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('applicant_kyc.name',$request->get('applicant_name'))
			->where('application_details.state_id',$request->get('state'))
			->where('application_details.distt_id',$request->get('district'))
			->where('advertisement_details.date_of_certification',$request->get('approval_date'))
			->where('advertisement_details.date_of_telecast_from', '<' , $request->get('valid_from'))
			->where('advertisement_details.date_of_telecast_to', '>' ,$request->get('valid_to'))	
						
            ->first();
		return ($getAllPendingListByPolitical);
	}

public function candidateapplicationlist($request){
	$getAllapp = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			
			 ->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('applicant_kyc.mobileno',$request)
            ->get();
		return ($getAllapp);
	}


public function getCEOcurrentaddlist($state){
	$mytime = date('Y-m-d');
	$getAllapp = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			
			 ->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('application_details.state_id',$state)
			->where('advertisement_details.date_of_telecast_from', '<' , $mytime)
			->where('advertisement_details.date_of_telecast_to', '>' ,$mytime)
            ->get();
		return ($getAllapp);
	}


	public function getDEOcurrentaddlist($district){
		$mytime = date('Y-m-d');
	$getAllapp = DB::connection('mysql2')->table('applicant_kyc')
            ->join('advertisement_details', 'applicant_kyc.id', '=', 'advertisement_details.applicant_id')
            ->join('application_details', 'application_details.applicant_id', '=', 'advertisement_details.applicant_id')
			
			 ->join('m_status', 'advertisement_details.ad_status', '=', 'm_status.id')
			 ->join('m_district', 'application_details.distt_id', '=', 'm_district.DIST_NO')
            ->join('m_state', 'application_details.state_id', '=', 'm_state.ST_CODE')
            ->join('channel_master', 'advertisement_details.channel_id', '=', 'channel_master.channel_id')
            ->select('advertisement_details.*', 'applicant_kyc.*','applicant_kyc.address as kyadd', 'applicant_kyc.state_id as kyc_state_id', 'applicant_kyc.distt_id as kyc_distt_id', 'application_details.*','application_details.address as detailadd',
			'application_details.state_id as detailstate_id','application_details.distt_id as detaildistt_id','m_status.status as media_status','m_district.DIST_NAME as detail_distt_name','m_state.ST_NAME as detail_state_name','channel_master.channel_type_id as channel_type')
			->where('application_details.distt_id',$district)
			->where('advertisement_details.date_of_telecast_from', '<' , $mytime)
			->where('advertisement_details.date_of_telecast_to', '>' ,$mytime)
            ->get();
		return ($getAllapp);
	}


	public function getProfileOtp($Phone_no,$api_otp){


 	    //DB::enableQueryLog();

	        $getAllPendingListByPolitical = DB::connection('mysql2')->table('officer_login')
            ->join('m_state', 'officer_login.st_code', '=', 'm_state.ST_CODE')
            ->join('m_district', 'officer_login.dist_no', '=', 'm_district.row_id')
            ->join('m_ac', 'officer_login.ac_no', '=', 'm_ac.id')

            ->select('officer_login.*', 'm_state.ST_NAME as StateName' , 'm_district.DIST_NAME as DistrictName' , 'm_ac.AC_NAME as ACNAME')

			->where('officer_login.Phone_no',$Phone_no)
			->where('officer_login.api_otp',$api_otp)						
            ->first();

            //dd(DB::getQueryLog());

            //print_r($getAllPendingListByPolitical); die;
		return ($getAllPendingListByPolitical);
	}
	
	///////////update by vinay for api END/////////
}
