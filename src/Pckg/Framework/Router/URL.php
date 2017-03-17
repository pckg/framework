<?php

namespace Pckg\Framework\Router;

class URL
{

    public $domain;

    public $url;

    public $params;

    public $protocol;

    public $absolute;

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams(array $params = [])
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param mixed $protocol
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @param mixed $absolute
     */
    public function setAbsolute($absolute = true)
    {
        $this->absolute = $absolute;

        return $this;
    }

    /**
     * @param mixed $absolute
     */
    public function setRelative($relative = true)
    {
        $this->absolute = !$relative;

        return $this;
    }

    public function __toString()
    {
        return (string)($this->absolute
            ? $this->absolute()
            : $this->relative());
    }

    public function absolute()
    {
        return $this->protocol . '://' . $this->domain . $this->relative();
    }

    public function relative()
    {
        return $this->url . http_build_query($this->params);
    }

}