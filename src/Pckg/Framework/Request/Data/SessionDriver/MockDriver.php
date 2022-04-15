<?php

namespace Pckg\Framework\Request\Data\SessionDriver;

use SessionHandler;
use Exception;

class MockDriver extends SessionHandler
{
    protected $state = null;

    protected $sessions = [];

    public function close()
    {
        $this->state = 'closed';
        return $this;
    }

    public function destroy($id)
    {
        if (isset($this->sessions[$id])) {
            unset($this->sessions[$id]);
        }

        return $this;
    }

    public function gc($max_lifetime)
    {
        return $this;
    }

    public function open($path, $name)
    {
        return $this;
    }

    public function read($id)
    {
        return $this->sessions[$id] ?? [];
    }

    public function write($id, $data)
    {
        $this->sessions[$id] = $data;

        return $this;
    }

    public function validateId($session_id)
    {
        return isset($this->sessions[$session_id]);
    }

    public function updateTimestamp($session_id, $session_data)
    {
        return $this;
    }

    public function regenerate()
    {
        return $this;
    }
}
