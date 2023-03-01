<?php

namespace App\models\Expenditure;

use Illuminate\Database\Eloquent\Model;
use DB;

class ExpSchModel extends Model
{
    protected $table ="expenditure_schedule_1";
    protected $fillable = ['candidate_id']; //only the field names inside the array can be mass-assign

    public function GetSch1Data($userID)
	{
		return DB::table("expenditure_schedule_1")->select('expenditure_schedule_1.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch2Data($userID)
	{
		return DB::table("expenditure_schedule_2")->select('expenditure_schedule_2.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch3Data($userID)
	{
		return DB::table("expenditure_schedule_3")->select('expenditure_schedule_3.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	
	public function GetSch4Data($userID)
	{
		return DB::table("expenditure_schedule_4")->select('expenditure_schedule_4.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch4aData($userID)
	{
		return DB::table("expenditure_schedule_4a")->select('expenditure_schedule_4a.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch5Data($userID)
	{
		return DB::table("expenditure_schedule_5")->select('expenditure_schedule_5.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	
	public function GetSch6Data($userID)
	{
		return DB::table("expenditure_schedule_6")->select('expenditure_schedule_6.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch7Data($userID)
	{
		return DB::table("expenditure_schedule_7")->select('expenditure_schedule_7.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch8Data($userID)
	{
		return DB::table("expenditure_schedule_8")->select('expenditure_schedule_8.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch9Data($userID)
	{
		return DB::table("expenditure_schedule_9")->select('expenditure_schedule_9.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetSch10Data($userID)
	{
		return DB::table("expenditure_schedule_10")->select('expenditure_schedule_10.*')->where('candidate_id',$userID)->get()->toArray();
	}
	
	public function GetAnnuxureData($userID)
	{
		return DB::table("expenditure_annexure_e2")->select('expenditure_annexure_e2.*')->where('candidate_id',$userID)->get()->toArray();
	}
}
