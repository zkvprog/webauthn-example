<?php

namespace App\System;

use App\Db\PdoConnector;
use App\System\Interfaces\ActiveRecord;
use App\System\Interfaces\DbConnector;
use PDO;

abstract class AbstractRepository implements ActiveRecord
{
    protected string $table;
    protected PDO $db;

    public function __construct()
    {
        $this->db = PdoConnector::getInstance();
    }

    public function create(array $data): string|false
    {
        $attrKeys = array_keys($data);
        $columns = implode(", ", $attrKeys);
        $values = ":" . implode(", :", $attrKeys);

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $stmt = $this->db->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function read(string $condition = "", array $params = [], ?string $fields = null, bool $readOne = false): array
    {
        $fields ??= '*';

        $query = "SELECT $fields FROM {$this->table}";

        if (!empty($condition)) {
            $query .= " WHERE $condition";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        if ($readOne) {
            return $stmt->fetch();
        } else {
            return $stmt->fetchAll();
        }
    }

    public function update(array $data, string $condition, array $params = []): int
    {
        $valuesList = [];
        foreach ($data as $key => $value) {
            $valuesList[] = "$key=:$key";
        }
        $valuesListAsString = implode(", ", $valuesList);;

        $query = "UPDATE {$this->table} SET $valuesListAsString WHERE $condition";
        $stmt = $this->db->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete(string $condition, array $params = []): int
    {
        $query = "DELETE FROM {$this->table} WHERE $condition";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}