<?php

namespace Pckg\Framework\Request\Session;

class SessionUser
{

    protected $user;

    protected $attributes = [];

    public function setAttribute($key, $val = '')
    {
        $this->attributes[$key] = $val;

        return $this;
    }

    public function emptyAttribute($key)
    {
        $this->attributes[$key] = null;

        return $this;
    }

    public function getAttribute($key, $default = null)
    {
        return isset($this->attributes[$key])
            ? $this->attributes[$key]
            : $default;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function unsetAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

}