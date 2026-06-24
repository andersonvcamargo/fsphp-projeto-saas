<?php

namespace Source\Models\Faq;

use Source\Core\Model;

class Question extends Model
{
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        return parent::__construct("faq_questions", ["id"], ["channel_id", "question", "response"]);
    }
    /**
     * Summary of save
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Canal, pergunta e resposta são obrigatórios");
            return false;
        }

        $safe = $this->safe();

        if (!empty($this->id)) {
            $questionId = (int)$this->id;
            $this->update($safe, "id = :id", "id={$questionId}");

            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }

            $found = $this->findById($questionId);
            if ($found) {
                $this->data = $found->data();
            }
            return true;
        }

        $questionId = $this->create($safe);
        if ($this->fail() || !$questionId) {
            $this->message->error("Erro ao criar, verifique os dados");
            return false;
        }

        $found = $this->findById($questionId);
        if ($found) {
            $this->data = $found->data();
        }
        return true;
    }
}
