<?php

namespace Source\Models\Faq;

use Source\Core\Model;

class Channel extends Model
{
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        return parent::__construct("faq_channels", ["id"], ["channel", "description"]);
    }
    /**
     * Summary of save
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->required()) {
            $this->message->warning("Canal e descrição são obrigatórios");
            return false;
        }

        $safe = $this->safe();

        if (!empty($this->id)) {
            $channelId = (int)$this->id;
            $this->update($safe, "id = :id", "id={$channelId}");

            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }

            $found = $this->findById($channelId);
            if ($found) {
                $this->data = $found->data();
            }
            return true;
        }

        $channelId = $this->create($safe);
        if ($this->fail() || !$channelId) {
            $this->message->error("Erro ao criar, verifique os dados");
            return false;
        }

        $found = $this->findById($channelId);
        if ($found) {
            $this->data = $found->data();
        }
        return true;
    }
}
