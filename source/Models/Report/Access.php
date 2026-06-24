<?php

namespace Source\Models\Report;


use Source\Core\Model;
use Source\Core\Session;

class Access extends Model
{
    public function __construct()
    {
        return parent::__construct("report_access", ["id"], ["users", "views", "pages"]);
     
    }
    /**
     * Summary of report
     * @return Access
     */
    public function report(): Access
    {
        /** @var Access|null $find */

        $find = $this->find("DATE(created_at) = DATE(now())")->fetch();
        $session = new Session();

        if (!$find) {
            $this->users = 1;
            $this->views = 1;
            $this->pages = 1;

            setcookie("access", true, time() + 86400, "/");
            $session->set("access", true);

            $this->save();
            return $this;
        }

        if (!filter_input(INPUT_COOKIE, "access")) {
            $find->users += 1;
            setcookie("access", true, time() + 86400, "/");
        }

        if (!$session->has("access")) {
            $find->views += 1;
            $session->set("access", true);
        }

        $find->pages += 1;
        $find->save();
        return $this;
    }

    public function save(): bool
    {
        $safe = $this->safe();

        if (!empty($this->id)) {
            $accessId = $this->id;
            $this->update($safe, "id = :id", "id={$accessId}");
            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }
            return true;
        }

        $accessId = $this->create($safe);
        if ($this->fail()) {
            $this->message->error("Erro ao cadastrar, verifique os dados");
            return false;
        }

        $this->data = $this->findById($accessId)->data();
        return true;
    }
}
