# Routing

_Helpers: url() route() vueRoute()_

## Registration

Routes can be easily defined in any Provider:

```
...
class SomeProvider extends Provider {
    public function routes() {
        return [
            'index' => route('/foo', 'foo', \Bar\Controller\Baz::class),
            'contact' => vueRoute('/vue', 'vue-component'),
        ];
    }   
}
```

## Groups

When sharing same parameters, they can be easily grouped:

```
public function routes() {
    return [
        routeGroup([
            'controller' => Baz::class,
            'urlPrefix' => '/foo',
            'namePrefix' => 'named',
        ], [
            'bar' => route('/bar', 'baz),
            'bar' => route('/bar/[baz]', 'baz)->resolvers([
                'baz' => BazResolver::class,
            ]),
        ]),
    ];
}
```

# Resolvers

Common usage is with named route parameters and then implementing resolver:
```
class Bar extends \Pckg\Framework\Router\RouteResolver {

    public function resolve($value) {
        return (new Foos())->where('id', $value)->withPermissionsToRead()->oneOrFail();
    }
    
    public function parametrize($record) {
        return $record->id;
    }

}
```
