<?php

namespace Pckg\Framework\Router\Command;

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
                $realResolver = resolve($resolver);
            }

            $k = $data[$urlKey] ?? null;
            $resolved = $realResolver->resolve(urldecode($k));

            /**
             * Validate resolved value for access?
             */

            if (is_string($urlKey)) {
                $data[$urlKey] = $resolved;
            } else {
                $data[] = $resolved;
            }

            if (!is_int($urlKey)) {
                router()->resolve($urlKey, $resolved);
                /**
                 * Remove resolved key.
                 * Why? Can we delete it?
                 */
                if (isset($data[$urlKey])) {
                    //unset($data[$urlKey]);
                }
            }
        }

        return $data;
    }
}