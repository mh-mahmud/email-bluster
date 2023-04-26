<?php
return[
    'JS_SOURCE' => 'src',
    'SYS_THEME_ASSETS_PATH' => public_path().DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR,
    'SYS_THEME_ASSETS_URL' => env('APP_URL').'assets/',
    'DEFAULT_SITE_HEADER' => 'Email Blaster',
    'DEFAULT_SITE_TITLE' => 'Email Blaster',
    'DEVELOPER_TEAM' => 'Genuity Systems Ltd',
    'DEVELOPER_TEAM_WEB_URL' => 'http://www.genuitysystems.com',
    'BASE_URL' => '/',
    'PROJECT_PREFIX' => 'email_blaster',
    'USER_TYPE_CUSTOMER' => 'CU',
    'USER_TYPE_ADMIN' => 'AU',
    'DISCOUNT_TYPE_FIXED' => 'DTF',
    'DISCOUNT_TYPE_PERCENTAGE' => 'DTP',

     /**
     * Status constant
     */
    'ACTIVE' => 'A',
    'INACTIVE' => 'I',
    'STATUS_ACTIVE' => 'A',
    'STATUS_INACTIVE' => 'I',
    'STATUS_PENDING' => 'P',
    'STATUS_EMAIL_CONFIRMED' => 'E',
    'STATUS_DELETED' => 'D',
    'STATUS_ARCHIVE' => 'AR',
    'PAID' => 'P',
    'DUE' => 'D',
    'YES' => 'Y',
    'NO' => 'N',
    /**
     * email config
     */
    'FROM_EMAIL' => 'razu@genuitysystems.com',
    'TO_EMAIL' => 'razu@genuitysystems.com',
    'REPLY_TO_EMAIL' => 'razu@genuitysystems.com',
    'FROM_EMAIL_NAME' => "Email Blaster",
    'CAMPAIGN_FROM_EMAIL' => "razu@genuitysystems.com",
    'CAMPAIGN_TO_EMAIL' => "razu@genuitysystems.com",
    'CAMPAIGN_EMAIL_SUBJECT' => "Email Blaster Campaign",

    'PAGINATION_LIMIT' => 20,
    'PAGINATION_MIN_LIMIT' => 20,
    'CAMPAIGN_EMAIL_SEND_LIMIT' => 10,
    'CURRENT_DATE_TIME' => date('Y-m-d H:i:s'),
    'CURRENT_DATE' => date('Y-m-d'),
    'CURRENT_TIME' => date('H:i:s'),
    'DB_DATE_FORMAT' => 'Y-m-d',
    'DB_TIME_FORMAT' => 'H:i:s',
    'DATEFORMAT' => date('m/d/Y'),
    'CUSTOM_DATE_FORMAT' => 'm/d/Y',
    'SHOW_DATE_FORMAT' => 'M d, Y',
    'SHOW_DATETIME_FORMAT' => 'M d, Y, g:i a',
    'SHOW_CUSTOM_DATETIME_FORMAT' =>  'm-d-Y H:i:s',

    /*
    * Model Constance
    */
    'DATA_FETCH_UUID' => 'uuid',
    'DATA_FETCH_ID' => 'id',
    'DATA_FETCH_ALL' => 'all',
    'DATA_FETCH_LIST' => 'list',
    'DATA_FETCH_FIRST' => 'first',
    'DATA_FETCH_COUNT' => 'count',



    /*
    * Flash Message Type
    */
    'FLASH_MSG_INFO' => 'info',
    'FLASH_MSG_WARNING' => 'warning',
    'FLASH_MSG_SUCCESS' => 'success',
    'FLASH_MSG_ERROR' => 'error',


    /**
     * Database Field 
     */
    'DB_FIELD_UUID' => 'uuid', 
    'DB_FIELD_ID' => 'id',

    'USER_TYPE' => [
        'AU' => 'Admin',
        'MU' => 'Manager',
        'AG' => 'Agent',
        'SU' => 'Supervisor'
    ],
    'USER_STATUS' => [
        'A' => 'Active',
        'I' => 'Inactive'
    ],
    'DELETE_STATUS' => [
        '0' => 'Deleted',
        '1' => 'Not Deleted'
    ],
    'CAMPAIGN_PROFILE_STATUS' => [
        'I' => 'Inactive',
        'P' => 'Processing',
        'S' => 'Stop',
        'D' => 'Done',
    ],
    'LEADS_STATUS' => [
        '0' => 'Unsend',
        '1' => 'Send',
        '7' => 'Invalid Email',
        '8' => 'Select For Processing',
        '9' => 'Error',
    ],
    'EMAIL_CSV_DIR' => public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR,
    'EMAIL_CSV_PATH' => public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'email_list.csv',
    'CAMPAIGN_FILE_PATH' => "E:/usr/local/email_blaster/",
    'LEADS_CSV_DIR' => 'usr'.DIRECTORY_SEPARATOR.'local'.DIRECTORY_SEPARATOR.'eblaster'.DIRECTORY_SEPARATOR.'csv'.DIRECTORY_SEPARATOR,
];

