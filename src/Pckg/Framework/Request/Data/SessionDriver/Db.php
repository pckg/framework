<?php

namespace Pckg\Framework\Request\Data\SessionDriver;

use Pckg\Framework\Request\Session\Record\Session;

class Db extends FileDriver
{

    protected $record;

    public function open($savePath, $name)
    {
        $sessionId = session_id();

        $this->record = Session::getAndUpdateOrCreate(['hash' => $sessionId], ['timestamp' => date('Y-m-d H:i:s')]);

        return !!$this->record;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        return $this->record->data;
    }

    public function write($sessionId, $data)
    {
        $ok = $this->record->setAndSave(['timestamp' => date('Y-m-d H:i:s'), 'data' => $data]);

        return !!$ok;
    }

    public function destroy($sessionId)
    {
        $ok = $this->record->delete();

        return !!$ok;
    }

    public function gc($maxLifetime)
    {
        return true;
    }

    public function regenerate()
    {
        return $this;
    }
}
