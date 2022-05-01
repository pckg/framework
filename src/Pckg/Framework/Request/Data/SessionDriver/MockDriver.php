<?php

namespace Pckg\Framework\Request\Data\SessionDriver;

use SessionHandler;

class MockDriver extends SessionHandler
{
    protected $state = null;

    protected $sessions = [];

    public function close()
    {
        $this->state = 'closed';
        return true;
    }

    public function destroy($id)
    {
        if (isset($this->sessions[$id])) {
            unset($this->sessions[$id]);
        }

        return true;
    }

    public function gc($max_lifetime)
    {
        return true;
    }

    public function open($path, $name)
    {
        return true;
    }

    public function read($id)
    {
        return $this->sessions[$id] ?? [];
    }

    public function write($id, $data)
    {
        $this->sessions[$id] = $data;

        return true;
    }

    public function validateId($session_id)
    {
        return isset($this->sessions[$session_id]);
    }

    public function updateTimestamp($session_id, $session_data)
    {
        return true;
    }

    public function regenerate()
    {
        return $this;
    }
}
