<?php

namespace Pckg\Framework\Helper;

/**
 * Trait Traits
 * @package Pckg\Framework\Helper
 */
trait Traits
{
    public function response()
    {
        return response();
    }

    public function request()
    {
        return request();
    }

    public function post($key = null, $default = null)
    {
        return post($key, $default);
    }

    public function get($key = null, $default = null)
    {
        return get($key, $default);
    }

    public function server($key = null, $default = null)
    {
        return server($key, $default);
    }

    public function cookie($key = null, $default = null)
    {
        return cookie($key, $default);
    }

    public function auth()
    {
        return auth();
    }

    public function router()
    {
        return router();
    }

    public function assetManager()
    {
        return assetManager();
    }

    public function seoManager()
    {
        return seoManager();
    }

    public function vueManager()
    {
        return vueManager();
    }

    public function localeManager()
    {
        return localeManager();
    }
}
