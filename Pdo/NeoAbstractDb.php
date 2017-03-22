<?php
namespace NeoCms\Pdo;

class NeoAbstractDb
{
    /**
     * @var \PDO
     */
    protected $cnx = [];

    protected $connectionName;

    protected $log = [];

    protected $debug = false;

    protected $mtime = 0;

    protected $connectionParams = [];

    /**
     * @var \DataCollector\Logger
     */
    protected $logger;

    public function __construct($params, $connectionName, $logger)
    {
        if (defined('DEBUG_DB') && DEBUG_DB == true) {
            $this->debug = true;
        }

        $this->setLogger($logger);

        $this->connectionParams[$connectionName] = $params;
        $this->connectionName                    = $connectionName;

        return $this;
        //return $this->_getConnection($params, $connectionName);
    }

    /**
     * @param \DataCollector\Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    private final function getConnection($params, $connectionName)
    {
        $this->connectionName = $connectionName;
        try {
            $this->cnx[$this->connectionName] = @new \PDO(
                "mysql:host={$params['host']};dbname={$params['dbname']}",
                $params['username'],
                $params['password'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PdoException $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            \httpError::error503();

            return false;
        }

        // charset
        if (!is_null($params['charset'])) {
            $this->cnx[$this->connectionName]->query('SET NAMES '.$params['charset']);
        } else {
            $this->cnx[$this->connectionName]->query('SET NAMES Latin1');
        }

        //$this->cnx[$this->connectionName]->select_db($params['dbname']);
        return $this->cnx[$this->connectionName];
    }

    /*private function fetchAssoc($handle)
    {
        if ($handle !== false) {
            $result = $handle->fetch_assoc();
        }
        return isset($result) ? $result : null;
    }*/

    public function fetchall($sql)
    {
        $handle = $this->query($sql);
        $result = $handle->fetchAll(\PDO::FETCH_ASSOC);

        return !empty($result) ? $result : null;
    }

    public function fetchone($sql)
    {
        $handle = $this->query($sql);
        $result = $handle->fetch(\PDO::FETCH_NUM);

        return isset($result[0]) ? $result[0] : null;
    }

    /*public function fetchcol($sql)
    {
        $handle = $this->query($sql);
        $result = $this->fetchAssoc($handle)();

        return isset($result) ? $result : null;
    }*/

    public function fetchrow($sql)
    {
        $handle = $this->query($sql);
        if (!is_null($handle)) {
            $result = $handle->fetch(\PDO::FETCH_ASSOC);
        }

        return !empty($result) ? $result : null;
    }

    public function foundRows()
    {
        return $this->cnx[$this->connectionName]->query('SELECT FOUND_ROWS()')->fetchColumn();
    }

    /**
     * @param $sql
     * @return \PDOStatement|null
     */
    public function query($sql)
    {
        if ($this->debug) {
            $mtime = microtime(true);
        }

        if (!isset($this->cnx[$this->connectionName]) || get_class($this->cnx[$this->connectionName]) != '\\NeoCms\\NeoDb\\Pdo\\NeoAbstractDB') {
            $connected = $this->getConnection($this->connectionParams[$this->connectionName], $this->connectionName);
        } else {
            $connected = true;
        }

        if ($connected) {
            try {
                $result = $this->cnx[$this->connectionName]->query($sql);
            } catch (\PDOException $e) {
                trigger_error($e->getMessage()."\n".$e->getTraceAsString(), E_USER_WARNING);
            }
        } else {
            $result = null;
        }

        if ($this->debug) {
            $mtime = microtime(true) - $mtime;
            $this->mtime += $mtime;
            $this->log[] = [
                'time'  => $mtime,
                'link'  => $this->cnx[$this->connectionName]->getAttribute(\PDO::ATTR_CONNECTION_STATUS),
                'query' => $sql,
            ];

        }

        /*if (!$result) {
            try {
                throw new \Exception($this->cnx[$this->connectionName]->error . '<br>' . $sql);
            } catch (\Exception $e) {
                trigger_error($e->getMessage() . "\n" . $e->getTraceAsString(), E_USER_WARNING);

            }
        }*/

        return isset($result) && $result !== false ? $result : null;
    }

    /**
     * @param $sql
     * @return \PDOStatement
     */
    public function prepare($sql)
    {

        if (!isset($this->cnx[$this->connectionName]) || get_class($this->cnx[$this->connectionName]) != 'mysqli') {
            $connected = $this->getConnection($this->connectionParams[$this->connectionName], $this->connectionName);
        } else {
            $connected = true;
        }

        if ($connected) {
            try {
                $result = $this->cnx[$this->connectionName]->prepare($sql);
            } catch (\PDOException $e) {
                trigger_error($e->getMessage()."\n".$e->getTraceAsString(), E_USER_WARNING);
            }
        } else {
            $result = false;
        }

        return $result;
    }

    public function lastInsertId()
    {
        $id = $this->cnx[$this->connectionName]->lastInsertId();

        return isset($id) ? $id : 0;
    }

    public final function closeConnection()
    {
        if (isset($this->cnx[$this->connectionName])) {

            //vide le registre
            \NeoCms\NeoDb::resetDB($this->connectionName);

            // coupe la connexion facon pdo
            $this->cnx[$this->connectionName] = null;

            //unset la connexion
            unset($this->cnx[$this->connectionName]);
        }
    }

    /**
     * @param $string
     * @return mixed
     */
    public function escapeString($string)
    {
        return $string;
        // return $this->quote($string);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function quote($string)
    {
        if (!isset($this->cnx[$this->connectionName]) || get_class($this->cnx[$this->connectionName]) != 'mysqli') {
            $this->getConnection($this->connectionParams[$this->connectionName], $this->connectionName);
        }

        return $this->cnx[$this->connectionName]->quote($string);

    }

    public function affected_rows(\PDOStatement $handle)
    {
        return $handle->rowCount();
    }

    public function print_query($sql, $placeholders)
    {
        foreach ($placeholders as $k => $v) {
            $sql = preg_replace('/:'.$k.'/', "'".$v."'", $sql);
        }

        return $sql;
    }

    public function destruct()
    {
        if (defined('ENV') && ENV == 'dev' && defined('ENV_DEBUG') && ENV_DEBUG === true) {
            if (!empty($this->debug)) {
                $this->logger->log('total', count($this->log), 'Db');
                $this->logger->log(
                    'time',
                    $this->mtime,
                    'Db'
                );

                foreach ($this->log as $k => $d) {
                    $this->logger->log($k, $d, 'Db');
                }
            }
            $this->log = [];
        }
    }
}
