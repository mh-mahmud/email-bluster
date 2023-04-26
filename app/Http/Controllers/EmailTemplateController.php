<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;

class EmailTemplateController extends AppController
{
    public $Service;
    
    public function __construct()
    {
        $this->Service = new EmailTemplateService();
    }
    public function getTemplateList(){
        // Get email template list
        return $this->Service->getList();

    }
    public function getTemplateBuilder(){
        return view('email_template_builder.index');
    }
    public function loadPage(){
        view()->addLocation(base_path());
        return view("email_template_builder.template-load-page");
    }
    public function blankPage(){
        view()->addLocation(base_path());
        return view("email_template_builder.template-blank-page");
    }
    public function emailLang(){
        view()->addLocation(base_path());
        return view("email_template_builder.lang-1");
    }
    public function updateBlockInfo(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.update_block_info");
    }
    public function loadTemplates(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.load_templates");
    }
    public function getFiles(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.get-files");
    }
    public function save(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.save_template");
    }
    public function getTemplateBlocks(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.get_template_blocks");
    }
    public function upload(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.upload_template");
    }
    public function delete(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.delete_template");
    }
    public function import(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.template_import");
    }
    public function uploadTemplateImage(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.upload");
    }
    public function export(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.export");
    }
    public function emailSend(){ 
        view()->addLocation(base_path());
        return view("email_template_builder.send");
    }



}
