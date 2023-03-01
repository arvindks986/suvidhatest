<?php

namespace App;

use Illuminate\Database\Eloquent\Model; //ElectiondetailsMaster
use DB;


class commonModel extends Model
{
	public function insertData($table, $data)
	{
		$add = DB::table($table)->insert($data);
		return $add;
	}
	public function selectAll($table, $order, $orderby)
	{
		$get = DB::table($table)->select('*')->orderBy($order, $orderby)->get();;
		return ($get);
	}
	public function selectone($table, $field, $fieldvalue)
	{
		$g = DB::table($table)->select('*')->where($field, $fieldvalue)->first();

		return ($g);
	}
	public function updatedata($table, $field, $fieldvalue, $ndata)
	{
		$g = DB::table($table)->where($field, $fieldvalue)->update($ndata);

		return ($g);
	}
	public function removerecord($table, $field, $fieldvalue)
	{
		$g = DB::table($table)->where($field, $fieldvalue)->delete();
		return ($g);
	}
	public function countrecords($table, $field, $fieldvalue)
	{
		$cnt = DB::table($table)->select('*')->where($field, $fieldvalue)->get()->count();
		return ($cnt);
	}
	public function getuserbyuserid($uid)
	{
		$data = DB::table('officer_master')->where('officer_id', $uid)->first();
		return $data;
	}
	public function getunewserbyuserid($uid)
	{
		$data = DB::table('officer_login')->where('id', $uid)->first();
		return $data;
	}
	public function getroleprevilagebyroleid($roleid)
	{
		$data = DB::table('role_previlage')->where('role_id', $roleid)->get();
		return $data;
	}

	public function getallmodule()
	{
		$data = DB::table('module_master')->get();
		return $data;
	}

	public function pass_encrypt($string)
	{

		return base64_encode($string);
	}

	public function pass_decrypt($string)
	{

		$string =  explode("(Sb$|||@", base64_decode($string));
		$string =  explode("|||@", base64_decode($string[1]));
		return $string[1];
	}
	public function getallecction()
	{
		$get = DB::table('election_master')->get();
		return ($get);
	}
	public function getecctionBYid($id)
	{
		$r = DB::table('election_master')->where('election_id', $id)->first();
		return ($r);
	}
	public function getallschedule()
	{
		$get = DB::table('m_schedule')->select('*')->where('CURRENTELECTION', 'Y')->get();
		return ($get);
	}
	public function getschedulebyid($id)
	{
		$get = DB::table('m_schedule')->select('*')->where('CURRENTELECTION', 'Y')->where('SCHEDULEID', $id)->first();
		return ($get);
	}

	public function getallstate()
	{
		$g = DB::table('m_state')->orderBy('ST_CODE', 'ASC')->get();
		return ($g);
	}
	public function getstatebystatecode($st)
	{
		$g = DB::table('m_state')->where('ST_CODE', $st)->first();
		return ($g);
	}
	public function getalldistrictbystate($st)
	{
		$g = DB::table('m_district')->where('ST_CODE', $st)->get();
		return ($g);
	}
	public function getdistrictbydistrictno($st, $disno)
	{
		$g = DB::table('m_district')->where('ST_CODE', $st)->where('DIST_NO', $disno)->first();
		return ($g);
	}
	public function getacbystate($st)
	{
		$g = DB::table('m_pc')->where('ST_CODE', $st)->orderBy('PC_NO', 'ASC')->get();
		
		return ($g);
	}
	public function getacbyacno($st, $acno)
	{
		$g = DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $acno)->first();
		return ($g);
	}
	public function getallacbypcno($st, $pcno)
	{
		$g = DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $pcno)->first();
		return ($g);
	}
	public function getpcbystate($st)
	{
		$g = DB::table('m_pc')->where('ST_CODE', $st)->get();
		return ($g);
	}
	public function getpcbypcno($st, $pcno)
	{
		$g = DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $pcno)->first();
		return ($g);
	}
	public function getpcname($st, $pcno)
	{
		$g = DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $pcno)->first();
		return ($g);
	}

	function getAcByst($stcode, $disttno)
	{
		$getAclist = DB::table('m_ac')->where('ST_CODE', $stcode)->where('DIST_NO_HDQTR', $disttno)->orderBy('AC_NO', 'asc')->get();
		return $getAclist;
	}
	public function getacname($st, $acno)
	{
		$g = DB::table('m_pc')->where('ST_CODE', $st)->where('PC_NO', $acno)->first();
		return ($g);
	}
	public function getparty($party_id)
	{
		$g = DB::table('m_party')->where('CCODE', $party_id)->first();
		return ($g);
	}
	public function getsymbol($sid)
	{
		$g = DB::table('m_symbol')->where('SYMBOL_NO', $sid)->first();
		return ($g);
	}
	public function getelectionlist($st, $acno, $ctype)
	{
		$g = DB::table('m_election_details')->where('ST_CODE', $st)->where('CONST_TYPE', strtoupper($ctype))->where('CONST_NO', $acno)->get();

		return ($g);
	}
	public function getcurrentelectiongroup($st)
	{
		$list = DB::table('m_cur_elec')
			->select('ConstType', 'ST_CODE', 'ELECTION_ID')->where('ST_CODE', $st)
			->groupBy('ConstType', 'ST_CODE', 'ELECTION_ID')->get();
		return $list;
	}
	public function getecurrentelection($st, $ctype)
	{
		$g = DB::table('m_cur_elec')->where('ST_CODE', $st)->where('ConstType', strtoupper($ctype))->get();
		return ($g);
	}
	function election_details_cons($st_code, $consno, $ctype, $offcer_level = '')
	{
		if ($offcer_level == '')
			$rec = DB::table('m_election_details')->where('ST_CODE', $st_code)->where('CONST_NO', $consno)->where('CONST_TYPE', strtoupper($ctype))->first();
		elseif ($offcer_level == "CEO")
			$rec = DB::table('m_cur_elec')->where('ST_CODE', $st_code)->get();
		//dd($rec);
		return $rec;
	}
	function Audit_log_data($app_id, $user_id, $table_name, $id, $column_name, $old_value, $new_value, $ipaddress, $procedure_name, $trigger_name, $event_type, $event_status, $create_date)
	{
		$n_data = array(
			'app_id' => $app_id, 'user_id' => $user_id, 'table_name' => $table_name, 'id' => $id, 'column_name' => $column_name,		'old_value' => $old_value, 'new_value' => $new_value, 'ipaddress' => $ipaddress, 'procedure_name' => $procedure_name,
			'trigger_name' => $trigger_name, 'event_type' => $event_type, 'event_status' => $event_status, 'create_date' => $create_date
		);
		$add = DB::table('audit_log_table')->insert($n_data);
		return $add;
	}
	public function currentelectiondetails($st_code, $cons_type, $election_id)
	{
		$req = DB::table('m_election_details')->where('ST_CODE', $st_code)->where('CONST_TYPE', strtoupper($cons_type))->where('ELECTION_ID', $election_id)->get();

		return ($req);
	}
	public function allstatus()
	{
		$get = DB::table('m_status')->select('*')->orderBy('id', 'ASC')->get();;
		return ($get);
	}
	public function getnameBystatusid($id)
	{
		$g = DB::table('m_status')->select('status')->where('id', $id)->first();
		return ($g->status);
	}
	function election_details($st_code, $acno, $pcno, $id, $offcer_level = '')
	{
		if ($offcer_level == 'ECI' || $offcer_level == 'ECI-OFFICE') {
			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.st_code')
				->where('officer_login.id', '=', $id)->where('m_election_details.CONST_TYPE', '=', 'PC')
				->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->get();

			return $list;
		}
		if ($offcer_level == 'CEO' || $offcer_level == 'CEO-OFFICE') {

			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.st_code')
				->where('officer_login.id', '=', $id)->where('m_election_details.ST_CODE', '=', $st_code)
				->where('m_election_details.CONST_TYPE', '=', 'PC')
				->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->get();
			return $list;
		}

		if ($offcer_level == 'DEO' || $offcer_level == 'DEO-OFFICE') {
			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.st_code')
				->where('officer_login.id', '=', $id)->where('m_election_details.ST_CODE', '=', $st_code)
				->where('m_election_details.CONST_TYPE', '=', 'PC') //->where('officer_login.dist_no','=',$dist_no)
				->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->get();
			return $list;
		} elseif ($offcer_level == 'PC' || $offcer_level == 'ROPCOFFICE') {
			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.ST_CODE', 'm_election_details.CONST_NO', '=', 'officer_login.PC_NO')
				->where('m_election_details.CONST_TYPE', '=', 'PC')

				->where('m_election_details.ST_CODE', '=', $st_code)
				->where('m_election_details.CONST_NO', '=', $pcno)
				->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->first();
			return $list;
		} // end officer level
		if ($offcer_level == 'AC' || $offcer_level == 'ROACOFFICE') {
			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.st_code', 'm_election_details.CONST_NO', '=', 'officer_login.ac_no')
				->where('m_election_details.CONST_TYPE', '=', 'AC')
				->where('officer_login.id', '=', $id)
				->where('m_election_details.ST_CODE', '=', $st_code)
				->where('m_election_details.CONST_NO', '=', $acno)->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->first();
			if (isset($list)) {
				return $list;
			} else {
				$list = DB::table('m_election_details')
					->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.ST_CODE', 'm_election_details.CONST_NO', '=', 'officer_login.PC_NO')
					->where('m_election_details.CONST_TYPE', '=', 'PC')
					->where('officer_login.id', '=', $id)
					->where('m_election_details.ST_CODE', '=', $st_code)
					->where('m_election_details.CONST_NO', '=', $pcno)->groupBy('m_election_details.ScheduleID')
					->select('m_election_details.*')->first();
				return $list;
			}
		} // end officer level

	}



 function election_detailsac($st_code, $pcno, $dist_no, $id, $offcer_level = '')
	{
		if ($offcer_level == 'ECI' || $offcer_level == 'ECI-OFFICE') {
			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.st_code')
				->where('officer_login.id', '=', $id)->where('m_election_details.CONST_TYPE', '=', 'PC')
				->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->get();

			return $list;
		}
		if ($offcer_level == 'CEO' || $offcer_level == 'CEO-OFFICE') {

			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.st_code')
				->where('officer_login.id', '=', $id)->where('m_election_details.ST_CODE', '=', $st_code)
				->where('m_election_details.CONST_TYPE', '=', 'PC')
				->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->get();
			return $list;
		}


		if ($offcer_level == 'DEO' || $offcer_level == 'DEO-OFFICE' || $offcer_level == 'PCI') {
			$list = DB::table('m_election_details')
				->Join(
					"officer_login",
					[
						['m_election_details.ST_CODE', '=', 'officer_login.st_code'], ['m_election_details.CONST_NO', '=', 'officer_login.ac_no']
					]
				)

				->where('m_election_details.ST_CODE', '=', $st_code)
				->where('m_election_details.CONST_TYPE', '=', 'PC')->where('officer_login.dist_no', '=', $dist_no)
				->groupBy('m_election_details.ScheduleID')
				->select('m_election_details.*')->get();

			return $list;
		}
		if ($offcer_level == 'PC' ) {
			$list = DB::table('m_election_details')
				->leftjoin('officer_login', 'm_election_details.ST_CODE', '=', 'officer_login.st_code', 'm_election_details.CONST_NO', '=', 'officer_login.ac_no')
				->where('m_election_details.CONST_TYPE', '=', 'PC')
				->where('officer_login.id', '=', $id)
				->where('m_election_details.ST_CODE', '=', $st_code)
				->where('m_election_details.CONST_NO', '=', $pcno)
				->select('m_election_details.*')->first();
			return $list;
		}
	}




 function getAcByst_test($st)
 {


 	$g = DB::table('m_ac')->where('ST_CODE', $st)->orderBy('AC_NO', 'ASC')->get();
		
		return ($g);
 }




 
       public function verifycandidateprofile($name,$fname,$stcode,$dist,$ac)
	{
		$list = DB::table('candidate_personal_detail')
				
				 ->where('candidate_personal_detail.cand_name', 'LIKE', '%'.$name.'%')
				 ->where('candidate_personal_detail.candidate_father_name', 'LIKE', '%'.$fname.'%')
				->where('candidate_personal_detail.candidate_residence_stcode', '=', $stcode)
				->where('candidate_personal_detail.candidate_residence_districtno', '=', $dist)
				->where('candidate_personal_detail.candidate_residence_acno', '=', $ac)
				
				->select('cand_name','candidate_father_name')->first(); 

			/*	$list = DB::table('candidate_nomination_detail')
            ->leftjoin('candidate_personal_detail', 'candidate_nomination_detail.candidate_id', '=', 'candidate_personal_detail.candidate_id') 
                ->where('candidate_nomination_detail.st_code', $stcode)
                 ->where('candidate_nomination_detail.district_no', $dist)
                ->where('candidate_nomination_detail.ac_no', $ac)
               ->where('candidate_personal_detail.cand_name', 'LIKE', '%'.$name.'%')
				 ->where('candidate_personal_detail.candidate_father_name', 'LIKE', '%'.$fname.'%')
             ->where('candidate_nomination_detail.symbol_id','<>','200')
                ->where('candidate_nomination_detail.application_status','<>','11')
                ->first(); */

				return json_encode($list);
	}







}
