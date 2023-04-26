<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name', 
        'last_name', 
        'email', 
        'password',
        'username', 
        'status', 
        'type'
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * Get user full name
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name; 
    }
}
