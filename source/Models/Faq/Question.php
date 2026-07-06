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
}
