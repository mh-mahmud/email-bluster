<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignAttachment extends Model
{
    protected $table = 'campaign_attachments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'campaign_id', 
        'filename' 
    ];

}
