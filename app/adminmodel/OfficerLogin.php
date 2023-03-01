<?php

namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use DB;

class Officerlogin extends Model
{
use HasApiTokens, Notifiable;
 protected $table = 'officer_login';
 //protected $connection = 'mysql2';

   /**
   * The database primary key value.
   *
   * @var string
   */
   protected $primaryKey = 'id';


    protected $fillable = [
        'Phone_no', 'api_otp',
    ];

	/**
     * Get the state for this model.
     */
    public function state()
    {
        return $this->hasOne('App\Models\States','ST_CODE','st_code');
    }
	
	 public function district()
    {
        return $this->hasOne('App\Models\Districts','row_id','dist_no');
    }
	
	 public function ac()
    {
        return $this->hasOne('App\Models\AC','AC_NO','ac_no');
    }
	
	 public function pc()
    {
        return $this->hasOne('App\Models\PC','PC_NO','pc_no');
    }
	
	
	
     protected $dates = [];

   /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
   protected $casts = [];
}
