<?php namespace App\models\Admin;

use Illuminate\Database\Eloquent\Model;

class IndexcardLogModel extends Model
{
    protected $table = 'indexcard_log';

    protected $fillable = ['st_code', 'pc_no', 'year', 'submitted_by', 'created_at', 'updated_at'];

    public $timestamps = false;

    
    public static function add_log($data){
        $object = new IndexcardLogModel();
        $object->st_code = $data['st_code'];
        $object->pc_no = $data['pc_no'];
        $object->year = date('Y');
        $object->finalize = $data['finalize'];
        $object->finalize_by_ro = 1;
        $object->submitted_by = \Auth::user()->officername;
        $object->save();
    }

    public static function add_ceo_log($data){
        $log_data = VoterModel::find($data['id']);

        $object = new IndexcardLogModel();
        $object->st_code = $data['st_code'];
        $object->pc_no = $log_data->pc_no;
        $object->year = date('Y');

        $object->finalize = $data['finalize'];
      
        if($data['finalize']){
            $object->finalize_by_ceo = 1;
        }

        $object->submitted_by = \Auth::user()->officername;
        $object->save();
    }
	
	public static function counting_definalize_log($data){
        $object = new IndexcardLogModel();
        $object->st_code = $data['st_code'];
        $object->pc_no = $data['pc_no'];
        $object->	type_finalize = 'counting';
        $object->submitted_by = \Auth::user()->officername;
        $object->save();
    }
	
	public static function nomination_definalize_log($data){
        $object = new IndexcardLogModel();
        $object->st_code = $data['st_code'];
        $object->pc_no = $data['pc_no'];
        $object->	type_finalize = 'nomination';
        $object->submitted_by = \Auth::user()->officername;
        $object->save();
    }
    

}