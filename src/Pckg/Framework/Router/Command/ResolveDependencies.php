<?php namespace Pckg\Framework\Router\Command;

use Pckg\Framework\Router;

class ResolveDependencies
{

    /**
     * @var Router
     */
    protected $router;

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
    public function __construct(Router $router, array $resolvers = null)
    {
        $this->router = $router;
        $this->resolvers = $resolvers;
    }

    /**
     * @return array|mixed
     */
    public function execute()
    {
        $router = $this->router->get();

        $data = $this->router->get('data');
        foreach ($this->resolvers as $urlKey => $resolver) {
            if (is_object($resolver)) {
                /**
                 * Resolver was passed.
                 */
                $realResolver = $resolver;
            } else if (is_array($resolver)) {
                /**
                 * Array was passed with resolver value.
                 */
                $realResolver = resolve(array_keys($resolver)[0]);
                $router[$urlKey] = end($resolver);
            } else {
                /**
                 * Create resolver.
                 */
                $realResolver = resolve($resolver);
            }

            $resolved = $realResolver->resolve($router[$urlKey] ?? $this->router->getCleanUri());

            if (is_string($urlKey)) {
                $data[$urlKey] = $resolved;
            }

            $data[] = $resolved;
            if (!is_int($urlKey)) {
                $this->router->resolve($urlKey, $resolved);
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