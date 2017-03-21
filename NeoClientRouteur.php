<?php
namespace NeoCms;

use Symfony\Component\Config\Definition\Exception\Exception;

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
     * @var string
     */
    private $pathConf = '/home/projects/NeoConf/';

    public function __construct($pathConf = null)
    {
        if (!is_null($pathConf)) {
            $this->pathConf = $pathConf;
        }
    }

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
        if (file_exists($this->pathConf . "neocms.json") !== false) {
            $clients = array();
            $_clients = json_decode(file_get_contents($this->pathConf . "neocms.json"));
            foreach ($_clients as $id => $cli) {
                $cli->client_id = $id;
                $clients[$cli->client_url] = (array) $cli;
            }
            return $clients;
        }
        throw new Exception("Unable to load client list from " . $this->pathConf . "neocms.json");
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