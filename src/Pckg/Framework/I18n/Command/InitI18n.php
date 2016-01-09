<?php

namespace Pckg\Framework\I18n\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
use Pckg\Framework\Config;
use Pckg\Framework\Response;

class InitI18n extends AbstractChainOfReponsibility
{

    protected $config;

    protected $response;

    protected $context;

    public function __construct(Config $config, Response $response, Context $context)
    {
        $this->config = $config;
        $this->context = $context;
        $this->response = $response;
    }

    public function execute(callable $next)
    {
        $config = $this->config->__toArray();
        if (isset($config['defaults']['i18n'])) {
            $i18n = $config['defaults']['i18n'];
            if (!isset($i18n['type'])) $i18n['type'] = "session";

            $request = request();

            if (!isset($i18n['current'])) // because it can be overridden
                foreach ($i18n['langs'] AS $key => $lang) {
                    if ($i18n['type'] == "domain" && strpos($request->host(), $lang['code']) === 0) {
                        $i18n['current'] = $key;

                    } else if ($i18n['type'] == "url" && strpos($request->url(), $lang['code']) === 0) {
                        $i18n['current'] = $key;

                    } else if ($i18n['type'] == "cookie"
                        && isset($_COOKIE['lfw'])
                        && ($cookie = json_decode($_COOKIE['lfw']))
                        && isset($cookie['i18n'])
                        && $cookie['i18n'] == $lang['code']
                    ) {
                        $i18n['current'] = $key;
                    }

                    if (isset($i18n['current'])) break;
                }

            if (!isset($i18n['current'])) $i18n['current'] = $i18n['default'];

            // set session
            $_SESSION['lfw']['i18n'] = $i18n['current'];

            if ($i18n['force'] == true) // perform redirect
                if ($i18n['type'] == "domain" && strpos($request->host(), $lang['code']) !== 0) {
                    $this->response->redirect($request->scheme() . "://" . $i18n['langs'][$i18n['current']]['code'] . "." . $config['defaults']['domain'] . $request->url());

                } else if ($i18n['type'] == "url" && strpos($request->url(), $lang['code']) !== 0) {
                    die("url doesnt work ... yet ... =)");
                    $this->response->redirect($request->scheme() . "://" . $request->host() . $request->url());

                }
        }

        return $next();
    }

}