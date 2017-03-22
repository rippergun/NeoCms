<?php
Namespace NeoCms;

class NeoDb
{
    protected $connectionName;
    protected $registry;

    /**
     * @var \DataCollector\Logger
     */
    protected $logger;

    /**
     * NeoDb constructor.
     * @param $connectionName
     * @param \DataCollector\Logger $logger
     */
    public function __construct($connectionName, $logger)
    {
        $this->connectionName = strtoupper($connectionName);
        $this->registry = \Zend_Registry::getInstance();
        $this->setLogger($logger);
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
//            $params['host'] = isset($params['host']) ? $params['host'] : DATABASE_HOST;
//            $params['username'] = isset($params['username']) ? $params['username'] : DATABASE_USER;
//            $params['password'] = isset($params['password']) ? $params['password'] : DATABASE_PASSWD;
//            $params['dbname'] = isset($params['dbname']) ? $params['dbname'] : DATABASE_NAME;

            if (!isset($params['charset'])) {
                if (defined('DATABASE_CHARSET')) {
                    $params['charset'] = DATABASE_CHARSET;
                } else {
                    $params['charset'] = null; // 'UTF-8';
                }
            }

            $cnx = new Pdo\NeoAbstractDb($params, $this->connectionName, $this->logger);

            $this->registry->set(strtoupper($this->connectionName), $cnx);
        } else {
            $cnx = $this->registry[strtoupper($this->connectionName)];
        }

        return $cnx;
    }

    static function resetDB($connectionName)
    {
       // require_once('Zend/Registry.php');
        $registry = \Zend_Registry::getInstance();
        $registry->set(strtoupper($connectionName), false);
    }
}
