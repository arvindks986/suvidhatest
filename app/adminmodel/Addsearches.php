<?php

namespace App\adminmodel;
use Illuminate\Database\Eloquent\Model;
use DB;

class Addsearches extends Model
{

 protected $table = 'add_searches';
 protected $connection = 'mysql2';

   /**
   * The database primary key value.
   *
   * @var string
   */
   protected $primaryKey = 'id';


    protected $fillable = [
        'valid_from', 'valid_to', 'approval_date', 'state', 'district' , 'applicant_name' ,
    ];

     protected $dates = [];

   /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
   protected $casts = [];
}
