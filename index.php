<?php
ob_start();

require __DIR__ ."/vendor/autoload.php";

/** 
 * BOOTSTRAP
 */

use Source\Core\Session;
use CoffeeCode\Router\Router;

$session = new Session();
$route = new Router(url("/"),":");

/**
 * WEB ROUTES
 */
$route->namespace("Source\App");
$route->get("/", "Web:home", "home", []);
$route->get("/sobre", "Web:about", "about", []);
$route->get("/termos", "Web:terms", "terms", []);

//blog
$route->group("/blog");
$route->get("/", "Web:blog", "blog", []);
$route->get("/p/page/{page}", "Web:blog", "blog", []);
$route->post("/buscar", "Web:blogSearch", "blogSearch", []);
$route->get("/buscar/{terms}/{page}", "Web:blogSearch", "blogSearch", []);
$route->get("/{uri}", "Web:blogPost", "blogPost", []);
$route->get("/em/{category}", "Web:blogCategory", "blogCategory", []);
$route->get("/em/{category}/{page}", "Web:blogCategory", "blogCategory", []);

//auth
$route->group(null);
$route->get("/entrar", "Web:login", "login", []);
$route->post("/entrar", "Web:login", "login", []);
$route->get("/cadastrar", "Web:register", "register", []);
$route->post("/cadastrar", "Web:register", "register", []);
$route->get("/recuperar", "Web:forget", "forget", []);
$route->post("/recuperar", "Web:forget", "forget", []);
$route->get("/recuperar/{code}", "Web:reset", "reset", []);
$route->post("/recuperar/resetar", "Web:reset", "reset", []);


//optin
$route->get("/confirma", "Web:confirm", "confirm", []);
$route->get("/obrigado/{email}", "Web:success", "success", []);

//services
$route->get("/termos", "Web:terms", "terms", []);

/**
 * APP
 */
$route->group("/app");
$route->get("/", "App:home", "home", []);
$route->get("/receber", "App:income", "income", []);
$route->get("/pagar", "App:expense", "expense", []);
$route->get("/fatura/{invoice_id}", "App:invoice", "invoice", []);

$route->get("/perfil", "App:profile", "profile", []);
$route->get("/sair", "App:logout", "logout", []);

/**
 * ERROR ROUTES
 */
$route->namespace("Source\App")->group("/ops");
$route->get("/{errcode}", "Web:error", "error", []);

/**
 * ROUTE
 */
$route->dispatch();

/**
 * ERROR REDIRECT
 */
if($route->error()){
    $route->redirect("/ops/{$route->error()}");
}

ob_end_flush();