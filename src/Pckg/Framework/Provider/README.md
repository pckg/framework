# Providers

Providers collect and register different objects, files and other things that are logically connected and needed to
provide some functionality. Many aspects of framework are designed as providers, for example `Environment`
and `Application`.

```

class SomeProvider extends \Pckg\Framework\Provider
{
    public function apps()
    {
        return [
            InheritedApp::class,
        ];
    }

    public function providers()
    {
        return [
            InheritedProvider::class,
        ];
    }

    public function routes()
    {
        return [];
    }

    public function middlewares()
    {
        return [];
    }

    public function afterwares()
    {
        return [];
    }

    /**
      * Twig files loaded as view('Foo/Bar:some/file') will be loaded from 'Foo/Bar/View/some/file.twig'
     */
    public function paths()
    {
        // register auto-load paths for views (Twig)
        return [];
    }

    public function consoles()
    {
        return [];
    }

    /**
      * Assets for Foo/Bar/Provider/Some will be loaded from Foo/Bar/public/.
     */
    public function assets()
    {
        return [
            'group' => [
                'some/asset.js,
            ],
        ];
    }

    /**
      * Object available in ALL Twig templates as variables.
     /*
    public function viewObjects()
    {
        return [];
    }

    public function listeners()
    {
        return [
            'some-event' => [
                SomeEventHandler::class,
                function() {
                    // custom handler
                }
            ],
        ];
    }

    public function autoload()
    {
        return [];
    }

    public function classMaps()
    {
        return [];
    }

    /**
      * See pckg/queue repository.
     */
    public function jobs()
    {
        return [];
    }

    /**
      * Register available services to be resolved.
      * Those are automatically injected and available to be resolved with resolve('name');
     */
    public function services()
    {
        return [
            SomeService::class => function() {
                return new SomeService('some', 'config');
            },
            'bar' => function() {
                return new SomeService('some', 'config');
            },
        ];
    }

    /**
      * Migrations tied to provider.
     /*
    public function migrations()
    {
        return [
            SomeMigration::class,
        ];
    }
}
```
