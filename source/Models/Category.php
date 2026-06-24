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
    /**
     * Summary of save
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Título é obrigatório");
            return false;
        }

        $safe = $this->safe();

        /** Category Update */
        if (!empty($this->id)) {
            $categoryId = (int)$this->id;
            $this->update($safe, "id = :id", "id={$categoryId}");

            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }

            $found = $this->findById($categoryId);
            if ($found) {
                $this->data = $found->data();
            }
            return true;
        }

        /** Category Create */
        $categoryId = $this->create($safe);
        if ($this->fail() || !$categoryId) {
            $this->message->error("Erro ao criar, verifique os dados");
            return false;
        }

        $found = $this->findById($categoryId);
        if ($found) {
            $this->data = $found->data();
        }
        return true;
    }
}
