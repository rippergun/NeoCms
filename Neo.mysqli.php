<?php
Namespace NeoDb\Mysqli;

class NeoAbstractDB
{
    /**
     * @var \mysqli
     */
    protected $cnx = array();

    protected $connectionName;

    protected $log = array();

    protected $debug = false;

    protected $mtime = 0;

    protected $connectionParams = array();

    public function __construct($params, $connectionName)
    {
        if (defined('DEBUG_DB') && DEBUG_DB == true) {
            $this->debug = true;
        }

        $this->connectionParams[$connectionName] = $params;
        $this->connectionName             = $connectionName;
        return $this;
        //return $this->_getConnection($params, $connectionName);
    }

    private final function _getConnection($params, $connectionName)
    {
        $this->connectionName             = $connectionName;
        try {
            $this->cnx[$this->connectionName] = @new \mysqli($params['host'], $params['username'], $params['password']);
        } catch (\Exception $e) {
            return false;
        }

        if ($this->cnx[$this->connectionName]->connect_error) {
            trigger_error(utf8_decode($this->cnx[$this->connectionName]->connect_error), E_USER_WARNING);
            return false;
        }

        // charset
        if (!is_null($params['charset'])) {
            $this->cnx[$this->connectionName]->query('SET NAMES ' . $params['charset']);
        } else {
            $this->cnx[$this->connectionName]->query('SET NAMES Latin1');
        }

        $this->cnx[$this->connectionName]->select_db($params['dbname']);
        return $this->cnx[$this->connectionName];
    }

    private function fetchAssoc($handle)
    {
        if ($handle !== false) {
            $result = $handle->fetch_assoc();
        }
        return isset($result) ? $result : null;
    }

    public function fetchall($sql)
    {
        $handle = $this->query($sql);
        while ($res = $this->fetchAssoc($handle)) {
            $result[] = $res;
        }
        return isset($result) ? $result : null;
    }

    public function fetchone($sql)
    {
        $handle = $this->query($sql);
        $result = $handle->fetch_row();
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
        $result = $this->fetchAssoc($handle);

        return isset($result) ? $result : null;
    }

    public function foundRows()
    {
        return $this->fetchone('SELECT FOUND_ROWS()');
    }

    public function query($sql)
    {
        if ($this->debug) {
            $mtime = microtime(true);
        }

        if(!isset($this->cnx[$this->connectionName]) || get_class($this->cnx[$this->connectionName]) != 'mysqli' ) {
            $connected = $this->_getConnection($this->connectionParams[$this->connectionName], $this->connectionName);
        } else {
            $connected = true;
        }

        if ($connected) {
            $result = $this->cnx[$this->connectionName]->query($sql);
        } else {
            $result = false;
        }

        if ($this->debug) {
            $mtime = microtime(true) - $mtime;
            $this->mtime += $mtime;
            $this->log[] = number_format($mtime, 4) . " seconde : $sql ";
        }

        if (!$result) {
            try {
                throw new \Exception($this->cnx[$this->connectionName]->error . '<br>' . $sql);
            } catch (\Exception $e) {
                trigger_error($e->getMessage() . "\n" . $e->getTraceAsString(), E_USER_WARNING);

            }
        }
        return isset($result) ? $result : null;
    }

    public function lastInsertId()
    {
        $id = $this->cnx[$this->connectionName]->insert_id;
        return isset($id) ? $id : 0;
    }

    public final function closeConnection()
    {
        if (isset($this->cnx[$this->connectionName]) && is_resource($this->cnx[$this->connectionName])) {
            $this->cnx[$this->connectionName]->close();
            \NeoDB::resetDB($this->connectionName);
            unset($this->cnx[$this->connectionName]);
        }
    }

    public function escapeString($string)
    {
        if(!isset($this->cnx[$this->connectionName]) || get_class($this->cnx[$this->connectionName]) != 'mysqli' ) {
            //var_dump($this->connectionParams, $this->connectionName);
            $this->_getConnection($this->connectionParams[$this->connectionName], $this->connectionName);
        }

        return $this->cnx[$this->connectionName]->escape_string($string);
    }

    public function affected_rows()
    {
        return $this->cnx[$this->connectionName]->affected_rows;
    }

    public final function __destruct()
    {
        if ($this->debug) {
            echo '<div id="debug">' . count($this->log) . ' requêtes SQL en ' . number_format($this->mtime, 2) . '<ul>';
            foreach ($this->log as $log) {
                echo "<li>$log</li>";
            }
            echo '</ul></div>';
        }
    }
}
