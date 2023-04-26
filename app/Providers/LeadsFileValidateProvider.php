<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class LeadsFileValidateProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('LeadsFileValidate', function ($attribute, $value, $parameters, $validator) { 
            $error = $value->getError(); 
            $fileExtension = strtolower($value->getClientOriginalExtension());
            $csvPath = $value->getPathName();
            $csvArr = file($csvPath); 
            $emailCsvPath = config("dashboard_constant.EMAIL_CSV_PATH");
            $csvDir = config("dashboard_constant.EMAIL_CSV_DIR"); 
            $invalidEmailCsvPath = $csvDir.DIRECTORY_SEPARATOR.$parameters[0]."_invalid_email_list.csv";  
            if(!file_exists($csvDir)){
                mkdir($csvDir, 0755, true);
            }
            
            if( ($error > 0) || ($fileExtension != "csv") || ($value->getClientSize() <= 0) ){
                return false;
            }
            else{
                $fp = fopen($emailCsvPath, 'w'); 
                $existFileArr = [];
                if(file_exists($invalidEmailCsvPath) && ($parameters[1] !== 'true') ){
                    // edit existing file
                    $inFp = fopen($invalidEmailCsvPath, 'a');
                    $existFileArr = file($invalidEmailCsvPath); 
                }else{
                    // open new file
                    $inFp = fopen($invalidEmailCsvPath, 'w');
                }

                $validItems = array_filter($csvArr, function($val, $key) use($fp, $inFp, $existFileArr){
                    $valArr = explode(",",trim($val));
                    $name = trim($valArr[0]);
                    $email = trim($valArr[1]);
                    $emailLength = strlen($email);
                    $csvFields = [$name, $email];
                    $csvStr = $name.",".$email."\n";
                    // generate valid csv file 
                    if( !( empty($email) || ($emailLength > 255 ) ||  !filter_var($email, FILTER_VALIDATE_EMAIL) || (strlen($name) > 255) ) ){
                        fputcsv($fp, $csvFields);
                    }else{
                        if(!in_array($csvStr,$existFileArr)){
                            fputcsv($inFp, $csvFields);
                        }
                    }
                    
                }, ARRAY_FILTER_USE_BOTH);

                fclose($fp);
                return true;
            }
        });

        Validator::replacer('LeadsFileValidate', function($message, $attribute, $rule, $parameters) {
            return str_replace($message, "Something went wrong. Please upload a valid csv file.", $message);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
