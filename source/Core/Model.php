<?php

namespace Source\Core;

use Source\Support\Message;
use Source\Models\User;



abstract class Model
{
    /** @var object|null */
    protected $data;

    /** @var \PDOException|null */
    protected $fail;

    /** @var Message|null */
    protected $message;

    /** @var string */
    protected $query;

    /** @var array|null */
    protected $params;

    /** @var string */
    protected $order;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var string $entity database table */
    protected static $entity;

    /** @var array $protected no update or create */
    protected static $protected;

    /** @var array $entity database table */
    protected static $required;

    /**
     * Model constructor.
     * @param string $entity database table name
     * @param array $protected table protected columns
     * @param array $required table required columns
     */
    public function __construct(string $entity, array $protected, array $required)
    {
        self::$entity = $entity;
        self::$protected = array_merge($protected, ['created_at', "updated_at"]);
        self::$required = $required;

        $this->message = new Message();
        $this->params = [];
        $this->group = "";
        $this->order = "";
        $this->limit = "";
        $this->offset = "";
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @return null|object
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return \PDOException
     */
    public function fail(): ?\PDOException
    {
        return $this->fail;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    /**
     * Summary of find
     * @param mixed $terms
     * @param mixed $params
     * @param string $columns
     * @return Model|mixed
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*")
    {
        if ($terms) {
            $this->query = "SELECT {$columns} FROM " . static::$entity . " WHERE {$terms}";
            parse_str($params, $this->params);
        } else {
            $this->query = "SELECT {$columns} FROM " . static::$entity;
        }

        return $this;
    }

    /**
     * Summary of findById
     * @param int $id
     * @param string $columns
     * @return User|mixed|Model
     */
    public function findById(int $id, string $columns = "*"): ?Model
    {
        $find = $this->find("id = :id", "id={$id}", $columns);
        return $find->fetch();
    }



    /**
     * Summary of order
     * @param string $columnOrder
     * @return Model
     */
    public function order(string $columnOrder): Model
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }
    /**
     * Summary of limit
     * @param int $limit
     * @return Model
     */
    public function limit(int $limit): Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }
    /**
     * Summary of offset
     * @param int $offset
     * @return Model
     */
    public function offset(int $offset): Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * Summary of fetch
     * @param bool $all
     * @return Model|array|null
     */
    public function fetch(bool $all = false): Model|array|null
    {
        try {
            $sql = $this->query
                . $this->group
                . $this->order
                . $this->limit
                . $this->offset;

            $stmt = Connect::getInstance()->prepare($sql);
            $stmt->execute($this->params);

            if ($all) {
                $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
                $result = $stmt->fetchAll();

                return $result ?: null;
            }

            $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
            $result = $stmt->fetch();

            return $result ?: null;
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Summary of count
     * @param string $key
     * @return int
     */
    public function count(string $key = "id"): int
    {
        $stmt = Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }






    /**
     * Summary of create
     * @param array $data
     * @return bool|string|null
     */
    protected function create(array $data): ?int
    {
        try {
            $columns = "`" . implode("`, `", array_keys($data)) . "`";
            $values = ":" . implode(", :", array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO " . static::$entity . " ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }



    /**
     * Summary of update
     * @param array $data
     * @param string $terms
     * @param string|array $params
     * @return int|null
     */
    protected function update(array $data, string $terms, $params): ?int
    {
        try {
            $dateSet = [];
            foreach ($data as $bind => $value) {
                $dateSet[] = "`{$bind}` = :{$bind}";
            }
            $dateSet = implode(", ", $dateSet);
            if (is_string($params)) {
                parse_str($params, $params);
            }

            $stmt = Connect::getInstance()->prepare("UPDATE " . static::$entity . " SET {$dateSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));
            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Save (Create or Update)
     * Automatically detects if the record is new or existing
     * @return bool|int|null
     */
    public function save()
    {
        if (!$this->required()) {
            return false;
        }

        $safe = $this->safe();

        if (empty($this->id)) {
            return $this->create($safe);
        } else {
            return $this->update($safe, "id = :id", "id={$this->id}");
        }
    }

    /**
     * Summary of delete
     * @param string $terms
     * @param null|string $params
     * @return bool
     */
    public function delete(string $terms, string $params): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM " . static::$entity . " WHERE {$terms} = :key");

            if ($params) {
                $parsedParams = [];
                parse_str($params, $parsedParams);
                $stmt->execute($parsedParams);
                return true;
            }

            $stmt->execute();
            return true;
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    public function destroy(): bool
    {
        if (empty($this->id)) {
            return false;
        }

        $destroy = $this->delete("id = :id", "id={$this->id}");
        return $destroy;
    }


    /**
     * @return array|null
     */
    protected function safe(): ?array
    {
        $safe = (array)$this->data;
        foreach (static::$protected as $unset) {
            unset($safe[$unset]);
        }
        if (isset($safe['group'])) {
            unset($safe['group']);
        }
        return $safe;
    }

    /**
     * @param array $data
     * @return array|null
     */
    private function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }
        return $filter;
    }

    /**
     * @return bool
     */
    protected function required(): bool
    {
        $data = (array)$this->data();
        foreach (static::$required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}
