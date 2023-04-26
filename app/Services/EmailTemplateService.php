<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailTemplateService extends AppService
{
    public function getList(){
        // Get email template list
        return $list = EmailTemplate::pluck('name', 'id')->all(); 
    }
}
