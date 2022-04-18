<?php

namespace Pckg\Framework\Router\Command;

use Pckg\Concept\Resolver;
use Pckg\Framework\Provider\ResolvesMultiple;
use Pckg\Framework\Provider\RouteResolver;
use Pckg\Framework\Router;

class ResolveDependencies
{

    /**
     * @var array
     */
    protected $resolvers = [];

    /**
     * ResolveDependencies constructor.
     *
     * @param Router $router
     * @param        $resolvers
     */
    public function __construct(array $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @return array|mixed
     */
    public function execute()
    {
        $router = router();
        $data = router()->get('data');
        $resolvers = $this->resolvers;

        /**
         * First resolve datas.
         */
        foreach ($data as $k => $v) {
            if (isset($resolvers[$k])) {
                continue;
            }

            $router->resolve($k, $v);
        }

        foreach ($resolvers as $urlKey => $resolver) {
            if (is_only_callable($resolver)) {
                /**
                 * Callable was passed to optimize things.
                 */
                $realResolver = $resolver();
            } else if (is_object($resolver)) {
                /**
                 * Resolver was passed.
                 */
                $realResolver = $resolver;
            } else if (is_array($resolver)) {
                /**
                 * Array was passed with resolver value.
                 */
                $realResolver = resolve(array_keys($resolver)[0]);
                $data[$urlKey] = end($resolver);
            } else {
                /**
                 * Create resolver.
                 */
                $realResolver = resolve($resolver, $data);
            }

            $k = $data[$urlKey] ?? null;
            $resolved = is_object($realResolver) && \Pckg\Concept\Helper\object_implements($realResolver, RouteResolver::class)
                ? $realResolver->resolve(urldecode($k))
                : $realResolver;

            /**
             * Validate resolved value for access?
             */
            $resolvesMultiple = object_implements($realResolver, ResolvesMultiple::class);
            if (is_string($urlKey)) {
                $data[$urlKey] = $resolved;
                router()->resolve($urlKey, $resolved);
            } elseif ($resolvesMultiple) {
                foreach ($resolved as $resolvedKey => $resolvedValue) {
                    $data[$resolvedKey] = $resolvedValue;
                    router()->resolve($resolvedKey, $resolvedValue);
                }
            } else {
                $data[] = $resolved;
            }
        }

        return $data;
    }
}
