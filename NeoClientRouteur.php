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
                return $this->checkConfUrl($_SERVER['argv'][1]);
            } else {
                return false;
            }
        } else {
            return $this->checkConfUrl($_SERVER['HTTP_HOST']);
        }
    }

    /**
     * renvoit les infos client selon l'url
     * @param string $client_url
     */
    public final function checkClientName($client_url)
    {
        return $this->checkConfUrl($client_url);
    }

      /**
     * charge la conf depuis le fichier de conf neocms.php
     */
    public final function loadConfJson()
    {
        if (file_exists(PATH_CONF . "neocms.json") !== false) {
            $clients = array();
            $_clients = json_decode(file_get_contents(PATH_CONF . "neocms.json"));
            foreach ($_clients as $id => $cli) {
                $cli->client_id = $id;
                $clients[$cli->client_url] = (array) $cli;
            }
            return $clients;
        }
    }

    private final function checkConfUrl($client_url)
    {
        if ($this->clients == null) {
            $this->clients = $this->loadConfJson();
        }

        foreach ($this->clients as $client) {
            if ($client['client_url'] == $client_url) {
                return $client;
            }
        }

        return false;
    }
}

?>