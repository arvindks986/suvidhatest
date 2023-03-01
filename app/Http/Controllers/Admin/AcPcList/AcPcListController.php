<?php

namespace App\Http\Controllers\Admin\AcPcList;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB, Validator, Config, Session;
use Illuminate\Support\Facades\Hash;
use App\models\Admin\StateModel;
use App\models\Admin\PcModel;
use App\models\Admin\AcModel;
use App\models\Admin\DistrictModel;

class AcPcListController extends Controller {
 
	protected $pcModel;
	protected $acModel;
	protected $stateModel;
	protected $districtodel;
	
	public function __construct(PcModel $pcModel, AcModel $acModel, StateModel $stateModel, DistrictModel $districtModel) {
		$this->pcModel = $pcModel;
		$this->acModel = $acModel;
		$this->stateModel = $stateModel;
		$this->districtModel = $districtModel;
		$this->middleware(function (Request $request, $next) {
			return $next($request);
		});
	}
	
	public function getAcPcList(Request $request){
		$data = array();
		$data['user_data'] = Auth::user();
		if ( $data['user_data']['role_id'] == 4 ){
			$getStates = $this->stateModel->select('*')->where('ST_CODE', '=', $data['user_data']['st_code'])->first();
		} else {
			$getStates = $this->stateModel->select('*')->orderBy('ST_NAME')->get();
		}
		$dbnm = \DB::connection()->getDatabaseName();
		$typ = ( ( $dbnm == 'suvidha_2022_06_e16' ) ? 'pc' : 'ac' );
		return view('admin.ac_pc.index', $data, compact($data))->with(array('states' => $getStates, 'currentUserRole' => $data['user_data']['role_id'], 'typ' => $typ));
	}

	public function fetchAcPcList(Request $request){
		$s_t = $request->input('s_t');
		$t_p = $request->input('t_p');
		if ( $s_t && $t_p ){
			if ( $s_t != 'all' ){
				if ( $t_p == 'ac' ){
					$data = $this->acModel->where('ST_CODE', '=', $s_t)->get();
				} else {
					$data = $this->pcModel->where('ST_CODE', '=', $s_t)->get();
				}
				$st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $s_t)->first();
				$data['DATA_COUNT'] = count($data);
				$data['ST_NAME'] = $st_nm['ST_NAME'];
				$data['SHORTNAME'] = $st_nm['SHORTNAME'];
				$data['ST_NAME_HI'] = $st_nm['ST_NAME_HI'];
				return $data;
			} else {
				if ( $t_p == 'ac' ){
					$data = $this->acModel->orderBy('ST_CODE', 'ASC')->get();
				} else {
					$data = $this->pcModel->orderBy('ST_CODE', 'ASC')->get();
				}
				foreach( $data as $i => $v ){
					$all_st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $v['ST_CODE'])->first();
					$v['ALL_ST_DETAILS'] = $all_st_nm;
				}
				$data['DATA_COUNT'] = count($data);
				$data['ST_NAME'] = '';
				$data['SHORTNAME'] = '';
				$data['ST_NAME_HI'] = '';
				return $data;
			}
		}
	}

	public function fetchAcsList(Request $request){
		$pc_no = $request->input('pc_no');
		$st_no = $request->input('st_no');
		$data=array();
		$data['user_data'] = Auth::user();
		if ( $pc_no ){
			$acs = $this->acModel->where('PC_NO', '=', $pc_no)->where('ST_CODE', '=', $st_no)->get();
		}
		$pcName = $this->getPcNameByNo($pc_no, $st_no);
		$acs['DATA_COUNT'] = count($acs);
		$acs['PC_NAME'] = $pcName['PC_NAME'];
		$st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $st_no)->first();
		$acs['ST_NAME'] = $st_nm['ST_NAME'];
		$acs['SHORTNAME'] = $st_nm['SHORTNAME'];
		$acs['ST_NAME_HI'] = $st_nm['ST_NAME_HI'];
		return $acs;
	}

	public function getPcNameByNo($pc_no, $st_no){
		if ( $pc_no ){
			$data = $this->pcModel->select('PC_NAME')->where('PC_NO', '=', $pc_no)->where('ST_CODE', '=', $st_no)->first();
			return $data;
		}
	}
	
	public function getAcPcMappingReport(Request $request){
		$url = [];
		$s_t = $request->input('s_t');
		$pc_no = ( ( $request->input('pc_no') ) ? $request->input('pc_no') : '' );
		$_token = $request->input('_token');
		if ( $s_t ){
			$url['link'] = url('/').'/eci/generate-acpc-mapping-report?_token='.$_token.'&s_t='.$s_t.'&pc_no='.$pc_no;
			return $url;
		}
	}
	
	public function downloadAcPcMappingReport($s_t='', $pc_no=''){
		$s_t = $_GET['s_t']; $pc_no = $_GET['pc_no'];
		if ( $s_t ){
			$st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $s_t)->first();
			$file = 'AcPc_Mapping_in_'. ( ( $s_t != 'all' ) ? $st_nm['ST_NAME'] : 'All_States' );
			$filename = $file."_".date("Y-m-d_H-i",time());
			$csv_output = "sep= |";
			$csv_output .= "\n";
			if ( $s_t != 'all' ){
				$csv_output .= 'S. No. | State Code | State Name | PC No. | PC Name | PC Type';
			} else {
				$csv_output .= 'State Code | State Name | PC No. | PC Name | PC Type';
			}
			$csv_output .= "\n";
			
			if ( $s_t != 'all' ){
				if ( $pc_no && $pc_no != '' ){
					$data = $this->pcModel->where('PC_NO', '=', $pc_no)->where('ST_CODE', '=', $s_t)->get();
				} else {
					$data = $this->pcModel->where('ST_CODE', '=', $s_t)->get();
				}
				$n = 1;
				foreach( $data as $k => $v ){
					if ( $v['ST_CODE'] ){
						$csv_output .= $n.'| '.$v['ST_CODE'].'| '.$st_nm['ST_NAME'].'| '.$v['PC_NO'].'| '.$v['PC_NAME'].'| '.$v['PC_TYPE'];
						$csv_output .= "\n";
						$allACsForPC = $this->getACsListByPC($v['ST_CODE'], $v['PC_NO']);
						if ( count ( $allACsForPC ) > 0 ){
							$csv_output .= "\n";
							$csv_output .= ' | | S. No. | AC No. | AC Name | AC Type';
							$csv_output .= "\n"; 
							$m = 1;
							foreach( $allACsForPC as $i => $j ){
								$csv_output .= ''.'| '.''.'| '.$m.'| '.$j['AC_NO'].'| '.$j['AC_NAME'].'| '.$j['AC_TYPE'];
								$csv_output .= "\n"; 
								$m++;
							}
						}
						$csv_output .= "\n"; 
					}
					$n++;
				}
			} else {
				$all_states = $this->stateModel->select('ST_NAME', 'ST_CODE')->get();
				foreach( $all_states as $k => $v ){
					$state_code = trim($v['ST_CODE']);
					$pcs[$state_code] = $this->pcModel->where('ST_CODE', '=', $v['ST_CODE'])->get(); 
				}
				foreach( $pcs as $i => $j ){
					if ( $i ){
						foreach( $j as $l => $m ){
							$st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $m['ST_CODE'])->first();
							$csv_output .= $m['ST_CODE'].'| '.$st_nm['ST_NAME'].'| '.$m['PC_NO'].'| '.$m['PC_NAME'].'| '.$m['PC_TYPE'];
							$csv_output .= "\n";
							$allACsForPC = $this->getACsListByPC($m['ST_CODE'], $m['PC_NO']);
							if ( count ( $allACsForPC ) > 0 ){
								$csv_output .= "\n";
								$csv_output .= ' | | S. No. | AC No. | AC Name | AC Type';
								$csv_output .= "\n"; 
								$b = 1;
								foreach( $allACsForPC as $n => $o ){
									$csv_output .= ''.'| '.''.'| '.$b.'| '.$o['AC_NO'].'| '.$o['AC_NAME'].'| '.$o['AC_TYPE'];
									$csv_output .= "\n"; 
									$b++;
								}
							}
							$csv_output .= "\n"; 
						}
					}
				}
			}
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=".$filename.".csv");
			header("Content-Transfer-Encoding: binary");
			$data = stripcslashes($csv_output); 
			echo $data;
			die;
		}
	}
	
	public function getACsListByPC($st_no = '', $pc_no = ''){
		$pc_no = $pc_no;
		$st_no = $st_no;
		$data=array();
		$data['user_data'] = Auth::user();
		if ( $pc_no ){
			$acs = $this->acModel->where('PC_NO', '=', $pc_no)->where('ST_CODE', '=', $st_no)->get();
		}
		return $acs;
	}
	
	public function getReport(Request $request){
		$url = [];
		$s_t = $request->input('s_t');
		$t_p = $request->input('t_p');
		$_token = $request->input('_token');
		if ( $s_t && $t_p ){
			$url['link'] = url('/').'/eci/generate-report?_token='.$_token.'&s_t='.$s_t.'&t_p='.$t_p;
			return $url;
		}
	}

	public function downloadReport($s_t='', $t_p=''){
		$s_t = $_GET['s_t']; $t_p = $_GET['t_p'];
		if ( $s_t && $t_p ){
			if ( $s_t != 'all' ){
				$st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $s_t)->first();
				$file = ( ( $t_p == 'ac' ) ? 'AC' : 'PC' ) . 's_in_'. $st_nm['ST_NAME'];
				$filename = $file."_".date("Y-m-d_H-i",time());
				$csv_output = "sep= |";
				$csv_output .= "\n";
				if ( $t_p == 'ac' ){
					$csv_output .= 'S. No. | State Code | State Name | AC No. | AC Name | AC Type';
				} else {
					$csv_output .= 'S. No. | State Code | State Name | PC No. | PC Name | PC Type';
				}
				$csv_output .= "\n";
				if ( $t_p == 'ac' ){
					$data = $this->acModel->where('ST_CODE', '=', $s_t)->get();
				} else {
					$data = $this->pcModel->where('ST_CODE', '=', $s_t)->get();
				}
			} else {
				$file = ( ( $t_p == 'ac' ) ? 'AC' : 'PC' ) . 's_in_All_States';
				$filename = $file."_".date("Y-m-d_H-i",time());
				$csv_output = "sep= |";
				$csv_output .= "\n";
				if ( $t_p == 'ac' ){
					$csv_output .= 'S. No. | State Code | State Name | AC No. | AC Name | AC Type';
				} else {
					$csv_output .= 'S. No. | State Code | State Name | PC No. | PC Name | PC Type';
				}
				$csv_output .= "\n";
				if ( $t_p == 'ac' ){
					$data = $this->acModel->orderBy('ST_CODE', 'ASC')->get();
				} else {
					$data = $this->pcModel->orderBy('ST_CODE', 'ASC')->get();
				}
			}
			$n = 1;
			foreach( $data as $k => $v ){
				if ( $v['ST_CODE'] ){
					$all_st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $v['ST_CODE'])->first();
					$v['ALL_ST_DETAILS'] = $all_st_nm;
					if ( $v['ALL_ST_DETAILS'] ){
						$allst_nm = $v['ALL_ST_DETAILS']['ST_NAME'];
					} else {
						$allst_nm = '';
					}
					if ( $t_p == 'ac' ){
						$csv_output .= $n.'| '.$v['ST_CODE'].'| '.( ( $s_t == 'all' ) ? $allst_nm : $st_nm['ST_NAME'] ).'| '.$v['AC_NO'].'| '.$v['AC_NAME'].'| '.$v['AC_TYPE'];
					} else {
						$csv_output .= $n.'| '.$v['ST_CODE'].'| '.( ( $s_t == 'all' ) ? $allst_nm : $st_nm['ST_NAME'] ).'| '.$v['PC_NO'].'| '.$v['PC_NAME'].'| '.$v['PC_TYPE'];
					}
					$csv_output .= "\n"; 
				}
				$n++;
			}
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=".$filename.".csv");
			header("Content-Transfer-Encoding: binary");
			$data = stripcslashes($csv_output);
			echo $data;
			die;
		}
	}

	public function getPcReport(Request $request){
		$url = [];
		$pc_no = $request->input('pc_no');
		$st_no = $request->input('st_no');
		$_token = $request->input('_token');
		if ( $pc_no && $st_no ){
			$url['link'] = url('/').'/eci/generate-pc-report?_token='.$_token.'&pc_no='.$pc_no.'&st_no='.$st_no;
			return $url;
		}
	}

	public function downloadPcReport($pc_no='', $st_no=''){
		$pc_no = $_GET['pc_no']; $st_no = $_GET['st_no'];
		if ( $pc_no && $st_no ){
			$acs = $this->acModel->where('PC_NO', '=', $pc_no)->where('ST_CODE', '=', $st_no)->get();
			$pcName = $this->getPcNameByNo($pc_no, $st_no);
			$pc_name = $pcName['PC_NAME'];
			$st_nm = $this->stateModel->select('ST_NAME', 'SHORTNAME', 'ST_NAME_HI')->where('ST_CODE', '=', $st_no)->first();

			$file = 'ACs_in_PC:'. $pc_name;
			$filename = $file."_".date("Y-m-d_H-i",time());

			$csv_output = "sep= |";
			$csv_output .= "\n";
			$csv_output .= 'S. No. | State | PC Name | PC No | AC No. | AC Name | AC Type';
			$csv_output .= "\n";
			$n = 1;
			foreach( $acs as $k => $v ){
				if ( $v['ST_CODE'] ){
					$csv_output .= $n.'| '.$st_nm['ST_NAME'].'| '.$pc_name.'| '.$v['PC_NO'].'| '.$v['AC_NO'].'| '.$v['AC_NAME'].'| '.$v['AC_TYPE'];
					$csv_output .= "\n"; 
				}
				$n++;
			}
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=".$filename.".csv");
			header("Content-Transfer-Encoding: binary");
			$data = stripcslashes($csv_output);
			echo $data;
			die;
		}
	}
	
	public function getStateNameByCode($scode){
		$stateName = $this->stateModel->select('*')->where('ST_CODE', '=', $scode)->first();
		return $stateName;
	}

}