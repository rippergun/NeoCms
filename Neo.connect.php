<?php
//require_once('Zend/Registry.php');
require_once('Neo.mysqli.php');
require_once('Neo.pdo.php');

class NeoDB
{
    protected $connectionName;
    protected $registry;

    public function __construct($connectionName)
    {
        $this->connectionName = strtoupper($connectionName);
        $this->registry = Zend_Registry::getInstance();
    }

    /**
     * @param null $params
     * @return mixed|\NeoDb\Mysqli\NeoAbstractDB|\NeoDb\Pdo\NeoAbstractDB
     * @throws Exception
     */
    public function getDB($params = null)
    {
        if (empty($this->connectionName)) {
            throw new Exception('Connexion Vide');
        }

        if (!isset($this->registry[strtoupper($this->connectionName)]) || $this->registry[strtoupper($this->connectionName)] === false) {

            $params['host'] = isset($params['host']) ? $params['host'] : DATABASE_HOST;
            $params['username'] = isset($params['username']) ? $params['username'] : DATABASE_USER;
            $params['password'] = isset($params['password']) ? $params['password'] : DATABASE_PASSWD;
            $params['dbname'] = isset($params['dbname']) ? $params['dbname'] : DATABASE_NAME;

            if (!isset($params['charset'])) {
                if (defined('DATABASE_CHARSET')) {
                    $params['charset'] = DATABASE_CHARSET;
                } else {
                    $params['charset'] = null; // 'UTF-8';
                }
            }
            if (defined('MYSQL_DRIVER') && MYSQL_DRIVER == 'PDO') {
                $cnx = new \NeoDb\Pdo\NeoAbstractDB($params, $this->connectionName);
            } else {
                $cnx = new \NeoDb\Mysqli\NeoAbstractDB($params, $this->connectionName);
            }

            $this->registry->set(strtoupper($this->connectionName), $cnx);
        } else {
            //echo "using registry $this->connectionName \n";
            $cnx = $this->registry[strtoupper($this->connectionName)];
        }

        return $cnx;
    }

    static function resetDB($connectionName)
    {
       // require_once('Zend/Registry.php');
        $registry = Zend_Registry::getInstance();
        $registry->set(strtoupper($connectionName), false);
    }
}