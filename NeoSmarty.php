<?php
Namespace NeoCms;
/**
 *
 * @todo tofix ne convient que pour CMS_services et cms_php.
 * @author RiPPeR
 *
 */
class NeoSmarty
{
    /**
     * cr�e une instance smarty
     *
     * @param  string $sompile
     * @param bool $cache
     * @return object
     */

    private static $instance;

    /**
     * @param $compile
     * @param null $cache
     * @return \Smarty
     */
    public static function get ($compile, $cache = null)
    {
//        if (ENV == 'dev') {
//            require_once ('smarty/libs/Smarty.class.php');
//        } else {
//            require_once ('smarty3/libs/Smarty.class.php');
//        }

        self::$instance = new \Smarty();

        // Set default workdirs
        self::$instance->cache_dir    = PATH_CACHE;
        self::$instance->compile_dir  = PATH_COMPILE;
        self::$instance->template_dir = PATH_TEMPLATE;
        self::$instance->config_dir = PATH_CONFIG;

        if (!is_null($cache)) {
            self::$instance->caching = $cache;
        } else {
            self::$instance->caching = CACHE;
        }

        self::$instance->cache_id = self::makeCacheId();

        self::$instance->compile_check        = true;
        self::$instance->force_compile        = false;
        self::$instance->cache_modified_check = false;
        self::$instance->use_sub_dirs         = false;
        self::$instance->debugging        = defined('DEBUG_SMARTY') ? DEBUG_SMARTY : false;

        self::$instance->default_template_handler_func = array('getSmarty', 'templateFallback');

        return self::$instance;
    }

    /**
     * cr�e un identifiant de cache en fonction de l'url
     *
     * @return string
     */
    public static function makeCacheId ()
    {
        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['PHP_SELF'] . '_' . md5($_SERVER['QUERY_STRING']);
        } else {
            return $_SERVER['PHP_SELF'];
        }
    }

    public static function templateFallback ($resource_type, $resource_name, &$template_source, &$template_timestamp, $smarty_obj)
    {
        if (strpos($resource_name, '_mobi')) {
            $tpl = str_replace('_mobi', '_html', $resource_name);
        } elseif (strpos($resource_name, '_xml')) {
            $tpl = str_replace('_xml', '_html', $resource_name);
        }

        if (isset($tpl) && !empty($tpl)){

            return $tpl;
        } else {
            return $smarty_obj->template_dir[0] . 'common/404.tpl';
        }

    }

}

?>