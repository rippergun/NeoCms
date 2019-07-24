<?php
Namespace NeoCms;

use NeoCms\Pdo\NeoAbstractDb;

class NeoDb
{
    protected $connectionName;
    protected $registry;

    /**
     * @var \DataCollector\Logger
     */
    protected $logger;

    protected $debug;

    /**
     * NeoDb constructor.
     * @param $connectionName
     * @param \DataCollector\Logger $logger
     * @param bool $debug
     */
    public function __construct($connectionName, $logger, $debug = false)
    {
        $this->connectionName = strtoupper($connectionName);
        $this->registry = \Zend_Registry::getInstance();
        $this->setLogger($logger);
        $this->debug = $debug;
    }

    /**
     * @param \DataCollector\Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param null $params
     * @return \NeoCms\Pdo\NeoAbstractDB
     * @throws \Exception
     */
    public function getDB($params = null)
    {
        if (empty($this->connectionName)) {
            throw new \Exception('Connexion Vide');
        }

        if (!isset($this->registry[strtoupper($this->connectionName)]) || $this->registry[strtoupper($this->connectionName)] === false) {

            if (!isset($params['charset'])) {
                if (defined('DATABASE_CHARSET')) {
                    $params['charset'] = DATABASE_CHARSET;
                } else {
                    $params['charset'] = null; // 'UTF-8';
                }
            }

            $cnx = new Pdo\NeoAbstractDb($params, $this->connectionName, $this->logger, $this->debug);

            $this->registry->set(strtoupper($this->connectionName), $cnx);
        } else {
            $cnx = $this->registry[strtoupper($this->connectionName)];
        }

        return $cnx;
    }

    static function resetDB($connectionName)
    {
        $registry = \Zend_Registry::getInstance();
        $registry->set(strtoupper($connectionName), false);
    }
}
