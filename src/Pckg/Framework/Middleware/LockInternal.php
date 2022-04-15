<?php

namespace Pckg\Framework\Middleware;

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

        /**
         * Check for permissions.
         */
        if (static::hasAccess(auth())) {
            return $next();
        }

        /**
         * Respond with static response.
         */
        response()->unauthorized('Page is not public');
    }

    public static function hasAccess(\Pckg\Auth\Service\Auth $auth)
    {
        if ($auth->isAdmin()) {
            return true;
        }

        /**
         * Check for values in header or get parameter.
         */
        $internal = request()->getHeader('X-Pckg-Anonymous');
        if (!$internal) {
            $internal = request()->get('internal', null);
        }

        /**
         * Restrict access when none are set.
         */
        if (!$internal) {
            return false;
        }

        return auth()->isValidInternalGetParameter($internal);
    }
}
