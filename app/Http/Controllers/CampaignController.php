<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CampaignProfile;
use App\Models\Lead;
use DB;
use App\Services\EmailTemplateService;
use App\Services\LeadsService;
use App\Services\CampaignService;

class CampaignController extends AppController
{
    public $Service;
    public $EmailTemplateService;
    public $LeadsService;

    public function __construct()
    {
        $this->Service = new CampaignService();
        $this->EmailTemplateService = new EmailTemplateService();
        $this->LeadsService = new LeadsService();
    }
    
    /**
     * get campaign profile list
     */
    public function getProfileList(Request $request)
    {
        // Get campaignProfile list
        $data = $this->Service->getPagination($request); 
        $layoutData['js_plugin'] = $this->getJsPlugin(["JSP_BOOTSTRAP_SELECT2","JSP_BOOTSTRAP_BOOTBOX"]);
        $layoutData['campStatus'] = config("dashboard_constant.CAMPAIGN_PROFILE_STATUS");
        $layoutData['title'] = 'Campaign List | '.config("app.name");
        $layoutData['breadcrumb'] = [
            "links" =>[
                [
                    "name" => "Campaign",
                    "url" => url("#/campaign-profile-list"),
                    "icon" => "flaticon-users"
                ],
                [
                    "name" => "Campaign List",
                    "url" => url("#/campaign-profile-list"),
                    "icon" => "flaticon-list-1"
                ]
            ],
            "singleButton" =>[
                [
                    "name" => "Add Campaign Profile",
                    "url" => url("#/campaign-profile-create"),
                    "icon" => "la la-plus",
                    "class" => "m-btn--air btn-accent"
                ]

            ]
        ];

        $layoutData['data'] = $data['data'];
        // pagination meta value
        $layoutData['meta'] = $data['meta'];
        // pagination links
        $layoutData['links'] = $data['links'];
        
        // Return collection of list as a reosurce
		return response()->json($layoutData);  
    }
    /**
     * Show details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Get campaign profile
        $data = $this->Service->getDetail($id);
        $attachments = $this->Service->getCampAttachments($id);
        $layoutData['emailTemplateList'] = $this->EmailTemplateService->getList();
        $layoutData['campStatus'] = config("dashboard_constant.CAMPAIGN_PROFILE_STATUS");
        $layoutData['title'] = 'Campaign Detail | '.config("app.name");
        $layoutData['breadcrumb'] = [
            "links" =>[
                [
                    "name" => "Campaign List",
                    "url" => url("#/campaign-profile-list"),
                    "icon" => "flaticon-list-1"
                ],
                [
                    "name" => "Campaign Details",
                    "url" => url("#/campaign-profile-detail/".$id),
                    "icon" => "flaticon-interface-9"
                ]
            ],
            "singleButton" =>[
                [
                    "name" => "Edit Campaign Profile",
                    "url" => url("#/campaign-profile-edit/".$id),
                    "icon" => "la la-plus",
                    "class" => "m-btn--air btn-accent"
                ]

            ]

        ];
        $layoutData['data'] = $data;
        $layoutData['attachments'] = $attachments;
        $layoutData['leadStatuSummary'] = $this->LeadsService->getLeadStatusSummary($id);
        return response()->json($layoutData); 
    }

    public function create(){
        $layoutData['js_plugin'] = $this->getJsPlugin(["JSP_BOOTSTRAP_DATEPICKER"]);
        $layoutData['emailTemplateList'] = $this->EmailTemplateService->getList();
        $layoutData['title'] = 'Campaign Create | '.config("app.name");
        $layoutData['breadcrumb'] = [
            "links" => [
                [
                    "name" => "Campaign",
                    "url" => url("#/campaign-profile-list"),
                    "icon" => "flaticon-users"
                ],
                [
                    "name" => "Campaign Create",
                    "url" => url("#/campaign-profile-create"),
                    "icon" => "flaticon-plus"
                ]
            ]    
        ];
        return $layoutData;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        $data = $this->Service->save($request); 
        $responseMsg = [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_TYPE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS') : config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS_TITLE') : config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => $data[config('msg_label.MSG_MESSAGE')]
            ]
        ];
        return response()->json($responseMsg);  
        
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Get campaign profile
        $data = $this->Service->getDetail($id);
        $attachments = $this->Service->getCampAttachments($id);
        $layoutData['js_plugin'] = $this->getJsPlugin(["JSP_BOOTSTRAP_DATEPICKER","JSP_BOOTSTRAP_BOOTBOX"]);
        $layoutData['emailTemplateList'] = $this->EmailTemplateService->getList();
        $layoutData['campStatus'] = config("dashboard_constant.CAMPAIGN_PROFILE_STATUS");
        $layoutData['campFilePath'] = config("dashboard_constant.CAMPAIGN_FILE_PATH"); 
        $layoutData['title'] = 'Campaign Edit | '.config("app.name");
        $layoutData['breadcrumb'] = [
            "links" =>[
                [
                    "name" => "Campaign List",
                    "url" => url("#/campaign-profile-list"),
                    "icon" => "flaticon-list-1"
                ],
                [
                    "name" => "Edit Campaign",
                    "url" => url("#/campaign-profile-edit/".$id),
                    "icon" => "flaticon-edit-1"
                ]
            ]    
        ];
        $layoutData['data'] = $data;
        $layoutData['attachments'] = $attachments;
        return response()->json($layoutData); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    { 
        $data = $this->Service->updateData($request);
        $responseMsg = [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_TYPE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS') : config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS_TITLE') : config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => $data[config('msg_label.MSG_MESSAGE')]
            ]
        ];
        return response()->json($responseMsg);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->Service->delete($id); 
        $responseMsg = [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_TYPE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS') : config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS_TITLE') : config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => $data[config('msg_label.MSG_MESSAGE')]
            ]
        ];
        return response()->json($responseMsg); 
    }
    /**
     * Remove campaign attachment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAttachment($id)
    {
        $data = $this->Service->deleteCampAttachment($id); 
        $responseMsg = [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_TYPE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS') : config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS_TITLE') : config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => $data[config('msg_label.MSG_MESSAGE')]
            ]
        ];
        return response()->json($responseMsg); 
    }

    /**
     * uploads leads email from CSV file
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadLeads(Request $request){ 
        $res = $this->LeadsService->leadsEmailUpload($request);
        return response()->json($res);
    }

    public function sendCampaignEmail(){
        $res = $this->LeadsService->sendLeadsEmail();
        return response()->json($res);
    }
    /**
     * resend unsend campaign email
     * @param int $campId
     */
    public function resendCampaignEmail($campId){ 
        $res = $this->LeadsService->resendLeadsEmail($campId);
        return response()->json($res);
    }
    /**
     * update campaign status
     * 
     */
    public function updateCampaignStatus($campId,$status){
        $updtData = ['status' => $status];
        $data = $this->Service->updateCampaign($campId, $updtData);
        $responseMsg = [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_TYPE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS') : config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS_TITLE') : config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => $data[config('msg_label.MSG_MESSAGE')]
            ]
        ];
        return response()->json($responseMsg); 
    }
    /**
     * get campaign email list
     * @param string $campId
     */
    public function getEmailList($campId, Request $request)
    {   
        // Get campaignProfile list
        $data = $this->Service->getEmailListPagi($campId, $request); 
        $layoutData['js_plugin'] = $this->getJsPlugin(["JSP_BOOTSTRAP_SELECT2","JSP_BOOTSTRAP_BOOTBOX"]);
        $layoutData['leadStatus'] = config("dashboard_constant.LEADS_STATUS");
        $layoutData['campaignDetail'] = $this->Service->getDetail($campId);
        $layoutData['title'] = 'Campaign Email List | '.config("app.name"); 
        $layoutData['breadcrumb'] = [
            "links" =>[
                [
                    "name" => "Campaign List",
                    "url" => url("#/campaign-profile-list"),
                    "icon" => "flaticon-users"
                ],
                [
                    "name" => "Campaign Emails",
                    "url" => url("#/email-list/".$campId),
                    "icon" => "flaticon-list-1"
                ]
            ]
        ];
        $layoutData['data'] = $data['data'];
        // pagination meta value
        $layoutData['meta'] = $data['meta'];
        // pagination links
        $layoutData['links'] = $data['links'];
        
        // Return collection of list as a reosurce
		return response()->json($layoutData);  
    }
    /**
     * delete campaign email
     * @param string $campId
     * @param string $email
     * 
     */
    public function deleteEmail($campId, $email)
    { 
        $data = $this->Service->deleteCampEmail($campId, $email);  
        $responseMsg = [
            config('msg_label.RESPONSE_MSG') => [
                config('msg_label.MSG_TYPE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS') : config('msg_label.MSG_ERROR'),
                config('msg_label.MSG_TITLE') => $data[config('msg_label.MSG_RESULT')] == true ? config('msg_label.MSG_SUCCESS_TITLE') : config('msg_label.MSG_ERROR_TITLE'), 
                config('msg_label.MSG_MESSAGE') => $data[config('msg_label.MSG_MESSAGE')]
            ]
        ];
        return response()->json($responseMsg); 
    }
    /**
     * export campaign email
     * @param string $campId
     */
    public function exportLeadsEmail($campId){ 
        $res = $this->LeadsService->exportLeadsEmail($campId); 
        return response()->json($res); 
    }
    /**
     * export leads invalid email
     */
    public function exportInvalidEmail($campId){
        $data = $this->LeadsService->exportInvalidEmail($campId); 
        if(isset($data[config('msg_label.MSG_RESULT')]) && ($data[config('msg_label.MSG_RESULT')] == false)){ 
            return redirect('/')->with($this->Service->processControllerResponse(false, "Csv file not exists!"));
        }
    }
}
