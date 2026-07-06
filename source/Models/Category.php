<?php

namespace Source\Models;

use Source\Core\Model;

class Category extends Model
{
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        return parent::__construct("categories", ["id"], ["title", "id"]);
    }
    /**
     * Summary of findByUri
     * @param string $uri
     * @param string $columns
     * @return array|mixed|Model|null
     */
    public function findByUri(string $uri, string $columns = "*"): ?Category
    {
        $find = $this->find("uri = :uri", "uri={$uri}", $columns);
        return $find->fetch();
    }
}
