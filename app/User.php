<?php
namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
     use HasApiTokens, Notifiable;
     protected $table = 'user_login';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'candidate_id','authority_id','name','party_id', 'email', 'password','mobile','remember_token','access_token','role_id','OTP','login_flag','isActive','device_type','device_id','verify_otp','app_id'
    ];
 
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	/*public function user_master()
	{
		return $this->hasOne('App\user_master'); 
	}*/
}
