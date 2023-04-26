<?php

/*

  1. Make sure that you set SITE_URL and SITE_DIRECTORY correctly.
  2. Check uploads and exports folder in your server, if they do not exist ,please create those folders
  3. If you have any problem with installation, please create ticket in our support system.

*/

//main variables

define("SITE_URL", URL::to("/")."/");
define("SITE_DIRECTORY", base_path().DIRECTORY_SEPARATOR);

define("EMAIL_TEMPLATE_DIRECTORY", SITE_DIRECTORY."email_template_builder".DIRECTORY_SEPARATOR);


//uploads directory,url
define("STORAGE_DIRECTORY", SITE_DIRECTORY."public".DIRECTORY_SEPARATOR."storage".DIRECTORY_SEPARATOR);
define("UPLOADS_DIRECTORY",STORAGE_DIRECTORY."email_template_uploads".DIRECTORY_SEPARATOR);
define("UPLOADS_URL",SITE_URL.'public/storage/email_template_uploads/');

//EXPORTS directory,url
define("EXPORTS_DIRECTORY",STORAGE_DIRECTORY."email_template_exports".DIRECTORY_SEPARATOR);
define("EXPORTS_URL",SITE_URL.'public/storage/email_template_exports/');


//DB settings
define('DB_SERVER',config('database.connections.mysql.host'));
define('DB_USER',config('database.connections.mysql.username'));
define('DB_PASS' ,config('database.connections.mysql.password'));
define('DB_NAME', config('database.connections.mysql.database'));

// email smtp settings

define('EMAIL_SMTP',config('mail.host'));
define('EMAIL_PASS' ,config('mail.password'));
define('EMAIL_ADDRESS', config('mail.username'));



?>
