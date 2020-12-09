<?php namespace Pckg\Framework\Middleware;

use Pckg\Auth\Controller\Auth;

class LockInternal
{

    public function execute(callable $next)
    {
        /**
         * Skip in console and for admin.
         */
        if (!isHttp() || request()->isOptions() || auth()->isAdmin()) {
            return $next();
        }

        /**
         * Check by route.
         */
        $tags = router()->get('tags');
        if (!in_array('tag:internal', $tags)) {
            return $next();
        }

        if (config('pckg.auth.tags.tag:internal')(auth())) {
            return $next();
        }

        /**
         * Respond with static response.
         */
        response()->unauthorized('Page is not public yet');
    }

    public static function hasAccess(\Pckg\Auth\Service\Auth $auth)
    {

        if ($auth->isAdmin()) {
            return true;
        }

        if (!($internal = request()->get('internal', null))) {
            return false;
        }

        try {
            /**
             * We will generate password on request
             */
            $decoded = json_decode(base64_decode($internal), true);

            /**
             * Validate request first.
             */
            $signature = $decoded['signature'];
            unset($decoded['signature']);
            if ($signature !== sha1(json_encode($decoded))) {
                return false;
            }

            /**
             * Validate timestamp.
             */
            $timestamp = $decoded['timestamp'];
            if (strtotime($timestamp) < strtotime('-3hours')) {
                return false;
            }

            /**
             * Validate hash.
             */
            $hash = $decoded['hash'];
            $identifier = config('identifier', null);
            if ($auth->hashedPasswordMatches($hash, $identifier . $timestamp . $auth->getSecurityHash())) {
                return true;
            }
        } catch (Throwable $e) {
            error_log(exception($e));
        }

        return false;
    }

}