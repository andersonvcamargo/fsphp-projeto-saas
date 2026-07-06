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
}
