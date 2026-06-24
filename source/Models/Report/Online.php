<?php

namespace Source\Models\Report;


use Source\Core\Model;
use Source\Core\Session;


class Online extends Model
{

    private $sessionTime;
    /**
     * Summary of __construct
     * @param int $sessionTime
     */
    public function __construct(int $sessionTime = 20)
    {
        $this->sessionTime = $sessionTime;
        return parent::__construct("report_online", ["id"], ["ip", "url", "agent"]);
    }

    /**
     * Summary of findByActive
     * @param bool $count
     * @return array|int|null
     */
    public function findByActive(bool $count = false)
    {
        $find = $this->find("updated_at >= NOW() - INTERVAL {$this->sessionTime} MINUTE");
        if ($count) {
            return $find->count();
        }

        return $find->fetch(true);
    }

    /**
     * Summary of report
     * @return Online
     */
    public function report(bool $clear = true): Online
    {
        $session = new Session();

        if (!$session->has("online")) {
            $this->user = ($session->authUser ?? null);
            $this->url = htmlspecialchars(filter_input(INPUT_GET, "route") ?? "/", ENT_QUOTES, "UTF-8");
            $this->ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");
            $this->agent = filter_input(INPUT_SERVER, "HTTP_USER_AGENT");

            $this->save();
            $session->set("online", $this->id);
            return $this;
        }

        $find = $this->findById($session->online);
        if(!$find){
            $session->unset("online");
            return $this;
        }

        $find->user = ($session->authUser ?? null);
        $find->url = htmlspecialchars(filter_input(INPUT_GET, "route") ?? "/", ENT_QUOTES, "UTF-8");
        $find->pages += 1;
        $find->save();

        if($clear){
            $this->clear();
        }

        return $this;
    }

    public function clear(): void
    {
        $this->delete("updated_at <= NOW() - INTERNVAL {$this->sessionTime}", "");
    }

    /**
     * Summary of save
     * @return bool
     */
    public function save(): bool
    {
        $safe = $this->safe();

        if (!empty($this->id)) {
            $onlineId = $this->id;
            $this->update($safe, "id = :id", "id={$onlineId}");
            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }
            return true;
        }

        $onlineId = $this->create($safe);
        if ($this->fail()) {
            $this->message->error("Erro ao cadastrar, verifique os dados");
            return false;
        }

        $this->data = $this->findById($onlineId)->data();
        return true;
    }
}
