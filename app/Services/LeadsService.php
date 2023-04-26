<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\EmailTemplate;
use App\Services\CampaignService;
use DB;
use Mail;
use Validator;

class LeadsService extends AppService
{
    public $CampaignService;

    public function __construct()
    {
        $this->CampaignService = new CampaignService();
    }
    public function leadsEmailUpload($request){ 
        ini_set('upload_max_filesize', '40M');
        ini_set('post_max_size', '40M');
        $deleteOldEmails = $request->input('delete_emails');
        $campId = $request->input('campaign_id');

        $validator = Validator::make($request->all(),[ 
            'file' => 'LeadsFileValidate:'.$campId.','.$deleteOldEmails
        ])->validate();

        
        $filesource= $request->file('file');
        $fileExtension= $filesource->getClientOriginalExtension();
        $filePath = config("dashboard_constant.EMAIL_CSV_PATH"); 

        $csvDir = config("dashboard_constant.EMAIL_CSV_DIR"); 
        $invEmailCsvPath = $csvDir.DIRECTORY_SEPARATOR.$campId."_invalid_email_list.csv";  
        
        $file = str_replace('\\', '/', $filePath);

        $csvPath = $filesource->getPathName();
        $totalEmails = count(file($csvPath)); 
        $totalValidEmails = count(file($filePath));        
        $totalInvEmails = count(file($invEmailCsvPath));   
           
        if($deleteOldEmails == 'true'){
            $this->CampaignService->deleteCampOldEmail($campId);
            $totalInvEmails = $totalEmails - $totalValidEmails;    
        }

        $sql = "LOAD DATA LOCAL INFILE '".$file."' IGNORE INTO TABLE leads
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\n' 
        (name,email) SET name = TRIM(name), email = TRIM(email), campaign_id = {$campId}"; 
        $res = DB::connection()->getpdo()->exec($sql);

        $totalUpload = $res;
        // update valid/invalid emails count
        $this->CampaignService->updateValidInvalidEmailsCount($campId, $totalUpload, $totalInvEmails);
        // delete generated csv file after upload
        unlink($filePath);
        
        $responseMsg = [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_RESULT') => false,
                config('msg_label.MSG_TYPE') => config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => 'Could not upload Leads!'
            ]
        ];
        if($res){
            $responseMsg = [
                config('msg_label.RESPONSE_MSG') => [
                    config('msg_label.MSG_RESULT') => true,
                    config('msg_label.MSG_TYPE') => config('msg_label.MSG_SUCCESS'),
                    config('msg_label.MSG_TITLE') => config('msg_label.MSG_SUCCESS_TITLE'), 
                    config('msg_label.MSG_MESSAGE') => 'Leads upload successfully!'
                ]
            ];
        }
        
        return $responseMsg;
    }
    /**
     * send campaign leads email
     */
    public function sendLeadsEmail(){
        $campList = $this->getEmailCampList();
        $dashConfig = config('dashboard_constant');
        
        if(count($campList) > 0){ 
            foreach($campList as $camp){   
                $emails = $this->getCampEmailList($camp->campaign_id);  
                if(count($emails) > 0){ 
                    $from = $camp->from_email ? trim($camp->from_email) : $dashConfig['CAMPAIGN_FROM_EMAIL'];
                    $fromName = $camp->from_email_name ? trim($camp->from_email_name) : $dashConfig['CAMPAIGN_FROM_EMAIL'];
                    $subject = $camp->from_email_subject ? $camp->from_email_subject : $dashConfig['CAMPAIGN_EMAIL_SUBJECT'];
                    // get template html
                    $templateData = $this->createEmailTemplate($camp); 

                    foreach($emails as $email => $name){
                        $to = trim($email); 
                        $send = $this->sendEmail($to, $from, $fromName, $subject, $templateData); 
                        if($send["result"] == true){
                            $upStatus = $this->updateLeadsEmailStatus($camp->campaign_id, $email);
                        }

                    }
                    
                }
                
            }
           
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Email send successfully"
            ];
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Something went wrong. There are no data to send email"
        ];
        
    }
    /**
     * get unique campaign list from leads table
     */
    public function getEmailCampList(){
        $now = date("Y-m-d H:i:s");
        $campList = Lead::join('campaign_profile', 'leads.campaign_id', '=', 'campaign_profile.id')
        ->leftJoin('templates', 'campaign_profile.email_template_id', '=', 'templates.id')
        ->where("leads.status","0")
        ->where("campaign_profile.status","P")
        ->where("campaign_profile.start_time", "<=", $now)
        ->where("campaign_profile.end_time", ">=", $now)
        ->groupBy("leads.campaign_id")
        ->orderBy('leads.campaign_id', 'asc')
        ->select('leads.campaign_id','campaign_profile.title', 'campaign_profile.from_email_name', 'campaign_profile.from_email', 
        'campaign_profile.from_email_subject', 'campaign_profile.tag', 'campaign_profile.email_template_id', 'templates.name as email_template_name','templates.bg_color','templates.content')
        ->get();
        return $campList;
    }
    /**
     * get email list by campaign id
     * @param int $campId
     */
    public function getCampEmailList($campId){ 
        $limit = config('dashboard_constant.CAMPAIGN_EMAIL_SEND_LIMIT');
        $emails = Lead::where('leads.status','0')
        ->where('leads.campaign_id', $campId)
        ->limit($limit)
        ->pluck("name","email")->all(); 
        return $emails;
    }

    /**
     * create email template
     * @param object $data
     */
    public function createEmailTemplate($data){ 
        $tempHtml = "";
        if(isset($data->email_template_id) && !empty($data->email_template_id)){ 
            // get all template blocks
            $blockData = $this->getTemplateBlocks($data->email_template_id); 
            $style = "";
            if(isset($data->bg_color)){
                $style .= "background-color:".$data->bg_color.";";
            }
            $tempHtml .= "<div style='".$style."'>";
            foreach($blockData as $key => $val){
                $tempHtml .= $val->content;
            }
            $tempHtml .= "</div>";
        }
        return $tempHtml;
    }

    /**
     * get template blocks
     * @param int $tempId
     */
    public function getTemplateBlocks($tempId){
        $data = DB::table('template_blocks')
        ->where("template_blocks.template_id",$tempId)
        ->get(); 
        return $data;
    }

    /**
     * Update status by campaign id
     * @param int $campId 
     * @param string $email
     * return true/false
     */
    public function updateLeadsEmailStatus($campId, $email){
        $res = Lead::where(['campaign_id' => $campId, 'email' => $email])
          ->update(['status' => 1]);

        if($res){
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Status update successfully"
            ];
        }else{
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Status update failed"
            ];
        }
    }
    /**
     * re-send campaign leads email
     * @param int $campId
     */
    public function resendLeadsEmail($campId){ 
        if($campId){ 
            $dashConfig = config('dashboard_constant');
            // get campign details
            $campDetail = $this->CampaignService->getDetail($campId);

            $from = $campDetail->from_email ? $campDetail->from_email : $dashConfig['CAMPAIGN_FROM_EMAIL'];
            $fromName = $campDetail->from_email_name ? $campDetail->from_email_name : $dashConfig['CAMPAIGN_FROM_EMAIL'];
            $subject = $campDetail->from_email_subject ? $campDetail->from_email_subject : $dashConfig['CAMPAIGN_EMAIL_SUBJECT'];
            
            $emails = $this->getCampEmailList($campId);  
            if(count($emails) > 0){ 
                // get template html
                $templateData = $this->createEmailTemplate($campDetail);
                foreach($emails as $email => $name){
                    $to = $email;
                    $send = $this->sendEmail($to, $from, $fromName, $subject, $templateData);
                    if($send["result"] == true){
                        $upStatus = $this->updateLeadsEmailStatus($camp->campaign_id, $email);
                    }

                }
                return [
                    config('msg_label.RESPONSE_MSG') => [
                        config('msg_label.MSG_RESULT') => true,
                        config('msg_label.MSG_TYPE') => config('msg_label.MSG_SUCCESS'),
                        config('msg_label.MSG_TITLE') => config('msg_label.MSG_SUCCESS_TITLE'), 
                        config('msg_label.MSG_MESSAGE') => 'Email send successfully'
                    ]
                ];
            }
            
        }
        return [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_RESULT') => false,
                config('msg_label.MSG_TYPE') => config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => 'Something went wrong. There are no data to send email'
            ]
        ];
       
        
    }
    /**
     * get leads summary detail
     * @param string $campId
     */
    public function getLeadStatusSummary($campId){
        $data = Lead::select('status',DB::raw('count(*) as total')) 
            ->where('campaign_id', $campId)
            ->groupBy('status')->pluck('total','status');
        return $data;    
    }

    public function exportLeadsEmail($campId){  
        $filename = "campaign-email-".$campId."-".time().".csv";
        // $filePath = '/tmp/' . $filename; 
        $filePath = config("dashboard_constant.LEADS_CSV_DIR").$filename; 
        // if (file_exists($filePath))
        // {
        //     // delete file from server after download
        //     unlink($filePath);
        // }
        $leadStatus = config("dashboard_constant.LEADS_STATUS");
        $statusCaseStr = "CASE ";
        foreach($leadStatus as $stkey => $stvalue){
            if($stkey == '1'){
                continue;
            }
            $statusCaseStr .= " WHEN leads.status = ".$stkey." THEN '".$stvalue."'";
        }
        $statusCaseStr .= " ELSE '".$leadStatus['1']."' END as status";
        $sql = "SELECT leads.campaign_id, cp.title, leads.name, leads.email, cp.from_email, ".$statusCaseStr.", IF(leads.updated_at IS NULL, '', leads.updated_at)  FROM leads LEFT JOIN
        campaign_profile as cp on cp.id = leads.campaign_id
        WHERE leads.campaign_id = ".$campId."
        INTO OUTFILE '".$filePath."'
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\r\n'";
      
        $data = DB::select($sql);

        if (file_exists($filePath))
        {
            // add header to existing csv file
            $fileData = "Campaign Id,Campaign Name,Name,Email,Email From,Status,Send Time\n";
            $fileData .= file_get_contents($filePath);
            file_put_contents($filePath, $fileData);
            // download csv
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$filename);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            // delete file from server after download
            // unlink($filePath);
            exit;

        }

    }
    /**
     * export leads invalid csv files
     */
    public function exportInvalidEmail($campId){
        $csvDir = config("dashboard_constant.EMAIL_CSV_DIR"); 
        $filename = $campId."_invalid_email_list.csv";
        $filePath = $csvDir.DIRECTORY_SEPARATOR.$filename;  
        if (file_exists($filePath))
        {
            // download csv
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$filename);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
        }else{ 
            return $this->processServiceResponse(false, "Csv file not exists", "");
        }
    }
    
}
