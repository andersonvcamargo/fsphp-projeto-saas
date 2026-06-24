<?php

namespace Source\Core;

use Source\Support\Seo;
use Source\Support\Message;

/**
 * FSPHP | Class Controller
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Core
 */
class Controller
{
    protected $view;
    protected $seo;
    protected $message;
    /**
     * Summary of __construct
     * @param mixed $pathtoViews
     */
    public function __construct($pathtoViews = null)
    {
        $this->view = new View($pathtoViews);
        $this->seo = new Seo($pathtoViews);
        $this->message = new Message();
    }

}