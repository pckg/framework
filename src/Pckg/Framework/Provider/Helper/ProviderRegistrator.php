<?php

namespace Pckg\Framework\Provider\Helper;

trait ProviderRegistrator {

    public function registerProviders($providers) {
        foreach ($providers as $provider => $config) {
            if (is_int($provider)) {
                $provider = $config;
            }

            $provider = new $provider($this->manager);
            $provider->register();
        }
    }

}