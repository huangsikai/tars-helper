<?php

namespace Hsk\TarsHelper\Util;

/**
 * DB类 临时用.
 */
class Db
{
    private $connector__ = null;

    private $statement__ = null;

    public function __construct($config = [])
    {
        $database = 'my_tars';
        $host     = 'localhost:3306';
        $user     = 'root';
        $pwd      = 'root';

        if($config){
            extract($config);
        }

        $this->connector__ = new \PDO('mysql:dbname=' . $database . ';host=' . $host, $user, $pwd, [
            \PDO::ATTR_ORACLE_NULLS      => \PDO::NULL_TO_STRING,
            \PDO::ATTR_STRINGIFY_FETCHES => false,
            \PDO::ATTR_AUTOCOMMIT        => true,
            \PDO::ATTR_PERSISTENT        => true,
        ]);
        $this->connector__->query('set names utf8mb4');
        $this->connector__->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function insert($table, $datas)
    {
        $fields       = [];
        $params       = [];
        $placeholders = [];
        foreach ($datas as $field => $value) {
            $fields[]       = $field;
            $params[]       = $value;
            $placeholders[] = '?';
        }

        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $placeholders) . ')';

        $this->execute__($sql, $params);

        return $this->rowCount();
    }

    public function update($table, $where, $datas)
    {
        $fields       = [];
        $params       = [];
        $whereFields  = [];
        foreach ($datas as $field => $value) {
            $fields[]       = $field . ' = ?';
            $params[]       = $value;
        }

        foreach ($where as $f => $v) {
            $whereFields[]  = $f . ' = ?';
            $params[]       = $v;
        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $fields) . ' WHERE ' . implode(' AND ', $whereFields);
        $this->execute__($sql, $params);

        return $this->rowCount();
    }

    /**
     * 查找.
     *
     * @param $sql
     * @param $params
     *
     * @return array
     */
    public function select($sql, $params = [])
    {
        $this->execute__($sql, $params);

        if ($this->rowCount() > 0) {
            return $this->statement__->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    /**
     * 查找.
     *
     * @param $sql
     * @param $params
     *
     * @return array
     */
    public function first($sql, $params = [])
    {
        $this->execute__($sql, $params);

        if ($this->rowCount() > 0) {
            return $this->statement__->fetch(\PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    /**
     * Exec SQL.
     *
     * @param string $sql
     * @param array  $params
     *
     * @return $this
     */
    private function execute__(string $sql, array $params = [])
    {
        $this->statement__ = $this->connector__->prepare($sql);

        if (empty($params)) {
            $this->statement__->execute();
        } else {
            $this->statement__->execute($params);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function rowCount()
    {
        return $this->statement__->rowCount();
    }
}
