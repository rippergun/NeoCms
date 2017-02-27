<?php
namespace NeoCms;
/**
 * @package mvc
 * charge la conf du client depuis Neoconf
 * @dependency Neoconf
 * @author RiPPeR
 *
 */
class NeoClientRouteur
{
    public $clients = null;

    /**
     * renvoit les infos client selon le HOST
     */
    public final function checkUrl()
    {
        if (php_sapi_name() == 'cli') {

            if (isset($_SERVER['argv'][1])) {
                return $this->_checkConfUrl($_SERVER['argv'][1]);
            } else {
                return false;
            }
        } else {
            return $this->_checkConfUrl($_SERVER['HTTP_HOST']);
        }
    }

    /**
     * renvoit les infos client selon l'url
     * @param string $client_url
     */
    public final function checkClientName($client_url)
    {
        return $this->_checkConfUrl($client_url);
    }

    /**
     * charge la conf depuis le fichier de conf neocms.php
     */
    public final function loadConf()
    {
        if (($handle = fopen(PATH_CONF . "neocms.php", "r")) !== false) {
            $i       = 0;
            $clients = array();
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                if ($i == 0) {
                    $champs = $data;
                } else {
                    foreach ($champs as $key => $champ) {
                        $tmp[$champ] = $data[$key];
                    }
                    $clients[] = $tmp;
                }
                $i++;
            }
            fclose($handle);
            $this->_setCache($clients);
            return $clients;
        }
    }

    private final function _checkConfUrl($client_url)
    {
        if ($this->clients == null && !$this->clients = $this->_getCache('clients')) {
            $this->clients = $this->loadConf();
        }

        foreach ($this->clients as $client) {
            if ($client['client_url'] == $client_url) {
                return $client;
            }
        }

        return false;
    }

    /**
     * charge la conf depuis le fichier de conf neocms.php
     */
    public final function loadConfJson()
    {
        if (file_exists(PATH_CONF . "neocms.json") !== false) {
            $i       = 0;
            $clients = array();

            $_clients = json_decode(file_get_contents(PATH_CONF . "neocms.json"));

            foreach ($_clients as $id => $cli) {
                $cli->client_id = $id;
                $clients[$cli->client_url] = (array) $cli;
            }

            $this->_setCache($clients);
            return $clients;
        }
    }

    private final function _checkConfUrlJson($client_url)
    {
        if ($this->clients == null && !$this->clients = $this->_getCache('clients')) {
            $this->clients = $this->loadConf();
        }

        if (isset($this->clients[$client_url])) {
            return (array) $this->clients[$client_url];
        }

        return false;
    }

    private final function _setCache($clients)
    {
        //@todo linux
        return false;
    }

    private final function _getCache($key)
    {
        //@todo linux
        return false;
    }
}

?>