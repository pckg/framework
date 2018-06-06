<?php namespace Pckg\Framework\Router\Command;

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
    public function __construct(array $resolvers = null)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @return array|mixed
     */
    public function execute()
    {
        $router = router()->get();

        $data = router()->get('data');
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

            $k = $router[$urlKey] ?? router()->getCleanUri();
            $resolved = $realResolver->resolve(urldecode($k));

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