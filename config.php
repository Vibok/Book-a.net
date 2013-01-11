<?php
define('CONF_PATH', __DIR__ . DIRECTORY_SEPARATOR);
if (function_exists('ini_set')) {
    ini_set('include_path', CONF_PATH . PATH_SEPARATOR . '.');
} else {
    throw new Exception("No puedo añadir la API base al include_path.");
}

//Estoy en local
define('CONF_LOCAL', false);
define('CONF_DEBUG', false);

//Estoy en mantenimiento
define('CONF_MAINTENANCE', false);
define('CONF_MYIP', '');


define('PEAR', CONF_PATH . 'library' . '/' . 'pear' . '/');
if (function_exists('ini_set')) {
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PEAR);
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . CONF_PATH . 'library/paypal');
} else {
    throw new Exception("No puedo añadir las librerías PEAR al include_path.");
}

if (!defined('PHPMAILER_CLASS')) {
    define ('PHPMAILER_CLASS', CONF_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.phpmailer.php');
}
if (!defined('PHPMAILER_LANGS')) {
    define ('PHPMAILER_LANGS', CONF_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR);
}
if (!defined('PHPMAILER_SMTP')) {
    define ('PHPMAILER_SMTP', CONF_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.smtp.php');
}
if (!defined('PHPMAILER_POP3')) {
    define ('PHPMAILER_POP3', CONF_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.pop3.php');
}

// Metadata
define('CONF_META_TITLE', '');
define('CONF_META_DESCRIPTION', '');
define('CONF_META_KEYWORDS', '');
define('CONF_META_AUTHOR', 'Onliners Web Development');
define('CONF_META_COPYRIGHT', '');

// Database
define('CONF_DB_DRIVER', 'mysql');
define('CONF_DB_HOST', 'localhost');
define('CONF_DB_PORT', 3306);
define('CONF_DB_CHARSET', 'UTF-8');
define('CONF_DB_SCHEMA', 'schema');
define('CONF_DB_USERNAME', 'username');
define('CONF_DB_PASSWORD', 'password');

//Uploads i catxe
define('CONF_DATA_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);

// Mail
define('CONF_MAIL_FROM', 'noreply@example.com');
define('CONF_MAIL_NAME', 'Noreply');
define('CONF_MAIL_TYPE', 'mail');
define('CONF_MAIL_SMTP_AUTH', true);
define('CONF_MAIL_SMTP_SECURE', 'ssl');
define('CONF_MAIL_SMTP_HOST', '');
define('CONF_MAIL_SMTP_PORT', 465);
define('CONF_MAIL_SMTP_USERNAME', '');
define('CONF_MAIL_SMTP_PASSWORD', '');

define('CONF_MAIL', 'info@example.com');
define('CONF_CONTACT_MAIL', 'contact@example.com');

// Language
define('CONF_DEFAULT_LANG', 'es');

// url
define('SITE_URL', 'http://example.com');
define('SRC_URL', 'http://example.com');

// Cron params
define('CRON_PARAM', '');
define('CRON_VALUE', '');


/****************************************************
Paypal Config Values
****************************************************/
define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=');
define('PAYPAL_DEVELOPER_PORTAL', '');
define('PAYPAL_DEVICE_ID', '');
define('PAYPAL_APPLICATION_ID', '');
define('PAYPAL_BUSINESS_ACCOUNT', '');
define('PAYPAL_IP_ADDRESS', '127.0.0.1');
define('PAYPAL_API_USER', '');
define('PAYPAL_API_PASS', '');
define('PAYPAL_API_SIGNATURE', '');
define('PAYPAL_EP_API', 'https://api-3t.sandbox.paypal.com/2.0');
define('PAYPAL_EP_IPN', 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
define('PAYPAL_EP_PERM', 'https://svcs.sandbox.paypal.com/');
/****************************************************
TPV constants
****************************************************/
define('TPV_MERCHANT_CODE', '');
define('TPV_REDIRECT_URL', '');
define('TPV_ENCRYPT_KEY', '');
define('TPV_WEBSERVICE_URL', '');


/******************************************************
OAUTH APP's Secrets
*******************************************************/
if (!defined('OAUTH_LIBS')) {
    define ('OAUTH_LIBS', CONF_PATH . 'library' . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . 'SocialAuth.php');
}

//Facebook
define('OAUTH_FACEBOOK_ID', ''); //
define('OAUTH_FACEBOOK_SECRET', ''); //


//Twitter
define('OAUTH_TWITTER_ID', ''); //
define('OAUTH_TWITTER_SECRET', ''); //

//Linkedin
define('OAUTH_LINKEDIN_ID', ''); //
define('OAUTH_LINKEDIN_SECRET', ''); //


//Un secreto inventado cualquiera para encriptar los emails que sirven de secreto en openid
define('OAUTH_OPENID_SECRET','');

