<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignProfile extends Model
{
    protected $casts = [
        'id' => 'string',
    ];
    protected $table = 'campaign_profile';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag', 
        'title', 
        'email_template_id', 
        'status', 
        'start_time', 
        'end_time',
        'email_limit'
        
    ];

}
