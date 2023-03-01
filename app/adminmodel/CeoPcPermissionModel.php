<?php
namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
    
class CeoPcPermissionModel extends Model 
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function deletedata($table,$where)
    {
        $now = Carbon::now();
        $data=DB::table($table)
                ->where($where)
              ->update(['deleted_at' => $now ]);
        return $data;
    }


    
    public function insertdata($table,$data)
    {
        $data=DB::table($table)->insert($data);
        return $data;
    }
    
    public function updatetable($table,$where,$content)
    {
//        DB::enableQueryLog();
        $data=DB::table($table)
                ->where($where)
                ->update($content);
//        dd(DB::getQueryLog());
        return $data;
    }
    public function getAllPermsData($stcode)
    {
         $data=DB::table('permission_type as a')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
                 ->where('a.st_code',$stcode)
                ->select('a.id as p_id','m.permission_name as pname')
                ->get()->toArray();
//        dd(DB::getQueryLog());
        return $data;
    }
    public function getpermsndetails($id)
    {
        $data=DB::table('permission_type as a')
//            ->join('permission_required_doc as b','a.id','=','b.permission_id')
                 ->join('role_master as r','a.role_id','=','r.role_id')
                ->join('permission_master as m','m.id','=','a.permission_type_id')
            ->join('authority_type as c',\DB::raw("FIND_IN_SET(c.id,a.authority_type_id)"),">",\DB::raw("'0'"))
            ->select('a.*','c.*','c.id as auth_id')
            ->select('a.id as p_id','a.authority_type_id',DB::raw("GROUP_CONCAT(c.name SEPARATOR ',') as 'auth_name'"),'m.permission_name as pname','a.role_id','r.role_name')
            ->where('a.id',$id)    
            ->groupBy('a.id','m.permission_name','a.authority_type_id')
            ->get()->toArray();
        return $data;
    }
    public function getpermsndocdetails($id)
    {
//        DB::enableQueryLog();
        $data=DB::table('permission_required_doc as a')
                ->select('a.*','a.id as doc_id')
                ->where('permission_id',$id)
                ->whereNull('deleted_at')
            ->get()->toArray();
//        dd(DB::getQueryLog());
        return $data;
    }
    public function totalPermissionReport($where)
    {
        //DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as l','a.user_id','=','l.id')
                ->select(DB::raw('sum(CASE WHEN a.approved_status = 0 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Pending'),DB::raw('sum(CASE WHEN a.approved_status = 2 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Accepted'),DB::raw('sum(CASE WHEN a.approved_status = 1 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Inprogress'),DB::raw('sum(CASE WHEN a.approved_status = 3 AND a.cancel_status = 0 THEN 1 ELSE 0 END) as Rejected'),DB::raw('count(*) as total'))
                ->where($where)
                ->where('l.role_id','!=','NULL')
                //->groupBy('approved_status')
                ->get();
        //dd(DB::getQueryLog());
        return $data;
    }
    public function totalReportDetails($where,$status)
    {
//        print_r($status);die;
        $data=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                ->where('a.st_code',$where)
                ->where('approved_status',$status)
                 ->where('a.cancel_status',0)
                ->get()->toArray();
return $data;
    }
     public function totalPermissionReportData($where)
    {
       
         $data=DB::table('permission_request as a')
                
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->where('a.st_code',$where)
                ->get()->toArray();
                return $data;
        
    }
    
    public function totalPendingReportDetails($where)
    {
         $data=DB::table('permission_request as a')
                 
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                 ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','a.id as permission_id','b.id as login _id','m.permission_name as pname')
                ->where('a.st_code',$where)
                ->where('a.approved_status',0)
                 ->where('a.cancel_status',0)
                ->get()->toArray();
return $data;
    }
    
    
    public function getAllAuthorityTypeData($where)
    {
        $data=DB::table('authority_type')
                ->select('*')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    public function getAuthorityTypeDetails($where)
    {
        $data=DB::table('authority_type')
                ->select('*')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    
     public function getNodaldetails($id)
    {
         $data=DB::table('permission_assigned_auth as a')
                ->join('authority_masters as b','a.authority_id','=','b.id')
                ->join('authority_masters_mapping as e','e.authority_masters_id','=','b.id')
                ->join('authority_type as c','e.auth_type_id','=','c.id')
                ->select('a.*','b.*','c.name as auth_name')
                ->where('a.permission_request_id',$id)
                ->get()->toArray();
       return $data;
    }
    public function getRodetails($id,$status)
    {
        $data=DB::table('permission_request as a');
                $data->join('permission_request_comment as b','a.id','=','b.permission_request_id');
                $data->join('permission_type as d','a.permission_type_id','=','d.id');
                 $data->select('b.*','a.approved_status','a.cancel_status','d.role_id');
                $data->where('a.id',$id);
                $result=$data->get()->toArray();
        return $result;
    }
     public function getDetails($id,$locid)
    {
//         echo $id;die;
//         DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->join('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                })
                ->join('m_ac as g',function ($join){
                    $join->on('g.AC_NO','=','a.ac_no')
                         ->on('g.ST_CODE', '=', 'a.st_code');
                });
                if($locid != 'other' && $locid != '0')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id');
//                       ->on('l.st_code','=','a.st_code')
//                       ->on('l.dist_no','=','a.dist_no')
//                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other' && $locid != '0')
                {
                $data->select('a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','g.AC_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray();
//                dd($result);
//                dd(DB::getQueryLOg());
        return $result;
    }
    public function getInrtaDetails($id,$locid)
    {
//         echo $id;die;
//         DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code')
                ->join('m_district as f',function ($join){
                    $join->on('f.DIST_NO','=','a.dist_no')
                         ->on('f.ST_CODE', '=', 'a.st_code');
                });
//                ->join('m_ac as g',function ($join){
//                    $join->on('g.AC_NO','=','a.ac_no')
//                         ->on('g.ST_CODE', '=', 'a.st_code');
//                });
                if($locid != 'other' && $locid != '0')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id');
//                       ->on('l.st_code','=','a.st_code')
//                       ->on('l.dist_no','=','a.dist_no')
//                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other' && $locid != '0')
                {
                $data->select('a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','f.DIST_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray();
//                dd($result);
//                dd(DB::getQueryLOg());
        return $result;
    }
    
    public function getAuthType($stcode)
    {
        $data=DB::table('authority_type')
                ->select('*')
                ->where('st_code',$stcode)
                ->get()->toArray();
            return $data;
    }
    
    public function getAllPermissiontype()
    {
        $data=DB::table('permission_master')
                ->select('*')
                ->get()->toArray();
            return $data;
    }
    
     public function getAgentList($where)
    {
        $data=DB::table('officer_login')
                ->select('*')
                ->where('role_id','=','23')
                ->where($where)
                ->get()->toArray();
        return $data;
    }
    public function getAgentDetails($id)
    {
        $data=DB::table('officer_login')
                ->select('*')
                ->where('role_id','=','23')
                ->where('id',$id)
                ->get()->toArray();
        return $data;
    }
     public function getofficerlevel()
    {
        $data=DB::table('role_master')
                ->select('*')
                ->whereIn('role_id',[4,5,18,20])
                ->select('role_id','role_name','role_level')
                ->get()->toArray();
        return $data;
    }
    
    //offline Permission
    public function getLoginUserdetails($uid)
    {				
		$data = DB::table('officer_login as a')
                        ->join('m_state as b','a.st_code','=','b.ST_CODE')
                        ->select('b.ST_NAME')
                        ->where('a.id',$uid )
                        ->first();
		return $data;
    }
    public function getAllUserType()
    {
        $data=DB::table('user_role')
                ->select('*')
                ->where('role_level','=','2')
                ->get()->toArray();
        return $data;
    }
     public function getAllDist($st)
    {
         $data = DB::table('m_district')
                    ->select('DIST_NO','DIST_NAME')
                    ->where('ST_CODE',$st )
                    ->get()->toArray();
	 return $data;
    }
    
    public function getUserDetails($mb)
    {
        $data=DB::table('user_login as a')
                ->join('user_data as b','a.id','=','b.user_login_id')
                ->join('user_role as c','c.role_id','=','a.role_id')
                ->join('m_party as p','p.CCODE','=','a.party_id')
                ->select('a.*','b.*','c.role_name','a.id as login_id','p.PARTYNAME','b.name as user_name')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    public function getUserappDetails($mb)
    {
        $data=DB::table('user_login as a')
                ->join('user_data as b','a.id','=','b.user_login_id')
                ->select('a.*','b.*','a.id as login_id','b.name as user_name')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    
    public function getLoginCandDetails($mb)
    {
        $data=DB::table('user_login as a')
                ->join('user_role as c','c.role_id','=','a.role_id')
                 ->join('m_party as p','p.CCODE','=','a.party_id')
                ->select('a.*','c.role_name','a.id as login_id','p.PARTYNAME')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    
    public function getLoginappCandDetails($mb)
    {
        $data=DB::table('user_login as a')
                ->select('a.*','a.id as login_id')
                ->where('a.mobile',$mb)
                ->get()->toArray();
        return $data;
    }
    public function getPermissionDetails($id,$stcode,$role)
    {
        //DB::enableQueryLog();
        $data1=DB::table('permission_request as a')
                ->join('user_login as b','a.user_id','=','b.id')
                 ->join('user_data as ud','ud.user_login_id','=','b.id')
                ->join('user_role as c','b.role_id','=','c.role_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->select('a.*','ud.name','c.role_name','m.permission_name as pname','a.id as permission_id','b.id as login _id')
                ->where('a.st_code',$stcode)
                ->where('d.role_id',$role)
                ->get()->toArray();
        return $data1;
    }
    public function getCEOdetails($id)
    {
        $data=DB::table('permission_request as a')
                ->join('permission_request_comment as b','a.id','=','b.permission_request_id')
                ->select('b.*','a.approved_status','a.cancel_status')
                ->where('a.id',$id)
                ->get()->toArray();
        return $data;
    }
    public function getceopermsnDetails($id,$locid)
    {
//         echo $id;die;
//         DB::enableQueryLog();
        $data=DB::table('permission_request as a')
                ->join('user_login as b','b.id','=','a.user_id')
                ->join('user_data as ud','ud.user_login_id','=','a.user_id')
                ->join('permission_type as d','a.permission_type_id','=','d.id')
                ->join('permission_master as m','m.id','=','d.permission_type_id')
                ->join('m_state as e','e.ST_CODE','=','a.st_code');
                
                if($locid != 'other' && $locid != '0')
                {
                $data->join('location_master as l',function($join){
                    $join->on('l.id','=','a.location_id');
//                       ->on('l.st_code','=','a.st_code')
//                       ->on('l.dist_no','=','a.dist_no')
//                       ->on('l.ac_no','=','a.ac_no');
                });
                }
                
                if($locid != 'other' && $locid != '0')
                {
                $data->select('a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','ud.*','l.location_name','a.id as permission_id','b.id as login _id');
                }
                else {
                    $data->select('a.*','a.added_at as subdate','ud.name','m.permission_name as pname','b.mobile','e.ST_NAME','ud.*','a.id as permission_id','b.id as login _id');
                }
                $data->where('a.id',$id);
//                $data->distinct('a.id');
                
                $result=$data->get()->toArray();
//                dd($result);
//                dd(DB::getQueryLOg());
        return $result;
    }
    
     //authority master
    public function getAuthority($stcode)
    {
        $data=DB::table('authority_type as a')
                ->select('a.*')
                 ->where(array('a.st_code'=>$stcode))
                ->get()->toArray();
        return $data;
    }
    public function getAllAuthorityData($created)
    {
        $data=DB::table('authority_masters as a')
                ->join('authority_masters_mapping as m','a.id','=','m.authority_masters_id')
                ->join('authority_type as b','m.auth_type_id','=','b.id')
                ->select('a.*','b.name as auth_type_name1','a.id as nodal_id','m.*','m.auth_type_id as authid')
                ->Where('m.created_by',$created)
                ->where('a.created_by',$created)
                ->get()->toArray();
        return $data;
    }
    public function getAuthorityDetails($id)
    {
        $data=DB::table('authority_masters as a')
                ->select('a.*','a.id as nodal_id')
                ->where('a.id',$id)
                ->get()->toArray();
        return $data;
    }
}
