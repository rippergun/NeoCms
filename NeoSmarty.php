<?php
Namespace NeoCms;
/**
 *
 * @todo tofix ne convient que pour CMS_services et cms_php.
 * @author RiPPeR
 *
 */
class NeoSmarty extends \Smarty
{
    /**
     * NeoSmarty constructor.
     * @param null $cache
     */
    public function __construct ($cache = null)
    {
        parent::__construct();

        if (!is_null($cache)) {
            $this->caching = $cache;
        } else {
            $this->caching = CACHE;
        }

        $this->cache_id = self::makeCacheId();
        $this->default_template_handler_func = array('\\NeoCms\\NeoSmarty', 'templateFallback');

    }

    /**
     * crée un identifiant de cache en fonction de l'url
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

    /**
     * @param $resource_type
     * @param $resource_name
     * @param $template_source
     * @param $template_timestamp
     * @param $smarty_obj
     * @return mixed|string
     */
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
