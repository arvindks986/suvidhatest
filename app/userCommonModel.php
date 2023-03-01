<?php
namespace App;
use Illuminate\Database\Eloquent\Model; //ElectiondetailsMaster
use DB;
class userCommonModel extends Model 
{    
	public function getelectionState($etype)
	{  
		$g = DB::table('m_election_details')->where('CONST_TYPE',strtoupper($etype))->groupBy('ST_CODE')->get();
		return ($g);
	}
	public function getelectionlist($st,$ctype)
	{  
		$g = DB::table('m_election_details')->where('ST_CODE',$st)->where('CONST_TYPE',strtoupper($ctype))->groupBy('ELECTION_TYPE')->get();
		return ($g);
	}
	public function currentelectiondetails($st_code,$cons_type,$electionType)
	{
		$req= DB::table('m_election_details')->where('ST_CODE',$st_code)->where('CONST_TYPE',strtoupper($cons_type))->where('ELECTION_TYPE',$electionType)->get();
		return ($req);
	}
	public function getacbyacno($st,$acno)
	{
		$g = DB::table('m_ac')->where('ST_CODE',$st)->where('AC_NO',$acno)->first();
		return ($g);
	}
	public function currentelectionschedule($st_code,$cons_type,$electionType,$ac)
	{
		$req= DB::table('m_election_details')->where('ST_CODE',$st_code)->where('CONST_TYPE',strtoupper($cons_type))->where('ELECTION_TYPE',$electionType)->where('CONST_NO',$ac)->get();
		return ($req);
	}
	public function getSchedule($scheduleid){
		$req = DB::table('m_schedule')->where('SCHEDULEID',$scheduleid)->first();
		return ($req);
	}
}
