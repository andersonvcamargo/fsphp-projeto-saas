<?php

namespace Source\Models;

use Source\Core\Model;

class Post extends Model
{
    /** @var bool */
    private $all;

    /**
     * Summary of __construct
     * @param bool $all = ignote status and post_at
     */
    public function __construct(bool $all = false)
    {
        $this->all = $all;
        return parent::__construct("posts", ["id"], ["title", "id", "subtitle", "content"]);
    }

    /**
     * Summary of find
     * @param mixed $terms
     * @param mixed $params
     * @param string $columns
     * @return mixed|Model
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*")
    {
        if (!$this->all) {
            $terms = "status = :status AND post_at <= NOW()" . ($terms ? " AND {$terms}" : "");
            $params = "status=post" . ($params ? "&{$params}" : "");
        }
        return parent::find($terms, $params, $columns);
    }
    public function findByUri(string $uri, string $columns = "*"): ?Post
    {
        $find = $this->find("uri = :uri", "uri={$uri}", $columns);
        return $find->fetch();
    }

    /**
     * Summary of author
     * @return mixed|Model|User|null
     */
    public function author(): ?User
    {
        if ($this->author) {
            return (new User())->findById((int)$this->author);
        }
        return null;
    }

    /**
     * Summary of category
     * @return mixed|Model|User|null
     */
    public function category(): ?Category
    {
        if ($this->category) {
            return (new Category())->findById((int)$this->category);
        }
        return null;
    }
    
    /**
     * Summary of save
     * @return bool
     * 
     */
    public function save(): bool
    {
        if (!$this->required()) {
            return false;
        }

        $safe = $this->safe();

        /** Post update */
        if (!empty($this->id)) {
            $postId = (int)$this->id;
            $this->update($safe, "id = :id", "id={$postId}");
            
            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }
            
            $found = $this->findById($postId);
            if ($found) {
                $this->data = $found->data();
            }
            return true;
        }

        /** Post Create */
        $lastId = $this->create($safe);
        if (!$lastId) {
            $this->message->error("Erro ao criar post, verifique os dados");
            return false;
        }

        $found = $this->findById($lastId);
        if ($found) {
            $this->data = $found->data();
        }
        return true;
    }
}
