# Controllers

## Actions

Each actions is prefixed with request method and suffixed with 'Action'. For example, when user makes `GET` request
on `foo` action, `getFooAction()` is called.

# Middlewares

Middlewares are nothing else than simple filters or commands that get executed before controller action gets called. You
can use them for restricting access, check for autologin cookie, log requests or in any other way. All you have to do is
implement `execute(callable $next)` method and return `$next()` if you want to continue with execution.

With bottom example we restrict remote access and allow only access from localhost.

```
class RestrictAccess {
    public function execute() {
        if (server('REMOTE_ADDR') === '127.0.0.1') {
            return $next();
        }
        
        throw new Exception('Invalid access');
    }
}
```

They can be defined on application / provider level:

```
public function middlewares() {
    return [
        RestrictAccess::class,
    ];
}
```

... or on route level:

```
public function routes() {
    return [
    'route' => route('/route', 'action', Controller::class)
                ->middlewares([RestrictAccess::class]):
    ];
}
```

# Afterwares

The only difference with middlewares is that afterwares gets executed after controller action. Afterwares could be used
for logging or enriching responses.

```
class LogEmptyRequest {
    public function execute() {
        $output = response()->getOutput();
        if ($output) {
            Foo::bar();
        }
        return $next();
    }
}
```
