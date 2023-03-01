<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\commonModel;
use App\adminmodel\ECIModel;

use App\Classes\xssClean;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

ini_set("memory_limit", "1500M");
set_time_limit('2400');
ini_set("pcre.backtrack_limit", "10000000");


class PublishTestController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->commonModel = new commonModel();
		$this->ECIModel = new ECIModel();
		$this->xssClean = new xssClean;
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */

	/*     protected function guard(){
        return Auth::guard();
    } */


	public function update_turnout_index(Request $request)
	{

		Config::set('database.default', "suivhdalivetest");
		DB::reconnect('suivhdalivetest');

		$scheduleid = $request->scheduleid;
		$scheduleid = 1;

		$dataArr = DB::table('pd_scheduledetail')
			//->where('scheduleid',$scheduleid)
			->get();

		foreach ($dataArr as $raw) {
			DB::table('pd_scheduledetail_publish')
				->where('st_code', $raw->st_code)
				->where('ac_no', $raw->ac_no)
				->where('pc_no', $raw->pc_no)
				->where('scheduleid', $scheduleid)
				->update([
					'est_turnout_round1' 	=> $raw->est_turnout_round1,
					'est_turnout_round2' 	=> $raw->est_turnout_round2,
					'est_turnout_round3' 	=> $raw->est_turnout_round3,
					'est_turnout_round4' 	=> $raw->est_turnout_round4,
					'est_turnout_round5' 	=> $raw->est_turnout_round5,
					'est_turnout_total' 	=> $raw->est_turnout_total,
					'close_of_poll' 		=> $raw->close_of_poll,
					'electors_total' 		=> $raw->electors_total,
					'est_voters' 			=> $raw->est_voters
				]);
		}

		echo 'Updation done';
	}



	public function show_turnout_index(Request $request)
	{

		Config::set('database.default', "suivhdalivetest");
		DB::reconnect('suivhdalivetest');

		$results_data = [];
		$scheduleid = 1;
		$results_data = DB::table('pd_scheduledetail as pds')
			->join('pd_scheduledetail_publish as pds_temp', [['pds.st_code', '=', 'pds_temp.st_code'], ['pds.ac_no', '=', 'pds_temp.ac_no'], ['pds.pc_no', '=', 'pds_temp.pc_no']])
			->select('pds.st_code', 'pds.ac_no', 'pds.scheduleid', 'pds.est_turnout_total', 'pds.electors_total', 'pds.est_voters', 'pds_temp.est_turnout_total as est_turnout_total_temp', 'pds_temp.electors_total as electors_total_temp', 'pds_temp.est_voters as est_voters_temp')
			//->where('pds.scheduleid', $scheduleid)
			->groupBy('pds.st_code', 'pds.ac_no')
			->get()->toArray();

		return view('admin.turnout.update_turnout.update_trunout_app', ['results_data' => $results_data]);
	}
}  // end class