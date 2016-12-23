<?php
ob_start();
setlocale(LC_ALL, 'fr_FR');

if (isset($_SERVER['SERVER_NAME']))
{
	session_start();
}

$_serverName = strtolower(php_uname('n'));

switch ($_serverName)
{
	case 'neoweb':
		define('PATH_ROOT', '/home/projects/cms_services');
		define('SITE_URL', 'http://services.neonovis.lan');
		define('DATABASE_NAME', 'cms_services');		//
		define('DATABASE_USER', 'root');		//
		define('DATABASE_PASSWD', 'fa5stuir');		//
		//define ('SMTP_SERVER', 'smtp.free.fr');
        define('TLD', '.lan');
		break;
	default :
		define('PATH_ROOT', '/home/projects/cms_services');
		define('SITE_URL', 'http://services.neonovis.com');
		define('DATABASE_NAME', 'cms_services');		//
		define('DATABASE_USER', 'root');		//
		define('DATABASE_PASSWD', 'fa5stuir');		//
		//define ('SMTP_SERVER', '127.0.0.1');
        define('TLD', '.com');
		break;

}

define('SITE_NAME', 'NeoCMS Services');
define('PATH_SITE', SITE_URL); //alias de site url

//===============================================
//== constantes =================================

define('FOLDER_TEMPLATE',			'templates');
define('FOLDER_UPLOADED_IMAGES',	'uploadimage');
define('FOLDER_REPUPLOAD',			'repupload');

define('PATH_TEMPLATE',				PATH_ROOT.'/'.FOLDER_TEMPLATE);
define('PATH_CONFIG',				PATH_ROOT.'/'.FOLDER_TEMPLATE . '/configs');
define('PATH_TO_UPLOADED_IMAGES', 	PATH_ROOT.'/'.FOLDER_UPLOADED_IMAGES);
define('PATH_TO_REPUPLOAD', 		PATH_ROOT .'/'. FOLDER_REPUPLOAD);

//define('URL_TEMPLATE',				SITE_URL.'/'.FOLDER_TEMPLATE.'/');
define('URL_TO_UPLOADED_IMAGES', 	SITE_URL.'/'.FOLDER_UPLOADED_IMAGES.'/');
define('URL_TO_REPUPLOAD', 			SITE_URL.'/'.FOLDER_REPUPLOAD.'/');

//define('FRONT_URL_IMAGES', 		SITE_URL.'/images/');
//define('FRONT_PATH_IMAGES', 		PATH_SITE.'/images/');

define('BACK_URL_IMAGES', 		'http://static.neonovis'.TLD.'/images/');
define('BACK_URL_JS', 		'http://static.neonovis'.TLD.'/js/');
define('BACK_URL_CSS', 		'http://static.neonovis'.TLD.'/css/backoffice/');

define('PATH_INCLUDE', 			PATH_ROOT.'/'.'include');
define('PATH_CACHE', '/tmp/smarty/cache/cms_services/');
define('PATH_COMPILE', '/tmp/smarty/compile/cms_services/');
//define('BACK_URL_IMAGES', 'http://www.neonovis.com/cms/images/');

//$b_dev = true;
define('CACHE', false);
?>