<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\CampaignProfile;
use App\Models\Lead;
use App\Models\CampaignAttachment;
use Illuminate\Validation\Rule;
use DB;
use Validator;

class CampaignService extends AppService
{
    
    /**
     * get pagination data
     */
    public function getPagination($request){
        $queryParam = $request->query();   
        $query = DB::table('campaign_profile');
        if(isset($queryParam['title']) && !empty($queryParam['title']) ){
            $query->where("campaign_profile.title","LIKE","%".$queryParam['title']."%");
        }
        if(isset($queryParam['status']) && !empty($queryParam['status']) ){
            $query->where("campaign_profile.status",$queryParam['status']);
        }
        if( 
            isset($queryParam['start_time']) && \DateTime::createFromFormat('Y-m-d H:i', $queryParam['start_time']) !== false
            && isset($queryParam['end_time']) && \DateTime::createFromFormat('Y-m-d H:i', $queryParam['end_time']) !== false
        ){
            $startTime = $queryParam['start_time'];
            $endTime = $queryParam['end_time'];
            $query->where("campaign_profile.start_time",">=",$queryParam['start_time']);
            $query->where("campaign_profile.end_time","<=",$queryParam['end_time']);
            
        }
        $query->orderBy('campaign_profile.created_at', 'DESC');
        $data = $query->paginate(config('dashboard_constant.PAGINATION_LIMIT')); 
        return $this->paginationDataFormat($data->toArray());
    }
    /**
     * get total number of send, unsend count from leads
     * @param array $campIds
     */
    public function getSendUnsendCount($campIds){
        $data = Lead::select('campaign_id',DB::raw('SUM(if(leads.status = 1 , 1, 0)) as send,SUM(if(leads.status = 0 , 1, 0)) as unsend') )
        ->whereIn('campaign_id',$campIds)
        ->groupBy('campaign_id')->get()->toArray(); 
        $list = array_column($data, NULL, 'campaign_id');
        return $list;
    }
    /**
     * save data
     * @param array request
     */
    public function save($request){
        Validator::make($request->all(),[
            'tag' => 'required|string|max:6|alpha_num|unique:campaign_profile',
            'title' => 'required|string|max:25',
            'email_template_id' => 'required|integer',
            'start_time' => 'required|date_format:Y-m-d H:i|after:now',
            'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
            'from_email_name' => 'required|max:255',
            'from_email' => 'required|email|max:255',
            'from_email_subject' => 'required|max:255',
        ])->validate();

        // Create or Update a campaign
        $dataObj =  new CampaignProfile;
        
        $id = strrev(strtotime(date("Y-m-d H:i:s")));
        $dataObj->id = $id;
        $dataObj->tag = $request->input('tag');
        $dataObj->title = $request->input('title');
        $dataObj->email_template_id = $request->input('email_template_id');
        $dataObj->start_time = $request->input('start_time');
        $dataObj->end_time = $request->input('end_time');
        $dataObj->from_email_name = $request->input('from_email_name');
        $dataObj->from_email = $request->input('from_email');
        $dataObj->from_email_subject = $request->input('from_email_subject');
        $dataObj->email_limit = $request->input('email_limit');
        $dataObj->status = "I";
            
        if($dataObj->save()) { 
            $path = config("dashboard_constant.CAMPAIGN_FILE_PATH"); 
            $files = $request->file('attachment');
            if(!empty($files)){
                foreach($files as $fkey => $file){
                    $autoId = $this->genRandNum(9);
                    $fileName =  $autoId."-".$file->getClientOriginalName();
                    $file->move($path, $fileName); 
                    $fileArr[] = [
                        "id" =>  $autoId,
                        "campaign_id" => $id,
                        "filename" => $fileName,
                    ];
                }
                if(!empty($fileArr)){
                    DB::table("campaign_attachments")->insert($fileArr);
                }
            }    

            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Campaign Added Successfully!",
                config('msg_label.MSG_DATA') => $dataObj
            ];
           
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Campaign Added Failed!",
            config('msg_label.MSG_DATA') => $dataObj
        ];
    }
    /**
     * get details
     * $param int $id
     */
    public function getDetail($id){
        //Get campaign profile
        return CampaignProfile::findOrFail($id);

    }
    /**
     * get details
     * $param int $id
     */
    public function getCampAttachmentDetail($id){
        //Get campaign attachment profile
        return DB::table("campaign_attachments")->find($id); 

    }

    /**
     * update data
     * @param array request
     */
    public function updateData($request){
        Validator::make($request->all(),[
            'tag' => 'required|string|max:6|alpha_num|unique:campaign_profile,tag,'.$request->id,
            'title' => 'required|string|max:25',
            'email_template_id' => 'required|integer',
            'start_time' => 'required|date_format:Y-m-d H:i',
            'end_time' => 'required|date_format:Y-m-d H:i|after:start_time',
            'from_email_name' => 'required|max:255',
            'from_email' => 'required|email|max:255',
            'from_email_subject' => 'required|max:255',
            'email_limit' => 'required|min:1'
        ])->validate();

        $dataObj = $this->getDetail($request->id);

        $dataObj = CampaignProfile::findOrFail($request->id);
        $dataObj->tag = $request->input('tag');
        $dataObj->title = $request->input('title');
        $dataObj->email_template_id = $request->input('email_template_id');
        $dataObj->start_time = $request->input('start_time');
        $dataObj->end_time = $request->input('end_time');
        $dataObj->from_email_name = $request->input('from_email_name');
        $dataObj->from_email = $request->input('from_email');
        $dataObj->from_email_subject = $request->input('from_email_subject');
        $dataObj->email_limit = $request->input('email_limit');
        if($dataObj->save()) {
            $path = config("dashboard_constant.CAMPAIGN_FILE_PATH"); 
            $files = $request->file('attachment');
            if(!empty($files)){
                foreach($files as $fkey => $file){
                    $autoId = $this->genRandNum(9);
                    $fileName =  $autoId."-".$file->getClientOriginalName();
                    $file->move($path, $fileName); 
                    $fileArr[] = [
                        "id" =>  $autoId,
                        "campaign_id" => $request->id,
                        "filename" => $fileName,
                    ];
                }
                if(!empty($fileArr)){
                    DB::table("campaign_attachments")->insert($fileArr);
                }
            }    
            
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Campaign Update Successfully!",
                config('msg_label.MSG_DATA') => $dataObj
            ];
           
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Campaign Update Failed!",
            config('msg_label.MSG_DATA') => $dataObj
        ];

    }

    /**
     * delete data
     * @param int $id
     */
    public function delete($id){
        $dataObj = $this->getDetail($id);
       
        if($dataObj->delete()) {
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Campaign Deleted Successfully!",
                config('msg_label.MSG_DATA') => $dataObj
            ];
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Campaign Deleted Failed!",
            config('msg_label.MSG_DATA') => $dataObj
        ];
    }
    /**
     * delete campaign attachment data
     * @param int $id
     */
    public function deleteCampAttachment($id){
        $dataObj = $this->getAttachmentDetail($id);
        if($dataObj->delete()){
            $filePath = config("dashboard_constant.CAMPAIGN_FILE_PATH").$dataObj->filename;
            unlink($filePath);
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Campaign File Deleted Successfully!",
                config('msg_label.MSG_DATA') => $dataObj
            ];
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Campaign File Deleted Failed!",
            config('msg_label.MSG_DATA') => $dataObj
        ];
    }

    public function updateCampaign($campId, $updtData){
        $res = CampaignProfile::where('id', $campId)->update($updtData); 
        if($res){
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Campaign Status Update Successfully!",
                config('msg_label.MSG_DATA') => $updtData
            ];
           
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Campaign Status Update Failed!",
            config('msg_label.MSG_DATA') => $updtData
        ];
    }
    /**
     * get campaign email pagination data
     * @param string $campId
     */
    public function getEmailListPagi($campId,$request){ 
        $queryParam = $request->query();    
        $query = Lead::where("leads.campaign_id",$campId); 
        if(isset($queryParam['email']) && !empty($queryParam['email']) ){ 
            $query->where("leads.email","LIKE","%".$queryParam['email']."%");
        }
        if(isset($queryParam['status']) && !empty($queryParam['status']) ){
            $query->where("leads.status",$queryParam['status']);
        }
        $data = $query->paginate(config('dashboard_constant.PAGINATION_LIMIT'));
        return $this->paginationDataFormat($data->toArray());
    }
    /**
     * delete campaign email
     * @param string $campId
     * @param string $email
     * 
     */
    public function deleteCampEmail($campId, $email){
        $result = Lead::where(["campaign_id" => $campId, "email" => $email])->limit(1)->delete(); 
        if($result){
            $this->updateValidInvalidEmailsCount($campId, -1, 0);
            return[
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Email Deleted Successfully!",
                config('msg_label.MSG_DATA') => $result
            ];
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Email Deleted Failed!",
            config('msg_label.MSG_DATA') => $result
        ];
    }
    /**
     * delete campaign email
     * @param string $campId
     * 
     */
    public function deleteCampOldEmail($campId){
        $result = Lead::where(["campaign_id" => $campId])->delete(); 
        if($result) {
            $this->updateCampaign($campId, ['valid_emails' => 0, 'invalid_emails' => 0, 'send' => 0]);
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Email Deleted Successfully!",
                config('msg_label.MSG_DATA') => $result
            ];
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Email Deleted Failed!",
            config('msg_label.MSG_DATA') => $result
        ];
    }
    /**
     * update valid/invalid emails count
     * @param string @campId
     * @param int @valid
     * @param int @invalid
     */
    public function updateValidInvalidEmailsCount($campId, $valid = 0, $invalid = 0){
        $result = DB::statement("update `campaign_profile` set `valid_emails` = `valid_emails` + {$valid}, `invalid_emails` = {$invalid}, status = 'S' where `id` = {$campId}");
        if($result) {
            return [
                config('msg_label.MSG_RESULT') => true,
                config('msg_label.MSG_MESSAGE') => "Campaign Updated Successfully!",
                config('msg_label.MSG_DATA') => $result
            ];
        }
        return [
            config('msg_label.MSG_RESULT') => false,
            config('msg_label.MSG_MESSAGE') => "Campaign Updated Failed!",
            config('msg_label.MSG_DATA') => $result
        ];

    }
    /**
     * get campaign profile attachments
     * @param int $campId
     */
    public function getCampAttachments($campId){
        $data = DB::table("campaign_attachments")->where("campaign_id", $campId)->get();
        return $data;
    }
    public function getAttachmentDetail($id){
        return CampaignAttachment::findOrFail($id);
    }


}
