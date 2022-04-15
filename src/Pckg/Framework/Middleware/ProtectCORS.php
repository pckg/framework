<?php

namespace Pckg\Framework\Middleware;

class ProtectCORS
{
    public function execute(callable $next)
    {
        /**
         * Skip non-HTTP requests.
         */
        if (!isHttp()) {
            return $next();
        }

        $config = config('pckg.cors', [
            'allow' => [
                'origin' => '*', // @T00D00 - build a allow-list
                'methods' => 'GET, POST, PATCH, PUT, DELETE, OPTIONS, SEARCH',
                'headers' => 'X-Pckg-Csrf, Content-Type, Accept, Authorization, X-Requested-With, Application',
                'credentials' => 'true',
            ],
            'vary' => 'Origin',
        ]);

        /**
         * Skip CORS procedures when config is not set.
         */
        if (!$config) {
            return $next();
        }

        /**
         * CORS only applies for frontend requests made from other browsers / domains.
         * When there's api key set, check for allowed domains?
         *   API should have IP on the allow-list.
         * What about csrf header?
         *   CSRF header should match with session info.
         */

        /**
         * * or host?
         */
        if ($origin = ($config['allow']['origin'] ?? null)) {
            /**
             * Send the same origin as we see it.
             */
            $origin = server('HTTP_ORIGIN', server('HTTP_REFERER', null));
            if ($origin) {
                header('Access-Control-Allow-Origin: ' . $origin);
            }
        }

        /**
         * Skip the rest for non-OPTIONS requests?
         */
        if (!request()->isOptions()) {
            return $next();
        }

        /**
         * GET, POST, PATCH, PUT, DELETE, OPTIONS
         */
        if ($methods = ($config['allow']['methods'] ?? null)) {
            /**
             * @T00D00 - fetch allowed methods from router?
             */
            header('Access-Control-Allow-Methods: ' . $methods);
        }

        /**
         * X-Pckg-Csrf, Content-Type, Accept, Authorization, X-Requested-With, Application
         */
        if ($headers = ($config['allow']['headers'] ?? null)) {
            header('Access-Control-Allow-Headers: ' . $headers);
        }

        /**
         * true
         */
        if ($credentials = ($config['allow']['credentials'] ?? null)) {
            header('Access-Control-Allow-Credentials: ' . $credentials);
        }

        /**
         * Origin
         */
        if ($vary = ($config['vary'] ?? null)) {
            header('Vary: ' . $vary);
        }

        return $next();
    }
}
