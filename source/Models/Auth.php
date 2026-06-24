<?php

namespace Source\Models;

use Source\Core\Model;
use Source\Core\View;
use Source\Support\Email;
use Source\Core\Session;

class Auth extends Model
{
    public function __construct()
    {
        parent::__construct("user", ["id"], ["email", "password"]);
    }
    /**
     * Summary of user
     * @return mixed|Model|User|null
     */
    public static function user()
    {
        $session = new Session();
        if (!$session->has("authUser")) {
            return null;
        }

        return (new User())->findById($session->authUser);
    }
    /**
     * Summary of logout
     * @return void
     */
    public static function logout()
    {
        $session = new Session();
        $session->unset("authUser");
    }



    /**
     * Summary of register
     * @param User $user
     * 
     */
    public function register(User $user)
    {
        if (!$user->save()) {
            return false;
        }

        $view = new View(__DIR__ . "/../../shared/views/email");
        $message = $view->render("confirm", [
            "first_name" => $user->first_name,
            "confirm_link" => url("/obrigado/" . base64_encode($user->email))
        ]);

        (new Email())->bootstrap(
            "Ative sua conta no " . CONF_SITE_NAME,
            $message,
            $user->email,
            "{$user->first_name} {$user->last_name}"
        )->send();

        return true;
    }

    /**
     * Summary of login
     * @param string $email
     * @param string $password
     * @param bool $save
     * @return bool
     */
    public function login(string $email, string $password, bool $save = false): bool
    {
        if (!is_email($email)) {
            $this->message->warning("O e-mail informado não é valido");
            return false;
        }

        if ($save) {
            setcookie("authEmail", $email, time() + 604800, "/");
        } else {
            setcookie("authEmail", "", time() - 3600, "/");
            unset($_COOKIE["authEmail"]);
        }

        if (!is_passwd($password)) {
            $this->message->warning("A senha informada não é valida");
            return false;
        }

        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->error("O e-mail informado não está cadastrado");
            return false;
        }

        if (!passwd_verify($password, $user->password)) {
            $this->message->error("A senha informada não confere");
            return false;
        }

        if (passwd_rehash($user->password)) {
            $user->password = $password;
            $user->save();
        }

        //LOGIN
        (new Session())->set("authUser", $user->id);
        $this->message->success("Login efetuado com sucesso")->flash();
        return true;
    }


    public function forget(string $email): bool
    {
        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->warning("O e-mail informado não está cadastrado");
            return false;
        }

        $user->forget = md5(uniqid(rand(), true));
        $user->save();

        $view = new View(__DIR__ . "/../../shared/views/email");
        $message = $view->render("forget", [
            "first_name" => $user->first_name,
            "forget_link" => url("/recuperar/{$user->email}|{$user->forget}")
        ]);

        (new Email())->bootstrap(
            "Recupere sua senha no " . CONF_SITE_NAME,
            $message,
            $user->email,
            "{$user->first_name} {$user->last_name}"
        )->send();

        return true;
    }
    /**
     * Summary of reset
     * @param string $email
     * @param string $code
     * @param string $password
     * @param string $passwordRe
     * @return bool
     */
    public function reset(string $email, string $code, string $password, string $passwordRe): bool
    {
        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->warning("A conta para recuperação não foi encontrada");
            return false;
        }

        if ($user->forget != $code) {
            $this->message->error("Desculpa, mas o código de verificação não é válido");
            return false;
        }

        if (!is_passwd($password)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->info("Sua senha deve ter entre {$min} e {$max} caracteres");
            return false;
        }

        if ($password != $passwordRe) {
            $this->message->warning("Você informou duas senhas diferentes");
            return false;
        }

        $user->password = passwd($password);
        $user->forget = null;
        $user->save();
        return true;
    }
}
